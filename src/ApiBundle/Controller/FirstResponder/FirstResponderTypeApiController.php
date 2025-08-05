<?php

namespace App\ApiBundle\Controller\FirstResponder;

use App\ApiBundle\Service\FirstResponderTypeService;
use App\ApiBundle\Service\UtilService;
use App\Entity\FirstResponderType;
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
 * Class FirstResponderTypeApiController
 * @package App\ApiBundle\Controller\FirstResponder
 */
class FirstResponderTypeApiController extends AbstractController
{
    /**
     * @Get("/first-responder-type", name="api_first_responder_type_list",  options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/first-responder-type",
     *     tags={"First Responder Type"},
     *     summary="Authorises user via access token and list first responder type",
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
     * @param FirstResponderTypeService $firstResponderTypeService
     * @param LoggerInterface $firstResponderApiLogger
     * @return JsonResponse
     */
    public function firstResponderTypeListAction(
        Request $request,
        UtilService $utilService,
        FirstResponderTypeService $firstResponderTypeService,
        LoggerInterface $firstResponderApiLogger
    ) {
        try {
            $firstResponderTypeId = $request->get('id');
            $name = $request->get('name');
            $data = $firstResponderTypeService->getFirstResponderTypeList($firstResponderTypeId, $name);
            return $utilService->makeResponse(Response::HTTP_OK, null, $data);
        } catch (\Exception $exception) {
            $firstResponderApiLogger->error('[api_first_responder_type_list]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Post("/first-responder-type", name="api_first_responder_type_add",  options={ "method_prefix" = false })
     *
     * @SWG\Post(
     *     path="/api/user/first-responder-type",
     *     tags={"First Responder Type"},
     *     summary="Authorises user via access token and create first responder type",
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
     *              @SWG\Property(
     *                  property="name",
     *                  type="string",
     *                  ),
     *              ),
     * )
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
     * @param FirstResponderTypeService $firstResponderTypeService
     * @param LoggerInterface $firstResponderApiLogger
     * @return JsonResponse
     */
    public function firstResponderTypeAddAction(
        Request $request,
        UtilService $utilService,
        FirstResponderTypeService $firstResponderTypeService,
        LoggerInterface $firstResponderApiLogger
    ) {
        try {
            $data = json_decode($request->getContent(), true);
            $name = isset($data['name']) ? $data['name'] : null;
            if (empty($name)) {
                return $utilService
                    ->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters missing.");
            }
            $firstResponderTypeService->addFirstResponderType($name);
            return $utilService->makeResponse(Response::HTTP_OK, "Item added successfully.");
        } catch (\Exception $exception) {
            $firstResponderApiLogger->error('[api_first_responder_type_add]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Put("/first-responder-type/{id}", name="api_first_responder_type_edit",  options={ "method_prefix" = false })
     *
     * @SWG\Put(
     *     path="/api/user/first-responder-type/{id}",
     *     tags={"First Responder Type"},
     *     summary="Authorises user via access token and update first responder type",
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
     *                  property="name",
     *                  type="string",
     *                  ),
     *              ),
     * )
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
     * @param FirstResponderTypeService $firstResponderTypeService
     * @param LoggerInterface $firstResponderApiLogger
     * @return JsonResponse
     */
    public function firstResponderTypeEditAction(
        $id,
        Request $request,
        UtilService $utilService,
        FirstResponderTypeService $firstResponderTypeService,
        LoggerInterface $firstResponderApiLogger
    ) {
        try {
            $typeObj = $firstResponderTypeService->getFirstResponderTypeById($id);
            if (!$typeObj instanceof FirstResponderType) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Item doesn't exist.");
            }
            $data = json_decode($request->getContent(), true);
            $name = isset($data['name']) ? $data['name'] : null;
            if (empty($name) || empty($id)) {
                return $utilService
                    ->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters missing.");
            }
            $firstResponderTypeService->updateFirstResponderTypeById((int)$id, $name);
            return $utilService->makeResponse(Response::HTTP_OK, "Item updated successfully.");
        } catch (\Exception $exception) {
            $firstResponderApiLogger->error('[api_first_responder_type_edit]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Delete("/first-responder-type/{id}", name="api_first_responder_type_delete",  options={ "method_prefix" = false })
     *
     * @SWG\Delete(
     *     path="/api/user/first-responder-type/{id}",
     *     tags={"First Responder Type"},
     *     summary="Authorises user via access token and delete first responder type",
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
     * @param FirstResponderTypeService $firstResponderTypeService
     * @param LoggerInterface $firstResponderApiLogger
     * @return JsonResponse
     */
    public function firstResponderTypeDeleteAction(
        $id,
        UtilService $utilService,
        FirstResponderTypeService $firstResponderTypeService,
        LoggerInterface $firstResponderApiLogger
    ) {
        try {
            $response = $firstResponderTypeService->deleteFirstResponderTypeById((int)$id);
            if ($response) {
                return $utilService->makeResponse(Response::HTTP_OK, "Item deleted successfully.");
            }

            return $utilService->makeResponse(Response::HTTP_METHOD_NOT_ALLOWED, "This type is linked with one of first responders.");
        } catch (\Exception $exception) {
            $firstResponderApiLogger->error('[api_first_responder_type_delete]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }
}