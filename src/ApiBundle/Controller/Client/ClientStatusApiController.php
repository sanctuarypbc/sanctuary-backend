<?php

namespace App\ApiBundle\Controller\Client;

use App\ApiBundle\Service\ClientStatusService;
use App\ApiBundle\Service\UtilService;
use App\Entity\ClientStatus;
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
 * Class ClientStatusApiController
 * @package App\ApiBundle\Controller\Client
 */
class ClientStatusApiController extends AbstractController
{
    /**
     * @Get("/client-status", name="api_client_status_list",  options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/client-status",
     *     tags={"Client Status"},
     *     summary="Authorises user via access token and list client status",
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
     * @param ClientStatusService $clientStatusService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function clientStatusListAction(
        Request $request,
        UtilService $utilService,
        ClientStatusService $clientStatusService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $clientStatusId = $request->get('id');
            $name = $request->get('name');
            $data = $clientStatusService->getClientStatusList($clientStatusId, $name);
            return $utilService->makeResponse(Response::HTTP_OK, null, $data);
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_client_status_list]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Post("/client-status", name="api_client_status_add",  options={ "method_prefix" = false })
     *
     * @SWG\Post(
     *     path="/api/user/client-status",
     *     tags={"Client Status"},
     *     summary="Authorises user via access token and create client status",
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
     * @param ClientStatusService $clientStatusService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function clientStatusAddAction(
        Request $request,
        UtilService $utilService,
        ClientStatusService $clientStatusService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $data = json_decode($request->getContent(), true);
            $name = isset($data['name']) ? $data['name'] : null;
            if (empty($name)) {
                return $utilService
                    ->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters missing.");
            }
            $clientStatusService->addClientStatus($name);
            return $utilService->makeResponse(Response::HTTP_OK, "Item added successfully.");
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_client_status_add]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Put("/client-status/{id}", name="api_client_status_edit",  options={ "method_prefix" = false })
     *
     * @SWG\Put(
     *     path="/api/user/client-status/{id}",
     *     tags={"Client Status"},
     *     summary="Authorises user via access token and update client status",
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
     * @param ClientStatusService $clientStatusService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function clientStatusEditAction(
        $id,
        Request $request,
        UtilService $utilService,
        ClientStatusService $clientStatusService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $typeObj = $clientStatusService->getClientStatusById($id);
            if (!$typeObj instanceof ClientStatus) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Item doesn't exist.");
            }
            $data = json_decode($request->getContent(), true);
            $name = isset($data['name']) ? $data['name'] : null;
            if (empty($name) || empty($id)) {
                return $utilService
                    ->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters missing.");
            }
            $clientStatusService->updateClientStatusById((int)$id, $name);
            return $utilService->makeResponse(Response::HTTP_OK, "Item updated successfully.");
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_client_status_edit]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Delete("/client-status/{id}", name="api_client_status_delete",  options={ "method_prefix" = false })
     *
     * @SWG\Delete(
     *     path="/api/user/client-status/{id}",
     *     tags={"Client Status"},
     *     summary="Authorises user via access token and delete client status",
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
     * @param ClientStatusService $clientStatusService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function clientStatusDeleteAction(
        $id,
        UtilService $utilService,
        ClientStatusService $clientStatusService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $clientStatusService->deleteClientStatusById((int)$id);
            return $utilService->makeResponse(Response::HTTP_OK, "Item deleted successfully.");
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_client_status_delete]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }
}