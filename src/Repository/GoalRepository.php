<?php

namespace App\Repository;

use App\Entity\Goal;
use App\Enum\CommonEnum;
use App\Enum\GoalEnum;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Goal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Goal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Goal[]    findAll()
 * @method Goal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GoalRepository extends AbstractRepository
{
    /**
     * GoalRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Goal::class);
    }

    /**
     * @param array $data
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCount(array $data)
    {
        $qb = $this->createQueryBuilder('g')
            ->select('COUNT(1) as count');

        $qb = $this->addFilters($qb, $data);

        return $qb->getQuery()->getSingleScalarResult();
    }
    /**
     * @param array $data
     * @return int|mixed|string
     */
    public function getGoals(array $data)
    {
        $perPage = empty($data['per_page']) ? CommonEnum::PER_PAGE_DEFAULT : (int) $data['per_page'];
        $page = empty($data['page']) ? 1 : (int) $data['page'];
        if ($page <= 0) {
            $page = 1;
        }

        $qb = $this->createQueryBuilder('g')
            ->select('g.id, g.title, g.description, g.created, g.updated')
            ->orderBy('g.created', 'DESC')
            ->setFirstResult(($page-1) * $perPage)
            ->setMaxResults($perPage);

        $qb = $this->addFilters($qb, $data);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param QueryBuilder $qb
     * @param array $data
     * @return QueryBuilder
     */
    public function addFilters(QueryBuilder $qb, array $data)
    {
        $qb->andWhere('g.status = :status')
            ->setParameter('status', GoalEnum::STATUS_ACTIVE);

        if (!empty($data['id'])) {
            $qb->andWhere('g.id = :goadId')
                ->setParameter('goadId', $data['id']);
        }

        return $qb;
    }
}
