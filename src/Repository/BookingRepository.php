<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\Facility;
use App\Entity\FacilityInventoryType;
use App\Entity\User;
use App\Enum\CommonEnum;
use App\Enum\StatusEnum;
use App\Enum\UserEnum;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends AbstractRepository
{
    /**
     * BookingRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * @param $data
     * @param $facilityInventoryType
     * @param $facility
     * @param $facilityUser
     * @param $performedBy
     * @param $booking
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createBooking($data, FacilityInventoryType $facilityInventoryType, Facility $userFacility, User $requestedUser, User $user, Booking $booking)
    {
        $booking->setPerformed($user);
        $booking->setStatus(StatusEnum::ACTIVE);
        $booking->setUser($requestedUser);
        $booking->setFacilityInventoryType($facilityInventoryType);
        $booking->setFacility($userFacility);
        if (!empty($data['check_in'])) {
            $booking->setCheckIn(new \DateTime($data['check_in']));
        }
        $booking->setNotes(isset($data['notes']) ? $data['notes'] : null);
        $booking->setRoomNumber($data['room_number']);
        $this->persist($booking, true);

        return ["status" => true];
    }

    /**
     * @param QueryBuilder $qb
     * @param User $user
     * @param $client
     * @param $data
     * @param $paginate
     * @return QueryBuilder
     * @throws \Exception
     */
    public function addFilters(QueryBuilder $qb,User $user, $client, $data, $paginate = true)
    {
        $perPage = empty($data['per_page']) ? CommonEnum::PER_PAGE_DEFAULT : (int) $data['per_page'];
        $page = empty($data['page']) ? 1 : (int) $data['page'];
        $fromDate = $data['from_date'] ?? null;
        $toDate = $data['to_date'] ?? null;
        $search = $data['search'] ?? null;
        if ($page <= 0) {
            $page = 1;
        }

        $qb->leftJoin('bf.user','u')
            ->leftJoin('bf.facility','f')
            ->leftJoin('bf.facilityInventoryType','fiy')
            ->leftJoin('u.clientDetail','cd')
            ->where('bf.status =:status')
            ->setParameter("status", StatusEnum::ACTIVE)
            ->orderBy('bf.created' , 'DESC');

        $qb->andWhere('cd.facility = :facility_id')
            ->setParameter('facility_id', $user->getFacility());
        if ($paginate) {
            $qb->setFirstResult(($page-1) * $perPage)
                ->setMaxResults($perPage);
        }

        if ($client instanceof User) {
            $qb->andWhere('bf.user =:client')
                ->setParameter('client', $client);
        }
        if (!in_array(UserEnum::ROLE_SUPER_ADMIN, $user->getRoles())) {
            $qb->andWhere('bf.performed =:user')
                ->setParameter('user', $user);
        }

        if($search){
            $qb->andWhere('u.firstName LIKE :query OR u.lastName LIKE :query OR u.gender LIKE :query OR bf.roomNumber LIKE :query')
                ->setParameter('query', '%' .$search. '%' );
        }

        if ($fromDate && $toDate) {
            $qb->andWhere('bf.checkIn >= :fromDate')
                ->andWhere('bf.checkOut < :toDate OR (bf.checkIn >= :fromDate AND bf.checkIn <= :toDate  AND bf.checkOut IS NULL)')
                ->setParameter('fromDate', new \DateTime($fromDate))
                ->setParameter('toDate', new \DateTime($toDate));
        } elseif ($fromDate && $toDate === null) {
            $qb->andWhere('bf.checkIn >= :fromDate')
                ->setParameter('fromDate', new \DateTime($fromDate));
        }
        return $qb;
    }

    /**
     * @param User $user
     * @param $client
     * @param $data
     * @return QueryBuilder|mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     */
    public function getCount(User $user, $client, $data)
    {
        $qb = $this->createQueryBuilder('bf')
            ->select('count(bf.id)');

        $qb = $this->addFilters($qb, $user, $client, $data, false);
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param User $user
     * @param $client
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function getBookingOfClient(User $user, $client, $data)
    {
        $qb = $this->createQueryBuilder('bf')
            ->select('bf.id, u.id as client_id, f.id as facility_id, fiy.id as facility_inventory_type_id, bf.notes, bf.checkIn as check_in, bf.checkOut as check_out, bf.roomNumber as room_number');

        $qb = $this->addFilters($qb, $user, $client, $data);
        return $qb->getQuery()->getResult();

    }

    /**
     * @param string $roomNumber
     * @param Facility $facility
     * @return int|null
     */
    public function getRoomAvailability(string $roomNumber, Facility $facility)
    {
        return $this->createQueryBuilder('bf')
            ->where('bf.facility =:facility')
            ->andwhere('bf.status =:status')
            ->andwhere('bf.roomNumber =:roomNumber')
            ->andwhere('bf.checkOut IS NULL')
            ->andwhere('bf.checkIn IS NOT NULL')
            ->setParameter('facility', $facility)
            ->setParameter('roomNumber', $roomNumber)
            ->setParameter("status", StatusEnum::ACTIVE)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function getBookingOfUser(User $user)
    {
        return $this->createQueryBuilder('bf')
            ->select('bf.id')
            ->where('bf.user =:user')
            ->andwhere('bf.status =:status')
            ->setParameter('user', $user)
            ->setParameter("status", StatusEnum::ACTIVE)
            ->getQuery()->getResult();
    }
}
