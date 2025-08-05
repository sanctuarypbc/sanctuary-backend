<?php

namespace App\Repository;

use App\Entity\ClientGoal;
use App\Entity\ClientGoalTask;
use App\Enum\TaskEnum;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ClientGoalTask|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClientGoalTask|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClientGoalTask[]    findAll()
 * @method ClientGoalTask[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientGoalTaskRepository extends AbstractRepository
{
    /**
     * ClientGoalTaskRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientGoalTask::class);
    }

    /**
     * @param ClientGoal $clientGoal
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(ClientGoal $clientGoal)
    {
        $goalTasks = $this->getEntityManager()->getRepository('App:Task')
            ->findBy(['goal' => $clientGoal->getGoal(), 'status' => TaskEnum::STATUS_ACTIVE]);

        foreach ($goalTasks as $task) {
            $clientGoalTask = new ClientGoalTask();
            $clientGoalTask->setClientGoal($clientGoal);
            $clientGoalTask->setTask($task);

            $this->persist($clientGoalTask);
        }
    }

    /**
     * @param int $clientGoalId
     * @return int|mixed|string
     */
    public function getClientGoalTasks(int $clientGoalId)
    {
        return $this->createQueryBuilder('cgt')
            ->select('cgt.id, t.text, cgt.completed, cgt.completedAt as completed_at')
            ->innerJoin('cgt.task', 't')
            ->where('cgt.clientGoal = :clientGoal')
            ->setParameter('clientGoal', $clientGoalId)
            ->getQuery()->getResult();
    }

    /**
     * @param int $clientGoalId
     * @param false $completedOnly
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getClientGoalTasksCount(int $clientGoalId, $completedOnly = false)
    {
        $qb = $this->createQueryBuilder('cgt')
            ->select('COUNT(1) as count')
            ->where('cgt.clientGoal = :clientGoal')
            ->setParameter('clientGoal', $clientGoalId);

        if ($completedOnly) {
            $qb->andWhere('cgt.completed = :completed')
                ->setParameter('completed', TaskEnum::STATUS_COMPLETED);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}
