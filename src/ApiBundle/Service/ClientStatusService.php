<?php

namespace App\ApiBundle\Service;

use App\Entity\ClientStatus;
use App\Enum\CommonEnum;
use App\Enum\StatusEnum;
use App\Repository\ClientStatusRepository;

/**
 * Class ClientStatusService
 * @package App\ApiBundle\Service
 */
class ClientStatusService
{
    /** @var ClientStatusRepository  */
    private $clientStatusRepository;

    /**
     * ClientStatusService constructor.
     * @param ClientStatusRepository $clientStatusRepository
     */
    public function __construct(ClientStatusRepository $clientStatusRepository)
    {
        $this->clientStatusRepository = $clientStatusRepository;
    }

    /**
     * @param $name
     * @return ClientStatus
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addClientStatus($name)
    {
        return $this->clientStatusRepository->addClientStatus($name);
    }

    /**
     * @param $clientStatusId
     * @param $name
     * @return mixed
     */
    public function updateClientStatusById($clientStatusId, $name)
    {
        return $this->clientStatusRepository->updateClientStatusById($clientStatusId, $name);
    }

    /**
     * @param $clientStatusId
     * @return mixed
     */
    public function deleteClientStatusById($clientStatusId)
    {
        return $this->clientStatusRepository->deleteClientStatusById($clientStatusId);
    }

    /**
     * @param $id
     * @return \App\Entity\ClientStatus|null
     */
    public function getClientStatusById($id)
    {
        return $this->clientStatusRepository->findOneBy(['id' => $id, 'status' => StatusEnum::ACTIVE]);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getClientStatusLikeName($name)
    {
        return $this->clientStatusRepository->getClientStatusLikeName($name);
    }

    /**
     * @param $id
     * @param $name
     * @return \App\Entity\ClientStatus|null
     */
    public function getClientStatusByIdAndName($id, $name)
    {
        return $this->clientStatusRepository->findOneBy(['id' => $id, 'name' => $name, 'status' => StatusEnum::ACTIVE], ['id' => 'DESC']);
    }

    /**
     * @param $clientStatusId
     * @param $name
     * @return array
     */
    public function getClientStatusList($clientStatusId, $name)
    {
        $response = [];
        if ($clientStatusId || $name) {
            $clientStatus = empty($name) ? $this->getClientStatusById($clientStatusId) : (
                empty($clientStatusId) ? $this->getClientStatusLikeName($name) : $this->getClientStatusByIdAndName($clientStatusId, $name)
            );
            if (!empty($clientStatus) && is_array($clientStatus)) {
                foreach ($clientStatus as $item) {
                    $response [] = $this->makeSingleStatusResponse($item);
                }
            } elseif (!empty($clientStatus)) {
                $response [] = $this->makeSingleStatusResponse($clientStatus);

            }
            return $response;
        }

        $clientStatuses = $this->clientStatusRepository->findBy(['status' => StatusEnum::ACTIVE], ['id' => 'DESC']);
        foreach ($clientStatuses as $clientStatus) {
            $response [] = $this->makeSingleStatusResponse($clientStatus);
        }
        return $response;
    }

    /**
     * @param ClientStatus $clientStatus
     * @return array
     */
    public function makeSingleStatusResponse(ClientStatus $clientStatus)
    {
        $singleData = [];
        $singleData['id'] = $clientStatus->getId();
        $singleData['name'] = $clientStatus->getName();
        $singleData['status'] = $clientStatus->getStatus();
        $singleData['created_on'] = $clientStatus->getCreated()->format(CommonEnum::DATE_FORMAT);

        return $singleData;
    }

    /**
     * @return array
     */
    public function getActiveClientStatusesIdAndName()
    {
        return $this->clientStatusRepository->getActiveClientStatusesIdAndName();
    }
}
