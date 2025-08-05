<?php

namespace App\ApiBundle\Controller\Facility;

use App\ApiBundle\Service\ClientService;
use App\ApiBundle\Service\FacilityService;
use App\ApiBundle\Service\UserService;
use App\ApiBundle\Service\UtilService;
use App\Entity\User;
use App\Enum\FacilityEnum;
use App\Enum\UserEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use Swagger\Annotations as SWG;

/**
 * Class FacilityApiController
 * @package App\ApiBundle\Controller\Facility
 */
class FacilityApiController extends AbstractController
{
    /**
     * @Get("/facility", name="api_facility_list",  options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/facility",
     *     tags={"Facility"},
     *     summary="Authorises user via access token and list facility",
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
     *         name="zip_code",
     *         in="query",
     *         type="number",
     *         required=false,
     *         description="Zipcode"
     *     ),
     *     @SWG\Parameter(
     *         name="radius",
     *         in="query",
     *         type="number",
     *         required=false,
     *         description="Radius"
     *     ),
     *     @SWG\Parameter(
     *         name="pets_allowed",
     *         in="query",
     *         type="boolean",
     *         required=false,
     *         description="Pets allowed"
     *     ),
     *     @SWG\Parameter(
     *         name="client_details",
     *         in="query",
     *         type="boolean",
     *         required=false,
     *         description="Client details"
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
     * @param LoggerInterface $facilityApiLogger
     * @param FacilityService $facilityService
     * @return JsonResponse
     */
    public function facilityListAction(
        Request $request,
        UtilService $utilService,
        LoggerInterface $facilityApiLogger,
        FacilityService $facilityService
    ) {
        try {
            $facilities = $facilityService->getFilteredFacilities($request);
            $data = $facilityService->makeApiResponseByFacilitiesArray($facilities);
            return $utilService->makeResponse(Response::HTTP_OK, null, $data);
        } catch (\Exception $exception) {
            $facilityApiLogger->error('[api_facility_list]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Post("/facility", name="api_facility_add",  options={ "method_prefix" = false })
     *
     * @SWG\Post(
     *     path="/api/user/facility",
     *     tags={"Facility"},
     *     summary="Authorises user via access token and create facility",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *
     *     @SWG\Parameter(
     *         ref="#parameters/authorization"
     *     ),
     *     @SWG\Parameter(
     *         name="request",
     *         in="formData",
     *         type="string",
     *         required=true,
     *         description="Json encoded request"
     *     ),
     *     @SWG\Parameter(
     *         name="desktop_logo",
     *         in="formData",
     *         type="file",
     *         required=true,
     *         description="Desktop logo"
     *     ),
     *     @SWG\Parameter(
     *         name="mobile_logo",
     *         in="formData",
     *         type="file",
     *         required=true,
     *         description="Mobile logo"
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
     * @param LoggerInterface $facilityApiLogger
     * @param FacilityService $facilityService
     * @param UserService $userService
     * @return JsonResponse
     */
    public function createFacilityAction(
        Request $request,
        UtilService $utilService,
        LoggerInterface $facilityApiLogger,
        FacilityService $facilityService,
        UserService $userService
    ) {
        try {
            $data = json_decode($request->get('request'), true);
            $desktopLogo = $request->files->get('desktop_logo');
            $mobileLogo = $request->files->get('mobile_logo');
            $validRequest = $utilService->checkRequiredFieldsByRequestedData($data, FacilityEnum::CREATE_FACILITY_API_REQUIRED_FIELDS);
            if (!$validRequest || empty($desktopLogo) || empty($mobileLogo) || !isset($data['type_id'])) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Some required parameters are missing.");
            }

            $emailExist = $userService->getUserByEmail($data['contact_email']);
            if ($emailExist) {
                return $utilService->makeResponse(Response::HTTP_CONFLICT, "Email already exists.");
            }

            $usernameExist = $userService->getUserByUsername($data['username']);
            if ($usernameExist) {
                return $utilService->makeResponse(Response::HTTP_CONFLICT, "Username already exists.");
            }

            $urlPrefixExist = $facilityService->getFacilityByUrlPrefix($data['url_prefix']);
            if ($urlPrefixExist) {
                return $utilService->makeResponse(Response::HTTP_CONFLICT, "Url prefix already exists.");
            }

            if (!empty($desktopLogo) && !$utilService->verifyImageDimension(
                    $desktopLogo,
                    FacilityEnum::DESKTOP_LOGO_MAX_WIDTH,
                    FacilityEnum::DESKTOP_LOGO_MAX_HEIGHT
                )) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Invalid dimension for web logo.");
            }

            if (!empty($mobileLogo) && !$utilService->verifyImageDimension(
                    $mobileLogo,
                    FacilityEnum::MOBILE_LOGO_MAX_WIDTH,
                    FacilityEnum::MOBILE_LOGO_MAX_HEIGHT
                )) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Invalid dimension for mobile logo.");
            }

            $response = $facilityService->createFacilityFromRequestData($data, $desktopLogo, $mobileLogo);
            if ($response['status'] === false) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $response['message']);
            }
            return $utilService->makeResponse(Response::HTTP_OK, $response['message']);
        } catch (\Exception $exception) {
            $facilityApiLogger->error('[api_create_facility]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Delete("/facility/{id}", name="api_facility_delete",  options={ "method_prefix" = false })
     *
     * @SWG\Delete(
     *     path="/api/user/facility/{id}",
     *     tags={"Facility"},
     *     summary="Authorises user via access token and delete facility",
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
     * @param FacilityService $facilityService
     * @param LoggerInterface $facilityApiLogger
     * @return JsonResponse
     */
    public function facilityDeleteAction(
        $id,
        UtilService $utilService,
        FacilityService $facilityService,
        LoggerInterface $facilityApiLogger
    ) {
        try {
            if($facilityService->deleteFacilityById((int)$id)) {
                return $utilService->makeResponse(Response::HTTP_OK, "Facility is deleted successfully.");
            }
            return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Either Facility does not exist or assigned to some client");
        } catch (\Exception $exception) {
            $facilityApiLogger->error('[api_facility_delete]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Post("/facility/{id}", name="api_facility_edit",  options={ "method_prefix" = false })
     *
     * @SWG\Post(
     *     path="/api/user/facility/{id}",
     *     tags={"Facility"},
     *     summary="Authorises user via access token and update facility",
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
     *         name="request",
     *         in="formData",
     *         type="string",
     *         required=false,
     *         description="Json encoded request"
     *     ),
     *     @SWG\Parameter(
     *         name="desktop_logo",
     *         in="formData",
     *         type="file",
     *         required=false,
     *         description="Desktop logo"
     *     ),
     *     @SWG\Parameter(
     *         name="mobile_logo",
     *         in="formData",
     *         type="file",
     *         required=false,
     *         description="Mobile logo"
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
     * @param LoggerInterface $facilityApiLogger
     * @param FacilityService $facilityService
     * @return JsonResponse
     */
    public function editFacilityAction(
        $id,
        Request $request,
        UtilService $utilService,
        LoggerInterface $facilityApiLogger,
        FacilityService $facilityService
    ) {
        try {
            $data = json_decode($request->get('request'), true);
            $desktopLogo = $request->files->get('desktop_logo');
            $mobileLogo = $request->files->get('mobile_logo');
            $validRequest = $utilService->checkIfRequestHasFieldsToUpdate($data, FacilityEnum::FACILITY_POSSIBLE_UPDATE_FIELDS_ALL);
            if (!$validRequest && empty($desktopLogo) && empty($mobileLogo)) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Some parameters required.");
            }

            $facilityWithUrlPrefix = $facilityService->getFacilityByUrlPrefix($data['url_prefix']);
            if ($facilityWithUrlPrefix && $facilityWithUrlPrefix->getId() != $id) {
                return $utilService->makeResponse(Response::HTTP_CONFLICT, "Url prefix already exists.");
            }

            if (!empty($desktopLogo) && !$utilService->verifyImageDimension(
                $desktopLogo,
                FacilityEnum::DESKTOP_LOGO_MAX_WIDTH,
                FacilityEnum::DESKTOP_LOGO_MAX_HEIGHT
            )) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Invalid dimension for web logo.");
            }

            if (!empty($mobileLogo) && !$utilService->verifyImageDimension(
                $mobileLogo,
                FacilityEnum::MOBILE_LOGO_MAX_WIDTH,
                FacilityEnum::MOBILE_LOGO_MAX_HEIGHT
            )) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Invalid dimension for mobile logo.");
            }

            $response = $facilityService->updateFacility($id, $data, $desktopLogo, $mobileLogo);
            if ($response['status'] === false) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $response['message']);
            }

            return $utilService->makeResponse(Response::HTTP_OK, $response['message']);
        } catch (\Exception $exception) {
            $facilityApiLogger->error('[api_update_facility]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Get("/download-facility-logo", name="download_facility-logo",  options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/download-facility-logo",
     *     tags={"Facility"},
     *     summary="Authorises user via access token and download facility logo",
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
     *         required=true,
     *         description="Facility Id"
     *     ),
     *     @SWG\Parameter(
     *         name="type",
     *         in="query",
     *         type="string",
     *         required=true,
     *         description="Type: desktop or mobile"
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
     * @param FacilityService $facilityService
     * @return BinaryFileResponse|JsonResponse
     */
    public function downloadAction(
        Request $request,
        UtilService $utilService,
        FacilityService $facilityService
    ) {
        $id = $request->query->get('id', null);
        $type = $request->query->get('type', null);
        if (empty($id) || empty($type)) {
            return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Required Params missing");
        }
        $result = $facilityService->getFacilityLogoUrl($id, $type);
        if ($result === false) {
            return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Facility does not exist");
        }
        $response = new BinaryFileResponse($result['logo']);
        if ($result['last_modified'] instanceof \DateTime) {
            $response->setLastModified($result['last_modified']);
        }
        return $response;
    }

    /**
     * @Get("/facility-clients", name="api_facility_clients",  options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/facility-clients",
     *     tags={"Facility"},
     *     summary="Authorises user via access token and get facility clients",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *
     *     @SWG\Parameter(
     *         ref="#parameters/authorization"
     *     ),
     *     @SWG\Parameter(
     *         name="facility_user_id",
     *         in="query",
     *         type="integer",
     *         required=false,
     *         description="Facility user Id"
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
     * @param UserService $userService
     * @param UtilService $utilService
     * @param ClientService $clientService
     * @param LoggerInterface $facilityApiLogger
     * @return JsonResponse
     */
    public function facilityClientsListAction(
        Request $request,
        UserService $userService,
        UtilService $utilService,
        ClientService $clientService,
        LoggerInterface $facilityApiLogger
    ) {
        try {
            $user = $this->getUser();
            if ($this->isGranted(UserEnum::ROLE_SUPER_ADMIN)) {
                $facilityUserId = $request->query->get('facility_user_id');
                if (!isset($facilityUserId)) {
                    return $utilService->makeResponse(Response::HTTP_NOT_FOUND, "Facility user id missing.");
                }
                $user = $userService->getUserById($request->query->get('facility_user_id'));
                if (!$user instanceof User) {
                    return $utilService->makeResponse(Response::HTTP_NOT_FOUND, "Facility user not found against id.");
                }
            }
            $clientsDetails = $clientService->getFacilityClients($user);
            $data = $clientService->makeClientResponseByClientDetails($clientsDetails);
            return $utilService->makeResponse(Response::HTTP_OK, null, $data);
        } catch (\Exception $exception) {
            $facilityApiLogger->error('[api_facility_clients]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }
}