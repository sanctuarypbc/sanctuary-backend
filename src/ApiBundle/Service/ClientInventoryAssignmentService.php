<?php

namespace App\ApiBundle\Service;

use App\Entity\ClientDetail;
use App\Entity\FacilityInventory;
use App\Entity\User;
use App\Enum\FacilityInventoryEnum;
use App\Repository\ClientInventoryAssignmentRepository;

/**
 * Class ClientInventoryAssignmentService
 * @package App\ApiBundle\Service
 */
class ClientInventoryAssignmentService
{
    /** @var ClientInventoryAssignmentRepository  */
    private $clientInventoryAssignmentRepository;

    /** @var FacilityInventoryService  */
    private $facilityInventoryService;

    /** @var ClientService  */
    private $clientService;

    /**
     * DependentService constructor.
     * @param ClientInventoryAssignmentRepository $clientInventoryAssignmentRepository
     * @param FacilityInventoryService $facilityInventoryService
     * @param ClientService $clientService
     */
    public function __construct(
        ClientInventoryAssignmentRepository $clientInventoryAssignmentRepository,
        FacilityInventoryService $facilityInventoryService,
        ClientService $clientService
    ) {
        $this->clientInventoryAssignmentRepository = $clientInventoryAssignmentRepository;
        $this->facilityInventoryService = $facilityInventoryService;
        $this->clientService = $clientService;
    }

    /**
     * @param $data
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function assignInventoryToClient($data)
    {
        if (!is_array($data['inventory_data'])) {
            return ['status' => false, 'message' => 'Invalid inventory data provided'];
        }

        $clientDetailObj = $this->clientService->getClientDetailByUserId((int)$data['client_id']);
        $response = $this->validateInventoryData($clientDetailObj, $data);
        if ($response['status'] === false) {
            return $response;
        }

        foreach ($data['inventory_data'] as $inventoryData) {
            /** @var FacilityInventory $inventory */
            $inventory = $this->facilityInventoryService->getInventory($inventoryData['inventory_id']);

            $this->clientInventoryAssignmentRepository
                ->assignInventoryToClient($inventory, $clientDetailObj, $inventoryData['quantity'], $data['assigned_at']);

            $inventory->setTotalAvailable($inventory->getTotalAvailable() - $inventoryData['quantity']);
        }
        $this->clientInventoryAssignmentRepository->flush();

        return ['status' => true, 'message' => "Inventory assigned to client"];
    }

    /**
     * @param ClientDetail $clientDetailObj
     * @param $data
     * @return array|bool[]
     */
    public function validateInventoryData(ClientDetail $clientDetailObj, $data)
    {
        foreach ($data['inventory_data'] as $inventoryData) {
            if (!isset($inventoryData['inventory_id']) || !isset($inventoryData['quantity'])) {
                return ['status' => false, 'message' => 'Invalid inventory data provided'];
            }

            /** @var FacilityInventory $inventory */
            $inventory = $this->facilityInventoryService->getInventory($inventoryData['inventory_id']);
            if (empty($inventory)) {
                return ['status' => false, 'message' => 'Inventory doesn\'t exist'];
            }

            $inventoryAvailable = $this->facilityInventoryService->checkInventoryAvailability($inventory, $inventoryData['quantity']);
            if (!$inventoryAvailable) {
                return ['status' => false, 'message' => 'Inventory not available'];
            }

            if (empty($clientDetailObj) || $clientDetailObj->getFacility()->getId() !== $inventory->getFacility()->getId()) {
                return ['status' => false, 'message' => 'Invalid client id provided'];
            }

            $clientInventory = $this->clientInventoryAssignmentRepository
                ->findOneBy(['clientDetail' => $clientDetailObj, 'facilityInventory' => $inventory]);
            if (!empty($clientInventory)) {
                return ['status' => false, 'message' => 'Inventory already assigned to requested client'];
            }

            $inventoryAvailable = $this->facilityInventoryService->checkInventoryAvailability($inventory, $inventoryData['quantity']);
            if (!$inventoryAvailable) {
                return ['status' => false, 'message' => 'Inventory ' . $inventory->getName() . ' not available'];
            }
        }

        return ['status' => true];
    }

    /**
     * @param User $user
     * @param $data
     * @return array
     * @throws \Exception
     */
    public function saveClientInventoryAction(User $user, $data)
    {
        $clientDetailObj = $this->clientService->getClientDetailByUserId((int)$data['client_id']);
        if (empty($clientDetailObj) || $clientDetailObj->getFacility()->getId() !== $user->getFacility()->getId()) {
            return ['status' => false, 'message' => 'Invalid client id provided'];
        }

        if ($data['type'] !== FacilityInventoryEnum::ACTION_CHECKIN && $data['type'] !== FacilityInventoryEnum::ACTION_CHECKOUT) {
            return ['status' => false, 'message' => 'Invalid type provided'];
        }

        $this->clientInventoryAssignmentRepository->saveClientInventoryAction($clientDetailObj, $data['type'], $data['time']);
        return ['status' => true, 'message' => "Action performed successfully"];
    }
}
