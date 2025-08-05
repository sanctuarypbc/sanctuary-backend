<?php

namespace App\ApiBundle\Service;

use App\Entity\ClientDetail;
use App\Entity\FirstResponderDetail;
use App\Entity\Organization;
use App\Entity\User;
use App\Enum\CommonEnum;
use App\Enum\FirstResponderEnum;
use App\Enum\StatusEnum;
use App\Enum\UserEnum;
use App\Repository\ClientDetailRepository;
use App\Repository\FirstResponderDetailRepository;
use FOS\UserBundle\Model\UserManager;

/**
 * Class FirstResponderService
 * @package App\ApiBundle\Service
 */
class FirstResponderService
{
    /** @var FirstResponderDetailRepository  */
    private $firstResponderDetailRepository;

    /** @var FirstResponderTypeService  */
    private $firstResponderTypeService;

    /** @var OrganizationService  */
    private $organizationService;

    /** @var UserManager  */
    private $userManager;

    /** @var ClientDetailRepository  */
    private $clientDetailRepository;

    /**
     * ClientService constructor.
     * @param FirstResponderDetailRepository $firstResponderDetailRepository
     * @param FirstResponderTypeService $firstResponderTypeService
     * @param OrganizationService $organizationService
     * @param UserManager $userManager
     * @param ClientDetailRepository $clientDetailRepository
     */
    public function __construct(
        FirstResponderDetailRepository $firstResponderDetailRepository,
        FirstResponderTypeService $firstResponderTypeService,
        OrganizationService $organizationService,
        UserManager $userManager,
        ClientDetailRepository $clientDetailRepository
    ) {
        $this->firstResponderDetailRepository = $firstResponderDetailRepository;
        $this->firstResponderTypeService = $firstResponderTypeService;
        $this->organizationService = $organizationService;
        $this->userManager = $userManager;
        $this->clientDetailRepository = $clientDetailRepository;
    }

    /**
     * @return FirstResponderDetail[]
     */
    public function getAllActiveFRDetails()
    {
        return $this->firstResponderDetailRepository->findBy(['status' => StatusEnum::ACTIVE], ['id' => 'Desc']);
    }

    /**
     * @param Organization $organization
     * @return FirstResponderDetail[]
     */
    public function getAllActiveFRDetailsByOrganization(Organization $organization)
    {
        return $this->firstResponderDetailRepository->findBy(['organization' => $organization , 'status' => StatusEnum::ACTIVE]);
    }

    /**
     * @param int $userId
     * @return \App\Entity\FirstResponderDetail|null
     */
    public function getFirstResponderDetailByUserId(int $userId)
    {
        return $this->firstResponderDetailRepository->findOneBy(['user' => $userId, 'status' => StatusEnum::ACTIVE]);
    }

    /**
     * @param int $userId
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteFirstResponderById(int $userId)
    {
        $frDetailObj = $this->getFirstResponderDetailByUserId($userId);
        if (!$frDetailObj instanceof FirstResponderDetail) {
            return false;
        }

        $client = $this->clientDetailRepository->findOneBy(['firstResponder' => $frDetailObj, 'status' => StatusEnum::ACTIVE]);
        if ($client instanceof ClientDetail) {
            return false;
        }

        $this->firstResponderDetailRepository->deleteFirstResponderDetail($frDetailObj);

        return true;
    }

    /**
     * @param FirstResponderDetail[] $firstResponders
     * @return array
     */
    public function makeFirstResponderResponseByFRDetails($firstResponders)
    {
        $returnResponse = [];
        foreach ($firstResponders as $firstResponder) {
            $returnResponse[] = $this->makeSingleFRResponseByFRDetail($firstResponder);
        }
        return $returnResponse;
    }

    /**
     * @param FirstResponderDetail|null $firstResponderDetail
     * @return array
     */
    public function makeSingleFRResponseByFRDetail(FirstResponderDetail $firstResponderDetail = null)
    {
        $firstResponderResponse = [];
        if (empty($firstResponderDetail)) {
            return $firstResponderResponse;
        }
        $firstResponderUserObject = $firstResponderDetail->getUser();
        $firstResponderResponse = $firstResponderUserObject->toArray();
        $firstResponderResponse = array_merge($firstResponderResponse, $firstResponderDetail->toArray());
        $firstResponderResponse['date_added'] = $firstResponderDetail->getCreated()->format(CommonEnum::DATE_FORMAT);
        $firstResponderResponse['first_responder_type'] = null;
        $firstResponderResponse['organization'] = null;

        if (
            !empty($firstResponderDetail->getFirstResponderType()) &&
            $firstResponderDetail->getFirstResponderType()->getStatus() == StatusEnum::ACTIVE
        ) {
            $firstResponderResponse['first_responder_type'] = $this->firstResponderTypeService
                ->makeSingleTypeResponse($firstResponderDetail->getFirstResponderType());
        }
        if (
            !empty($firstResponderDetail->getOrganization()) &&
            $firstResponderDetail->getOrganization()->getStatus() == StatusEnum::ACTIVE
        ) {
            $firstResponderResponse['organization'] = $this->organizationService
                ->makeSingleOrganizationResponse($firstResponderDetail->getOrganization());
        }

        return $firstResponderResponse;
    }

    /**
     * @param int $userId
     * @param array $data
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateFRByRequest(int $userId, array $data)
    {
        $frUserObj = $this->userManager->findUserBy(['id' => $userId, 'enabled' => StatusEnum::ACTIVE]);
        $frDetailObject = $this->firstResponderDetailRepository
            ->findOneBy(['user' => $frUserObj, 'status' => StatusEnum::ACTIVE]);
        if (empty($frUserObj) || empty($frDetailObject)) {
            return ["status" => false, "message" => "First Responder doesn't exist."];
        }

        if (isset($data['type_id'])) {
            $frTypeObject = $this->firstResponderTypeService->getFirstResponderTypeById((int)$data['type_id']);
            if (empty($frTypeObject)) {
                return ["status" => false, "message" => "Invalid First Responder Type provided."];
            }
            $frDetailObject->setFirstResponderType($frTypeObject);
        }

        if (isset($data['organization_id'])) {
            $organizationObj = $this->organizationService->getOrganizationById((int)$data['organization_id']);
            if (empty($organizationObj)) {
                return ["status" => false, "message" => "Invalid Organization provided."];
            }
            $frDetailObject->setOrganization($organizationObj);
        }

        isset($data['nick_name']) ? $frDetailObject->setNickName($data['nick_name']) : null;
        isset($data['office_phone']) ? $frDetailObject->setOfficePhone($data['office_phone']) : null;
        isset($data['identification_number']) ? $frDetailObject->setIdentificationNumber($data['identification_number']) : null;

        foreach (FirstResponderEnum::FR_USER_ENTITY_POSSIBLE_UPDATE_FIELDS as $parameter => $field) {
            if (isset($data[$parameter])) {
                $setterFun = "set" . $field;
                $frUserObj->$setterFun($data[$parameter]);
            }
        }
        $this->firstResponderDetailRepository->flush();

        return ["status" => true, "message" => "First Responder updated successfully."];
    }

    /**
     * @param array $data
     * @return array|bool[]
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createFRByRequestData(array $data)
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
        $user->setRoles([UserEnum::ROLE_FIRST_RESPONDER]);

        $response = $this->firstResponderDetailRepository->createFRDetailByData($user, $data);
        if ($response['status'] === false) {
            return $response;
        }
        $this->userManager->updateUser($user);

        return ["status" => true, "message" => "First Responder created successfully."];
    }
}
