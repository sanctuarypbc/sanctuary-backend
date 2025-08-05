<?php

namespace App\Repository;

use App\Entity\ClientStatus;
use App\Enum\StatusEnum;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ClientStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClientStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClientStatus[]    findAll()
 * @method ClientStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientStatusRepository extends AbstractRepository
{
    /**
     * ClientStatusRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientStatus::class);
    }

    /**
     * @param $name
     * @return ClientStatus
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addClientStatus($name)
    {
        $clientStatus = new ClientStatus();
        $clientStatus->setName($name);

        $this->persist($clientStatus, true);
        return $clientStatus;
    }

    /**
     * @param $clientStatusId
     * @param $name
     */
    public function updateClientStatusById($clientStatusId, $name)
    {
        $this->createQueryBuilder('cs')
            ->update()
            ->set('cs.name', ':name')
            ->where('cs.id=:id')
            ->setParameters(['name' => $name, 'id' => $clientStatusId])
            ->getQuery()->execute();
    }

    /**
     * @param $clientStatusId
     */
    public function deleteClientStatusById($clientStatusId)
    {
        $this->createQueryBuilder('cs')
            ->update()
            ->set('cs.status', ':status')
            ->where('cs.id=:id')
            ->setParameters(['status' => StatusEnum::INACTIVE, 'id' => $clientStatusId])
            ->getQuery()->execute();
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getClientStatusLikeName($name)
    {
        $qb = $this->createQueryBuilder('cs');
        return $qb->where('cs.name like :name')
            ->andWhere('cs.status = :status')
            ->setParameter('name', '%'.$name.'%')
            ->setParameter('status', StatusEnum::ACTIVE)
            ->orderBy('cs.id', 'DESC')
            ->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function getActiveClientStatusesIdAndName()
    {
        return $this->createQueryBuilder('cs')
            ->select('cs.id, cs.name')
            ->where('cs.status = :status')
            ->setParameter('status', StatusEnum::ACTIVE)
            ->getQuery()->getResult();
    }
}
