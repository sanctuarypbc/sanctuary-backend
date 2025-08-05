<?php

namespace App\ApiBundle\Service;

use App\Entity\ClientOccupation;
use App\Enum\CommonEnum;
use App\Enum\StatusEnum;
use App\Repository\ClientOccupationRepository;

/**
 * Class ClientOccupationService
 * @package App\ApiBundle\Service
 */
class ClientOccupationService
{
    /** @var ClientOccupationRepository  */
    private $clientOccupationRepository;

    /**
     * ClientOccupationService constructor.
     * @param ClientOccupationRepository $clientOccupationRepository
     */
    public function __construct(ClientOccupationRepository $clientOccupationRepository)
    {
        $this->clientOccupationRepository = $clientOccupationRepository;
    }

    /**
     * @param $name
     * @return \App\Entity\ClientOccupation
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addClientOccupation($name)
    {
        return $this->clientOccupationRepository->addClientOccupation($name);
    }

    /**
     * @param $clientOccupationId
     * @param $name
     */
    public function updateClientOccupationById($clientOccupationId, $name)
    {
        return $this->clientOccupationRepository->updateClientOccupationById($clientOccupationId, $name);
    }

    /**
     * @param $clientOccupationId
     */
    public function deleteClientOccupationById($clientOccupationId)
    {
        return $this->clientOccupationRepository->deleteClientOccupationById($clientOccupationId);
    }

    /**
     * @param $id
     * @return ClientOccupation|null
     */
    public function getClientOccupationById($id)
    {
        return $this->clientOccupationRepository->findOneBy(['id' => $id, 'status' => StatusEnum::ACTIVE]);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getClientOccupationLikeName($name)
    {
        return $this->clientOccupationRepository->getClientOccupationLikeName($name);
    }

    /**
     * @param $id
     * @param $name
     * @return ClientOccupation|null
     */
    public function getClientOccupationByIdAndName($id, $name)
    {
        return $this->clientOccupationRepository->findOneBy(['id' => $id, 'name' => $name, 'status' => StatusEnum::ACTIVE]);
    }

    /**
     * @param $clientOccupationId
     * @param $name
     * @return array
     */
    public function getClientOccupationList($clientOccupationId, $name)
    {
        $response = [];
        if ($clientOccupationId || $name) {
            $clientOccupation = empty($name) ? $this->getClientOccupationById($clientOccupationId) : (
                empty($clientOccupationId) ? $this->getClientOccupationLikeName($name) : $this->getClientOccupationByIdAndName($clientOccupationId, $name)
            );
            if (!empty($clientOccupation) && is_array($clientOccupation)) {
                foreach ($clientOccupation as $item) {
                    $singleData = $this->makeSingleOccupationResponse($item);
                    $response[] = $singleData;
                }
            } elseif (!empty($clientOccupation)) {
                $response = $this->makeSingleOccupationResponse($clientOccupation);
            }
            return $response;
        }

        $clientOccupations = $this->clientOccupationRepository->findBy(['status' => StatusEnum::ACTIVE], ['id' => 'DESC']);
        foreach ($clientOccupations as $clientOccupation) {
            $singleData = $this->makeSingleOccupationResponse($clientOccupation);
            $response[] = $singleData;
        }
        return $response;
    }

    /**
     * @param ClientOccupation $clientOccupation
     * @return array
     */
    public function makeSingleOccupationResponse(ClientOccupation $clientOccupation)
    {
        $singleData = [];
        $singleData['id'] = $clientOccupation->getId();
        $singleData['name'] = $clientOccupation->getName();
        $singleData['status'] = $clientOccupation->getStatus();
        $singleData['created_on'] = $clientOccupation->getCreated()->format(CommonEnum::DATE_FORMAT);

        return $singleData;
    }
}
