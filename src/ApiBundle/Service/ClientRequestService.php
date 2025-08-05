<?php

namespace App\ApiBundle\Service;

use App\Entity\ClientRequest;
use App\Entity\Request;
use App\Entity\User;
use App\Enum\RequestEnum;
use App\Enum\UserEnum;
use App\Repository\ClientRequestRepository;
use App\Repository\RequestRepository;

/**
 * Class RequestService
 * @package App\ApiBundle\Service
 */
class ClientRequestService
{
    /** @var ClientRequestRepository  */
    private $clientRequestRepository;

    /** @var RequestRepository  */
    private $requestRepository;

    /** @var ClientService  */
    private $clientService;

    /**
     * RequestService constructor.
     * @param ClientRequestRepository $clientRequestRepository
     * @param RequestRepository $requestRepository
     * @param ClientService $clientService
     */
    public function __construct(
        ClientRequestRepository $clientRequestRepository,
        RequestRepository $requestRepository,
        ClientService $clientService
    ) {
        $this->clientRequestRepository = $clientRequestRepository;
        $this->requestRepository = $requestRepository;
        $this->clientService = $clientService;
    }

    /**
     * @param User $user
     * @param array $data
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @return mixed|integer
     */
    public function createClientRequest(User $user, array $data)
    {
        $requestObj = $this->requestRepository->findOneBy(['id' => $data['request_id'], 'status' => RequestEnum::STATUS_ACTIVE]);
        if (!$requestObj instanceof Request) {
            return "Request doesn't exist.";
        }

        $clientRequestExist = $this->clientRequestRepository
            ->findOneBy(['clientDetail' => $user->getClientDetail(), 'request' => $requestObj]);
        if ($clientRequestExist) {
            return "You have already applied for this request.";
        }

        $clientRequest = new ClientRequest();
        $clientRequest->setClientDetail($user->getClientDetail());
        $clientRequest->setRequest($requestObj);
        $clientRequest->setStatus(RequestEnum::CLIENT_REQUEST_PENDING);
        $this->requestRepository->persist($clientRequest, true);

        return $clientRequest;
    }

    /**
     * @param int $clientRequestId
     * @param array $data
     * @return bool|string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateClientRequest(int $clientRequestId, array $data)
    {
        $clientRequestObj = $this->clientRequestRepository->find($clientRequestId);
        if (!$clientRequestObj instanceof ClientRequest) {
            return "Client request doesn't exist.";
        }

        if (isset($data['status']) && !in_array($data['status'], RequestEnum::CLIENT_REQUEST_STATUS_ARRAY)) {
            return "Invalid status value provided.";
        }

        if (isset($data['status'])) {
            $clientRequestObj->setStatus($data['status']);
            $this->clientRequestRepository->flush();
        } else {
            $request = $this->requestRepository->findOneBy(['id' => $clientRequestObj->getRequest()]);
            if (isset($data['title'])) {
                $request->setTitle($data['title']);
            }
            if (isset($data['description'])) {
                $request->setDescription($data['description']);
            }
            $this->requestRepository->flush();
        }

        return true;
    }

    /**
     * @param User $user
     * @param int $clientRequestId
     * @return bool|string
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteClientRequest(User $user, int $clientRequestId)
    {
        $clientRequestObj = $this->clientRequestRepository
            ->findOneBy(['id' => $clientRequestId, 'clientDetail' => $user->getClientDetail()]);
        if (!$clientRequestObj instanceof ClientRequest) {
            return "Either client request doesn't exist or it doesn't belong to current user.";
        }

        $this->clientRequestRepository->remove($clientRequestObj, true);
        return true;
    }

    /**
     * @param User $user
     * @param array $data
     * @return int|mixed|string
     */
    public function getClientRequests(User $user, array $data)
    {
        $clientDetailObj = null;
        if (in_array(UserEnum::ROLE_ADVOCATE, $user->getRoles())) {
            if (empty($data['client_id'])) {
                return "Client id is required.";
            }

            $clientDetailObj = $this->clientService->getClientDetailByUserId($data['client_id']);
            if (empty($clientDetailObj)) {
                return "Client doesn't exist.";
            }

            if ($clientDetailObj->getAdvocate() !== $user->getAdvocateDetail()) {
                return "Client doesn't belong to current advocate.";
            }
        }

        return $this->clientRequestRepository->getClientRequests($user, $data, $clientDetailObj);
    }
}
