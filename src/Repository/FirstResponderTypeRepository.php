<?php

namespace App\Repository;

use App\Entity\FirstResponderType;
use App\Enum\StatusEnum;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FirstResponderType|null find($id, $lockMode = null, $lockVersion = null)
 * @method FirstResponderType|null findOneBy(array $criteria, array $orderBy = null)
 * @method FirstResponderType[]    findAll()
 * @method FirstResponderType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FirstResponderTypeRepository extends AbstractRepository
{
    /**
     * FirstResponderTypeRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FirstResponderType::class);
    }

    /**
     * @param $name
     * @return FirstResponderType
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addFirstResponderType($name)
    {
        $firstResponderType = new FirstResponderType();
        $firstResponderType->setName($name);

        $this->persist($firstResponderType, true);
        return $firstResponderType;
    }

    /**
     * @param $firstResponderTypeId
     * @param $name
     */
    public function updateFirstResponderTypeById($firstResponderTypeId, $name)
    {
        $this->createQueryBuilder('frt')
            ->update()
            ->set('frt.name', ':name')
            ->where('frt.id=:id')
            ->setParameters(['name' => $name, 'id' => $firstResponderTypeId])
            ->getQuery()->execute();
    }

    /**
     * @param int $firstResponderTypeId
     * @return bool
     */
    public function deleteFirstResponderTypeById($firstResponderTypeId)
    {
        $this->createQueryBuilder('frt')
            ->update()
            ->set('frt.status', ':status')
            ->where('frt.id=:id')
            ->setParameters(['status' => StatusEnum::INACTIVE, 'id' => $firstResponderTypeId])
            ->getQuery()->execute();

        return true;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getFirstResponderTypeLikeName($name)
    {
        $qb = $this->createQueryBuilder('frt');
        return $qb->where('frt.name like :name')
            ->andWhere('frt.status = :status')
            ->setParameter('name', '%'.$name.'%')
            ->setParameter('status', StatusEnum::ACTIVE)
            ->getQuery()->getResult();
    }
}
