<?php

namespace App\Repository;

use App\Entity\Facility;
use App\Entity\FacilityInventory;
use App\Entity\FacilityInventoryType;
use App\Enum\StatusEnum;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

class FacilityInventoryTypeRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FacilityInventoryType::class);
    }

    /**
     * @param Facility $facility
     * @param int|null $id
     * @param bool $getInventoryCount
     * @return array|null
     */
    public function getInventoryTypes(Facility $facility, $id = null, $getInventoryCount = false)
    {
        $qb = $this->createQueryBuilder('it')
            ->select('it.name, it.created, it.id');

        if ($getInventoryCount) {
            $qb = $qb->addSelect('SUM(i.capacity) AS total_capacity, SUM(i.totalAvailable) AS total_availability')
                ->leftJoin(FacilityInventory::class, 'i',Expr\Join::WITH, 'it.id = i.inventoryType AND i.status = :status')
                ->groupBy('it.id');
        } else {
            $qb = $qb->addSelect('i.id AS inventory_id')
                ->leftJoin(FacilityInventory::class, 'i',Expr\Join::WITH, 'it.id = i.inventoryType AND i.status = :status')
                ->groupBy('it.id');
        }

        $qb = $qb->andWhere('it.status = :status')
            ->setParameter('status', StatusEnum::ACTIVE)
            ->andWhere('it.facility = :facility')
            ->setParameter('facility', $facility->getId());

        if ($id) {
            $qb->andWhere('it.id = :id')
            ->setParameter('id', $id);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Facility $facility
     * @param string $name
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Facility $facility, $name)
    {
        $inventoryType = new FacilityInventoryType();
        $inventoryType->setName($name);
        $inventoryType->setFacility($facility);
        $this->persist($inventoryType, true);
    }
}