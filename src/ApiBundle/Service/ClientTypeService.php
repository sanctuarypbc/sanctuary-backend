<?php

namespace App\ApiBundle\Service;

use App\Entity\ClientDetail;
use App\Entity\ClientType;
use App\Enum\CommonEnum;
use App\Enum\StatusEnum;
use App\Repository\ClientDetailRepository;
use App\Repository\ClientTypeRepository;

/**
 * Class ClientTypeService
 * @package App\ApiBundle\Service
 */
class ClientTypeService
{
    /** @var ClientTypeRepository  */
    private $clientTypeRepository;

    /** @var ClientDetailRepository  */
    private $clientDetailRepository;

    /**
     * ClientTypeService constructor.
     * @param ClientTypeRepository $clientTypeRepository
     * @param ClientDetailRepository $clientDetailRepository
     */
    public function __construct(ClientTypeRepository $clientTypeRepository, ClientDetailRepository $clientDetailRepository)
    {
        $this->clientTypeRepository = $clientTypeRepository;
        $this->clientDetailRepository = $clientDetailRepository;
    }

    /**
     * @param $name
     * @return \App\Entity\ClientType
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addClientType($name)
    {
        return $this->clientTypeRepository->addClientType($name);
    }

    /**
     * @param $clientTypeId
     * @param $name
     */
    public function updateClientTypeById($clientTypeId, $name)
    {
        return $this->clientTypeRepository->updateClientTypeById($clientTypeId, $name);
    }

    /**
     * @return ClientType[]
     */
    public function getClientTypes()
    {
        return $this->clientTypeRepository->findBy(['status' => StatusEnum::ACTIVE]);
    }

    /**
     * @param int $clientTypeId
     * @return bool|void
     */
    public function deleteClientTypeById($clientTypeId)
    {
        $linkedClient = $this->clientDetailRepository
            ->findOneBy(['status' => StatusEnum::ACTIVE, 'clientType' => $clientTypeId]);
        if ($linkedClient instanceof ClientDetail) {
            return false;
        }

        return $this->clientTypeRepository->deleteClientTypeById($clientTypeId);
    }

    /**
     * @param $id
     * @return ClientType|null
     */
    public function getClientTypeById($id)
    {
        return $this->clientTypeRepository->findOneBy(['id' => $id, 'status' => StatusEnum::ACTIVE]);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getClientTypeLikeName($name)
    {
        return $this->clientTypeRepository->getClientTypeLikeName($name);
    }

    /**
     * @param $id
     * @param $name
     * @return ClientType|null
     */
    public function getClientTypeByIdAndName($id, $name)
    {
        return $this->clientTypeRepository->findOneBy(['id' => $id, 'name' => $name, 'status' => StatusEnum::ACTIVE]);
    }

    /**
     * @param $clientTypeId
     * @param $name
     * @return array
     */
    public function getClientTypeList($clientTypeId, $name)
    {
        $response = [];
        if ($clientTypeId || $name) {
            $clientType = empty($name) ? $this->getClientTypeById($clientTypeId) : (
                empty($clientTypeId) ? $this->getClientTypeLikeName($name) : $this->getClientTypeByIdAndName($clientTypeId, $name)
            );
            if (!empty($clientType) && is_array($clientType)) {
                foreach ($clientType as $item) {
                    $singleData = $this->makeSingleTypeResponse($item);
                    $response[] = $singleData;
                }
            } elseif (!empty($clientType)) {
                $response = $this->makeSingleTypeResponse($clientType);
            }
            return $response;
        }

        $clientTypes = $this->clientTypeRepository->findBy(['status' => StatusEnum::ACTIVE], ['id' => 'DESC']);
        foreach ($clientTypes as $clientType) {
            $singleData = $this->makeSingleTypeResponse($clientType);
            $response[] = $singleData;
        }
        return $response;
    }

    /**
     * @param ClientType $clientType
     * @return array
     */
    public function makeSingleTypeResponse(ClientType $clientType)
    {
        $singleData = [];
        $singleData['id'] = $clientType->getId();
        $singleData['name'] = $clientType->getName();
        $singleData['status'] = $clientType->getStatus();
        $singleData['created_on'] = $clientType->getCreated()->format(CommonEnum::DATE_FORMAT);

        return $singleData;
    }

    /**
     * @param array $firstResponders
     * @param ClientType $clientType
     * @return ClientDetail[]
     */
    public function getClientsStatsByFR(array $firstResponders, ClientType $clientType)
    {
        return $this->clientDetailRepository->getClientsCountByType($firstResponders, $clientType);
    }
}
