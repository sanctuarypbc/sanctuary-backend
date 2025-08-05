<?php

namespace App\ApiBundle\Service;

use App\Entity\Facility;
use App\Entity\FacilityInventory;
use App\Entity\FacilityInventoryType;
use App\Entity\User;
use App\Enum\CommonEnum;
use App\Enum\FacilityEnum;
use App\Enum\StatusEnum;
use App\Enum\UserEnum;
use App\Repository\FacilityInventoryRepository;
use App\Repository\FacilityInventoryTypeRepository;
use Symfony\Component\Security\Core\Security;

/**
 * Class FacilityInventoryService
 * @package App\ApiBundle\Service
 */
class FacilityInventoryService
{
    /** @var UtilService  */
    private $utilService;

    /** @var FacilityService */
    private $facilityService;

    /** @var FacilityInventoryRepository */
    private $inventoryRepository;

    /** @var User $user */
    private $user;

    /** @var FacilityInventoryTypeRepository  */
    private $facilityInventoryTypeRepository;

    /**
     * FacilityInventoryService constructor.
     * @param UtilService $utilService
     * @param FacilityService $facilityService
     * @param FacilityInventoryRepository $facilityInventoryRepository
     * @param Security $security
     * @param FacilityInventoryTypeRepository $facilityInventoryTypeRepository
     */
    public function __construct(
        UtilService $utilService,
        FacilityService $facilityService,
        FacilityInventoryRepository $facilityInventoryRepository,
        Security $security,
        FacilityInventoryTypeRepository $facilityInventoryTypeRepository
    ) {
        $this->utilService = $utilService;
        $this->facilityService = $facilityService;
        $this->inventoryRepository = $facilityInventoryRepository;
        $this->user = $security->getUser();
        $this->facilityInventoryTypeRepository = $facilityInventoryTypeRepository;
    }

    /**
     * @param int|null $id
     * @param int|null $facilityUserId
     * @return array|bool
     */
    public function getInventories($id = null,$facilityUserId = null)
    {

        if (in_array(UserEnum::ROLE_SUPER_ADMIN, $this->user->getRoles())) {
            $facility = $this->facilityService->getFacilityByUserId($facilityUserId);
        } else {
            $facility = $this->facilityService->getFacilityByUserId($this->user->getId());
        }

        // Check if provided facility id is valid
        if (!$facility instanceof Facility) {
            return false;
        }

        return $this->inventoryRepository->getInventoriesByFacility($facility, $id);
    }

    /**
     * @param $data
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createInventory($data)
    {
        $facility = $this->facilityService->getFacilityByUserId($this->user->getId());
        if (empty($facility)) {
            return ["status" => false, "message" => "Facility doesn't exist."];
        }

        return $this->inventoryRepository->create($facility, $data);
    }

    /**
     * @param $id
     * @param $data
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateInventory($id, $data)
    {
        $inventory = $this->getInventory($id);

        if (!$inventory instanceof FacilityInventory) {
            return ["status" => false, "message" => "Inventory doesn't exist."];
        }

        isset($data['name']) ? $inventory->setName($data['name']) : null;
        isset($data['capacity']) ? $inventory->setCapacity($data['capacity']) : null;

        if (isset($data['total_available'])) {
            $inventory->setTotalAvailable($data['total_available']);
            $inventory->setAvailabilityUpdateAt(new \DateTime('now'));
        }

        if (isset($data['inventory_type_id'])) {
            $inventoryTypeObj = $this->facilityInventoryTypeRepository->findOneBy([
                'id' => $data['inventory_type_id'],
                'facility' => $inventory->getFacility()
            ]);

            if (empty($inventoryTypeObj)) {
                return ["status" => false, "message" => "Invalid Inventory Type provided."];
            }

            $inventoryWithType = $this->facilityInventoryTypeRepository
                ->findOneBy(['inventoryType' => $inventoryTypeObj, 'status' => StatusEnum::ACTIVE]);
            if (!empty($inventoryWithType)) {
                return ["status" => false, "message" => "Inventory type is already assigned to an inventory"];
            }
            $inventory->setInventoryType($inventoryTypeObj);
        }
        $this->inventoryRepository->flush();
        $this->updateFacilityAvailableBeds($inventory->getFacility(), $data);
        return ["status" => true, "message" => "Inventory updated successfully."];
    }

    /**
     * @param Facility $facility
     * @param $data
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateFacilityAvailableBeds(Facility $facility, $data)
    {
        if (
            isset($data['total_available']) &&
            $data['total_available'] > 0 &&
            !$facility->getAvailableBeds()
        ) {
            $facility->setAvailableBeds(FacilityEnum::BEDS_AVAILABLE);
        } elseif (
            isset($data['total_available']) &&
            $data['total_available'] < 1 &&
            $facility->getAvailableBeds()
        ) {
            $this->inventoryRepository->updateFacilityAvailableBeds($facility);
        }

        $this->inventoryRepository->flush();
    }

    /**
     * @param int $id
     * @param bool $checkFacility
     * @return object|null
     */
    public function getInventory($id, $checkFacility = true)
    {
        $filter = ['id' => $id, 'status' => StatusEnum::ACTIVE];
        if ($checkFacility) {
            $facility = $this->facilityService->getFacilityByUserId($this->user->getId());
            if (!$facility instanceof Facility) {
                return null;
            }
            $filter['facility'] = $facility->getId();
        }

        return $this->inventoryRepository->findOneBy($filter);
    }

    /**
     * @param $id
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteInventory($id)
    {
        $inventory = $this->getInventory($id);

        if (!$inventory instanceof FacilityInventory) {
            return false;
        }

        $inventory->setStatus(StatusEnum::INACTIVE);
        $this->inventoryRepository->flush();

        $this->inventoryRepository->updateFacilityAvailableBeds($inventory->getFacility());

        return true;
    }

    /**
     * @param $inventories
     * @return array
     */
    public function makeInventoriesAPIResponse($inventories)
    {
        $response = [];
        foreach ($inventories as $inventory) {
            $response[] = $this->makeSingleInventoryResponse($inventory);
        }
        return $response;
    }

    /**
     * @param FacilityInventory $inventory
     * @return array
     */
    public function makeSingleInventoryResponse($inventory)
    {
        $singleData = [];
        if (empty($inventory)) {
            return $singleData;
        }
        $singleData['id'] = $inventory->getId();
        $singleData['name'] = $inventory->getName();
        $singleData['capacity'] = $inventory->getCapacity();
        $singleData['total_available'] = $inventory->getTotalAvailable();
        $singleData['availability_updated_at'] = $inventory->getAvailabilityUpdateAt() ?
            $inventory->getAvailabilityUpdateAt()->format(CommonEnum::DATE_FORMAT) :
            null;
        $singleData['status'] = $inventory->getStatus();
        $singleData['created_on'] = $inventory->getCreated()->format(CommonEnum::DATE_FORMAT);
        $singleData['inventory_type'] = null;

        if (!empty($inventory->getInventoryType())) {
            $inventoryTypeData = [];
            $inventoryTypeData['id'] = $inventory->getInventoryType()->getId();
            $inventoryTypeData['name'] = $inventory->getInventoryType()->getName();
            $inventoryTypeData['status'] = $inventory->getInventoryType()->getStatus();
            $inventoryTypeData['created'] = $inventory->getInventoryType()->getCreated()->format(CommonEnum::DATE_FORMAT);

            $singleData['inventory_type'] = $inventoryTypeData;
        }

        return $singleData;
    }

    /**
     * @param FacilityInventory $inventory
     * @param $quantity
     * @return bool
     */
    public function checkInventoryAvailability(FacilityInventory $inventory, $quantity)
    {
        return !($inventory->getTotalAvailable() - $quantity < 0);
    }

    /**
     * @param FacilityInventoryType $facilityInventoryType
     * @param Facility $facility
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateInventoryAvailablity(FacilityInventoryType $facilityInventoryType, Facility $facility, $isCheckOut = false)
    {
        $facilityInventory = $this->inventoryRepository->getFacilityAvailableBeds($facility, $facilityInventoryType, $isCheckOut);

        if (!$facilityInventory instanceof FacilityInventory) {
            return false;
        }

        if ($isCheckOut) {
            return $this->inventoryRepository->updateInventoryOnCheckOut($facilityInventory);
        }

        return $this->inventoryRepository->updateInventoryOnBooking($facilityInventory);
    }
}