<?php

namespace App\ApiBundle\Service;

use App\Entity\ClientRequest;
use App\Entity\ClientRequestComment;
use App\Entity\User;
use App\Enum\RequestEnum;
use App\Repository\ClientRequestCommentRepository;
use App\Repository\ClientRequestRepository;

/**
 * Class ClientRequestCommentService
 * @package App\ApiBundle\Service
 */
class ClientRequestCommentService
{
    /** @var ClientRequestCommentRepository */
    private $clientRequestCommentRepository;

    /** @var ClientRequestRepository  */
    private $clientRequestRepository;

    /**
     * ClientRequestCommentService constructor.
     * @param ClientRequestCommentRepository $clientRequestCommentRepository
     * @param ClientRequestRepository $clientRequestRepository
     */
    public function __construct(
        ClientRequestCommentRepository $clientRequestCommentRepository,
        ClientRequestRepository $clientRequestRepository
    ) {
        $this->clientRequestCommentRepository = $clientRequestCommentRepository;
        $this->clientRequestRepository = $clientRequestRepository;
    }

    /**
     * @param User $user
     * @param array $data
     * @return ClientRequestComment|string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createClientRequestComment(User $user, array $data)
    {
        $clientRequestObj = $this->clientRequestRepository->find($data['client_request_id']);
        if (!$clientRequestObj instanceof ClientRequest) {
            return "Client request doesn't exist.";
        }

        $clientRequestComment = new ClientRequestComment();
        $clientRequestComment->setUser($user);
        $clientRequestComment->setText($data['text']);
        $clientRequestComment->setClientRequest($clientRequestObj);
        $this->clientRequestCommentRepository->persist($clientRequestComment, true);

        return $clientRequestComment;
    }

    /**
     * @param User $user
     * @param int $clientRequestCommentId
     * @param array $data
     * @return bool|string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateClientRequestComment(User $user, int $clientRequestCommentId, array $data)
    {
        $clientRequestCommentObj = $this->clientRequestCommentRepository
            ->findOneBy(['id' => $clientRequestCommentId, 'status' => RequestEnum::STATUS_ACTIVE]);
        if (!$clientRequestCommentObj instanceof ClientRequestComment) {
            return "Comment doesn't exist.";
        }

        if ($clientRequestCommentObj->getUser() !== $user) {
            return "You don't have rights to update this comment.";
        }

        $clientRequestCommentObj->setText($data['text']);
        $this->clientRequestCommentRepository->flush();

        return true;
    }

    /**
     * @param User $user
     * @param int $clientRequestCommentId
     * @return bool|string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteClientRequestComment(User $user, int $clientRequestCommentId)
    {
        $clientRequestCommentObj = $this->clientRequestCommentRepository
            ->findOneBy(['id' => $clientRequestCommentId, 'status' => RequestEnum::STATUS_ACTIVE]);
        if (!$clientRequestCommentObj instanceof ClientRequestComment) {
            return "Comment doesn't exist.";
        }

        if ($clientRequestCommentObj->getUser() !== $user) {
            return "You don't have rights to update this comment.";
        }

        $clientRequestCommentObj->setStatus(RequestEnum::STATUS_DELETED);
        $this->clientRequestCommentRepository->flush();

        return true;
    }

    /**
     * @param User $user
     * @param array $data
     * @return int|mixed|string
     */
    public function getClientRequestComments(User $user, array $data)
    {
        $clientRequestObj = $this->clientRequestRepository->find($data['client_request_id']);
        if (empty($clientRequestObj)) {
            return "Client request doesn't exist.";
        }

        if ($clientRequestObj->getClientDetail()->getUser() !== $user && $clientRequestObj->getClientDetail()->getAdvocate()->getUser() !== $user) {
            return "You don't have rights to this client request.";
        }

        $totalCount = $this->clientRequestCommentRepository->getCount($user, $data);
        $dataArray = [];

        if ($totalCount > 0) {
            $dataArray = $this->clientRequestCommentRepository->getClientRequestComments($user, $data);
        }
        return ['count' => $totalCount, 'data' => array_reverse($dataArray)];
    }
}
