<?php

namespace App\Repository;

use App\Entity\ClientOccupation;
use App\Enum\StatusEnum;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ClientOccupation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClientOccupation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClientOccupation[]    findAll()
 * @method ClientOccupation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientOccupationRepository extends AbstractRepository
{
    /**
     * ClientOccupationRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientOccupation::class);
    }
    
    /**
     * @param $name
     * @return ClientOccupation
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addClientOccupation($name)
    {
        $clientOccupation = new ClientOccupation();
        $clientOccupation->setName($name);

        $this->persist($clientOccupation, true);
        return $clientOccupation;
    }

    /**
     * @param $clientOccupationId
     * @param $name
     */
    public function updateClientOccupationById($clientOccupationId, $name)
    {
        $this->createQueryBuilder('co')
            ->update()
            ->set('co.name', ':name')
            ->where('co.id=:id')
            ->setParameters(['name' => $name, 'id' => $clientOccupationId])
            ->getQuery()->execute();
    }

    /**
     * @param $clientOccupationId
     */
    public function deleteClientOccupationById($clientOccupationId)
    {
        $this->createQueryBuilder('co')
            ->update()
            ->set('co.status', ':status')
            ->where('co.id=:id')
            ->setParameters(['status' => StatusEnum::INACTIVE, 'id' => $clientOccupationId])
            ->getQuery()->execute();
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getClientOccupationLikeName($name)
    {
        $qb = $this->createQueryBuilder('co');
        return $qb->where('co.name like :name')
            ->andWhere('co.status = :status')
            ->setParameter('name', '%'.$name.'%')
            ->setParameter('status', StatusEnum::ACTIVE)
            ->getQuery()->getResult();
    }
}
