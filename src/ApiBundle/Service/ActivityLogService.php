<?php

namespace App\ApiBundle\Service;

use ActivityLogBundle\Entity\LogEntry;
use App\Entity\Booking;
use App\Entity\ClientRequest;
use App\Entity\Request;
use App\Entity\User;
use App\Enum\CommonEnum;
use App\Enum\UserEnum;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class ActivityLogService
 * @package App\ApiBundle\Service
 */
class ActivityLogService
{
    /** @var EntityManagerInterface  */
    private $entityManager;

    /**
     * UserService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @param $advocateId
     * @param $clientId
     * @return bool
     */
    public function getActivityLogsOnClient($fromDate, $toDate, $advocateId, $clientId)
    {
        if ($fromDate && $toDate) {
            $daysDiff = (strtotime($toDate) - strtotime($fromDate)) / 60 / 60 / 24;
            if ($daysDiff > 30) {
                return false;
            }
        }
        $user = null;
        if (!empty($advocateId) && !empty($clientId)) {
            $advocate = $this->entityManager->getRepository('App:User')->find(['id' => $advocateId]);
            if (!in_array(UserEnum::ROLE_ADVOCATE, $advocate->getRoles())) {
                return "You don't have rights to perform this action.";
            } else {
                $user = $this->entityManager->getRepository('App:User')->find(['id' => $clientId]);
            }
        }

        return $this->entityManager->getRepository('App:ClientDetail')->getActivityLogsOnClient($fromDate, $toDate, $user);
    }

    /**
     * @param $logs
     * @return array
     */
    public function makeActivityLogsResponse($logs)
    {
        $logsData = [];
        /** @var LogEntry $log */
        foreach ($logs as $log) {
            if ($log->getObjectClass() == User::class) {
                $user = $this->entityManager->getRepository('App:User')->find(['id' => $log->getObjectId()]);
                if (!$user || !$user->hasRole(UserEnum::ROLE_CLIENT)) {
                    continue;
                }
            } elseif ($log->getObjectClass() == Request::class) {
                $user = $log->getUser();
            } elseif ($log->getObjectClass() == Booking::class) {
                $user = $log->getUser();
            } elseif ($log->getObjectClass() == ClientRequest::class) {
                $clientRequest = $this->entityManager->getRepository('App:ClientRequest')->find($log->getObjectId());
                $request = $clientRequest->getRequest();
                $user = $request->getUser();
            } else {
                $clientDetailObj = $this->entityManager->getRepository('App:ClientDetail')->find($log->getObjectId());
                if (!$clientDetailObj || !$clientDetailObj->getUser()) {
                    continue;
                }
                $user = $clientDetailObj->getUser();
            }
            $entity = explode('\\', $log->getObjectClass());
            $singleData['id'] = $log->getId();
            $singleData['action_performed_by'] = !empty($log->getUser()) ? ($log->getUser()->getFullName() ?: $log->getUser()->getUsername()) : 'N/A';
            $singleData['old_data'] = $this->prepareData($log->getOldData(), true);
            $singleData['new_data'] = $this->prepareData($log->getData());
            $singleData['action'] = $log->getAction();
            $singleData['logged_at'] = $log->getLoggedAt()->format(CommonEnum::DATE_TIME_FORMAT);
            $singleData['client_name'] = $user->getFullName() ?: $user->getUsername();
            $singleData['entity'] = $entity[count($entity)-1];

            $logsData[] = $singleData;
        }
        return $logsData;
    }

    public function prepareData($data, $skipFiltration = false)
    {
        $filteredData = $data;
        if (!$skipFiltration) {
            $filteredData = $data ? array_filter($data, function ($value) {
                return ($value !== null && $value !== false && $value !== '');
            }) : $data;
        }

        if (isset($filteredData['advocate'])) {
            $obj = $this->entityManager->getRepository('App:AdvocateDetail')->find($filteredData['advocate']);
            $filteredData['advocate'] = $obj ? $obj->getUser()->getFullName() ?: $obj->getUser()->getUsername() : null;
        }

        if (isset($filteredData['firstResponder'])) {
            $obj = $this->entityManager->getRepository('App:FirstResponderDetail')->find($filteredData['firstResponder']);
            $filteredData['firstResponder'] = $obj ? $obj->getUser()->getFullName() ?: $obj->getUser()->getUsername() : null;
        }

        if (isset($filteredData['clientType'])) {
            $obj = $this->entityManager->getRepository('App:ClientType')->find($filteredData['clientType']);
            $filteredData['clientType'] = $obj ? $obj->getName() : null;
        }

        if (isset($filteredData['clientStatus'])) {
            $obj = $this->entityManager->getRepository('App:ClientStatus')->find($filteredData['clientStatus']);
            $filteredData['clientStatus'] = $obj ? $obj->getName() : null;
        }

        if (isset($filteredData['clientOccupation'])) {
            $obj = $this->entityManager->getRepository('App:ClientOccupation')->find($filteredData['clientOccupation']);
            $filteredData['clientOccupation'] = $obj ? $obj->getName() : null;
        }

        if (isset($filteredData['facility'])) {
            $obj = $this->entityManager->getRepository('App:Facility')->find($filteredData['facility']);
            $filteredData['facility'] = $obj ? $obj->getName() : null;
        }

        if (isset($filteredData['goal'])) {
            $obj = $this->entityManager->getRepository('App:Goal')->find($filteredData['goal']);
            $filteredData['goal'] = $obj ? $obj->getName() : null;
        }

        if (isset($filteredData['request'])) {
            $obj = $this->entityManager->getRepository('App:Request')->find($filteredData['request']);
            $filteredData['request'] = $obj ? $obj->getTitle() : null;
        }

        return $filteredData;
    }
}
