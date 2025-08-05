<?php

namespace App\ApiBundle\Service;

use App\Entity\ClientDetail;
use App\Entity\ClientEmploymentStatus;
use App\Enum\CommonEnum;
use App\Enum\StatusEnum;
use App\Repository\ClientDetailRepository;
use App\Repository\ClientEmploymentStatusRepository;

/**
 * Class ClientEmploymentStatusService
 * @package App\ApiBundle\Service
 */
class ClientEmploymentStatusService
{
    /** @var ClientEmploymentStatusRepository  */
    private $clientEmploymentStatusRepository;

    /** @var ClientDetailRepository  */
    private $clientDetailRepository;

    /**
     * ClientEmploymentStatusService constructor.
     * @param ClientEmploymentStatusRepository $clientEmploymentStatusRepository
     * @param ClientDetailRepository $clientDetailRepository
     */
    public function __construct(ClientEmploymentStatusRepository $clientEmploymentStatusRepository, ClientDetailRepository $clientDetailRepository)
    {
        $this->clientEmploymentStatusRepository = $clientEmploymentStatusRepository;
        $this->clientDetailRepository = $clientDetailRepository;
    }

    /**
     * @param $name
     * @return \App\Entity\ClientEmploymentStatus
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addClientEmploymentStatus($name)
    {
        return $this->clientEmploymentStatusRepository->addClientEmploymentStatus($name);
    }

    /**
     * @param $clientEmploymentStatusId
     * @param $name
     */
    public function updateClientEmploymentStatusById($clientEmploymentStatusId, $name)
    {
        return $this->clientEmploymentStatusRepository->updateClientEmploymentStatusById($clientEmploymentStatusId, $name);
    }

    /**
     * @param int $clientEmploymentStatusId
     * @return bool|void
     */
    public function deleteClientEmploymentStatusById($clientEmploymentStatusId)
    {
        $linkedClient = $this->clientDetailRepository
            ->findOneBy(['status' => StatusEnum::ACTIVE, 'clientEmploymentStatus' => $clientEmploymentStatusId]);
        if ($linkedClient instanceof ClientDetail) {
            return false;
        }

        return $this->clientEmploymentStatusRepository->deleteClientEmploymentStatusById($clientEmploymentStatusId);
    }

    /**
     * @param $id
     * @return ClientEmploymentStatus|null
     */
    public function getClientEmploymentStatusById($id)
    {
        return $this->clientEmploymentStatusRepository->findOneBy(['id' => $id, 'status' => StatusEnum::ACTIVE]);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getClientEmploymentStatusLikeName($name)
    {
        return $this->clientEmploymentStatusRepository->getClientEmploymentStatusLikeName($name);
    }

    /**
     * @param $id
     * @param $name
     * @return ClientEmploymentStatus|null
     */
    public function getClientEmploymentStatusByIdAndName($id, $name)
    {
        return $this->clientEmploymentStatusRepository->findOneBy(['id' => $id, 'name' => $name, 'status' => StatusEnum::ACTIVE]);
    }

    /**
     * @param $clientEmploymentStatusId
     * @param $name
     * @return array
     */
    public function getClientEmploymentStatusList($clientEmploymentStatusId, $name)
    {
        $response = [];
        if ($clientEmploymentStatusId || $name) {
            $clientEmploymentStatus = empty($name) ? $this->getClientEmploymentStatusById($clientEmploymentStatusId) : (
                empty($clientEmploymentStatusId) ? $this->getClientEmploymentStatusLikeName($name) : $this->getClientEmploymentStatusByIdAndName($clientEmploymentStatusId, $name)
            );
            if (!empty($clientEmploymentStatus) && is_array($clientEmploymentStatus)) {
                foreach ($clientEmploymentStatus as $item) {
                    $singleData = $this->makeSingleEmploymentStatusResponse($item);
                    $response[] = $singleData;
                }
            } elseif (!empty($clientEmploymentStatus)) {
                $response = $this->makeSingleEmploymentStatusResponse($clientEmploymentStatus);
            }
            return $response;
        }

        $clientEmploymentStatuss = $this->clientEmploymentStatusRepository->findBy(['status' => StatusEnum::ACTIVE], ['id' => 'DESC']);
        foreach ($clientEmploymentStatuss as $clientEmploymentStatus) {
            $singleData = $this->makeSingleEmploymentStatusResponse($clientEmploymentStatus);
            $response[] = $singleData;
        }
        return $response;
    }

    /**
     * @param ClientEmploymentStatus $clientEmploymentStatus
     * @return array
     */
    public function makeSingleEmploymentStatusResponse(ClientEmploymentStatus $clientEmploymentStatus)
    {
        $singleData = [];
        $singleData['id'] = $clientEmploymentStatus->getId();
        $singleData['name'] = $clientEmploymentStatus->getName();
        $singleData['status'] = $clientEmploymentStatus->getStatus();
        $singleData['created_on'] = $clientEmploymentStatus->getCreated()->format(CommonEnum::DATE_FORMAT);

        return $singleData;
    }
}
