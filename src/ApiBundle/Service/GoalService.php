<?php

namespace App\ApiBundle\Service;

use App\Entity\Goal;
use App\Entity\User;
use App\Enum\GoalEnum;
use App\Enum\StatusEnum;
use App\Enum\UserEnum;
use App\Repository\GoalRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class GoalService
 * @package App\ApiBundle\Service
 */
class GoalService
{
    /** @var EntityManagerInterface  */
    private $entityManager;

    /**
     * GoalService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param User $user
     * @param array $data
     * @return bool|string
     */
    public function createGoal(User $user, array $data)
    {
        if (!in_array(UserEnum::ROLE_SUPER_ADMIN, $user->getRoles())) {
            return "You don't have rights to perform this action.";
        }

        if (!is_array($data['tasks'])) {
            return "Invalid data format for tasks";
        }

        $goal = new Goal();
        $goal->setTitle($data['title']);
        !empty($data['description']) ? $goal->setDescription($data['description']) : null;

        foreach ($data['tasks'] as $task) {
            $this->entityManager->getRepository('App:Task')->create($goal, $task['text']);
        }

        $this->entityManager->persist($goal);
        $this->entityManager->flush();
        return true;
    }

    /**
     * @param User $user
     * @param int $goalId
     * @param array $data
     * @return bool|string
     */
    public function updateGoal(User $user, int $goalId, array $data)
    {
        if (!in_array(UserEnum::ROLE_SUPER_ADMIN, $user->getRoles())) {
            return "You don't have rights to perform this action.";
        }

        $goalObj = $this->entityManager->getRepository('App:Goal')->findOneBy(['id' => $goalId, 'status' => GoalEnum::STATUS_ACTIVE]);
        if (!$goalObj instanceof Goal) {
            return "Goal doesn't exist.";
        }

        isset($data['title']) ? $goalObj->setTitle($data['title']) : null;
        isset($data['description']) ? $goalObj->setDescription($data['description']) : null;
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param User $user
     * @param int $goalId
     * @return bool|string
     */
    public function deleteGoal(User $user, int $goalId)
    {
        if (!in_array(UserEnum::ROLE_SUPER_ADMIN, $user->getRoles())) {
            return "You don't have rights to perform this action.";
        }

        $goalObj = $this->entityManager->getRepository('App:Goal')->findOneBy(['id' => $goalId, 'status' => GoalEnum::STATUS_ACTIVE]);
        if (!$goalObj instanceof Goal) {
            return "Goal doesn't exist.";
        }

        $goalObj->setStatus(GoalEnum::STATUS_DELETED);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param User $user
     * @param array $data
     * @return array|string
     */
    public function getGoals(User $user, array $data)
    {
        if (in_array(UserEnum::ROLE_CLIENT, $user->getRoles())) {
            return "You don't have rights to perform this action.";
        }

        $totalCount = $this->entityManager->getRepository('App:Goal')->getCount($data);
        $return = ['count' => $totalCount, 'data' => []];

        if ($totalCount > 0) {
            $goals = $this->entityManager->getRepository('App:Goal')->getGoals($data);
            foreach ($goals as $goal) {
                $goalObj = $this->entityManager->getRepository('App:Goal')->findOneBy(['id'=>$goal['id']]);
                $goal['assigned_info'] = $this->entityManager->getRepository('App:ClientGoal')->getClientGoalInfo($goalObj);
                $goal['tasks'] = $this->entityManager->getRepository('App:Task')->getTasks(['goal_id' => $goal['id']]);

                $return['data'][] = $goal;
            }
        }

        return $return;
    }

    /**
     * @param User $user
     * @param array $data
     * @param string $action
     * @return bool|string
     */
    public function assignGoal(User $user, array $data, string $action)
    {
        if (in_array(UserEnum::ROLE_CLIENT, $user->getRoles())) {
            return "You don't have rights to perform this action.";
        }

        $clientUser = $this->entityManager->getRepository('App:User')
            ->findOneBy(['id' => $data['client_id'], 'enabled' => StatusEnum::ACTIVE]);

        if (empty($clientUser) || empty($clientUser->getClientDetail())) {
            return "Invalid client provided.";
        }

        if (!in_array($action, GoalEnum::ACTION_ARRAY)) {
            return "Invalid action value provided.";
        }

        $response = $action === GoalEnum::ACTION_ASSIGN ? $this->processGoalsAssignToClient($user, $clientUser, $data) :
            $this->processGoalsUnAssignToClient($user, $clientUser, $data);
        if ($response !== true) {
            return $response;
        }
        $this->entityManager->flush();
        return true;
    }

    /**
     * @param User $user
     * @param User $clientUser
     * @param array $data
     * @return string
     */
    public function processGoalsAssignToClient(User $user, User $clientUser, array $data)
    {
        foreach ($data['goal_ids'] as $goalId) {
            $goalObj = $this->entityManager->getRepository('App:Goal')
                ->findOneBy(['id' => (int)$goalId, 'status' => GoalEnum::STATUS_ACTIVE]);
            if (empty($goalObj)) {
                return "Goal doesn't exist";
            }
            $clientGoal = $this->entityManager->getRepository('App:ClientGoal')
                ->create($user, $clientUser->getClientDetail(), $goalObj);

            $this->entityManager->getRepository('App:ClientGoalTask')->create($clientGoal);
        }

        return true;
    }

    /**
     * @param User $user
     * @param User $clientUser
     * @param array $data
     * @return string
     */
    public function processGoalsUnAssignToClient(User $user, User $clientUser, array $data)
    {
        foreach ($data['goal_ids'] as $goalId) {
            $goalObj = $this->entityManager->getRepository('App:Goal')
                ->findOneBy(['id' => (int)$goalId, 'status' => GoalEnum::STATUS_ACTIVE]);
            if (empty($goalObj)) {
                continue;
            }
            $clientGoal = $this->entityManager->getRepository('App:ClientGoal')
                ->findOneBy(['goal' => $goalObj, 'clientDetail' => $clientUser->getClientDetail()]);

            !empty($clientGoal) ? $this->entityManager->remove($clientGoal) : null;
        }

        return true;
    }

    /**
     * @param User $user
     * @param array $data
     * @param bool $userIsClient
     * @return array|string
     */
    public function getClientGoals(User $user, array $data, bool $userIsClient)
    {
        $clientDetailObj = $user->getClientDetail();
        if (!$userIsClient) {
            $clientUser = $this->entityManager->getRepository('App:User')
                ->findOneBy(['id' => $data['client_id'], 'enabled' => StatusEnum::ACTIVE]);
            if (empty($clientUser)) {
                return "Client doesn't exist.";
            }

            $clientDetailObj = $clientUser->getClientDetail();
        }

        $returnArray = [];
        $clientGoals = $this->entityManager->getRepository('App:ClientGoal')->getClientGoals($clientDetailObj);
        foreach ($clientGoals as $clientGoal) {
            $clientGoal['total_tasks'] = $this->entityManager->getRepository('App:ClientGoalTask')
                ->getClientGoalTasksCount($clientGoal['id']);
            $clientGoal['completed_tasks'] = $this->entityManager->getRepository('App:ClientGoalTask')
                ->getClientGoalTasksCount($clientGoal['id'], true);
            $clientGoal['tasks'] = $this->entityManager->getRepository('App:ClientGoalTask')
                ->getClientGoalTasks($clientGoal['id']);
            $returnArray[] = $clientGoal;
        }

        return $returnArray;
    }

    /**
     * @param User $user
     * @param array $data
     * @param bool $userIsClient
     * @return array|string
     */
    public function getClientGoalTasks(User $user, array $data, bool $userIsClient)
    {
        $clientDetailObj = $user->getClientDetail();
        if (!$userIsClient) {
            $clientUser = $this->entityManager->getRepository('App:User')
                ->findOneBy(['id' => $data['client_id'], 'enabled' => StatusEnum::ACTIVE]);
            if (empty($clientUser)) {
                return "Client doesn't exist.";
            }

            $clientDetailObj = $clientUser->getClientDetail();
        }

        $clientGoal = $this->entityManager->getRepository('App:ClientGoal')
            ->findOneBy(['id' => $data['client_goal_id'], 'clientDetail' => $clientDetailObj]);
        if (empty($clientGoal)) {
            return "Client goal doesn't exist.";
        }

        return $this->entityManager->getRepository('App:ClientGoalTask')
            ->getClientGoalTasks($clientGoal->getId());
    }

    /**
     * @param User $user
     * @param array $data
     * @return bool|string
     */
    public function processTask(User $user, array $data)
    {
        $clientGoalTaskObj = $this->entityManager->getRepository('App:ClientGoalTask')
            ->find((int)$data['client_goal_task_id']);

        if (empty($clientGoalTaskObj)) {
            return "Task doesn't exist.";
        }
        $userIsAdvocate = in_array(UserEnum::ROLE_ADVOCATE, $user->getRoles());
        if ($clientGoalTaskObj->getClientGoal()->getClientDetail()->getUser() !== $user && !$userIsAdvocate) {
            return "You don't have rights to perform this action.";
        }

        $clientGoalTaskObj->setCompleted($data['completed']);
        $clientGoalTaskObj->setCompletedAt(new \DateTime('now'));
        $this->entityManager->flush();

        return true;
    }
}
