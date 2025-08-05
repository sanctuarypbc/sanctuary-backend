<?php

namespace App\Repository;

use App\Entity\Goal;
use App\Entity\Task;
use App\Enum\TaskEnum;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends AbstractRepository
{
    /**
     * TaskRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @param Goal $goal
     * @param string $text
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Goal $goal, string $text)
    {
        $task = new Task();
        $task->setText($text);
        $task->setGoal($goal);

        return $task;
    }

    /**
     * @param array $data
     * @return int|mixed|string
     */
    public function getTasks(array $data)
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t.id, t.text, t.created, t.updated')
            ->where('t.goal = :goal')
            ->andWhere('t.status = :status')
            ->setParameter('goal', (int)$data['goal_id'])
            ->setParameter('status', TaskEnum::STATUS_ACTIVE)
            ->orderBy('t.created', 'ASC');

        if (!empty($data['id'])) {
            $qb->andWhere('t.id = :taskId')
                ->setParameter('taskId', $data['id']);
        }

        return $qb->getQuery()->getResult();
    }
}
