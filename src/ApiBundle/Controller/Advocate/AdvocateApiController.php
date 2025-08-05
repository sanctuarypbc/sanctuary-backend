<?php

namespace App\ApiBundle\Controller\Advocate;

use App\ApiBundle\Service\AdvocateService;
use App\ApiBundle\Service\ClientService;
use App\ApiBundle\Service\UserService;
use App\ApiBundle\Service\UtilService;
use App\Enum\AdvocateEnum;
use Psr\Log\LoggerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use Swagger\Annotations as SWG;

/**
 * Class AdvocateApiController
 * @package App\ApiBundle\Controller\Advocate
 */
class AdvocateApiController extends AbstractFOSRestController
{
    /**
     * @Get("/advocate", name="api_advocate_list",  options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/advocate",
     *     tags={"Advocate"},
     *     summary="Authorises user via access token and returns advocate list",
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
     * @param AdvocateService $advocateService
     * @param LoggerInterface $advocateApiLogger
     * @return JsonResponse
     */
    public function advocateListAction(
        Request $request,
        UtilService $utilService,
        AdvocateService $advocateService,
        LoggerInterface $advocateApiLogger
    ) {
        try {
            $advocateDetails = $advocateService->getFilteredAdvocates($request);
            $data = $advocateService->makeAdvocatesApiResponse($advocateDetails);
            return $utilService->makeResponse(Response::HTTP_OK, null, $data);
        } catch (\Exception $exception) {
            $advocateApiLogger->error('[api_advocate_list]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Post("/advocate", name="api_advocate_create",  options={ "method_prefix" = false })
     *
     * @SWG\Post(
     *     path="/api/user/advocate",
     *     tags={"Advocate"},
     *     summary="Authorises user via access token and create an advocate",
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
     *                  property="identifier",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="email",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="username",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="password",
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
     *                  property="service_type_id",
     *                  type="integer",
     *                  ),
     *             @SWG\Property(
     *                  property="organization_id",
     *                  type="integer",
     *                  ),
     *             @SWG\Property(
     *                  property="language_ids",
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
     * @param LoggerInterface $advocateApiLogger
     * @param AdvocateService $advocateService
     * @param UserService $userService
     * @return JsonResponse
     */
    public function createAdvocateAction(
        Request $request,
        UtilService $utilService,
        LoggerInterface $advocateApiLogger,
        AdvocateService $advocateService,
        UserService $userService
    ) {
        try {
            $data = json_decode($request->getContent(), true);
            $validRequest = $utilService->checkRequiredFieldsByRequestedData($data, AdvocateEnum::CREATE_Advocate_API_REQUIRED_FIELDS);
            if (!$validRequest) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Some required parameters are missing.");
            }

            $emailExist = $userService->getUserByEmail($data['email']);
            if ($emailExist) {
                return $utilService->makeResponse(Response::HTTP_CONFLICT, "Email already exists.");
            }

            $usernameExist = $userService->getUserByUsername($data['username']);
            if ($usernameExist) {
                return $utilService->makeResponse(Response::HTTP_CONFLICT, "Username already exists.");
            }

            $response = $advocateService->createAdvocateByRequestData($data);
            if ($response['status'] === false) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $response['message']);
            }
            return $utilService->makeResponse(Response::HTTP_OK, $response['message']);
        } catch (\Exception $exception) {
            $advocateApiLogger->error('[api_create_advocate]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Put("/advocate/{id}", name="api_advocate_updaate",  options={ "method_prefix" = false })
     *
     * @SWG\Put(
     *     path="/api/user/advocate/{id}",
     *     tags={"Advocate"},
     *     summary="Authorises user via access token and update an advocate",
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
     *                  property="identifier",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="email",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="username",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="phone",
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
     *                  property="additional_phone",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="emergency_contact",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="service_type_id",
     *                  type="integer",
     *                  ),
     *             @SWG\Property(
     *                  property="organization_id",
     *                  type="integer",
     *                  ),
     *             @SWG\Property(
     *                  property="language_ids",
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
     * @param LoggerInterface $advocateApiLogger
     * @param AdvocateService $advocateService
     * @param UserService $userService
     * @return JsonResponse
     */
    public function editAdvocateAction(
        $id,
        Request $request,
        UtilService $utilService,
        LoggerInterface $advocateApiLogger,
        AdvocateService $advocateService,
        UserService $userService
    ) {
        try {
            $data = json_decode($request->getContent(), true);
            $validRequest = $utilService->checkIfRequestHasFieldsToUpdate($data, AdvocateEnum::ADVOCATE_POSSIBLE_UPDATE_FIELDS_ALL);
            if (!$validRequest) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Some parameters required.");
            }

            $emailExist = isset($data['email']) ? $userService->getUserByEmail($data['email']) : null;
            if ($emailExist && $emailExist->getId() != $id) {
                return $utilService->makeResponse(Response::HTTP_CONFLICT, "Email already exists.");
            }

            $usernameExist = isset($data['username']) ? $userService->getUserByUsername($data['username']) : null;
            if ($usernameExist && $usernameExist->getId() != $id) {
                return $utilService->makeResponse(Response::HTTP_CONFLICT, "Username already exists.");
            }

            $response = $advocateService->updateAdvocateByRequest((int)$id, $data);
            if ($response['status'] === false) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $response['message']);
            }

            return $utilService->makeResponse(Response::HTTP_OK, $response['message']);
        } catch (\Exception $exception) {
            $advocateApiLogger->error('[api_update_advocate]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }


    /**
     * @Delete("/advocate/{id}", name="api_advocate_delete",  options={ "method_prefix" = false })
     *
     * @SWG\Delete(
     *     path="/api/user/advocate/{id}",
     *     tags={"Advocate"},
     *     summary="Authorises user via access token and delete an advocate",
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
     * @param AdvocateService $advocateService
     * @param LoggerInterface $advocateApiLogger
     * @return JsonResponse
     */
    public function advocateDeleteAction(
        $id,
        UtilService $utilService,
        AdvocateService $advocateService,
        LoggerInterface $advocateApiLogger
    ) {
        try {
            if($advocateService->deleteAdvocateById((int)$id)) {
                return $utilService->makeResponse(Response::HTTP_OK, "Advocate is deleted successfully.");
            }
            return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Either advocate does not exist or assigned to some client");
        } catch (\Exception $exception) {
            $advocateApiLogger->error('[api_advocate_delete]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Get("/advocate-clients", name="api_advocate_clients",  options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/advocate-clients",
     *     tags={"Advocate"},
     *     summary="Authorises user via access token and get advocate clients",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *
     *     @SWG\Parameter(
     *         ref="#parameters/authorization"
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
     * @param ClientService $clientService
     * @param LoggerInterface $advocateApiLogger
     * @return JsonResponse
     */
    public function advocateClientsListAction(
        Request $request,
        UtilService $utilService,
        ClientService $clientService,
        LoggerInterface $advocateApiLogger
    ) {
        try {
            $advocateClientsDetail = $clientService->getFilteredAdvocateClients($this->getUser(), $request);
            $data = $clientService->makeClientResponseByClientDetails($advocateClientsDetail);
            return $utilService->makeResponse(Response::HTTP_OK, null, $data);
        } catch (\Exception $exception) {
            $advocateApiLogger->error('[api_advocate_clients]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }
}