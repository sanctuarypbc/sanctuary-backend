<?php

namespace App\ApiBundle\Controller\Organization;

use App\ApiBundle\Service\ClientService;
use App\ApiBundle\Service\ClientTypeService;
use App\ApiBundle\Service\FirstResponderService;
use App\ApiBundle\Service\OrganizationService;
use App\ApiBundle\Service\UserService;
use App\ApiBundle\Service\UtilService;
use App\Entity\ClientType;
use App\Entity\Organization;
use App\Entity\User;
use App\Enum\OrganizationEnum;
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
 * Class OrganizationApiController
 * @package App\ApiBundle\Controller\Organization
 */
class OrganizationApiController extends AbstractController
{
    /**
     * @Get("/organization-stats", name="api_organization_stats",  options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/organization-stats",
     *     tags={"Organization"},
     *     summary="Authorises user via access token and list organization",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *
     *     @SWG\Parameter(
     *         ref="#parameters/authorization"
     *     ),
     *     @SWG\Parameter(
     *         name="organization_id",
     *         in="query",
     *         type="integer",
     *         required=false,
     *         description="Organization Id"
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
     * @param LoggerInterface $adminApiLogger
     * @param OrganizationService $organizationService
     * @param FirstResponderService $firstResponderService
     * @return JsonResponse
     */
    public function getOrganizationStatsAction(
        Request $request,
        UtilService $utilService,
        LoggerInterface $adminApiLogger,
        OrganizationService $organizationService,
        FirstResponderService $firstResponderService
    ) {
        try {
            if ($this->isGranted(UserEnum::ROLE_ORGANIZATION)) {
                $organizationObj = $organizationService->getOrganizationByUser($this->getUser());
            } elseif ($this->isGranted(UserEnum::ROLE_SUPER_ADMIN)) {
                $organizationId = $request->get('organization_id');
                $organizationObj = $organizationService->getOrganizationById($organizationId);
            } else {
                return $utilService->makeResponse(Response::HTTP_UNAUTHORIZED, "You have not access to perform this action");
            }

            if (!$organizationObj instanceof Organization) {
                return $utilService->makeResponse(Response::HTTP_NOT_FOUND,'Organization not found.');
            }

            $firstResponders = $firstResponderService->getAllActiveFRDetailsByOrganization($organizationObj);

            if (empty($firstResponders)) {
                return $utilService->makeResponse(Response::HTTP_NOT_FOUND,'First Respondent does not exist.');
            }

            $response = $organizationService->getOrganizationStatsById($firstResponders);
            return $utilService->makeResponse(Response::HTTP_OK, null, $response);
        } catch (\Exception $exception) {
            $adminApiLogger->error('[api_organization_stats]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Get("/organization-client", name="api_organization_client",  options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/organization-client",
     *     tags={"Organization"},
     *     summary="Authorises user via access token and list organization",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *
     *     @SWG\Parameter(
     *         ref="#parameters/authorization"
     *     ),
     *     @SWG\Parameter(
     *         name="organization_id",
     *         in="query",
     *         type="integer",
     *         required=false,
     *         description="Organization Id"
     *     ),
     *     @SWG\Parameter(
     *          name="age",
     *          in="query",
     *          type="string",
     *          required=false,
     *          description="Provide age in term of range like : 20-25"
     *      ),
     *     @SWG\Parameter(
     *          name="gender",
     *          in="query",
     *          type="string",
     *          required=false,
     *          description="Gender"
     *      ),
     *     @SWG\Parameter(
     *          name="race",
     *          in="query",
     *          type="string",
     *          required=false,
     *          description="Race"
     *      ),
     *     @SWG\Parameter(
     *          name="ethnicity",
     *          in="query",
     *          type="string",
     *          required=false,
     *          description="Ethnicity"
     *      ),
     *     @SWG\Parameter(
     *          name="zip_code_address",
     *          in="query",
     *          type="string",
     *          required=false,
     *          description="Zip code of address"
     *      ),
     *     @SWG\Parameter(
     *          name="zip_code_incident",
     *          in="query",
     *          type="string",
     *          required=false,
     *          description="Zip code of incident"
     *      ),
     *     @SWG\Parameter(
     *          name="client_type_id",
     *          in="query",
     *          type="integer",
     *          required=false,
     *          description="Client Type Id"
     *      ),
     *     @SWG\Parameter(
     *          name="user_dependence",
     *          in="query",
     *          type="string",
     *          required=false,
     *          description="user dependence in the form of json like : { 'age_start': '20', 'age_end': '25', 'gender': 'male' }"
     *      ),
     *     @SWG\Parameter(
     *          name="date_time_incident_from",
     *          in="query",
     *          type="string",
     *          required=false,
     *          description="Datetime incident from"
     *      ),
     *     @SWG\Parameter(
     *          name="date_time_incident_to",
     *          in="query",
     *          type="string",
     *          required=false,
     *          description="Datetime incident to"
     *      ),
     *     @SWG\Parameter(
     *          name="from_date",
     *          in="query",
     *          type="string",
     *          required=false,
     *          description="From date"
     *      ),
     *     @SWG\Parameter(
     *          name="to_date",
     *          in="query",
     *          type="string",
     *          required=false,
     *          description="To date"
     *      ),
     *     @SWG\Parameter(
     *          name="is_graph",
     *          in="query",
     *          type="boolean",
     *          required=false,
     *          description="If you want to show graph then true, Default false"
     *      ),
     *     @SWG\Parameter(
     *          name="page",
     *          in="query",
     *          type="integer",
     *          required=false,
     *          description="Page number"
     *      ),
     *     @SWG\Parameter(
     *          name="per_page",
     *          in="query",
     *          type="integer",
     *          required=false,
     *          description="Records per page. Default 50"
     *      ),
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
     * @param LoggerInterface $adminApiLogger
     * @param OrganizationService $organizationService
     * @param FirstResponderService $firstResponderService
     * @param ClientService $clientService
     * @param ClientTypeService $clientTypeService
     * @return JsonResponse
     */
    public function getOrganizationClientsAction(
        Request $request,
        UtilService $utilService,
        LoggerInterface $adminApiLogger,
        OrganizationService $organizationService,
        FirstResponderService $firstResponderService,
        ClientService $clientService,
        ClientTypeService $clientTypeService
    ) {
        try {
            $data = $request->query->all();
            if ($this->isGranted(UserEnum::ROLE_ORGANIZATION)) {
                $organizationObj = $organizationService->getOrganizationByUser($this->getUser());
            } elseif ($this->isGranted(UserEnum::ROLE_SUPER_ADMIN)) {
                $organizationId = $data['organization_id'] ?? null;
                $organizationObj = $organizationService->getOrganizationById($organizationId);
            } else {
                return $utilService->makeResponse(Response::HTTP_UNAUTHORIZED, "You have not access to perform this action");
            }
            if (!$organizationObj instanceof Organization) {
                return $utilService->makeResponse(Response::HTTP_NOT_FOUND,'Organization not found.');
            }
            $firstResponders = $firstResponderService->getAllActiveFRDetailsByOrganization($organizationObj);
            if (empty($firstResponders)) {
                return $utilService->makeResponse(Response::HTTP_NOT_FOUND,'First Respondent does not exist.');
            }
            if (isset($data['is_graph']) && $data['is_graph'] === 'true' && !isset($data['date_time_incident_from']))  {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST,'Please provide the Incident date from');
            }
            if (isset($data['client_type_id']) && $data['client_type_id']) {
                $clientType = $clientTypeService->getClientTypeById($data['client_type_id']);
                if (!$clientType instanceof ClientType) {
                    return $utilService->makeResponse(Response::HTTP_NOT_FOUND,'Client type does not exist');
                }
            }
            $clientsArray = $clientService->getClientsByFR($firstResponders, $data);
            if (isset($data['is_graph']) && $data['is_graph'] === 'true') {
                $response['data'] = $clientsArray['data'];
            } else {
                $response['data'] = $clientService->makeClientResponseByClientDetails($clientsArray['data']);
            }
            $response['count'] = $clientsArray['count'];
            return $utilService->makeResponse(Response::HTTP_OK, null, $response);
        } catch (\Exception $exception) {
            $adminApiLogger->error('[api_organization_client]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR,  "Something went wrong.");
        }
    }

    /**
     * @Get("/organization", name="api_organization_list",  options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/organization",
     *     tags={"Organization"},
     *     summary="Authorises user via access token and list organization",
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
     * @param LoggerInterface $adminApiLogger
     * @param OrganizationService $organizationService
     * @return JsonResponse
     */
    public function getOrganizationAction(
        Request $request,
        UtilService $utilService,
        LoggerInterface $adminApiLogger,
        OrganizationService $organizationService
    ) {
        try {
            $organizationId = $request->get('id');
            if ($this->isGranted(UserEnum::ROLE_ORGANIZATION)) {
                $organizationObj = $organizationService->getOrganizationByUser($this->getUser());
            } elseif ($this->isGranted(UserEnum::ROLE_SUPER_ADMIN)) {
                $organizationObj = $organizationService->getOrganizationById($organizationId);
            } else {
                return $utilService->makeResponse(Response::HTTP_UNAUTHORIZED, "You have not access to perform this action");
            }
            if($organizationObj !== null) {
                $data = $organizationService->makeSingleOrganizationResponse($organizationObj);
            } else {
                $organizations = $organizationService->getAllActiveOrganizations();
                $data = $organizationService->makeOrganizationApiResponse($organizations);
            }
            return $utilService->makeResponse(Response::HTTP_OK, null, $data);
        } catch (\Exception $exception) {
            $adminApiLogger->error('[api_organization_list]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Post("/organization", name="api_organization_add",  options={ "method_prefix" = false })
     *
     * @SWG\Post(
     *     path="/api/user/organization",
     *     tags={"Organization"},
     *     summary="Authorises user via access token and create organization",
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
     *                  property="username",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="password",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="name",
     *                  type="string",
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
     *                  property="zip_code",
     *                  type="integer",
     *                  ),
     *             @SWG\Property(
     *                  property="state",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="contact_name",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="contact_phone",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="contact_email",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="type_id",
     *                  type="integer",
     *                  ),
     *             @SWG\Property(
     *                  property="client_type_ids",
     *                  type="string",
     *                  example="1,2"
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
     * @param Request $request
     * @param UtilService $utilService
     * @param LoggerInterface $adminApiLogger
     * @param OrganizationService $organizationService
     * @return JsonResponse
     */
    public function createOrganizationAction(
        Request $request,
        UtilService $utilService,
        LoggerInterface $adminApiLogger,
        OrganizationService $organizationService,
        UserService $userService
    ) {
        try {
            $data = json_decode($request->getContent(), true);
            $validRequest = $utilService->checkRequiredFieldsByRequestedData($data, OrganizationEnum::CREATE_ORGANIZATION_API_REQUIRED_FIELDS);
            if (!$validRequest) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Some required parameters are missing.");
            }

            $latLngData = $utilService->getLatLngByZipCode($data['street_address'] . " " . $data['city'] . " " . $data['state'] . ", " . $data['zip_code']);
            if (empty($latLngData)) {
                return $utilService->makeResponse(
                    Response::HTTP_BAD_REQUEST,
                    "Invalid address data provided."
                );
            }
            $usernameExist = $userService->getUserByUsername($data['username']);
            if ($usernameExist) {
                return $utilService->makeResponse(Response::HTTP_CONFLICT, "Username already exists.");
            }
            $contactEmail = $userService->getUserByEmail($data['contact_email']);
            if ($contactEmail) {
                return $utilService->makeResponse(Response::HTTP_CONFLICT, "Contact Email already exists.");
            }

            $data = array_merge($data, $latLngData);
            $response = $organizationService->createOrganizationByRequestData($data);
            if ($response['status'] === false) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $response['message']);
            }
            return $utilService->makeResponse(Response::HTTP_OK, $response['message'], $response["data"]);
        } catch (\Exception $exception) {
            $adminApiLogger->error('[api_create_organization]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Put("/organization/{id}", name="api_organization_edit",  options={ "method_prefix" = false })
     *
     * @SWG\Put(
     *     path="/api/user/organization/{id}",
     *     tags={"Organization"},
     *     summary="Authorises user via access token and update organization",
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
     *                  property="username",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="password",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="name",
     *                  type="string",
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
     *                  property="zip_code",
     *                  type="integer",
     *                  ),
     *             @SWG\Property(
     *                  property="state",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="contact_name",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="contact_phone",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="contact_email",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="type_id",
     *                  type="integer",
     *                  ),
     *             @SWG\Property(
     *                  property="client_type_ids",
     *                  type="string",
     *                  example="1,2"
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
     * @param LoggerInterface $adminApiLogger
     * @param OrganizationService $organizationService
     * @return JsonResponse
     */
    public function editOrganizationAction(
        $id,
        Request $request,
        UtilService $utilService,
        LoggerInterface $adminApiLogger,
        OrganizationService $organizationService,
        UserService $userService
    ) {
        try {
            $organizationObj = $organizationService->getOrganizationById($id);
            if (!$organizationObj instanceof Organization) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Organization doesn't exist.");
            }

            $data = json_decode($request->getContent(), true);
            $validRequest = $utilService->checkIfRequestHasFieldsToUpdate($data, OrganizationEnum::ORGANIZATION_POSSIBLE_UPDATE_FIELDS);
            if (!$validRequest) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Some parameters required.");
            }
            $latLngData = [];
            if (
                isset($data['street_address'], $data['city'], $data['zip_code'], $data['state']) &&
                empty($latLngData = $utilService->getLatLngByZipCode($data['street_address'] . " " . $data['city'] . " " . $data['state'] . ", " . $data['zip_code']))
            ) {
                return $utilService->makeResponse(
                    Response::HTTP_BAD_REQUEST,
                    "Invalid address data provided."
                );
            }

            if (isset($data['username'])) {
                $user = $userService->getUserByUsername($data['username']);
                if ($user instanceof  User && $organizationObj->getUser() instanceof  User && $user->getId() != $organizationObj->getUser()->getId()) {
                    return $utilService->makeResponse(Response::HTTP_CONFLICT, "Username already exists.");
                }
            }

            if ($data['contact_email']) {
                $user = $userService->getUserByEmail($data['contact_email']);
                if ($user instanceof  User && $organizationObj->getUser() instanceof  User && $user->getId() != $organizationObj->getUser()->getId()) {
                    return $utilService->makeResponse(Response::HTTP_CONFLICT, "Contact Email already exists.");
                }
            }

            $data = array_merge($data, $latLngData);
            $response = $organizationService->updateOrganizationByRequest($organizationObj, $data);
            if ($response['status'] === false) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $response['message']);
            }

            return $utilService->makeResponse(Response::HTTP_OK, $response['message']);
        } catch (\Exception $exception) {
            $adminApiLogger->error('[api_update_organization]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Delete("/organization/{id}", name="api_organization_delete",  options={ "method_prefix" = false })
     *
     * @SWG\Delete(
     *     path="/api/user/organization/{id}",
     *     tags={"Organization"},
     *     summary="Authorises user via access token and delete organization",
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
     * @param OrganizationService $organizationService
     * @param LoggerInterface $adminApiLogger
     * @return JsonResponse
     */
    public function organizationDeleteAction(
        $id,
        UtilService $utilService,
        OrganizationService $organizationService,
        LoggerInterface $adminApiLogger
    ) {
        try {
            $organizationObj = $organizationService->getOrganizationById($id);
            if (!$organizationObj instanceof Organization) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST,'Organization not found.');
            }

            if($organizationService->checkIfOrganizationIsLinked($organizationObj)) {
                return $utilService->makeResponse(
                    Response::HTTP_BAD_REQUEST,
                    "Organization is linked with a first responder or an advocate."
                );
            }

            if ($organizationService->deleteOrganization($organizationObj)) {
                return $utilService->makeResponse(Response::HTTP_OK, "Organization is deleted successfully.");
            }

            return $utilService->makeResponse(Response::HTTP_BAD_REQUEST,
                "Either organization does not exist or you don't have sufficient rights to delete this item.");
        } catch (\Exception $exception) {
            $adminApiLogger->error('[api_organization_delete]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }
}