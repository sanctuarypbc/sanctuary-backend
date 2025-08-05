<?php

namespace App\ApiBundle\Service;

use App\Entity\Goal;
use App\Entity\Task;
use App\Entity\User;
use App\Enum\GoalEnum;
use App\Enum\TaskEnum;
use App\Enum\UserEnum;
use App\Repository\GoalRepository;
use App\Repository\TaskRepository;

/**
 * Class TaskService
 * @package App\ApiBundle\Service
 */
class TaskService
{
    /** @var TaskRepository */
    private $taskRepository;

    /** @var GoalRepository  */
    private $goalRepository;

    /**
     * TaskService constructor.
     * @param TaskRepository $taskRepository
     * @param GoalRepository $goalRepository
     */
    public function __construct(TaskRepository $taskRepository, GoalRepository $goalRepository)
    {
        $this->taskRepository = $taskRepository;
        $this->goalRepository = $goalRepository;
    }

    /**
     * @param User $user
     * @param array $data
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createTask(User $user, array $data)
    {
        if (!in_array(UserEnum::ROLE_SUPER_ADMIN, $user->getRoles())) {
            return "You don't have rights to perform this action.";
        }

        $goalObject = $this->goalRepository->findOneBy(['id' => $data['goal_id'], 'status' => GoalEnum::STATUS_ACTIVE]);
        if (!$goalObject instanceof Goal) {
            return "Goal doesn't exist.";
        }

        $task = $this->taskRepository->create($goalObject, $data['text']);
        $this->taskRepository->persist($task, true);

        return true;
    }

    /**
     * @param User $user
     * @param int $taskId
     * @param array $data
     * @return bool|string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateTask(User $user, int $taskId, array $data)
    {
        if (!in_array(UserEnum::ROLE_SUPER_ADMIN, $user->getRoles())) {
            return "You don't have rights to perform this action.";
        }

        $taskObj = $this->taskRepository->findOneBy(['id' => $taskId, 'status' => TaskEnum::STATUS_ACTIVE]);
        if (!$taskObj instanceof Task) {
            return "Task doesn't exist.";
        }

        $taskObj->setText($data['text']);
        $this->taskRepository->flush();

        return true;
    }

    /**
     * @param User $user
     * @param int $taskId
     * @return bool|string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteTask(User $user, int $taskId)
    {
        if (!in_array(UserEnum::ROLE_SUPER_ADMIN, $user->getRoles())) {
            return "You don't have rights to perform this action.";
        }

        $taskObj = $this->taskRepository->findOneBy(['id' => $taskId, 'status' => TaskEnum::STATUS_ACTIVE]);
        if (!$taskObj instanceof Task) {
            return "Task doesn't exist.";
        }

        $taskObj->setStatus(TaskEnum::STATUS_DELETED);
        $this->taskRepository->flush();

        return true;
    }

    /**
     * @param User $user
     * @param array $data
     * @return int|mixed|string
     */
    public function getTasks(User $user, array $data)
    {
        if (in_array(UserEnum::ROLE_CLIENT, $user->getRoles())) {
            return "You don't have rights to perform this action.";
        }

        return $this->taskRepository->getTasks($data);
    }
}
