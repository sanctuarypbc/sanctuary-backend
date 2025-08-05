<?php

namespace App\ApiBundle\Service;

use App\Entity\AdvocateDetail;
use App\Entity\ClientDetail;
use App\Entity\User;
use App\Enum\AdvocateEnum;
use App\Enum\StatusEnum;
use App\Enum\UserEnum;
use App\Repository\AdvocateDetailRepository;
use App\Repository\ClientDetailRepository;
use App\Repository\LanguageRepository;
use FOS\UserBundle\Model\UserManager;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdvocateService
 * @package App\ApiBundle\Service
 */
class AdvocateService
{
    /** @var AdvocateDetailRepository  */
    private $advocateDetailRepository;

    /** @var OrganizationService  */
    private $organizationService;

    /** @var AdvocateServiceTypeService  */
    private $advocateServiceTypeService;

    /** @var ClientDetailRepository  */
    private $clientDetailRepository;

    /** @var UserManager  */
    private $userManager;

    /** @var LanguageRepository  */
    private $languageRepository;

    /**
     * AdvocateService constructor.
     * @param AdvocateDetailRepository $advocateDetailRepository
     * @param OrganizationService $organizationService
     * @param AdvocateServiceTypeService $advocateServiceTypeService
     * @param ClientDetailRepository $clientDetailRepository
     * @param UserManager $userManager
     * @param LanguageRepository $languageRepository
     */
    public function __construct(
        AdvocateDetailRepository $advocateDetailRepository,
        OrganizationService $organizationService,
        AdvocateServiceTypeService $advocateServiceTypeService,
        ClientDetailRepository $clientDetailRepository,
        UserManager $userManager,
        LanguageRepository $languageRepository
    ) {
        $this->advocateDetailRepository = $advocateDetailRepository;
        $this->organizationService = $organizationService;
        $this->advocateServiceTypeService = $advocateServiceTypeService;
        $this->clientDetailRepository = $clientDetailRepository;
        $this->userManager = $userManager;
        $this->languageRepository = $languageRepository;
    }

    /**
     * @param int $userId
     * @return AdvocateDetail|null
     */
    public function getAdvocateDetailByUserId(int $userId)
    {
        return $this->advocateDetailRepository->findOneBy(['user' => $userId, 'status' => StatusEnum::ACTIVE]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getFilteredAdvocates(Request $request)
    {
        $filterParameters = [];
        $filterParameters['id'] = $request->get('id');
        $filterParameters['searchText'] = $request->get('search_text');
        $filterParameters['identifier'] = $request->get('identifier');

        return $this->advocateDetailRepository->getFilteredAdvocates($filterParameters);
    }

    /**
     * @param AdvocateDetail[] $advocateDetails
     * @return array
     */
    public function makeAdvocatesApiResponse($advocateDetails)
    {
        $returnResponse = [];
        foreach ($advocateDetails as $advocateDetail) {
            $returnResponse[] = $this->makeSingleAdvocateResponseByAdvocateDetail($advocateDetail);
        }
        return $returnResponse;
    }

    /**
     * @param AdvocateDetail|null $advocateDetail
     * @return array
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function makeSingleAdvocateResponseByAdvocateDetail(AdvocateDetail $advocateDetail = null)
    {
        $advocateResponse = [];
        if (empty($advocateDetail)) {
            return $advocateResponse;
        }
        $userObject = $advocateDetail->getUser();
        $advocateResponse['id'] = $userObject->getId();
        $advocateResponse['first_name'] = $userObject->getFirstName();
        $advocateResponse['last_name'] = $userObject->getLastName();
        $advocateResponse['username'] = $userObject->getUsername();
        $advocateResponse['email'] = $userObject->getEmail();
        $advocateResponse['phone'] = $userObject->getPhone();
        $advocateResponse['identifier'] = $advocateDetail->getIdentifier();
        $advocateResponse['additional_phone'] = $advocateDetail->getAdditionalPhone();
        $advocateResponse['emergency_contact'] = $advocateDetail->getEmergencyContact();
        $advocateResponse['assigned_clients'] = $this->advocateDetailRepository->getAdvocateClientsCount($advocateDetail);
        $advocateResponse['organization'] = null;
        $advocateResponse['service_type'] = null;

        if (!empty($advocateDetail->getOrganization())) {
            $advocateResponse['organization'] = $this->organizationService
                ->makeSingleOrganizationResponse($advocateDetail->getOrganization());
        }
        if (!empty($advocateDetail->getServiceType())) {
            $advocateResponse['service_type'] = $this->advocateServiceTypeService
                ->makeSingleTypeResponse($advocateDetail->getServiceType());
        }

        $languagesData = [];
        foreach ($advocateDetail->getLanguage() as $language) {
            $singleLanguageData = [];
            $singleLanguageData['id'] = $language->getId();
            $singleLanguageData['name'] = $language->getName();
            $singleLanguageData['locale'] = $language->getLocale();
            $languagesData[] = $singleLanguageData;
        }
        $advocateResponse['languages'] = $languagesData;

        return $advocateResponse;
    }

    /**
     * @param int $userId
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteAdvocateById(int $userId)
    {
        $advocateDetailObj = $this->getAdvocateDetailByUserId($userId);
        if (!$advocateDetailObj instanceof AdvocateDetail) {
            return false;
        }

        $client = $this->clientDetailRepository->findOneBy(['advocate' => $advocateDetailObj, 'status' => StatusEnum::ACTIVE]);
        if ($client instanceof ClientDetail) {
            return false;
        }
        $this->advocateDetailRepository->deleteAdvocateDetail($advocateDetailObj);

        return true;
    }

    /**
     * @param $data
     * @return array|bool[]
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createAdvocateByRequestData($data)
    {
        /** @var User $user */
        $user = $this->userManager->createUser();
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setPlainPassword($data['password']);
        $user->setPhone(isset($data['phone']) ? $data['phone'] : null);
        $user->setGender(isset($data['gender']) ? $data['gender'] : null);
        $user->setFirstName(isset($data['first_name']) ? $data['first_name'] : null);
        $user->setLastName(isset($data['last_name']) ? $data['last_name'] : null);
        $user->setEnabled(StatusEnum::ACTIVE);
        $user->setRoles([UserEnum::ROLE_ADVOCATE]);

        $response = $this->advocateDetailRepository->createAdvocateDetailByData($user, $data);
        if ($response['status'] === false) {
            return $response;
        }
        $this->userManager->updateUser($user);

        return ["status" => true, "message" => "Advocate created successfully."];
    }

    /**
     * @param int $userId
     * @param array $data
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateAdvocateByRequest(int $userId, array $data)
    {
        $advocateUserObj = $this->userManager->findUserBy(['id' => $userId, 'enabled' => StatusEnum::ACTIVE]);
        $advocateDetailObject = $this->advocateDetailRepository
            ->findOneBy(['user' => $advocateUserObj, 'status' => StatusEnum::ACTIVE]);
        if (empty($advocateUserObj) || empty($advocateDetailObject)) {
            return ["status" => false, "message" => "Advocate doesn't exist."];
        }

        if (isset($data['service_type_id'])) {
            $advocateServiceTypeObject = $this->advocateServiceTypeService->getAdvocateServiceTypeById((int)$data['service_type_id']);
            if (empty($advocateServiceTypeObject)) {
                return ["status" => false, "message" => "Invalid Service Type provided."];
            }
            $advocateDetailObject->setServiceType($advocateServiceTypeObject);
        }

        if (isset($data['organization_id'])) {
            $organizationObj = $this->organizationService->getOrganizationById((int)$data['organization_id']);
            if (empty($organizationObj)) {
                return ["status" => false, "message" => "Invalid Organization provided."];
            }
            $advocateDetailObject->setOrganization($organizationObj);
        }

        if (isset($data['language_ids'])) {
            $this->advocateDetailRepository->removeAdvocateLanguages($advocateDetailObject);
            foreach (explode(',', $data['language_ids']) as $languageId) {
                $languageObj = $this->languageRepository->findOneBy(['id' => $languageId, 'status' => StatusEnum::ACTIVE]);
                if (empty($languageObj)) {
                    return ["status" => false, "message" => "Invalid Language provided."];
                }
                $advocateDetailObject->addLanguage($languageObj);
            }
        }

        isset($data['additional_phone']) ? $advocateDetailObject->setAdditionalPhone($data['additional_phone']) : null;
        isset($data['emergency_contact']) ? $advocateDetailObject->setEmergencyContact($data['emergency_contact']) : null;
        isset($data['identifier']) ? $advocateDetailObject->setIdentifier($data['identifier']) : null;

        foreach (AdvocateEnum::ADVOCATE_USER_ENTITY_POSSIBLE_UPDATE_FIELDS as $parameter => $field) {
            if (isset($data[$parameter])) {
                $setterFun = "set" . $field;
                $advocateUserObj->$setterFun($data[$parameter]);
            }
        }
        $this->advocateDetailRepository->flush();

        return ["status" => true, "message" => "Advocate updated successfully."];
    }

}
