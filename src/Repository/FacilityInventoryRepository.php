<?php

namespace App\Repository;

use App\Entity\Facility;
use App\Entity\FacilityInventory;
use App\Entity\FacilityInventoryType;
use App\Enum\FacilityEnum;
use App\Enum\StatusEnum;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class FacilityInventoryRepository
 * @package App\Repository
 */
class FacilityInventoryRepository extends AbstractRepository
{
    /**
     * FacilityInventoryRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FacilityInventory::class);
    }

    /**
     * @param Facility $facility
     * @param $id
     * @return array
     */
    public function getInventoriesByFacility(Facility $facility, $id)
    {
        if (!empty($id)) {
            return $this->findBy(['facility' => $facility, 'id' => $id, 'status' => StatusEnum::ACTIVE]);
        }
        return $this->findBy(['facility' => $facility, 'status' => StatusEnum::ACTIVE]);
    }

    /**
     * @param Facility $facility
     * @param $data
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Facility $facility, $data)
    {
        $inventory = new FacilityInventory();
        $inventory->setName(isset($data['name']) ? $data['name'] : null);
        $inventory->setCapacity(isset($data['capacity']) ? $data['capacity'] : null);

        if (isset($data['total_available'])) {
            $inventory->setTotalAvailable($data['total_available']);
            $inventory->setAvailabilityUpdateAt(new \DateTime('now'));

            if ($data['total_available'] > 0 && !$facility->getAvailableBeds()) {
                $facility->setAvailableBeds(FacilityEnum::BEDS_AVAILABLE);
            }
        }

        if (isset($data['inventory_type_id'])) {
            /** @var FacilityInventoryType $inventoryTypeObj */
            $inventoryTypeObj = $this->getEntityManager()->getRepository('App:FacilityInventoryType')
                ->findOneBy(['id' => $data['inventory_type_id'], 'status' => StatusEnum::ACTIVE]);

            if (empty($inventoryTypeObj) || $inventoryTypeObj->getFacility()->getId() !== $facility->getId()) {
                return ["status" => false, "message" => "Invalid Inventory Type provided."];
            }

//            $inventoryWithType = $this->findOneBy(['inventoryType' => $inventoryTypeObj, 'status' => StatusEnum::ACTIVE]);
//            if (!empty($inventoryWithType)) {
//                return ["status" => false, "message" => "Inventory type is already assigned to an inventory"];
//            }
            $inventory->setInventoryType($inventoryTypeObj);
        }
        $inventory->setFacility($facility);
        $this->persist($inventory, true);
        return ["status" => true, "message" => "Inventory Type created successfully."];
    }

    /**
     * @param Facility $facility
     * @param $data
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateFacilityAvailableBeds(Facility $facility)
    {
        $totalAvailable = $this->createQueryBuilder('fi')
            ->where('fi.status = :status')
            ->andWhere('fi.facility = :facility')
            ->setParameters(['facility' => $facility, 'status' => StatusEnum::ACTIVE])
            ->select('SUM(fi.totalAvailable)')
            ->getQuery()
            ->getSingleScalarResult();

        $facility->setAvailableBeds($totalAvailable > 0);
        $this->flush();
    }

    /**
     * @param FacilityInventoryType $facilityInventoryType
     * @return bool
     */
    public function checkIfInventoryTypeLinked(FacilityInventoryType $facilityInventoryType)
    {
        $inventory = $this->findOneBy(['inventoryType' => $facilityInventoryType, 'status' => StatusEnum::ACTIVE]);
        return $inventory instanceof FacilityInventory;
    }

    /**
     * @param FacilityInventory $facilityInventory
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateInventoryOnBooking(FacilityInventory $facilityInventory)
    {
        $totalAvailable = (int)$facilityInventory->getTotalAvailable() - FacilityEnum::ADD_BOOKING;
        if ($totalAvailable >= 0) {
            $facilityInventory->setTotalAvailable($totalAvailable);
            $this->flush();
            return true;
        }

        return false;
    }

    /**
     * @param FacilityInventory $facilityInventory
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateInventoryOnCheckOut(FacilityInventory $facilityInventory)
    {
        $totalAvailable = (int)$facilityInventory->getTotalAvailable() + FacilityEnum::ADD_BOOKING;
        if ((int)$facilityInventory->getCapacity() >= $totalAvailable) {
            $facilityInventory->setTotalAvailable($totalAvailable);
            $this->flush();
            return true;
        }

        return false;
    }

    /**
     * @param Facility $facility
     * @param FacilityInventoryType $facilityInventoryType
     * @param boolean $isCheckOut
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @return QueryBuilder
     */
    public function getFacilityAvailableBeds(Facility $facility, FacilityInventoryType $facilityInventoryType, $isCheckOut)
    {
        $qb = $this->createQueryBuilder('fi')
            ->select()
            ->where('fi.status = :status')
            ->andWhere('fi.facility = :facility')
            ->andWhere('fi.inventoryType = :inventoryType')
            ->setParameters(['facility' => $facility, 'status' => StatusEnum::ACTIVE, 'inventoryType' => $facilityInventoryType]);

        if ($isCheckOut) {
            $qb->andWhere('fi.totalAvailable >= 0');
        }else{
            $qb->andWhere('fi.totalAvailable > 0');
        }

        $resultSet = $qb->getQuery()->getResult();

        if(!empty($resultSet)){
            return $resultSet[0];
        }

        return false;
    }
}