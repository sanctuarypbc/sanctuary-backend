<?php

namespace App\Repository;

use App\Entity\ClientDetail;
use App\Entity\ClientInventoryAssignment;
use App\Enum\FacilityInventoryEnum;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ClientInventoryAssignment|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClientInventoryAssignment|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClientInventoryAssignment[]    findAll()
 * @method ClientInventoryAssignment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientInventoryAssignmentRepository extends AbstractRepository
{
    /**
     * ClientInventoryAssignmentRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientInventoryAssignment::class);
    }

    /**
     * @param $inventory
     * @param $clientDetailObj
     * @param $quantity
     * @param $assignedAt
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function assignInventoryToClient($inventory, $clientDetailObj, $quantity, $assignedAt)
    {
        $clientInventoryAssignment = new ClientInventoryAssignment();
        $clientInventoryAssignment->setClientDetail($clientDetailObj)
            ->setFacilityInventory($inventory)
            ->setQuantity($quantity)
            ->setAssignedAt(new \DateTime($assignedAt));

        $this->persist($clientInventoryAssignment);
    }

    /**
     * @param ClientDetail $clientDetailObj
     * @param $type
     * @param $time
     * @throws \Exception
     */
    public function saveClientInventoryAction(ClientDetail $clientDetailObj, $type, $time)
    {
        $qb = $this->createQueryBuilder('cia')
            ->update()
            ->where('cia.clientDetail = :clientDetail')
            ->setParameter('clientDetail', $clientDetailObj);

        if ($type === FacilityInventoryEnum::ACTION_CHECKIN) {
            $qb->set('cia.checkInAt', ':time')
                ->setParameter('time', new \DateTime($time))
                ->andWhere('cia.checkInAt IS NULL');
        } elseif ($type === FacilityInventoryEnum::ACTION_CHECKOUT) {
            $qb->set('cia.checkOutAt', ':time')
                ->setParameter('time', new \DateTime($time))
                ->andWhere('cia.checkOutAt IS NULL');
        }

        $qb->getQuery()->execute();
    }
}
