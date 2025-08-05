<?php

namespace App\ApiBundle\Controller\Client;

use App\ApiBundle\Service\ClientService;
use App\ApiBundle\Service\UserService;
use App\ApiBundle\Service\UtilService;
use App\Entity\User;
use App\Enum\ClientEnum;
use App\Enum\UserEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use Swagger\Annotations as SWG;

/**
 * Class ClientApiController
 * @package App\ApiBundle\Controller\Facility
 */
class ClientApiController extends AbstractController
{
    /**
     * @Get("/client", name="api_client_detail",  options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/client",
     *     tags={"Client"},
     *     summary="Authorises user via access token and list clients",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *
     *     @SWG\Parameter(
     *         ref="#parameters/authorization"
     *     ),
     *     @SWG\Parameter(
     *         name="id",
     *         in="query",
     *         type="integer",
     *         required=false,
     *         description="Id"
     *     ),
     *     @SWG\Parameter(
     *         name="advocate_assigned",
     *         in="query",
     *         type="boolean",
     *         required=false,
     *         description="Advocate assigned?"
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="Success",
     *      ),
     *     @SWG\Response(
     *          response=401,
     *          description="Unauthorized request",
     *      ),
     *      @SWG\Response(
     *          response=403,
     *          description="Access denied",
     *      ),
     *     @SWG\Response(
     *          response=422,
     *          description="Validation error",
     *      ),
     *     @SWG\Response(
     *          response=500,
     *          description="Internal server error",
     *      )
     * )
     *
     * @param Request $request
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @param ClientService $clientService
     * @return JsonResponse
     */
    public function getClientAction(
        Request $request,
        UtilService $utilService,
        LoggerInterface $clientApiLogger,
        ClientService $clientService
    ) {
        try {
            $clientId = $request->get('id');
            $hasAdvocate = $request->get('advocate_assigned', null);
            if (!empty($clientId)) {
                $haveAssignedClient = $clientService->getIfAdvocateHaveClients($this->getUser(), $request);
                if(in_array(UserEnum::ROLE_ADVOCATE, $this->getUser()->getRoles()) && $haveAssignedClient){
                    $clientDetail = $clientService->getClientDetailByUserId((int)$clientId);
                    $data = $clientService->makeSingleClientResponseByClientDetail($clientDetail);
                }else if(!in_array(UserEnum::ROLE_ADVOCATE, $this->getUser()->getRoles())){
                    $clientDetail = $clientService->getClientDetailByUserId((int)$clientId);
                    $data = $clientService->makeSingleClientResponseByClientDetail($clientDetail);
                }else {
                    return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Either client does not exist or not assigned");
                }
            } else {
                $clientDetails = $clientService->getFilteredClients($hasAdvocate);
                $data = $clientService->makeClientResponseByClientDetails($clientDetails);
            }
            return $utilService->makeResponse(Response::HTTP_OK, null, $data);
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_client_detail]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Post("/client", name="api_client_add",  options={ "method_prefix" = false })
     *
     * @SWG\Post(
     *     path="/api/user/client",
     *     tags={"Client"},
     *     summary="Authorises user via access token and create client",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *
     *     @SWG\Parameter(
     *         ref="#parameters/authorization"
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                  property="email",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="username",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="first_name",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="last_name",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="phone",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="dob",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="gender",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="race",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="ethnicity",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="incident_zip_code",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="total_dependents",
     *                  type="number",
     *                  ),
     *             @SWG\Property(
     *                  property="agreed_terms",
     *                  type="boolean",
     *                  ),
     *             @SWG\Property(
     *                  property="pet_status",
     *                  type="boolean",
     *                  ),
     *             @SWG\Property(
     *                  property="phone_with_cellular_service",
     *                  type="boolean",
     *                  ),
     *             @SWG\Property(
     *                  property="need_translator",
     *                  type="boolean",
     *                  ),
     *             @SWG\Property(
     *                  property="case_number",
     *                  type="string",
     *                  ),
     *              @SWG\Property(
     *                  property="date_of_incident",
     *                  type="datetime",
     *                  description="Format: 2000-01-01 00:00:00"
     *                  ),
     *             @SWG\Property(
     *                  property="valid_id",
     *                  type="boolean",
     *                  ),
     *             @SWG\Property(
     *                  property="abuser_location",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="number_of_pets",
     *                  type="integer",
     *                  ),
     *             @SWG\Property(
     *                  property="physically_disabled",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="need_medical_assistance",
     *                  type="boolean",
     *                  ),
     *             @SWG\Property(
     *                  property="contacted_family",
     *                  type="boolean",
     *                  ),
     *             @SWG\Property(
     *                  property="is_current_address",
     *                  type="boolean",
     *                  ),
     *             @SWG\Property(
     *                  property="street_address",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="city",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="state",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="zip",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="is_apartment",
     *                  type="boolean",
     *                  ),
     *             @SWG\Property(
     *                  property="apartment_unit_number",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="is_waitlisted",
     *                  type="boolean",
     *                  ),
     *             @SWG\Property(
     *                  property="waitlisted_facilities",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="notes",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="location",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="advocate_id",
     *                  type="integer",
     *                  ),
     *             @SWG\Property(
     *                  property="facility_id",
     *                  type="integer",
     *                  ),
     *             @SWG\Property(
     *                  property="type_id",
     *                  type="integer",
     *                  ),
     *             @SWG\Property(
     *                  property="status_id",
     *                  type="integer",
     *                  ),
     *             @SWG\Property(
     *                  property="occupation_id",
     *                  type="integer",
     *                  ),
     *             @SWG\Property(
     *                  property="first_responder_id",
     *                  type="integer",
     *                  )
     *              ),
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="Success",
     *      ),
     *     @SWG\Response(
     *          response=401,
     *          description="Unauthorized request",
     *      ),
     *      @SWG\Response(
     *          response=403,
     *          description="Access denied",
     *      ),
     *     @SWG\Response(
     *          response=422,
     *          description="Validation error",
     *      ),
     *     @SWG\Response(
     *          response=500,
     *          description="Internal server error",
     *      )
     * )
     *
     * @param Request $request
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @param ClientService $clientService
     * @param UserService $userService
     * @return JsonResponse
     */
    public function createClientAction(
        Request $request,
        UtilService $utilService,
        LoggerInterface $clientApiLogger,
        ClientService $clientService,
        UserService $userService
    ) {
        try {
            $data = json_decode($request->getContent(), true);
            $validRequest = $utilService->checkRequiredFieldsByRequestedData($data, ClientEnum::CREATE_CLIENT_API_REQUIRED_FIELDS);
            if (!$validRequest) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Some required parameters are missing.");
            }

            if(isset($data['email'])){
                $emailExist = $userService->getUserByEmail(trim($data['email']));
                if ($emailExist) {
                    return $utilService->makeResponse(Response::HTTP_CONFLICT, "Email already exist.");
                }
            }

            if (isset($data['phone'])) {
                $user = $userService->checkUserByPhoneNumber(trim($data['phone']));
                if ($user instanceof User) {
                    return $utilService->makeResponse(Response::HTTP_CONFLICT, "Phone Number already Exist");
                }
            }
            if (isset($data['date_of_incident']) && \DateTime::createFromFormat('Y-m-d H:i:s', $data['date_of_incident']) === FALSE) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Invalid date format provided");
            }

            $response = $clientService->createClientByRequestData($data, $this->getUser());
            if ($response['status'] === false) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $response['message']);
            }
            return $utilService->makeResponse(Response::HTTP_OK, $response['message'], isset($response['data']) ? $response['data'] : null);
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_update_client]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Put("/client/{id}", name="api_client_edit",  options={ "method_prefix" = false })
     *
     * @SWG\Put(
     *     path="/api/user/client/{id}",
     *     tags={"Client"},
     *     summary="Authorises user via access token and update client",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *
     *     @SWG\Parameter(
     *         ref="#parameters/authorization"
     *     ),
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer",
     *         required=true,
     *         description="Id"
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                  property="email",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="username",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="first_name",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="last_name",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="age",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="phone",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="dob",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="gender",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="race",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="ethnicity",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="incident_zip_code",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="total_dependents",
     *                  type="number",
     *                  ),
     *             @SWG\Property(
     *                  property="agreed_terms",
     *                  type="boolean",
     *                  ),
     *             @SWG\Property(
     *                  property="pet_status",
     *                  type="boolean",
     *                  ),
     *             @SWG\Property(
     *                  property="phone_with_cellular_service",
     *                  type="boolean",
     *                  ),
     *             @SWG\Property(
     *                  property="need_translator",
     *                  type="boolean",
     *                  ),
     *             @SWG\Property(
     *                  property="case_number",
     *                  type="string",
     *                  ),
     *              @SWG\Property(
     *                  property="date_of_incident",
     *                  type="datetime",
     *                  description="Format: 2000-01-01 00:00:00"
     *                  ),
     *             @SWG\Property(
     *                  property="valid_id",
     *                  type="boolean",
     *                  ),
     *             @SWG\Property(
     *                  property="abuser_location",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="number_of_pets",
     *                  type="integer",
     *                  ),
     *             @SWG\Property(
     *                  property="physically_disabled",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="need_medical_assistance",
     *                  type="boolean",
     *                  ),
     *             @SWG\Property(
     *                  property="contacted_family",
     *                  type="boolean",
     *                  ),
     *             @SWG\Property(
     *                  property="is_current_address",
     *                  type="boolean",
     *                  ),
     *             @SWG\Property(
     *                  property="street_address",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="city",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="state",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="zip",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="is_apartment",
     *                  type="boolean",
     *                  ),
     *             @SWG\Property(
     *                  property="apartment_unit_number",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="is_waitlisted",
     *                  type="boolean",
     *                  ),
     *             @SWG\Property(
     *                  property="waitlisted_facilities",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="notes",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="location",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="advocate_id",
     *                  type="integer",
     *                  ),
     *             @SWG\Property(
     *                  property="facility_id",
     *                  type="integer",
     *                  ),
     *             @SWG\Property(
     *                  property="type_id",
     *                  type="integer",
     *                  ),
     *             @SWG\Property(
     *                  property="status_id",
     *                  type="integer",
     *                  ),
     *             @SWG\Property(
     *                  property="occupation_id",
     *                  type="integer",
     *                  ),
     *              ),
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="Success",
     *      ),
     *     @SWG\Response(
     *          response=401,
     *          description="Unauthorized request",
     *      ),
     *      @SWG\Response(
     *          response=403,
     *          description="Access denied",
     *      ),
     *     @SWG\Response(
     *          response=422,
     *          description="Validation error",
     *      ),
     *     @SWG\Response(
     *          response=500,
     *          description="Internal server error",
     *      )
     * )
     *
     * @param $id
     * @param Request $request
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @param ClientService $clientService
     * @param UserService $userService
     * @return JsonResponse
     */
    public function editClientAction(
        $id,
        Request $request,
        UtilService $utilService,
        LoggerInterface $clientApiLogger,
        ClientService $clientService,
        UserService $userService
    ) {
        try {
            $data = json_decode($request->getContent(), true);
            if (empty($data)) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Some parameters required.");
            }

            if (isset($data['date_of_incident']) && \DateTime::createFromFormat('Y-m-d H:i:s', $data['date_of_incident']) === FALSE) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Invalid date format provided");
            }

            if (isset($data['phone'])) {
                $user = $userService->checkUserByPhoneNumber(trim($data['phone']));
                if (!empty($user) && $user->getId() !== (int)$id) {
                    return $utilService->makeResponse(Response::HTTP_CONFLICT, "Phone Number already Exist");
                }
            }

            $response = $clientService->updateClientByRequest($id, $data);
            if ($response['status'] === false) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $response['message']);
            }

            return $utilService->makeResponse(Response::HTTP_OK, $response['message']);
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_update_client]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Delete("/client/{id}", name="api_client_delete",  options={ "method_prefix" = false })
     *
     * @SWG\Delete(
     *     path="/api/user/client/{id}",
     *     tags={"Client"},
     *     summary="Authorises user via access token and delete client",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *
     *     @SWG\Parameter(
     *         ref="#parameters/authorization"
     *     ),
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer",
     *         required=true,
     *         description="Id"
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="Success",
     *      ),
     *     @SWG\Response(
     *          response=401,
     *          description="Unauthorized request",
     *      ),
     *      @SWG\Response(
     *          response=403,
     *          description="Access denied",
     *      ),
     *     @SWG\Response(
     *          response=422,
     *          description="Validation error",
     *      ),
     *     @SWG\Response(
     *          response=500,
     *          description="Internal server error",
     *      )
     * )
     *
     * @param $id
     * @param UtilService $utilService
     * @param ClientService $clientService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function firstResponderDeleteAction(
        $id,
        UtilService $utilService,
        ClientService $clientService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            if($clientService->deleteClientById((int)$id)) {
                return $utilService->makeResponse(Response::HTTP_OK, "Client is deleted successfully.");
            }
            return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Client does not exist.");
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_client_delete]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Get("/client-data-count", name="api_get_client_data_count",  options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/client-data-count",
     *     tags={"Client"},
     *     summary="Authorises user via access token and returns client data count",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *
     *     @SWG\Parameter(
     *         ref="#parameters/authorization"
     *     ),
     *     @SWG\Parameter(
     *         name="from_date",
     *         in="query",
     *         type="string",
     *         description="Format: yyyy-mm-dd",
     *         required=false,
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="Success",
     *      ),
     *     @SWG\Response(
     *          response=401,
     *          description="Unauthorized request",
     *      ),
     *      @SWG\Response(
     *          response=403,
     *          description="Access denied",
     *      ),
     *     @SWG\Response(
     *          response=422,
     *          description="Validation error",
     *      ),
     *     @SWG\Response(
     *          response=500,
     *          description="Internal server error",
     *      )
     * )
     *
     * @param Request $request
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @param ClientService $clientService
     * @return JsonResponse
     */
    public function getClientsDataCountAction(
        Request $request,
        UtilService $utilService,
        LoggerInterface $clientApiLogger,
        ClientService $clientService
    ) {
        try {
            $fromDate = $request->get('from_date');
            $data = $clientService->getClientsDataCount($fromDate);
            return $utilService->makeResponse(Response::HTTP_OK, '', $data);
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_get_client_data_count]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }
}