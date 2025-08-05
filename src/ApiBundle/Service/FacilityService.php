<?php

namespace App\ApiBundle\Service;

use App\Entity\ClientDetail;
use App\Entity\Facility;
use App\Entity\User;
use App\Enum\CommonEnum;
use App\Enum\FacilityEnum;
use App\Enum\StatusEnum;
use App\Enum\UserEnum;
use App\Repository\ClientDetailRepository;
use App\Repository\FacilityRepository;
use App\Repository\FacilityTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Model\UserManager;

/**
 * Class FacilityTypeService
 * @package App\ApiBundle\Service
 */
class FacilityService
{
    /** @var FacilityRepository  */
    private $facilityRepository;

    /** @var UtilService  */
    private $utilService;

    /** @var FacilityTypeRepository  */
    private $facilityTypeRepository;

    /** @var FacilityTypeService  */
    private $facilityTypeService;

    /** @var UserManager  */
    private $userManager;

    /** @var ClientDetailRepository  */
    private $clientDetailRepository;

    /**
     * @param User $user
     * @return array
     */
    public function getWaitListedFacilities(User $user)
    {
        $response = [];
        $facilities = $user->getWaitListedFacility();
        foreach ($facilities as $facilityObj) {
            /* @var $facilityObj Facility */
            $facility = [];
            $facility['id'] = $facilityObj->getId();
            $facility['name'] = $facilityObj->getName();
            $facility['created_on'] = $facilityObj->getCreated();
            $facility['facility_type'] = null;
            $facilityTypeObj = $facilityObj->getFacilityType();
            if (!empty($facilityTypeObj)) {
                $facility['facility_type'] = $this->facilityTypeService->makeSingleTypeResponse($facilityTypeObj);
            }
            $response[] = $facility;
        }
        return $response;
    }

    /**
     * FacilityService constructor.
     * @param FacilityRepository $facilityRepository
     * @param UtilService $utilService
     * @param FacilityTypeRepository $facilityTypeRepository
     * @param FacilityTypeService $facilityTypeService
     * @param UserManager $userManager
     */
    public function __construct(
        FacilityRepository $facilityRepository,
        UtilService $utilService,
        FacilityTypeRepository $facilityTypeRepository,
        FacilityTypeService $facilityTypeService,
        UserManager $userManager,
        ClientDetailRepository $clientDetailRepository
    ) {
        $this->facilityRepository = $facilityRepository;
        $this->utilService = $utilService;
        $this->facilityTypeRepository = $facilityTypeRepository;
        $this->facilityTypeService = $facilityTypeService;
        $this->userManager = $userManager;
        $this->clientDetailRepository =  $clientDetailRepository;
    }

    /**
     * @param User $user
     * @param $facilityIds
     * @return array
     */
    public function updateWaitListedFacilities(User $user, $facilityIds)
    {
        if(!empty($facilityIds)) {
            $facilityIds = explode(',', $facilityIds);
        }
        $alreadyExistFacilities = [];
        foreach ($user->getWaitListedFacility() as $facility) {
            $alreadyExistFacilities[] = (string)$facility->getId();
        }
        $removeFacilitiesIds = new ArrayCollection(
            array_diff($alreadyExistFacilities, $facilityIds)
        );

        $removeFacilities = $this->facilityRepository->getFacilitiesWithIds($removeFacilitiesIds);
        foreach ($removeFacilities as $removeFacility) {
            $user->removeWaitListedFacility($removeFacility);
        }
        $newFacilities = $this->facilityRepository->getFacilitiesWithIds($facilityIds);
        foreach ($newFacilities as $facility) {
            $user->addWaitListedFacility($facility);
        }

        return ['status' => true];
    }

    /**
     * @param User $user
     * @param $facilityIds
     * @return array
     */
    public function linkWaitListedFacilities(User $user, $facilityIds)
    {
        $facilityIds = explode(',', $facilityIds);
        if (empty($facilityIds)) {
            return ['status' => false, 'message' => 'Comma separated Facility ids missing'];
        }

        $facilities = $this->facilityRepository->getFacilitiesWithIds($facilityIds);
        foreach ($facilities as $facility) {
            $user->addWaitListedFacility($facility);
        }

        return ['status' => true];
    }

    /**
     * @return \App\Entity\Facility[]
     */
    public function getActiveFacilitiesList()
    {
        return $this->facilityRepository->findBy(['status' => StatusEnum::ACTIVE]);
    }

    /**
     * @param Facility[] $facilities
     * @return array
     */
    public function makeFacilitiesApiResponse($facilities)
    {
        $responseArray = [];
        foreach ($facilities as $facility) {
            $responseArray[] = $this->makeSingleFacilityResponse($facility);
        }
        return $responseArray;
    }

    /**
     * @param Facility $facility
     * @return array
     */
    public function makeSingleFacilityResponse(Facility $facility)
    {
        $responseArray = [];
        $responseArray['id'] = $facility->getId();
        $responseArray['name'] = $facility->getName();
        $responseArray['address'] = $facility->getAddress();
        $responseArray['lat'] = $facility->getLat();
        $responseArray['lng'] = $facility->getLng();
        $responseArray['first_name'] = $facility->getUser()->getFirstName();
        $responseArray['last_name'] = $facility->getUser()->getLastName();
        $responseArray['contact_phone'] = $facility->getUser()->getPhone();
        $responseArray['contact_email'] = $facility->getUser()->getEmail();
        $responseArray['available_beds'] = $facility->getAvailableBeds();
        $responseArray['dependents_allowed'] = $facility->getDependentsAllowed();
        $responseArray['pets_allowed'] = $facility->getPetsAllowed();
        $responseArray['hours_of_operation'] = $facility->getHoursOfOperation();
        $responseArray['status'] = $facility->getStatus();
        $responseArray['created_on'] = $facility->getCreated()->format(CommonEnum::DATE_TIME_FORMAT);
        $responseArray['facility_type'] = [];
        if (!empty($facility->getFacilityType())) {
            $typeDataArray = [];
            $typeDataArray['id'] = $facility->getFacilityType()->getId();
            $typeDataArray['name'] = $facility->getFacilityType()->getName();
            $typeDataArray['status'] = $facility->getFacilityType()->getStatus();
            $typeDataArray['created_on'] = $facility->getFacilityType()->getCreated()->format(CommonEnum::DATE_TIME_FORMAT);

            $responseArray['facility_type'] = $typeDataArray;
        }

        return $responseArray;
    }

    /**
     * @param Request $request
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getFilteredFacilities(Request $request)
    {
        $id = $request->get('id');
        $zipCode = $request->get('zip_code');
        $radius = $request->get('radius');
        $latLngData = [];
        if (!empty($zipCode)) {
            $latLngData = $this->utilService->getLatLngByZipCode($zipCode);
            if (empty($latLngData)) {
                return [];
            }
            $radius = $radius ?: FacilityEnum::DEFAULT_RADIUS;
        }
        $petsAllowed = $request->query->has('pets_allowed') ? $request->get('pets_allowed') : null;
        $getClientCount = $request->query->get('client_details', false);

        return $this->facilityRepository->getFacilitiesByFilters($id, $latLngData, $radius, $petsAllowed, $getClientCount);
    }

    /**
     * @param array $facilities
     * @return array
     */
    public function makeApiResponseByFacilitiesArray($facilities)
    {
        $returnResponse = [];
        foreach ($facilities as $facility) {
            $facility['facility_type'] = null;
            $facility['created_on'] = $facility['created'];
            if (!empty($facility['facility_type_id'])) {
                $facilityTypeObj = $this->facilityTypeRepository->find($facility['facility_type_id']);
                $facility['facility_type'] = $this->facilityTypeService->makeSingleTypeResponse($facilityTypeObj);
            }
            $returnResponse[] = $facility;
        }

        return $returnResponse;
    }

    /**
     * @param array $data
     * @param $desktopLogo
     * @param $mobileLogo
     * @return array|bool[]
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createFacilityFromRequestData(array $data, $desktopLogo, $mobileLogo)
    {
        $name = $this->getFirstLastName($data);

        $latLngData = $this->getLatLng($data);

        if (empty($latLngData)) {
            return ["status" => false, "message" => "Invalid Address provided."];
        }

        $data['lat'] = $latLngData['lat'];
        $data['lng'] = $latLngData['lng'];

        /** @var User $user */
        $user = $this->userManager->createUser();
        $user->setUsername($data['username']);
        $user->setEmail(isset($data['contact_email']) ? $data['contact_email'] : null);
        $user->setPlainPassword($data['password']);
        $user->setPhone(isset($data['contact_phone']) ? $data['contact_phone'] : null);
        $user->setFirstName($name['first_name']);
        $user->setLastName($name['last_name']);
        $user->setEnabled(StatusEnum::ACTIVE);
        $user->setRoles([UserEnum::ROLE_FACILITY]);

        $desktopLogoFileSource = !empty($desktopLogo) ? $this->utilService->moveFile($desktopLogo, FacilityEnum::FACILITY_LOGOS_DIR) : null;
        $mobileLogoFileSource = !empty($mobileLogo) ? $this->utilService->moveFile($mobileLogo, FacilityEnum::FACILITY_LOGOS_DIR) : null;
        $response = $this->facilityRepository->createFacility($user, $data, $desktopLogoFileSource, $mobileLogoFileSource);
        if ($response['status'] === false) {
            return $response;
        }
        $this->userManager->updateUser($user);

        return ["status" => true, "message" => "Facility created successfully."];
    }

    /**
     * @param $data
     * @return array
     */
    private function getFirstLastName($data)
    {
        $name = [];
        $completeName = explode(' ', $data['contact_name']);
        $name['first_name'] = $completeName[0];
        unset($completeName[0]);
        $name['last_name'] = implode(" ", $completeName);
        
        return $name;
    }

    /**
     * @param int $id
     * @return Facility|null
     */
    public function getFacilityById(int $id)
    {
        return $this->facilityRepository->findOneBy(['id' => $id, 'status' => StatusEnum::ACTIVE]);
    }

    /**
     * @param $data
     * @return bool
     */
    private function getLatLng($data) {
        $latLngData = $this->utilService
            ->getLatLngByZipCode($data['street_address'] . " ". $data['city'] . " " . $data['state'] . ", " . $data['zip_code']);

        return $latLngData;
    }


    /**
     * @param $id
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteFacilityById($id)
    {
        $facility = $this->getFacilityById($id);
        if (!$facility instanceof Facility) {
            return false;
        }

        $client = $this->clientDetailRepository->findOneBy(['facility' => $facility, 'status' => StatusEnum::ACTIVE]);
        if ($client instanceof ClientDetail) {
            return false;
        }

        $this->facilityRepository->deleteFacility($facility);

        return true;
    }

    /**
     * @param $id
     * @param $data
     * @param $desktopLogo
     * @param $mobileLogo
     * @return array|bool[]
     */
    public function updateFacility($id, $data, $desktopLogo, $mobileLogo)
    {
        $facility = $this->getFacilityById($id);
        if (!$facility instanceof Facility) {
            return ["status" => false, "message" => "Facility doesn't exist."];
        }

        $latLngData = $this->getLatLng($data);

        if (empty($latLngData)) {
            return ["status" => false, "message" => "Invalid Address provided."];
        }

        $data['lat'] = $latLngData['lat'];
        $data['lng'] = $latLngData['lng'];

        $desktopLogoFileSource = $previousDesktopLogoSource = $facility->getDesktopLogo();
        $mobileLogoFileSource = $previousMobileLogoSource = $facility->getMobileLogo();

        if (!empty($desktopLogo)) {
            $desktopLogoFileSource = $this->utilService->moveFile($desktopLogo, FacilityEnum::FACILITY_LOGOS_DIR);
        }

        if (!empty($mobileLogo)) {
            $mobileLogoFileSource = $this->utilService->moveFile($mobileLogo, FacilityEnum::FACILITY_LOGOS_DIR);
        }

        $response = $this->facilityRepository->setFacilityData($facility, $data, $desktopLogoFileSource, $mobileLogoFileSource);
        if ($response['status'] === false) {
            return $response;
        }
        $user = $facility->getUser();

        if (isset($data['contact_name'])) {
            $name = $this->getFirstLastName($data);
            $user->setFirstName($name['first_name']);
            $user->setLastName($name['last_name']);
        }

        isset($data['contact_phone']) ? $user->setPhone($data['contact_phone']) : null;
        isset($data['contact_email']) ? $user->setEmail($data['contact_email']) : null;
        isset($data['username']) ? $user->setUsername($data['username']) : null;
        $this->userManager->updateUser($user);

        if (!empty($desktopLogo) && $previousDesktopLogoSource && file_exists(".." . $previousDesktopLogoSource)) {
            unlink(".." . $previousDesktopLogoSource);
        }
        if (!empty($mobileLogo) && $previousMobileLogoSource && file_exists(".." . $previousMobileLogoSource)) {
            unlink(".." . $previousMobileLogoSource);
        }

        return ["status" => true, "message" => "Facility updated successfully."];
    }


    /**
     * @param $facilityUserId
     * @return Facility|null
     */
    public function getFacilityByUserId($facilityUserId)
    {
        return $this->facilityRepository->findOneBy(['user' => $facilityUserId, 'status' => StatusEnum::ACTIVE]);
    }

    /**
     * @param $id
     * @param $type
     * @return array|bool
     */
    public function getFacilityLogoUrl($id, $type)
    {
        $facility = $this->getFacilityById($id);
        if(!$facility instanceof Facility) {
            return false;
        }
        if ($type == FacilityEnum::DESKTOP_LOGO_TYPE) {
            $logo = $facility->getDesktopLogo();
        } else {
            $logo = $facility->getMobileLogo();
        }
        return ['logo' => $this->utilService->getProjectRootDir() . $logo,
            'last_modified' => $facility->getUpdated()];
    }

    /**
     * @param string $urlPrefix
     * @return Facility|null
     */
    public function getFacilityByUrlPrefix(string $urlPrefix)
    {
        return $this->facilityRepository->findOneBy(['urlPrefix' => $urlPrefix, 'status' => StatusEnum::ACTIVE]);
    }
}
