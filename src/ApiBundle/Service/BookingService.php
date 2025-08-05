<?php

namespace App\ApiBundle\Service;

use App\Entity\Booking;
use App\Entity\Facility;
use App\Entity\User;
use App\Enum\UserEnum;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FacilityInventoryRepository;
use Symfony\Component\Security\Core\Security;


/**
 * Class BookingService
 * @package App\ApiBundle\Service
 */
class BookingService
{
    /** @var User  */
    private $user;

    /** @var EntityManagerInterface  */
    private $entityManager;

    /**
     * @var BookingRepository
     */
    private $bookingRepository;

    /**
     * @var FacilityInventoryService
     */
    private $facilityInventoryService;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var FacilityInventoryRepository
     */
    private $facilityInventoryRepository;

    /**
     * BookingService constructor.
     * @param EntityManagerInterface $entityManager
     * @param BookingRepository $bookingRepository
     * @param FacilityInventoryTypeService $facilityInventoryTypeService
     * @param Security $security
     * @param UserService $userService
     * @param FacilityInventoryService $facilityInventoryService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        BookingRepository $bookingRepository,
        FacilityInventoryTypeService $facilityInventoryTypeService,
        Security $security,
        UserService $userService,
        FacilityInventoryService $facilityInventoryService,
        FacilityInventoryRepository $facilityInventoryRepository
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->entityManager = $entityManager;
        $this->facilityInventoryTypeService = $facilityInventoryTypeService;
        $this->user = $security->getUser();
        $this->userService = $userService;
        $this->facilityInventoryService = $facilityInventoryService;
        $this->facilityInventoryRepository = $facilityInventoryRepository;
    }

    /**
     * @param $data
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createBookFacility($data)
    {
        $userFacility = $this->user->getFacility();
        $requestedUser = $this->userService->getUserById($data['client_id']);

        if (!$requestedUser instanceof User) {
            return ["status" => false, "message" => "Requested User didn't exist"];
        }
        $facilityInventoryType = $this->facilityInventoryTypeService
            ->getFacilityInventoryTypeByFacility($userFacility, $data["facility_inventory_type_id"]);

        if (empty($facilityInventoryType)) {
            return ["status" => false, "message" => "Facility inventory type didn't exist"];
        }
        $bookingObj = $this->bookingRepository
            ->findOneBy(["performed" => $this->user, "user" => $requestedUser, "facility" => $userFacility, "facilityInventoryType" => $facilityInventoryType]);

        if (!$bookingObj instanceof Booking) {
            $booking = $this->getRoomAvailability($data['room_number'], $userFacility);
            if ($booking instanceof Booking) {
                return ["status" => false, "message" => "Room already assigned"];
            }
            $bookingObj = new Booking();
            $status = $this->facilityInventoryService->updateInventoryAvailablity($facilityInventoryType, $userFacility, false);
            if (!$status) {
                return ['status' => false, 'message' => 'Not available'];
            }
            $this->facilityInventoryRepository->updateFacilityAvailableBeds($userFacility);
        } elseif (empty($bookingObj->getCheckOut()) && isset($data['check_out']) && !empty($data['check_out'])) {
            $bookingObj->setCheckOut(new \DateTime($data['check_out']));
            $this->facilityInventoryRepository->updateFacilityAvailableBeds($userFacility);
            $this->facilityInventoryService->updateInventoryAvailablity($facilityInventoryType, $userFacility, true);
        }

        return $this->bookingRepository->createBooking($data, $facilityInventoryType, $userFacility, $requestedUser, $this->user, $bookingObj);
    }

    /**
     * @param User $user
     * @param array $data
     * @return array|bool
     */
    public function getBookingOfClient(User $user, array $data)
    {
        $client = isset($data['client_id']) ? $this->userService->getUserById($data['client_id']) : null;
        $dataArray = [];
        if (in_array(UserEnum::ROLE_SUPER_ADMIN, $this->user->getRoles())) {
            $facilityUser = isset($data['facility_user_id']) ? $this->userService->getUserById($data['facility_user_id']) : null;
            if ($facilityUser === null) {
                return ["status" => false, "message" => "Facility user id is missing"];
            }
            $dataArray = $this->bookingRepository->getBookingOfClient($facilityUser, $client, $data);
            $totalCount = $this->bookingRepository->getCount($facilityUser, $client, $data);
        } else {
            $dataArray = $this->bookingRepository->getBookingOfClient($user, $client, $data);
            $totalCount = $this->bookingRepository->getCount($user, $client, $data);
        }
        return ["status" => true, 'count' => $totalCount, 'data' => $dataArray];
    }

    /**
     * @param string $roomNumber
     * @param Facility $facility
     * @return Booking|null
     */
    public function getRoomAvailability(string $roomNumber, Facility $facility)
    {
        return $this->bookingRepository->getRoomAvailability($roomNumber, $facility);
    }

    /**
     * @param User $user
     * @return array
     */
    public function getBookingOfUser(User $user)
    {
        return $this->bookingRepository->getBookingOfUser($user);
    }
}
