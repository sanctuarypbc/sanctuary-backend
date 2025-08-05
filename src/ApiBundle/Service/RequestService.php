<?php

namespace App\ApiBundle\Service;

use App\Entity\Request;
use App\Entity\User;
use App\Enum\RequestEnum;
use App\Enum\UserEnum;
use App\Repository\RequestRepository;

/**
 * Class RequestService
 * @package App\ApiBundle\Service
 */
class RequestService
{
    /** @var RequestRepository */
    private $requestRepository;

    /**
     * RequestService constructor.
     * @param RequestRepository $requestRepository
     */
    public function __construct(RequestRepository $requestRepository)
    {
        $this->requestRepository = $requestRepository;
    }

    /**
     * @param User $user
     * @param array $data
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createRequest(User $user, array $data)
    {
        $request = new Request();
        $request->setUser($user);
        $request->setTitle($data['title']);
        !empty($data['description']) ? $request->setDescription($data['description']) : null;

        if (!in_array(UserEnum::ROLE_CLIENT, $user->getRoles())) {
            $request->setIsDefault(RequestEnum::STATUS_DEFAULT);
        }

        $this->requestRepository->persist($request, true);
        return $request->toArray();
    }

    /**
     * @param User $user
     * @param int $requestId
     * @param array $data
     * @return array|string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateRequest(User $user, int $requestId, array $data)
    {
        $requestObj = $this->requestRepository->findOneBy(['id' => $requestId, 'status' => RequestEnum::STATUS_ACTIVE]);
        if (!$requestObj instanceof Request) {
            return "Request doesn't exist.";
        }

        if ($requestObj->getUser() !== $user) {
            return "You don't have rights to perform this action.";
        }

        isset($data['title']) ? $requestObj->setTitle($data['title']) : null;
        isset($data['description']) ? $requestObj->setDescription($data['description']) : null;
        $this->requestRepository->flush();

        return $requestObj->toArray();
    }

    /**
     * @param User $user
     * @param int $requestId
     * @return bool|string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteRequest(User $user, int $requestId)
    {
        $requestObj = $this->requestRepository->findOneBy(['id' => $requestId, 'status' => RequestEnum::STATUS_ACTIVE]);
        if (!$requestObj instanceof Request) {
            return "Request doesn't exist.";
        }

        if ($requestObj->getUser() !== $user) {
            return "You don't have rights to perform this action.";
        }

        $requestObj->setStatus(RequestEnum::STATUS_DELETED);
        $this->requestRepository->flush();

        return true;
    }

    /**
     * @param User $user
     * @param array $data
     * @return int|mixed|string
     */
    public function getRequests(User $user, array $data)
    {
        return $this->requestRepository->getRequests($user, $data);
    }
}
