<?php

namespace App\ApiBundle\Controller\Client;

use App\ApiBundle\Service\ClientTypeService;
use App\ApiBundle\Service\UtilService;
use App\Entity\ClientType;
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
 * Class ClientTypeApiController
 * @package App\ApiBundle\Controller
 */
class ClientTypeApiController extends AbstractController
{
    /**
     * @Get("/client-type", name="api_client_type_list",  options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/client-type",
     *     tags={"Client Type"},
     *     summary="Authorises user via access token and list client type",
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
     * @param ClientTypeService $clientTypeService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function clientTypeListAction(
        Request $request,
        UtilService $utilService,
        ClientTypeService $clientTypeService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $clientTypeId = $request->get('id');
            $name = $request->get('name');
            $data = $clientTypeService->getClientTypeList($clientTypeId, $name);
            return $utilService->makeResponse(Response::HTTP_OK, null, $data);
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_client_type_list]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Post("/client-type", name="api_client_type_add",  options={ "method_prefix" = false })
     *
     * @SWG\Post(
     *     path="/api/user/client-type",
     *     tags={"Client Type"},
     *     summary="Authorises user via access token and create client type",
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
     * @param ClientTypeService $clientTypeService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function clientTypeAddAction(
        Request $request,
        UtilService $utilService,
        ClientTypeService $clientTypeService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $data = json_decode($request->getContent(), true);
            $name = isset($data['name']) ? $data['name'] : null;
            if (empty($name)) {
                return $utilService
                    ->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters missing.");
            }
            $clientTypeService->addClientType($name);
            return $utilService->makeResponse(Response::HTTP_OK, "Item added successfully.");
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_client_type_add]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Put("/client-type/{id}", name="api_client_type_edit",  options={ "method_prefix" = false })
     *
     * @SWG\Put(
     *     path="/api/user/client-type/{id}",
     *     tags={"Client Type"},
     *     summary="Authorises user via access token and update client type",
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
     * @param ClientTypeService $clientTypeService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function clientTypeEditAction(
        $id,
        Request $request,
        UtilService $utilService,
        ClientTypeService $clientTypeService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $typeObj = $clientTypeService->getClientTypeById($id);
            if (!$typeObj instanceof ClientType) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Item doesn't exist.");
            }
            $data = json_decode($request->getContent(), true);
            $name = isset($data['name']) ? $data['name'] : null;
            if (empty($name) || empty($id)) {
                return $utilService
                    ->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters missing.");
            }
            $clientTypeService->updateClientTypeById((int)$id, $name);
            return $utilService->makeResponse(Response::HTTP_OK, "Item updated successfully.");
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_client_type_edit]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Delete("/client-type/{id}", name="api_client_type_delete",  options={ "method_prefix" = false })
     *
     * @SWG\Delete(
     *     path="/api/user/client-type/{id}",
     *     tags={"Client Type"},
     *     summary="Authorises user via access token and delete client type",
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
     * @param ClientTypeService $clientTypeService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function clientTypeDeleteAction(
        $id,
        UtilService $utilService,
        ClientTypeService $clientTypeService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $response = $clientTypeService->deleteClientTypeById((int)$id);
            if ($response) {
                return $utilService->makeResponse(Response::HTTP_OK, "Item deleted successfully.");
            }

            return $utilService->makeResponse(Response::HTTP_METHOD_NOT_ALLOWED, "This type is linked with one of clients.");
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_client_type_delete]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }
}