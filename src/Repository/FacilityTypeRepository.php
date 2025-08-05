<?php

namespace App\Repository;

use App\Entity\FacilityType;
use App\Enum\StatusEnum;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FacilityType|null find($id, $lockMode = null, $lockVersion = null)
 * @method FacilityType|null findOneBy(array $criteria, array $orderBy = null)
 * @method FacilityType[]    findAll()
 * @method FacilityType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FacilityTypeRepository extends AbstractRepository
{
    /**
     * FacilityTypeRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FacilityType::class);
    }

    /**
     * @param $name
     * @return FacilityType
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addFacilityType($name)
    {
        $facilityType = new FacilityType();
        $facilityType->setName($name);

        $this->persist($facilityType, true);
        return $facilityType;
    }

    /**
     * @param $facilityTypeId
     * @param $name
     */
    public function updateFacilityTypeById($facilityTypeId, $name)
    {
        $this->createQueryBuilder('ft')
            ->update()
            ->set('ft.name', ':name')
            ->where('ft.id=:id')
            ->setParameters(['name' => $name, 'id' => $facilityTypeId])
            ->getQuery()->execute();
    }

    /**
     * @param int $facilityTypeId
     * @return bool
     */
    public function deleteFacilityTypeById($facilityTypeId)
    {
        $this->createQueryBuilder('ft')
            ->update()
            ->set('ft.status', ':status')
            ->where('ft.id=:id')
            ->setParameters(['status' => StatusEnum::INACTIVE, 'id' => $facilityTypeId])
            ->getQuery()->execute();

        return true;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getFacilityTypeLikeName($name)
    {
        $qb = $this->createQueryBuilder('ft');
        return $qb->where('ft.name like :name')
            ->andWhere('ft.status = :status')
            ->setParameter('name', '%' . $name . '%')
            ->setParameter('status', StatusEnum::ACTIVE)
            ->getQuery()->getResult();
    }
}
