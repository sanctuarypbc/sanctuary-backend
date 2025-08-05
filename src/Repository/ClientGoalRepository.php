<?php

namespace App\Repository;

use App\Entity\ClientDetail;
use App\Entity\ClientGoal;
use App\Entity\Goal;
use App\Entity\User;
use App\Enum\GoalEnum;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ClientGoal|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClientGoal|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClientGoal[]    findAll()
 * @method ClientGoal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientGoalRepository extends AbstractRepository
{
    /**
     * ClientGoalRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientGoal::class);
    }

    /**
     * @param User $assignedBy
     * @param ClientDetail $clientDetail
     * @param Goal $goal
     * @return ClientGoal
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(User $assignedBy, ClientDetail $clientDetail, Goal $goal)
    {
        $clientGoal = new ClientGoal();
        $clientGoal->setAssignedBy($assignedBy);
        $clientGoal->setClientDetail($clientDetail);
        $clientGoal->setGoal($goal);

        $this->persist($clientGoal);
        return $clientGoal;
    }

    /**
     * @param ClientDetail $clientDetail
     * @return int|mixed|string
     */
    public function getClientGoals(ClientDetail $clientDetail)
    {
        return $this->createQueryBuilder('cg')
            ->select('cg.id, g.id as goal_id, g.title, g.description, u.username as assignedBy, cg.created, cg.updated')
            ->innerJoin('cg.goal', 'g')
            ->innerJoin('cg.assignedBy', 'u')
            ->where('cg.status = :status')
            ->andWhere('cg.clientDetail = :clientDetail')
            ->setParameter('status', GoalEnum::STATUS_ACTIVE)
            ->setParameter('clientDetail', $clientDetail)
            ->getQuery()->getResult();
    }

    /**
     * @param Goal $goal
     * @return mixed
     */
    public function getClientGoalInfo(Goal $goal)
    {
        return $this->createQueryBuilder('cg')
            ->select('cu.id as user_id, cd.id as client_detail_id, u.id as assigned_by_user_id')
            ->innerJoin('cg.clientDetail', 'cd')
            ->innerJoin("cd.user", "cu")
            ->innerJoin('cg.assignedBy', 'u')
            ->where('cg.status = :status')
            ->andWhere('cg.goal = :goal')
            ->setParameter('status', GoalEnum::STATUS_ACTIVE)
            ->setParameter('goal', $goal)
            ->getQuery()->getResult();
    }
}
