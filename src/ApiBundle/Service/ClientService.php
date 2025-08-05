<?php

namespace App\ApiBundle\Service;

use App\Entity\AdvocateDetail;
use App\Entity\ClientDetail;
use App\Entity\ClientType;
use App\Entity\User;
use App\Entity\UserAddress;
use App\Enum\ClientEnum;
use App\Enum\CommonEnum;
use App\Enum\StatusEnum;
use App\Enum\UserEnum;
use App\Repository\ClientDetailRepository;
use App\Repository\UserAddressRepository;
use FOS\UserBundle\Model\UserManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ClientService
 * @package App\ApiBundle\Service
 */
class ClientService
{
    /** @var ClientDetailRepository  */
    private $clientDetailRepository;

    /** @var ClientTypeService  */
    private $clientTypeService;

    /** @var ClientStatusService  */
    private $clientStatusService;

    /** @var UserManager  */
    private $userManager;

    /** @var FacilityService  */
    private $facilityService;

    /** @var AdvocateService  */
    private $advocateService;

    /** @var ClientOccupationService  */
    private $clientOccupationService;

    /** @var MailService  */
    private $mailService;

    /** @var UtilService  */
    private $utilService;

    /** @var BookingService */
    private $bookingService;

    /** @var ClientEmploymentStatusService  */
    private $clientEmploymentStatusService;

    /** @var FirstResponderService  */
    private $firstResponderService;

    /** @var UserAddressRepository */
    private $userAddressRepository;

    /** @var TwilioService */
    private $twilioService;

    /** @var ParameterBagInterface */
    private $parameterBag;

    /** @var SmsService */
    private $smsService;

    /**
     * ClientService constructor.
     * @param ClientDetailRepository $clientDetailRepository
     * @param ClientTypeService $clientTypeService
     * @param ClientStatusService $clientStatusService
     * @param UserManager $userManager
     * @param FacilityService $facilityService
     * @param AdvocateService $advocateService
     * @param ClientOccupationService $clientOccupationService
     * @param MailService $mailService
     * @param UtilService $utilService
     * @param BookingService $bookingService
     * @param ClientEmploymentStatusService $clientEmploymentStatusService
     * @param FirstResponderService $firstResponderService
     * @param UserAddressRepository $userAddressRepository
     * @param TwilioService $twilioService
     * @param ParameterBagInterface $parameterBag
     * @param SmsService $smsService
     */
    public function __construct(
        ClientDetailRepository $clientDetailRepository,
        ClientTypeService $clientTypeService,
        ClientStatusService $clientStatusService,
        UserManager $userManager,
        FacilityService $facilityService,
        AdvocateService $advocateService,
        ClientOccupationService $clientOccupationService,
        MailService $mailService,
        UtilService $utilService,
        BookingService $bookingService,
        ClientEmploymentStatusService $clientEmploymentStatusService,
        FirstResponderService $firstResponderService,
        UserAddressRepository $userAddressRepository,
        TwilioService $twilioService,
        ParameterBagInterface $parameterBag,
        SmsService $smsService
    ) {
        $this->clientDetailRepository = $clientDetailRepository;
        $this->clientTypeService = $clientTypeService;
        $this->clientStatusService = $clientStatusService;
        $this->userManager = $userManager;
        $this->facilityService = $facilityService;
        $this->advocateService = $advocateService;
        $this->clientOccupationService = $clientOccupationService;
        $this->mailService = $mailService;
        $this->utilService = $utilService;
        $this->bookingService = $bookingService;
        $this->clientEmploymentStatusService = $clientEmploymentStatusService;
        $this->firstResponderService = $firstResponderService;
        $this->userAddressRepository = $userAddressRepository;
        $this->twilioService= $twilioService;
        $this->parameterBag= $parameterBag;
        $this->smsService = $smsService;
    }

    /**
     * @param User $user
     * @return ClientDetail[]
     */
    public function getClientsByAdvocate(User $user)
    {
        return $this->clientDetailRepository->findBy(['advocate' => $user]);
    }

    /**
     * @param null $hasAdvocate
     * @return ClientDetail[]
     */
    public function getFilteredClients($hasAdvocate = null)
    {
        return $this->clientDetailRepository->getFilteredClients($hasAdvocate);
    }

    /**
     * @param int $userId
     * @return ClientDetail|null
     */
    public function getClientDetailByUserId(int $userId)
    {
        return $this->clientDetailRepository->findOneBy(['user' => $userId, 'status' => StatusEnum::ACTIVE]);
    }

    /**
     * @param User $advocate
     * @param Request $request
     * @return mixed
     */
    public function getFilteredAdvocateClients(User $advocate, Request $request)
    {
        $id = $request->get('id');
        $searchText = $request->get('search_text');
        $statusId = $request->get('status_id');
        $typeId = $request->get('type_id');
        $statusName = $request->get('status_name');

        return $this->clientDetailRepository->getFilteredAdvocateClients(
            $advocate,
            $id,
            $searchText,
            $statusId,
            $typeId,
            $statusName
        );
    }

    /**
     * @param User $advocate
     * @param Request $request
     * @return mixed
     */
    public function getIfAdvocateHaveClients(User $advocate, Request $request)
    {
        $id = $request->get('id');

        $clientDetails = $this->clientDetailRepository->getIfAdvocateHaveClients(
            $advocate,
            $id
        );
        if(empty($clientDetails)){
            return false;
        }
        return true;
    }

    /**
     * @param ClientDetail[] $clientDetails
     * @return array
     */
    public function makeClientResponseByClientDetails($clientDetails)
    {
        $returnResponse = [];
        foreach ($clientDetails as $clientDetail) {
            $returnResponse[] = $this->makeSingleClientResponseByClientDetail($clientDetail);
        }
        return $returnResponse;
    }

    /**
     * @param UserAddress $address
     * @return array
     */
    private function getAddressFields(UserAddress $address)
    {
        $clientAddress = [];
        $clientAddress["is_current_address"] = $address->getAddressState() == 0;
        $clientAddress["street_address"] = $address->getStreetAddress();
        $clientAddress["city"] = $address->getCity();
        $clientAddress["state"] = $address->getState();
        $clientAddress["zip"] = $address->getZip();
        $clientAddress["is_apartment"] = $address->getIsApartment();
        if ($address->getIsApartment()) {
            $clientAddress["apartment_unit_number"] = $address->getApartmentUnitNumber();
        }

        return $clientAddress;
    }

    /**
     * @param ClientDetail|null $clientDetail
     * @return array
     */
    public function makeSingleClientResponseByClientDetail(ClientDetail $clientDetail = null)
    {
        $clientResponse = [];
        if (empty($clientDetail)) {
            return $clientResponse;
        }
        $clientObject = $clientDetail->getUser();
        $clientResponse = $clientObject->toArray();
        $clientResponse = array_merge($clientResponse, $clientDetail->toArray());
        $address = $clientObject->getAddress();
        if (empty($address)) {
            $addressFields = [];
        } else {
            $addressFields = $this->getAddressFields($address);
        }
        $clientResponse['address'] = $addressFields;
        $clientResponse['waitlisted_facilities'] = $this->facilityService->getWaitListedFacilities($clientObject);

        $clientResponse['date_added'] = $clientDetail->getCreated()->format(CommonEnum::DATE_FORMAT);
        $clientResponse['client_type'] = null;
        $clientResponse['client_status'] = null;
        $clientResponse['client_employment_status'] = null;
        $clientResponse['client_occupation'] = null;
        $clientResponse['client_assignment'] = null;
        $clientResponse['first_responder'] = null;
        $booking = $this->bookingService->getBookingOfUser($clientObject);
        $clientResponse['booking_id'] = sizeof($booking) === 0  ? null : $booking;

        if (!empty($clientDetail->getClientType()) && $clientDetail->getClientType()->getStatus() == StatusEnum::ACTIVE) {
            $clientResponse['client_type'] = $this->clientTypeService
                ->makeSingleTypeResponse($clientDetail->getClientType());
        }
        if (!empty($clientDetail->getClientStatus()) && $clientDetail->getClientStatus()->getStatus() == StatusEnum::ACTIVE) {
            $clientResponse['client_status'] = $this->clientStatusService
                ->makeSingleStatusResponse($clientDetail->getClientStatus());
        }
        if (!empty($clientDetail->getClientEmploymentStatus()) && $clientDetail->getClientEmploymentStatus()->getStatus() == StatusEnum::ACTIVE) {
            $clientResponse['client_employment_status'] = $this->clientEmploymentStatusService
                ->makeSingleEmploymentStatusResponse($clientDetail->getClientEmploymentStatus());
        }
        if (!empty($clientDetail->getClientOccupation()) && $clientDetail->getClientOccupation()->getStatus() == StatusEnum::ACTIVE) {
            $clientResponse['client_occupation'] = $clientDetail->getClientOccupation()->toArray();
            $clientResponse['client_occupation']['created_on'] = $clientDetail->getClientOccupation()->getCreated()->format(CommonEnum::DATE_FORMAT);
        }

        $clientResponse['advocate_id'] = $clientResponse['advocate_name'] = null;
        if ($clientDetail->getAdvocate()) {
            $clientResponse['advocate_id'] = $clientDetail->getAdvocate()->getUser()->getId();
            if (empty($clientDetail->getAdvocate()->getUser()->getFirstName()) && empty($clientDetail->getAdvocate()->getUser()->getLastName())) {
                $clientResponse['advocate_name'] = $clientDetail->getAdvocate()->getUser()->getEmail();
            } else {
                $clientResponse['advocate_name'] = $clientDetail->getAdvocate()->getUser()->getFirstName() . " " .
                    $clientDetail->getAdvocate()->getUser()->getLastName();
            }
        }

        $clientResponse['facility_id'] = $clientResponse['facility_name'] = null;
        if ($clientDetail->getFacility()) {
            $clientResponse['facility_id'] = $clientDetail->getFacility()->getId();
            $clientResponse['facility_name'] = $clientDetail->getFacility()->getName();

            $clientResponse['client_assignment'] = $this->clientDetailRepository->getClientInventoryAssignments($clientDetail);
        }

        if (!empty($clientDetail->getFirstResponder())) {
            $clientResponse['first_responder'] = $this->firstResponderService
                ->makeSingleFRResponseByFRDetail($clientDetail->getFirstResponder());
        }

        return $clientResponse;
    }

    /**
     * @param $clientId
     * @param $data
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function updateClientByRequest($clientId, $data)
    {
        $facilityObject = null;
        $advocateAssigned = false;
        /** @var User $clientObject */
        $clientObject = $this->userManager->findUserBy(['id' => $clientId]);
        $clientDetailObject = $this->clientDetailRepository
            ->findOneBy(['user' => $clientId, 'status' => StatusEnum::ACTIVE]);
        if (empty($clientObject) || empty($clientDetailObject)) {
            return ["status" => false, "message" => "Client doesn't exist."];
        }

        if (isset($data['facility_id'])) {
            $facilityObject = $this->facilityService->getFacilityById((int)$data['facility_id']);
            if (empty($facilityObject)) {
                return ["status" => false, "message" => "Invalid Facility provided."];
            }
            $clientDetailObject->setFacility($facilityObject);
            $text = $clientObject->getFirstName(). " " . $clientObject->getLastName();
            $text .= ClientEnum::GOT_NEW_FACILITY_MESSAGE_TO_ADVOCATE . $facilityObject->getName();
            if (!empty($clientDetailObject->getAdvocate())) {
                $this->twilioService->sendMessage($text, $clientDetailObject->getAdvocate()->getUser()->getPhone());
            }
        }

        $existingAdvocate = $clientDetailObject->getAdvocate();
        if (isset($data['advocate_id']) &&
            ((!$existingAdvocate instanceof AdvocateDetail) ||
                $existingAdvocate->getUser()->getId() != $data['advocate_id'])) {
            /** @var User $advocateUserObject */
            $advocateUserObject = $this->userManager
                ->findUserBy(['id' => (int)$data['advocate_id'], 'enabled' => StatusEnum::ACTIVE]);
            if (empty($advocateUserObject) || empty($advocateUserObject->getAdvocateDetail())) {
                return ["status" => false, "message" => "Invalid Advocate provided."];
            }
            $advocate = $this->advocateService->getAdvocateDetailByUserId($data['advocate_id']);
            $text = $clientObject->getFirstName() ." ". $clientObject->getLastName() . ClientEnum::GOT_NEW_CLIENT_MESSAGE_TO_ADVOCATE;
            if ($advocate->getUser() !== null) {
                $this->twilioService->sendMessage($text, $advocate->getUser()->getPhone());
            }
            $clientDetailObject->setAdvocate($advocateUserObject->getAdvocateDetail());
            $advocateAssigned = true;
        }


        if (isset($data['phone'])) {
            $clientObject->setPhone(trim($data['phone']));
            $text = ClientEnum::WELLCOME_MESSAGE_TO_CLIENT . $this->parameterBag->get('app_url') . "/client-login";
            $this->twilioService->sendMessage($text, $clientObject->getPhone());
        }

        $clientObject->setEmail($data['email']);

        if (isset($data['status_id'])) {
            $clientStatusObject = $this->clientStatusService->getClientStatusById((int)$data['status_id']);
            if (empty($clientStatusObject)) {
                return ["status" => false, "message" => "Invalid Client Status provided."];
            }
            $clientDetailObject->setClientStatus($clientStatusObject);
        }

        if (isset($data['occupation_id'])) {
            $clientOccupationObject = $this->clientOccupationService->getClientOccupationById((int)$data['occupation_id']);
            if (empty($clientOccupationObject)) {
                return ["status" => false, "message" => "Invalid Client Occupation provided."];
            }
            $clientDetailObject->setClientOccupation($clientOccupationObject);
        }

        if (isset($data['type_id'])) {
            $clientTypeObject = $this->clientTypeService->getClientTypeById((int)$data['type_id']);
            if (empty($clientTypeObject)) {
                return ["status" => false, "message" => "Invalid Client Type provided."];
            }
            $clientDetailObject->setClientType($clientTypeObject);
        }

        if (isset($data['date_of_incident'])) {
            $clientDetailObject->setDateOfIncident(new \DateTime($data['date_of_incident']));
        }

        foreach (ClientEnum::CLIENT_DETAIL_FIELDS as $parameter => $field) {
            if (isset($data[$parameter])) {
                $setterFun = "set" . $field;
                $clientDetailObject->$setterFun($data[$parameter]);
            }
        }

        foreach (ClientEnum::CLIENT_ENTITY_POSSIBLE_UPDATE_FIELDS as $parameter => $field) {
            if (isset($data[$parameter])) {
                $setterFun = "set" . $field;
                $clientObject->$setterFun($data[$parameter]);
            }
        }
        if (isset($data['agreed_terms'])) {
            $clientObject->setAgreedTerms($data['agreed_terms']);
            $data['agreed_terms'] ? $clientObject->setAgreedTermsAt(new \DateTime('now')) : null;
        }
        if (isset($data['is_waitlisted'])) {
            if (isset($data['waitlisted_facilities'])) {
                $facilityResponse = $this->facilityService->updateWaitListedFacilities($clientObject, $data['waitlisted_facilities']);
                if ($facilityResponse['status'] == false) {
                    return $facilityResponse;
                }
            } else {
                return ["status" => false, "message" => "Wait listed facilities missing."];
            }
        }
        $this->userAddressRepository->updateUserAddressFromData($clientObject, $data);
        $this->clientDetailRepository->flush();
        $sendFacilityData = isset($data['send_facility_data']) ? $data['send_facility_data'] : false;
        if ($advocateAssigned || $sendFacilityData) {
            if (empty($clientDetailObject->getAdvocate())) {
                return ["status" => true, "message" => "Client updated successfully."];
            }
            $this->mailService->sendEmailToAdvocateRegardingClient(
                $clientDetailObject->getAdvocate()->getUser(),
                $clientObject,
                $clientDetailObject->getFacility(),
                $sendFacilityData
            );
        }

        return ["status" => true, "message" => "Client updated successfully."];
    }

    /**
     * @param $data
     * @return string
     */
    public function getEmailFromData($data)
    {
        return $this->utilService->getDummmyEmail($data);
    }

    /**
     * @param array $data
     * @return array|bool[]
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function createClientByRequestData(array $data, User $performedBy)
    {
        /** @var User $user */
        $user = $this->userManager->createUser();
        $user->setUsername($this->utilService->getDummyUsername($data));
        $user->setEmail($data['email']);
        $user->setPlainPassword(UserEnum::DEFAULT_PASSWORD_PREFIX . time());
        $user->setPhone(isset($data['phone']) ? $data['phone'] : null);
        $user->setDob(isset($data['dob']) ? $data['dob'] : null);
        $user->setGender(isset($data['gender']) ? $data['gender'] : null);
        $user->setFirstName(isset($data['first_name']) ? $data['first_name'] : null);
        $user->setLastName(isset($data['last_name']) ? $data['last_name'] : null);
        $user->setEnabled(StatusEnum::ACTIVE);
        $user->setRoles([UserEnum::ROLE_CLIENT]);
        if (isset($data['agreed_terms'])) {
            $user->setAgreedTerms($data['agreed_terms']);
            $data['agreed_terms'] ? $user->setAgreedTermsAt(new \DateTime('now')) : null;
        }

        if (isset($data['is_waitlisted']) && $data['is_waitlisted']) {
            if (isset($data['waitlisted_facilities'])) {
                $responseFacilities = $this->facilityService->linkWaitListedFacilities($user, $data['waitlisted_facilities']);
            } else {
                return ["status" => false, "message" => "Wait listed facilities missing."];
            }
        }
        $response = $this->userAddressRepository->createUserAddressFromData($user, $data);
        if ($response['status'] === false) {
            return $response;
        }

        $response = $this->clientDetailRepository->addClientDetailsByData($user, $data);
        if ($response['status'] === false) {
            return  $response;
        }
        $this->userManager->updateUser($user);

        if (isset($data['phone']) && in_array(UserEnum::ROLE_FIRST_RESPONDER, $performedBy->getRoles())) {
            $text = ClientEnum::WELLCOME_MESSAGE_TO_CLIENT . $this->parameterBag->get('app_url') . "/client-login";
            $this->twilioService->sendMessage($text, $user->getPhone());
        }

        if (isset($data['advocate_id'])) {
            $facilityObj = null;
            $sendFacilityData = isset($data['send_facility_data']) ? $data['send_facility_data'] : false;
            if ($sendFacilityData && $data['facility_id']) {
                $facilityObj = $this->facilityService->getFacilityById((int)$data['facility_id']);
            }
            /** @var User $advocate */
            $advocate = $this->userManager->findUserBy(['id' => $data['advocate_id'], 'enabled' => StatusEnum::ACTIVE]);
            $text = "First Name: " . $user->getFirstName(). "\n";
            $text .= "Last Name: " . $user->getLastName(). "\n";
            $text .= "has been assigned to you at Sanctuary. Please check your notifications";
            $this->twilioService->sendMessage($text, $advocate->getPhone());
//            $this->mailService
//                ->sendEmailToAdvocateRegardingClient($advocate, $user, $facilityObj, $sendFacilityData);
        }

        $adminContacts = $this->smsService->getAllActivePhoneNumber();
        $dataContacts = $this->smsService->makeSmsApiResponse($adminContacts);
        if(!empty($dataContacts)){
            $name = $user->getFirstName(). " ".$user->getLastName();
            $messageBody = "A new client ".$name." is added to the system";
            foreach ($dataContacts as $contact) {
                $this->twilioService->sendMessage($messageBody, $contact['phone_number']);
            }
        }

        return ["status" => true, "message" => "Client created successfully.", "data" => [ 'client_detail_id' => $response['clientDetail']->getId(), 'id' => $user->getId()]];
    }

    /**
     * @param int $userId
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteClientById(int $userId)
    {
        $clientDetailObj = $this->getClientDetailByUserId($userId);
        if (!$clientDetailObj instanceof ClientDetail) {
            return false;
        }

        $this->clientDetailRepository->deleteClientDetail($clientDetailObj);
        return true;
    }

    /**
     * @param $fromDate
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getClientsDataCount($fromDate)
    {
        if ($fromDate) {
            $fromDate = (new \DateTime($fromDate))->format('Y-m-d');
            $toDate = (new \DateTime('today'))->format('Y-m-d');
            $clientDataCount = $this->clientDetailRepository->getClientsCountByDate($fromDate, $toDate);
        } else {
            $clientStatuses = $this->clientStatusService->getActiveClientStatusesIdAndName();
            $clientDataCount = $this->clientDetailRepository->getClientsCountByStatuses($clientStatuses);
        }

        return $clientDataCount;
    }

    /**
     * @param User $facilityUser
     * @return ClientDetail[]
     */
    public function getFacilityClients(User $facilityUser)
    {
        return $this->clientDetailRepository->getFacilityClients($facilityUser);
    }

    /**
     * @param array $firstResponders
     * @param array $data
     * @return ClientDetail[]
     */
    public function getClientsByFR(array $firstResponders, array $data)
    {
        $response['count'] = $this->clientDetailRepository->getClientsByFRCount($data, $firstResponders);
        $response['data'] = $this->clientDetailRepository->getClientsByFR($firstResponders, $data);
        return $response;
    }
}
