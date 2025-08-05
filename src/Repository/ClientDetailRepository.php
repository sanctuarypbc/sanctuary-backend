<?php

namespace App\Repository;

use ActivityLogBundle\Entity\LogEntry;
use App\Entity\ClientDetail;
use App\Entity\ClientOccupation;
use App\Entity\ClientRequest;
use App\Entity\ClientStatus;
use App\Entity\ClientType;
use App\Entity\Facility;
use App\Entity\Goal;
use App\Entity\Request;
use App\Entity\User;
use App\Enum\ClientEnum;
use App\Enum\CommonEnum;
use App\Enum\StatusEnum;
use DateInterval;
use DatePeriod;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use http\Exception;

/**
 * @method ClientDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClientDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClientDetail[]    findAll()
 * @method ClientDetail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientDetailRepository extends AbstractRepository
{
    /**
     * ClientDetailRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientDetail::class);
    }

    /**
     * @param User $advocate
     * @param $id
     * @param $searchText
     * @param $statusId
     * @param $typeId
     * @param $statusName
     * @return mixed
     */
    public function getFilteredAdvocateClients(User $advocate, $id, $searchText, $statusId, $typeId, $statusName)
    {
        $qb = $this->createQueryBuilder('cd')
            ->leftJoin('cd.user', 'user')
            ->leftJoin('cd.clientStatus', 'cs')
            ->where('cd.status = :clientStatus')
            ->andWhere('cd.advocate = :advocate')
            ->setParameter('clientStatus', StatusEnum::ACTIVE)
            ->setParameter('advocate', $advocate->getAdvocateDetail());

        if (!empty($id)) {
            $qb->andWhere('user.id = :clientId')
                ->setParameter('clientId', $id);
        }

        if (!empty(trim($searchText))) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('user.firstName', ':searchText'),
                $qb->expr()->like('user.lastName', ':searchText')
            ));
            $qb->setParameter('searchText', '%' . $searchText . '%');
        }

        if (!empty($statusId)) {
            $qb->andWhere('cd.clientStatus = :statusId')
                ->setParameter('statusId', $statusId);
        }

        if (!empty($typeId)) {
            $qb->andWhere('cd.clientType = :typeId')
                ->setParameter('typeId', $typeId);
        }

        if (!empty($statusName)) {
            $qb->andWhere('cs.name = :statusName')
                ->setParameter('statusName', $statusName);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param User $advocate
     * @param $id
     * @return mixed
     */
    public function getIfAdvocateHaveClients(User $advocate, $id)
    {
        $qb = $this->createQueryBuilder('cd')
            ->leftJoin('cd.user', 'user')
            ->where('cd.status = :clientStatus')
            ->andWhere('cd.advocate = :advocate')
            ->setParameter('clientStatus', StatusEnum::ACTIVE)
            ->setParameter('advocate', $advocate->getAdvocateDetail());

        if (!empty($id)) {
            $qb->andWhere('cd.user = :clientId')
                ->setParameter('clientId', $id);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param User $user
     * @param array $data
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addClientDetailsByData(User $user, array $data)
    {
        $clientDetail = new ClientDetail();
        $clientDetail->setUser($user);
        $clientDetail->setRace($data['race'] ?? null);
        $clientDetail->setEthnicity($data['ethnicity'] ?? null);
        $clientDetail->setIncidentZipCode($data['incident_zip_code'] ?? null);
        $dateOfIncident = isset($data['date_of_incident']) ? new \DateTime($data['date_of_incident']) : new \DateTime();
        isset($data['date_of_incident']) ? $clientDetail->setDateOfIncident($dateOfIncident) : null;
        foreach (ClientEnum::CLIENT_DETAIL_FIELDS as $key => $field) {
            $setterFun = 'set' . $field;
            isset($data[$key]) ? $clientDetail->$setterFun($data[$key]) : null;
        }

        if (isset($data['advocate_id'])) {
            $advocateUser = $this->getEntityManager()->getRepository(User::class)
                ->findOneBy(['id' => $data['advocate_id'], 'enabled' => StatusEnum::ACTIVE]);
            if (empty($advocateUser)) {
                return ["status" => false, "message" => "Invalid Advocate provided"];
            }
            $clientDetail->setAdvocate($advocateUser->getAdvocateDetail());
        }

        if (isset($data['status_id'])) {
            $clientStatusObj = $this->getEntityManager()->getRepository(ClientStatus::class)
                ->findOneBy(['id' => $data['status_id'], 'status' => StatusEnum::ACTIVE]);
            if (empty($clientStatusObj)) {
                return ["status" => false, "message" => "Invalid Client Status provided"];
            }
            $clientDetail->setClientStatus($clientStatusObj);
        }

        if (isset($data['type_id'])) {
            $clientTypeObj = $this->getEntityManager()->getRepository(ClientType::class)
                ->findOneBy(['id' => $data['type_id'], 'status' => StatusEnum::ACTIVE]);
            if (empty($clientTypeObj)) {
                return ["status" => false, "message" => "Invalid Client Type provided"];
            }
            $clientDetail->setClientType($clientTypeObj);
        }

        if (isset($data['occupation_id'])) {
            $clientOccupationObj = $this->getEntityManager()->getRepository(ClientOccupation::class)
                ->findOneBy(['id' => $data['occupation_id'], 'status' => StatusEnum::ACTIVE]);
            if (empty($clientOccupationObj)) {
                return ["status" => false, "message" => "Invalid Client Occupation provided"];
            }
            $clientDetail->setClientOccupation($clientOccupationObj);
        }

        if (isset($data['first_responder_id'])) {
            $firstResponderUserObj = $this->getEntityManager()->getRepository(User::class)
                ->findOneBy(['id' => $data['first_responder_id'], 'enabled' => StatusEnum::ACTIVE]);
            if (empty($firstResponderUserObj)) {
                return ["status" => false, "message" => "Invalid First Responder provided"];
            }
            $clientDetail->setFirstResponder($firstResponderUserObj->getFirstResponderDetail());
        }

        if (isset($data['facility_id'])) {
            $facilityObj = $this->getEntityManager()->getRepository(Facility::class)
                ->findOneBy(['id' => $data['facility_id'], 'status' => StatusEnum::ACTIVE]);
            if (empty($facilityObj)) {
                return ["status" => false, "message" => "Invalid Facility provided"];
            }
            $clientDetail->setFacility($facilityObj);
        }

        $this->persist($clientDetail);
        return ['status' => true, 'clientDetail' => $clientDetail];
    }

    /**
     * @param ClientDetail $clientDetail
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteClientDetail(ClientDetail $clientDetail)
    {
        $clientDetail->setStatus(StatusEnum::INACTIVE);
        $clientDetail->getUser()->setEnabled(StatusEnum::INACTIVE);

        $this->flush();
    }

    /**
     * @param $clientStatuses
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getClientsCountByStatuses($clientStatuses)
    {
        $query = "SELECT
            COUNT(CASE WHEN `status` = '" . StatusEnum::ACTIVE . "' THEN 1 END) AS all_clients";

        foreach ($clientStatuses as $clientStatus) {
            $query .= ", COUNT(CASE WHEN `client_status_id` = '" . $clientStatus['id']
                . "' AND `status` = '" . StatusEnum::ACTIVE . "' THEN 1 END) AS '"
                . str_replace(' ', '_', trim(addslashes($clientStatus['name'])))
                . "'";
        }

        $query = rtrim($query, ',');
        $query .= " FROM `client_detail`;";
        $connection = $this->getEntityManager()->getConnection();
        $stmt = $connection->prepare($query);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @param $user
     * @return LogEntry[]
     */
    public function getActivityLogsOnClient($fromDate, $toDate, $user)
    {
        $allowedClasses = [ClientDetail::class, User::class, Goal::class, Request::class, ClientRequest::class];
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->from('ActivityLogBundle:LogEntry', 'al')
            ->select('al')
            ->where('al.objectClass IN (:allowedClasses)')
            ->setParameter('allowedClasses', array_values($allowedClasses))
            ->orderBy('al.id', 'DESC');

        if ($user) {
            $qb->andWhere('al.user=:user')
                ->setParameter('user', $user);
        }

        if ($fromDate && $toDate) {
            $qb->andWhere('al.loggedAt > :fromDate')
                ->andWhere('al.loggedAt < :toDate')
                ->setParameter('fromDate', new \DateTime($fromDate))
                ->setParameter('toDate', new \DateTime($toDate));
        } else {
            $qb->andWhere('al.loggedAt > :fromDate')
                ->setParameter('fromDate', new \DateTime('-7 days'));
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getClientsCountByDate($fromDate, $toDate)
    {
        $query = "SELECT COUNT(id) as count, date(created) as `date` FROM `client_detail`
                    where created > '" . $fromDate . "' and created < '" . $toDate . "'
                    GROUP BY date(created)";

        $connection = $this->getEntityManager()->getConnection();
        $stmt = $connection->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * @param $hasAdvocate
     * @return ClientDetail[]
     */
    public function getFilteredClients($hasAdvocate)
    {
        $qb = $this->createQueryBuilder('cd')
            ->where('cd.status = :status')
            ->setParameter('status', StatusEnum::ACTIVE)
            ->orderBy('cd.id', 'DESC');

        if ($hasAdvocate === "true") {
            $qb->andWhere('cd.advocate IS NOT NULL');
        } elseif ($hasAdvocate === "false") {
            $qb->andWhere('cd.advocate IS NULL');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param User $facilityUser
     * @return ClientDetail[]
     */
    public function getFacilityClients(User $facilityUser)
    {
        return $this->createQueryBuilder('cd')
            ->where('cd.status = :clientStatus')
            ->andWhere('cd.facility = :facility')
            ->setParameter('clientStatus', StatusEnum::ACTIVE)
            ->setParameter('facility', $facilityUser->getFacility())
            ->getQuery()->getResult();
    }

    /**
     * @param ClientDetail $clientDetail
     * @return array|object[]
     */
    public function getClientInventoryAssignments(ClientDetail $clientDetail)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('cia.id, facilityInventory.id as inventory_id, cia.quantity, cia.checkOutAt, cia.assignedAt, cia.checkInAt')
            ->from('App:ClientInventoryAssignment', 'cia')
            ->leftJoin('cia.facilityInventory', 'facilityInventory')
            ->where('cia.clientDetail = :clientDetail')
            ->setParameter('clientDetail', $clientDetail)
            ->getQuery()->getResult();
    }

    /**
     * @param array $firstResponders
     * @param array $data
     * @return array|object[]
     * @throws \Exception
     */
    public function getClientsByFR(array $firstResponders, array $data)
    {
        $perPage = empty($data['per_page']) ? CommonEnum::PER_PAGE_DEFAULT : (int) $data['per_page'];
        $page = empty($data['page']) ? 1 : (int) $data['page'];
        $page = ($page <= 0) ? 1 : $page;
        if (isset($data['is_graph']) && $data['is_graph'] === 'true') {
            $fromDate = trim($data['date_time_incident_from']) ?? null;
            $toDate = trim($data['date_time_incident_to']) ?? null;
            $toDate = $toDate ?? date('Y-m-d');
            $response = [];

            $begin = new DateTime($fromDate);
            $end = new DateTime($toDate);
            $interval = new DateInterval('P1M');
            $daterange = new DatePeriod($begin, $interval, $end->modify('+1 day'));

            $startFirstMonth = (new DateTime($fromDate))->format("Y-m-d 00:00:00");
            $endFirstMonth =  (new DateTime($fromDate))->modify('last day of this month')->format("Y-m-d 23:59:59");

            $startLastMonth = (new DateTime($toDate)) ->modify('first day of this month')->format("Y-m-d 00:00:00");
            $endLastMonth =   (new DateTime($toDate)) ->format("Y-m-d 23:59:59");

            $firstMonth = $begin->format("Y-m");
            $lastMonth = $end->format("Y-m");
            $nextMonth = (new DateTime($fromDate))->modify('+1 months')->format("Y-m");

            foreach ($daterange as $key => $date)
            {
                $month = $date->format("Y-m");

                if($firstMonth == $lastMonth)
                {
                    $fromDateTime = $date->format("Y-m-d 00:00:00");
                    $toDateTime = (new DateTime($toDate))->format("Y-m-d 23:59:59");
                } else if($firstMonth != $lastMonth && $firstMonth == $month && $lastMonth == $nextMonth)
                {
                    $fromDateTime = $date->format("Y-m-d 00:00:00");
                    $toDateTime = $date->modify('last day of this month')->format("Y-m-d 23:59:59");
                } else if($firstMonth != $lastMonth && $nextMonth == $month && $lastMonth == $nextMonth)
                {
                    $fromDateTime = $date->modify('first day of this month')->format("Y-m-d 00:00:00");
                    $toDateTime = (new DateTime($toDate))->format("Y-m-d 23:59:59");
                } else if($firstMonth != $lastMonth && $lastMonth != $nextMonth && $firstMonth == $month)
                {
                    $fromDateTime = $startFirstMonth;
                    $toDateTime = $endFirstMonth;
                } else if($firstMonth != $lastMonth && $lastMonth != $nextMonth && $lastMonth == $month)
                {
                    $fromDateTime = $startLastMonth;
                    $toDateTime = $endLastMonth;
                } else {
                    $fromDateTime = $date->modify('first day of this month')->format("Y-m-d 00:00:00");
                    $toDateTime = $date->modify('last day of this month')->format("Y-m-d 23:59:59");
                }
                
                $data['date_time_incident_from'] = $fromDateTime;
                $data['date_time_incident_to'] = $toDateTime;
                $qb = $this->createQueryBuilder('cd')
                    ->select('COUNT(distinct cd.id) as count');
                $result = $this->addFilters($qb, $data, $firstResponders)
                            ->getQuery()
                            ->getSingleScalarResult();
                $response[$month] = $result;
            }
            return $response;
        }

        $qb = $this->createQueryBuilder('cd')
            ->setFirstResult(($page-1) * $perPage)
            ->setMaxResults($perPage)
            ->groupBy('cd.id');

        $qb = $this->addFilters($qb, $data, $firstResponders);
        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $firstResponders
     * @param ClientType $clientType
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getClientsCountByType(array $firstResponders, ClientType $clientType)
    {
        $qb = $this->createQueryBuilder('cd')
            ->select('COUNT(1) as count')
            ->where('cd.status = :status')
            ->setParameter('status', StatusEnum::ACTIVE)
            ->andWhere('cd.firstResponder IN (:firstResponder)')
            ->setParameter('firstResponder', $firstResponders)
            ->andWhere('cd.clientType = :clientType')
            ->setParameter('clientType', $clientType);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param array $firstResponders
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public function getClientsByFRCount(array $data, array $firstResponders)
    {
        $qb = $this->createQueryBuilder('cd')
            ->select('COUNT(DISTINCT cd.id) as count');
        $qb = $this->addFilters($qb, $data, $firstResponders);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param QueryBuilder $qb
     * @param array $data
     * @param array $firstResponders
     * @return QueryBuilder
     * @throws Exception
     */
    public function addFilters(QueryBuilder $qb, array $data, array $firstResponders)
    {
        $age = $data['age'] ?? null;
        $gender = $data['gender'] ?? null;
        $race = $data['race'] ?? null;
        $ethnicity = $data['ethnicity'] ?? null;
        $zipCodeIncident = $data['zip_code_incident'] ?? null;
        $zipCodeAddress = $data['zip_code_address'] ?? null;
        $dateTimeIncidentFrom = $data['date_time_incident_from'] ?? null;
        $dateTimeIncidentTo = $data['date_time_incident_to'] ?? null;
        $fromDate = $data['from_date'] ?? null;
        $toDate = $data['to_date'] ?? null;
        $userDependence = $data['user_dependence'] ?? null;
        $clientTypeId = $data['client_type_id'] ?? null;

        $qb->where('cd.status = :status')
            ->innerJoin('cd.user', 'user')
            ->setParameter('status', StatusEnum::ACTIVE)
            ->andWhere('cd.firstResponder IN (:firstResponder)')
            ->setParameter('firstResponder', $firstResponders)
            ->orderBy('cd.id', 'DESC');

        if ($age) {
            $age = explode('-', $age);
            $ageFrom = $age[0] ?? null;
            $ageTo = $age[1] ?? null;
            if ($ageFrom && $ageTo) {
                $qb->andWhere('cd.age BETWEEN :ageFrom AND :ageTo')
                    ->setParameter('ageFrom',$ageFrom)
                    ->setParameter('ageTo', $ageTo);
            }
        }

        if ($gender) {
            if (in_array($gender, ClientEnum::GENDER_TYPES, true)) {
                $qb->andWhere('user.gender = :gender')
                    ->setParameter('gender',$gender);
            } else {
                $qb->andWhere('user.gender NOT IN (:gender) or user.gender is null')
                    ->setParameter('gender',ClientEnum::GENDER_TYPES);
            }
        }

        if ($race) {
            if ($race === ClientEnum::OTHER_TYPE ) {
                $qb->andWhere('cd.race NOT IN (:race) or cd.race is null')
                    ->setParameter('race',ClientEnum::RACE_TYPES);
            } else {
                $qb->andWhere('cd.race = :race')
                    ->setParameter('race',$race);
            }
        }

        if ($ethnicity) {
            $qb->andWhere('cd.ethnicity = :ethnicity')
                ->setParameter('ethnicity',$ethnicity);
        }

        if ($zipCodeIncident) {
            $qb->andWhere('cd.incidentZipCode = :zipCodeIncident')
                ->setParameter('zipCodeIncident',$zipCodeIncident);
        }

        if ($zipCodeAddress) {
            $qb->innerJoin('user.address', 'add')
                ->andWhere('add.zip = :zipCodeAddress')
                ->setParameter('zipCodeAddress',$zipCodeAddress);
        }

        if ($userDependence) {
            $qb->innerJoin('cd.dependents', 'dep');
            $userDependence = json_decode($userDependence, true);
            $depStartAge = $userDependence['age_start'] ?? null;
            $depEndAge = $userDependence['age_end'] ?? null;
            $depGender = $userDependence['gender'] ?? null;
            if($depStartAge && $depEndAge && ($depGender != null && $depGender === ClientEnum::OTHER_TYPE)) {
                $qb->andWhere('(dep.gender NOT IN (:dep_gender) AND dep.age >= :startAge AND dep.age <= :endAge) 
                                or (dep.gender is null AND dep.age >= :startAge AND dep.age <= :endAge)')
                    ->setParameter('startAge', $depStartAge)
                    ->setParameter('endAge', $depEndAge)
                    ->setParameter('dep_gender', ClientEnum::GENDER_TYPES);
            } 
            else if ($depStartAge && $depEndAge && ($depGender != null && $depGender !== ClientEnum::OTHER_TYPE)) {
                $qb->andWhere('dep.age >= :startAge')
                    ->andWhere('dep.age <= :endAge')
                    ->andWhere('dep.gender =:dep_gender')
                    ->setParameter('startAge', $depStartAge)
                    ->setParameter('endAge', $depEndAge)
                    ->setParameter('dep_gender',$depGender);
            } 
            else if ($depStartAge && $depEndAge && $depGender == null) {
                $qb->andWhere('dep.age >= :startAge')
                    ->andWhere('dep.age <= :endAge')
                    ->setParameter('startAge', $depStartAge)
                    ->setParameter('endAge', $depEndAge);
            }
            else if ($depGender != null && $depGender === ClientEnum::OTHER_TYPE && !$depStartAge && !$depEndAge) {
                $qb->andWhere('dep.gender NOT IN (:dep_gender) or dep.gender is null')
                    ->setParameter('dep_gender', ClientEnum::GENDER_TYPES);
            }
            elseif ($depGender != null && $depGender !== ClientEnum::OTHER_TYPE && !$depStartAge && !$depEndAge) {
                $qb->andWhere('dep.gender =:dep_gender')
                    ->setParameter('dep_gender',$depGender);
            }
            $qb->andWhere('dep.status =:status')
                    ->setParameter('status', StatusEnum::ACTIVE);
        }

        if ($dateTimeIncidentFrom && $dateTimeIncidentTo) {
            $qb->andWhere('cd.dateOfIncident >= :fromDateIncident')
                ->andWhere('cd.dateOfIncident < :toDateIncident')
                ->setParameter('fromDateIncident', new \DateTime($dateTimeIncidentFrom))
                ->setParameter('toDateIncident', new \DateTime($dateTimeIncidentTo));
        } elseif ($dateTimeIncidentFrom && $dateTimeIncidentTo === null) {
            $qb->andWhere('cd.dateOfIncident >= :fromDateIncident')
                ->setParameter('fromDateIncident', new \DateTime($dateTimeIncidentFrom));
        }

        if ($clientTypeId) {
            $qb->andWhere('cd.clientType = :clientTypeId')
                ->setParameter('clientTypeId', $clientTypeId);
        }

        if ($fromDate && $toDate) {
            $qb->andWhere('cd.created >= :fromDate')
                ->andWhere('cd.created < :toDate')
                ->setParameter('fromDate', new \DateTime($fromDate))
                ->setParameter('toDate', new \DateTime($toDate));
        } elseif ($fromDate && $toDate === null) {
            $qb->andWhere('cd.created >= :fromDate')
                ->setParameter('fromDate', new \DateTime($fromDate));
        }

        return $qb;
    }
}
