<?php

namespace App\Repository;

use App\Entity\ClientEmploymentStatus;
use App\Enum\StatusEnum;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ClientEmploymentStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClientEmploymentStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClientEmploymentStatus[]    findAll()
 * @method ClientEmploymentStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientEmploymentStatusRepository extends AbstractRepository
{
    /**
     * ClientEmploymentStatusRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientEmploymentStatus::class);
    }

    /**
     * @param $name
     * @return ClientEmploymentStatus
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addClientEmploymentStatus($name)
    {
        $clientEmploymentStatus = new ClientEmploymentStatus();
        $clientEmploymentStatus->setName($name);

        $this->persist($clientEmploymentStatus, true);
        return $clientEmploymentStatus;
    }

    /**
     * @param $clientEmploymentStatusId
     * @param $name
     */
    public function updateClientEmploymentStatusById($clientEmploymentStatusId, $name)
    {
        $this->createQueryBuilder('ces')
            ->update()
            ->set('ces.name', ':name')
            ->where('ces.id=:id')
            ->setParameters(['name' => $name, 'id' => $clientEmploymentStatusId])
            ->getQuery()->execute();
    }

    /**
     * @param $clientEmploymentStatusId
     * @return bool
     */
    public function deleteClientEmploymentStatusById($clientEmploymentStatusId)
    {
        $this->createQueryBuilder('ces')
            ->update()
            ->set('ces.status', ':status')
            ->where('ces.id=:id')
            ->setParameters(['status' => StatusEnum::INACTIVE, 'id' => $clientEmploymentStatusId])
            ->getQuery()->execute();

        return true;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getClientEmploymentStatusLikeName($name)
    {
        $qb = $this->createQueryBuilder('ces');
        return $qb->where('ces.name like :name')
            ->andWhere('ces.status = :status')
            ->setParameter('name', '%'.$name.'%')
            ->setParameter('status', StatusEnum::ACTIVE)
            ->orderBy('ces.id', 'DESC')
            ->getQuery()->getResult();
    }
}
