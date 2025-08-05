<?php

namespace App\ApiBundle\Service;

use App\Entity\Organization;
use App\Entity\OrganizationType;
use App\Entity\User;
use App\Enum\CommonEnum;
use App\Enum\OrganizationEnum;
use App\Enum\StatusEnum;
use App\Repository\AdvocateDetailRepository;
use App\Repository\FirstResponderDetailRepository;
use App\Repository\OrganizationRepository;

/**
 * Class OrganizationService
 * @package App\ApiBundle\Service
 */
class OrganizationService
{
    /** @var OrganizationRepository  */
    private $organizationRepository;

    /** @var OrganizationTypeService  */
    private $organizationTypeService;

    /** @var ClientTypeService  */
    private $clientTypeService;

    /** @var FirstResponderDetailRepository  */
    private $firstResponderDetailRepository;

    /** @var AdvocateDetailRepository  */
    private $advocateDetailRepository;

    /** @var $userService */
    private $userService;

    /**
     * OrganizationService constructor.
     * @param OrganizationRepository $organizationRepository
     * @param OrganizationTypeService $organizationTypeService
     * @param ClientTypeService $clientTypeService
     * @param FirstResponderDetailRepository $firstResponderDetailRepository
     * @param AdvocateDetailRepository $advocateDetailRepository
     * @param UserService $userService
     */
    public function __construct(
        OrganizationRepository $organizationRepository,
        OrganizationTypeService $organizationTypeService,
        ClientTypeService $clientTypeService,
        FirstResponderDetailRepository $firstResponderDetailRepository,
        AdvocateDetailRepository $advocateDetailRepository,
        UserService $userService
    ) {
        $this->organizationRepository = $organizationRepository;
        $this->organizationTypeService = $organizationTypeService;
        $this->clientTypeService = $clientTypeService;
        $this->firstResponderDetailRepository = $firstResponderDetailRepository;
        $this->advocateDetailRepository = $advocateDetailRepository;
        $this->userService = $userService;
    }

    /**
     * @param int $organizationId
     * @return Organization|null
     */
    public function getOrganizationById($organizationId)
    {
        return $this->organizationRepository->findOneBy(['id' => $organizationId, 'status' => StatusEnum::ACTIVE]);
    }

    /**
     * @param array $firstResponderDetail
     * @return array
     */
    public function getOrganizationStatsById(array $firstResponderDetail)
    {
        $clientTypes = $this->clientTypeService->getClientTypes();
        $jsonResponse = [];
        foreach ($clientTypes as $clientType) {
            $clientsCountByType = $this->clientTypeService->getClientsStatsByFR($firstResponderDetail, $clientType);
            $response['client_type_id'] = $clientType->getId();
            $response['client_type_name'] = $clientType->getName();
            $response['clients_count'] = $clientsCountByType;
            $jsonResponse[] = $response;
        }
        return $jsonResponse;
    }

    /**
     * @param User $user
     * @return Organization|null
     */
    public function getOrganizationByUser(User $user)
    {
        return $this->organizationRepository->findOneBy(['user' => $user, 'status' => StatusEnum::ACTIVE]);
    }

    /**
     * @return Organization[]
     */
    public function getAllActiveOrganizations()
    {
        return $this->organizationRepository->findBy(['status' => StatusEnum::ACTIVE], ['id' => 'Desc']);
    }

    /**
     * @param $organizations
     * @return array
     */
    public function makeOrganizationApiResponse($organizations)
    {
        $response = [];
        foreach ($organizations as $organization) {
            $response[] = $this->makeSingleOrganizationResponse($organization);
        }
        return $response;
    }

    /**
     * @param $organization
     * @return array|bool
     */
    public function makeSingleOrganizationResponse($organization)
    {
        if (!$organization instanceof Organization) {
            return false;
        }
        $singleData = $organization->toArray();
        $singleData['created_on'] = $organization->getCreated()->format(CommonEnum::DATE_FORMAT);
        $singleData['organization_type'] = null;

        if (!empty($organization->getOrganizationType())) {
            $singleData['organization_type'] = $this->organizationTypeService
                ->makeSingleOrganizationTypeResponse($organization->getOrganizationType());
        }

        $clientTypesData = [];
        foreach ($organization->getClientTypes() as $clientType) {
            $singleClientTypeData = $this->clientTypeService->makeSingleTypeResponse($clientType);
            $clientTypesData[] = $singleClientTypeData;
        }
        $singleData['client_types'] = $clientTypesData;

        return $singleData;
    }

    /**
     * @param $data
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createOrganizationByRequestData($data)
    {
        $organizationType = $this->organizationTypeService->getOrganizationTypeById($data['type_id']);
        if (!$organizationType instanceof OrganizationType) {
            return ["status" => false, "message" => "Organization doesn't exist."];
        }

        $organization = new Organization();
        $organization->setStatus(StatusEnum::ACTIVE);
        $organization->setOrganizationType($organizationType);
        foreach (OrganizationEnum::ORGANIZATION_FORM_FIELDS_WITH_PROPERTIES as $field => $property) {
            $setterFun = 'set' . $property;
            isset($data[$field]) ? $organization->$setterFun($data[$field]) : null;
        }

        if (!$this->updateOrganizationClientTypes($organization, $data, false)) {
            return ["status" => false, "message" => "Invalid Client Type provided."];
        }

        $user = $this->userService->createOrganizaionUser($data);
        $organization->setUser($user);
        $this->organizationRepository->persist($organization, true);

        return ["status" => true, "message" => "Organization created successfully.", "data"  => [ "id" => $organization->getId()]];
    }

    /**
     * @param Organization $organization
     * @return bool
     */
    public function checkIfOrganizationIsLinked(Organization $organization)
    {
        $organizationFirstResponder = $this->firstResponderDetailRepository->findOneBy([
            'organization' => $organization,
            'status' => StatusEnum::ACTIVE
        ]);

        if (!empty($organizationFirstResponder)) {
            return true;
        }

        $organizationAdvocate = $this->advocateDetailRepository->findOneBy([
            'organization' => $organization,
            'status' => StatusEnum::ACTIVE
        ]);

        if (!empty($organizationAdvocate)) {
            return true;
        }

        return false;
    }

    /**
     * @param Organization $organization
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteOrganization(Organization $organization)
    {
        $organization->setStatus(StatusEnum::INACTIVE);
        $user = $organization->getUser();
        if ($user instanceof  User) {
            $this->userService->deleteUser($user);
        }
        $this->organizationRepository->flush();

        return true;
    }

    /**
     * @param Organization $organization
     * @param $requestData
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateOrganizationByRequest(Organization $organization, $requestData)
    {
        if (isset($requestData['type_id'])) {
            $organizationType = $this->organizationTypeService->getOrganizationTypeById($requestData['type_id']);
            if (!$organizationType instanceof OrganizationType) {
                return ["status" => false, "message" => "Invalid Organization Type provided."];
            }
            $organization->setOrganizationType($organizationType);
        }

        foreach (OrganizationEnum::ORGANIZATION_FORM_FIELDS_WITH_PROPERTIES as $parameter => $field) {
            if (isset($requestData[$parameter])) {
                $setterFun = "set" . $field;
                $organization->$setterFun($requestData[$parameter]);
            }
        }

        if (!$this->updateOrganizationClientTypes($organization, $requestData)) {
            return ["status" => false, "message" => "Invalid Client Type provided."];
        }
        if (isset($requestData['username']) || isset($requestData['password']) || isset($requestData['contact_email'])) {
            if ($organization->getUser() instanceof  User) {
                $user = $this->userService->updateOrganizaionUser($requestData, $organization->getUser());
            } else {
                if (!isset($requestData['username']) && !isset($requestData['password'])) {
                    return ["status" => false, "message" => "Provide username and password"];
                }
                $user = $this->userService->createOrganizaionUser($requestData);
            }
            $organization->setUser($user);
        }

        $this->organizationRepository->flush();
        return ["status" => true, "message" => "Organization updated successfully."];
    }

    /**
     * @param Organization $organization
     * @param $requestData
     * @param bool $isUpdate
     * @return bool
     */
    public function updateOrganizationClientTypes(Organization $organization, $requestData, $isUpdate = true)
    {
        if (isset($requestData['client_type_ids'])) {
            $isUpdate ? $this->organizationRepository->removeOrganizationClientTypes($organization) : null;
            foreach (explode(',', $requestData['client_type_ids']) as $clientTypeId) {
                $clientTYpeObj = $this->clientTypeService->getClientTypeById($clientTypeId);
                if (empty($clientTYpeObj)) {
                    return false;
                }
                $organization->addClientType($clientTYpeObj);
            }
        }
        return true;
    }
}
