<?php

namespace App\Repository;

use App\Entity\ClientType;
use App\Enum\StatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ClientType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClientType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClientType[]    findAll()
 * @method ClientType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientTypeRepository extends AbstractRepository
{
    /**
     * ClientTypeRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientType::class);
    }

    /**
     * @param $name
     * @return ClientType
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addClientType($name)
    {
        $clientType = new ClientType();
        $clientType->setName($name);

        $this->persist($clientType, true);
        return $clientType;
    }

    /**
     * @param $clientTypeId
     * @param $name
     */
    public function updateClientTypeById($clientTypeId, $name)
    {
        $this->createQueryBuilder('ct')
            ->update()
            ->set('ct.name', ':name')
            ->where('ct.id=:id')
            ->setParameters(['name' => $name, 'id' => $clientTypeId])
            ->getQuery()->execute();
    }

    /**
     * @param $clientTypeId
     * @return bool
     */
    public function deleteClientTypeById($clientTypeId)
    {
        $this->createQueryBuilder('ct')
            ->update()
            ->set('ct.status', ':status')
            ->where('ct.id=:id')
            ->setParameters(['status' => StatusEnum::INACTIVE, 'id' => $clientTypeId])
            ->getQuery()->execute();

        return true;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getClientTypeLikeName($name)
    {
        $qb = $this->createQueryBuilder('ct');
        return $qb->where('ct.name like :name')
            ->andWhere('ct.status = :status')
            ->setParameter('name', '%'.$name.'%')
            ->setParameter('status', StatusEnum::ACTIVE)
            ->getQuery()->getResult();
    }
}
