<?php

namespace App\ApiBundle\Service;

use App\Entity\Dependent;
use App\Enum\DependentEnum;
use App\Enum\StatusEnum;
use App\Repository\DependentRepository;

/**
 * Class DependentService
 * @package App\ApiBundle\Service
 */
class DependentService
{
    /** @var DependentRepository */
    private $dependentRepository;

    /** @var ClientService  */
    private $clientService;

    /**
     * DependentService constructor.
     * @param DependentRepository $dependentRepository
     * @param ClientService $clientService
     */
    public function __construct(DependentRepository $dependentRepository, ClientService $clientService) {
        $this->dependentRepository = $dependentRepository;
        $this->clientService = $clientService;
    }

    /**
     * @param $clientId
     * @param null $dependentId
     * @return Dependent[]
     */
    public function getClientDependents($clientId, $dependentId = null)
    {
        $client = $this->clientService->getClientDetailByUserId($clientId);
        if (empty($client)) {
            return ['status' => false, 'message' => 'Client doesn\'t exist.'];
        }

        return $this->dependentRepository->getClientDependentsData($clientId, $dependentId);
    }

    /**
     * @param $clientId
     * @param $dependentId
     * @return array|bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteClientDependentById($clientId, $dependentId)
    {
        $client = $this->clientService->getClientDetailByUserId($clientId);
        if (empty($client)) {
            return ['status' => false, 'message' => 'Client doesn\'t exist.'];
        }

        return $this->dependentRepository->deleteClientDependentById($clientId, $dependentId);
    }

    /**
     * @param $id
     * @param $clientId
     * @param $data
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateClientDependentByRequest($id, $clientId, $data)
    {
        $client = $this->clientService->getClientDetailByUserId($clientId);
        if (empty($client)) {
            return ['status' => false, 'message' => 'Client doesn\'t exist.'];
        }

        $dependent = $this->dependentRepository->findOneBy(['id' => $id, 'status' => StatusEnum::ACTIVE]);
        if (empty($dependent)) {
            return ['status' => false, 'message' => 'Dependent doesn\'t exist.'];
        }

        if ($dependent->getClientDetail()->getUser()->getId() !== $clientId) {
            return ['status' => false, 'message' => 'Dependent is not associated with provided client.'];
        }

        foreach (DependentEnum::DEPENDENT_POSSIBLE_UPDATE_FIELDS as $parameter => $field) {
            if (isset($data[$parameter])) {
                $setterFun = "set" . $field;
                $dependent->$setterFun($data[$parameter]);
            }
        }
        $this->dependentRepository->flush();

        return ["status" => true, "message" => "Dependent updated successfully."];
    }

    /**
     * @param $clientId
     * @param $dependentsData
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createClientDependentsByRequestData($clientId, $dependentsData)
    {
        $client = $this->clientService->getClientDetailByUserId($clientId);
        if (empty($client)) {
            return ['status' => false, 'message' => 'Client doesn\'t exist.'];
        }

        foreach ($dependentsData as $dependentsDatum) {
            $dependent = new Dependent();
            $dependent->setClientDetail($client);
            foreach (DependentEnum::DEPENDENT_POSSIBLE_UPDATE_FIELDS as $parameter => $field) {
                if (isset($dependentsDatum[$parameter])) {
                    $setterFun = "set" . $field;
                    $dependent->$setterFun($dependentsDatum[$parameter]);
                }
            }
            $this->dependentRepository->persist($dependent);
        }
        $this->dependentRepository->flush();

        return ["status" => true, "message" => "Dependent created successfully."];
    }
}
