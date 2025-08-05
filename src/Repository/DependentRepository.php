<?php

namespace App\Repository;

use App\Entity\Dependent;
use App\Enum\StatusEnum;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Dependent|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dependent|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dependent[]    findAll()
 * @method Dependent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DependentRepository extends AbstractRepository
{
    /**
     * DependentRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dependent::class);
    }

    /**
     * @param $clientId
     * @param $dependentId
     * @return Dependent[]
     */
    public function getClientDependentsData($clientId, $dependentId)
    {
        $qb = $this->createQueryBuilder('d')
            ->select('d.id, d.firstName, d.lastName, d.parent, d.gender, d.phone, d.age, d.clothingSize, d.shoeSize, d.created')
            ->leftJoin('d.clientDetail', 'cd')
            ->where('cd.user = :userId')
            ->setParameter('userId', $clientId)
            ->andWhere('cd.status = :clientStatus')
            ->setParameter('clientStatus', StatusEnum::ACTIVE)
            ->andWhere('d.status = :status')
            ->setParameter('status', StatusEnum::ACTIVE)
            ->orderBy('d.id', 'DESC');

        if (!empty($dependentId)) {
            $qb->andWhere('d.id = :dependentId')
                ->setParameter('dependentId', $dependentId);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param $clientId
     * @param $dependentId
     * @return array|bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteClientDependentById($clientId, $dependentId)
    {
        $dependent = $this->findOneBy(['id' => $dependentId, 'status' => StatusEnum::ACTIVE]);
        if (empty($dependent)) {
            return ['status' => false, 'message' => 'Dependent doesn\'t exist.'];
        }

        if ($dependent->getClientDetail()->getUser()->getId() !== $clientId) {
            return ['status' => false, 'message' => 'Dependent is not associated with provided client.'];
        }

        $dependent->setStatus(StatusEnum::INACTIVE);
        $this->flush();

        return true;
    }
}
