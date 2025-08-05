<?php

namespace App\Repository;

use App\Entity\AdvocateServiceType;
use App\Enum\StatusEnum;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method AdvocateServiceType|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdvocateServiceType|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdvocateServiceType[]    findAll()
 * @method AdvocateServiceType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvocateServiceTypeRepository extends AbstractRepository
{
    /**
     * AdvocateServiceTypeRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdvocateServiceType::class);
    }

    /**
     * @param $name
     * @return AdvocateServiceType
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addAdvocateServiceType($name)
    {
        $advocateServiceType = new AdvocateServiceType();
        $advocateServiceType->setName($name);

        $this->persist($advocateServiceType, true);
        return $advocateServiceType;
    }

    /**
     * @param $advocateServiceTypeId
     * @param $name
     */
    public function updateAdvocateServiceTypeById($advocateServiceTypeId, $name)
    {
        $this->createQueryBuilder('ast')
            ->update()
            ->set('ast.name', ':name')
            ->where('ast.id=:id')
            ->setParameters(['name' => $name, 'id' => $advocateServiceTypeId])
            ->getQuery()->execute();
    }

    /**
     * @param int $advocateServiceTypeId
     * @return bool
     */
    public function deleteAdvocateServiceTypeById($advocateServiceTypeId)
    {
        $this->createQueryBuilder('ast')
            ->update()
            ->set('ast.status', ':status')
            ->where('ast.id=:id')
            ->setParameters(['status' => StatusEnum::INACTIVE, 'id' => $advocateServiceTypeId])
            ->getQuery()->execute();

        return true;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getAdvocateServiceTypeLikeName($name)
    {
        $qb = $this->createQueryBuilder('ast');
        return $qb->where('ast.name like :name')
            ->andWhere('ast.status = :status')
            ->setParameter('name', '%'.$name.'%')
            ->setParameter('status', StatusEnum::ACTIVE)
            ->getQuery()->getResult();
    }
}
