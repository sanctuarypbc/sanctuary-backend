<?php

namespace App\ApiBundle\Controller\FirstResponder;

use App\ApiBundle\Service\FirstResponderService;
use App\ApiBundle\Service\UserService;
use App\ApiBundle\Service\UtilService;
use App\Enum\FirstResponderEnum;
use App\Enum\UserEnum;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
 * Class FirstResponderApiController
 * @package App\ApiBundle\Controller\FirstResponder
 */
class FirstResponderApiController extends AbstractController
{
    /**
     * @Get("/first-responder", name="api_first_responder_list",  options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/first-responder",
     *     tags={"First Responder"},
     *     summary="Authorises user via access token and list first responder",
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
     * @param LoggerInterface $firstResponderApiLogger
     * @param FirstResponderService $firstResponderService
     * @return JsonResponse
     */
    public function getFirstResponderAction(
        Request $request,
        UtilService $utilService,
        LoggerInterface $firstResponderApiLogger,
        FirstResponderService $firstResponderService
    ) {
        try {
            if (in_array(UserEnum::ROLE_FIRST_RESPONDER, $this->getUser()->getRoles(), true)) {
                $firstResponderUserId = $this->getUser()->getId();
            } elseif (in_array(UserEnum::ROLE_SUPER_ADMIN, $this->getUser()->getRoles(), true)) {
                $firstResponderUserId = $request->get('id');
            } else {
                return $utilService->makeResponse(Response::HTTP_UNAUTHORIZED, "you have not rights to process this request.");
            }
            if (!empty($firstResponderUserId)) {
                $firstResponderDetailObj = $firstResponderService->getFirstResponderDetailByUserId((int)$firstResponderUserId);
                $data = $firstResponderService->makeSingleFRResponseByFRDetail($firstResponderDetailObj);
            } else {
                $firstResponderDetailObjs = $firstResponderService->getAllActiveFRDetails();
                $data = $firstResponderService->makeFirstResponderResponseByFRDetails($firstResponderDetailObjs);
            }
            return $utilService->makeResponse(Response::HTTP_OK, null, $data);
        } catch (\Exception $exception) {
            $firstResponderApiLogger->error('[api_first_responder_detail]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Post("/first-responder", name="api_first_responder_add",  options={ "method_prefix" = false })
     *
     * @SWG\Post(
     *     path="/api/user/first-responder",
     *     tags={"First Responder"},
     *     summary="Authorises user via access token and create first responder",
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
     *                  property="nick_name",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="phone",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="office_phone",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="gender",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="identification_number",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="type_id",
     *                  type="integer",
     *                  ),
     *             @SWG\Property(
     *                  property="organization_id",
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
     * @param Request $request
     * @param UtilService $utilService
     * @param LoggerInterface $firstResponderApiLogger
     * @param FirstResponderService $firstResponderService
     * @param UserService $userService
     * @return JsonResponse
     */
    public function createFirstResponderAction(
        Request $request,
        UtilService $utilService,
        LoggerInterface $firstResponderApiLogger,
        FirstResponderService $firstResponderService,
        UserService $userService
    ) {
        try {
            $data = json_decode($request->getContent(), true);
            $validRequest = $utilService->checkRequiredFieldsByRequestedData($data, FirstResponderEnum::CREATE_FR_API_REQUIRED_FIELDS);
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

            $response = $firstResponderService->createFRByRequestData($data);
            if ($response['status'] === false) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $response['message']);
            }
            return $utilService->makeResponse(Response::HTTP_OK, $response['message']);
        } catch (\Exception $exception) {
            $firstResponderApiLogger->error('[api_create_first_responder]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Put("/first-responder/{id}", name="api_first_responder_edit",  options={ "method_prefix" = false })
     *
     * @SWG\Put(
     *     path="/api/user/first-responder/{id}",
     *     tags={"First Responder"},
     *     summary="Authorises user via access token and update first responder",
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
     *                  property="nick_name",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="phone",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="office_phone",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="gender",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="identification_number",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="type_id",
     *                  type="integer",
     *                  ),
     *             @SWG\Property(
     *                  property="organization_id",
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
     * @param LoggerInterface $firstResponderApiLogger
     * @param FirstResponderService $firstResponderService
     * @param UserService $userService
     * @return JsonResponse
     */
    public function editFirstResponderAction(
        $id,
        Request $request,
        UtilService $utilService,
        LoggerInterface $firstResponderApiLogger,
        FirstResponderService $firstResponderService,
        UserService $userService
    ) {
        try {
            $data = json_decode($request->getContent(), true);
            $validRequest = $utilService->checkIfRequestHasFieldsToUpdate($data, FirstResponderEnum::FR_POSSIBLE_UPDATE_FIELDS_ALL);
            if (!$validRequest) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Some parameters required.");
            }

            $emailExist = $userService->getUserByEmail($data['email']);
            if ($emailExist && $emailExist->getId() != $id) {
                return $utilService->makeResponse(Response::HTTP_CONFLICT, "Email already exists.");
            }

            $usernameExist = $userService->getUserByUsername($data['username']);
            if ($usernameExist && $usernameExist->getId() != $id) {
                return $utilService->makeResponse(Response::HTTP_CONFLICT, "Username already exists.");
            }

            $response = $firstResponderService->updateFRByRequest((int)$id, $data);
            if ($response['status'] === false) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $response['message']);
            }

            return $utilService->makeResponse(Response::HTTP_OK, $response['message']);
        } catch (\Exception $exception) {
            $firstResponderApiLogger->error('[api_update_first_responder]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Delete("/first-responder/{id}", name="api_first_responder_delete",  options={ "method_prefix" = false })
     *
     * @SWG\Delete(
     *     path="/api/user/first-responder/{id}",
     *     tags={"First Responder"},
     *     summary="Authorises user via access token and delete first responder",
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
     * @param FirstResponderService $firstResponderService
     * @param LoggerInterface $firstResponderApiLogger
     * @return JsonResponse
     */
    public function firstResponderDeleteAction(
        $id,
        UtilService $utilService,
        FirstResponderService $firstResponderService,
        LoggerInterface $firstResponderApiLogger
    ) {
        try {
            if($firstResponderService->deleteFirstResponderById((int)$id)) {
                return $utilService->makeResponse(Response::HTTP_OK, "First responder is deleted successfully.");
            }
            return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Either first responder does not exist or assigned to some client");
        } catch (\Exception $exception) {
            $firstResponderApiLogger->error('[api_first_responder_delete]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }
}