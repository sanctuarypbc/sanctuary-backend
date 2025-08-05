<?php

namespace App\ApiBundle\Controller\Client;

use App\ApiBundle\Service\ClientEmploymentStatusService;
use App\ApiBundle\Service\UtilService;
use App\Entity\ClientEmploymentStatus;
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
 * Class ClientEmploymentStatusApiController
 * @package App\ApiBundle\Controller
 */
class ClientEmploymentStatusApiController extends AbstractController
{
    /**
     * @Get("/client-employment-status", name="api_client_employment_status_list",  options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/client-employment-status",
     *     tags={"Client Employment Status"},
     *     summary="Authorises user via access token and list client employment status",
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
     * @param ClientEmploymentStatusService $clientEmploymentStatusService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function clientEmploymentStatusListAction(
        Request $request,
        UtilService $utilService,
        ClientEmploymentStatusService $clientEmploymentStatusService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $clientEmploymentStatusId = $request->get('id');
            $name = $request->get('name');
            $data = $clientEmploymentStatusService->getClientEmploymentStatusList($clientEmploymentStatusId, $name);
            return $utilService->makeResponse(Response::HTTP_OK, null, $data);
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_client_employment_status_list]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Post("/client-employment-status", name="api_client_employment_status_add",  options={ "method_prefix" = false })
     *
     * @SWG\Post(
     *     path="/api/user/client-employment-status",
     *     tags={"Client Employment Status"},
     *     summary="Authorises user via access token and create client employment status",
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
     * @param ClientEmploymentStatusService $clientEmploymentStatusService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function clientEmploymentStatusAddAction(
        Request $request,
        UtilService $utilService,
        ClientEmploymentStatusService $clientEmploymentStatusService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $data = json_decode($request->getContent(), true);
            $name = isset($data['name']) ? $data['name'] : null;
            if (empty($name)) {
                return $utilService
                    ->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters missing.");
            }
            $clientEmploymentStatusService->addClientEmploymentStatus($name);
            return $utilService->makeResponse(Response::HTTP_OK, "Item added successfully.");
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_client_employment_status_add]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Put("/client-employment-status/{id}", name="api_client_employment_status_edit",  options={ "method_prefix" = false })
     *
     * @SWG\Put(
     *     path="/api/user/client-employment-status/{id}",
     *     tags={"Client Employment Status"},
     *     summary="Authorises user via access token and update client employment status",
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
     * @param ClientEmploymentStatusService $clientEmploymentStatusService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function clientEmploymentStatusEditAction(
        $id,
        Request $request,
        UtilService $utilService,
        ClientEmploymentStatusService $clientEmploymentStatusService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $typeObj = $clientEmploymentStatusService->getClientEmploymentStatusById($id);
            if (!$typeObj instanceof ClientEmploymentStatus) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Item doesn't exist.");
            }
            $data = json_decode($request->getContent(), true);
            $name = isset($data['name']) ? $data['name'] : null;
            if (empty($name) || empty($id)) {
                return $utilService
                    ->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters missing.");
            }
            $clientEmploymentStatusService->updateClientEmploymentStatusById((int)$id, $name);
            return $utilService->makeResponse(Response::HTTP_OK, "Item updated successfully.");
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_client_employment_status_edit]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Delete("/client-employment-status/{id}", name="api_client_employment_status_delete",  options={ "method_prefix" = false })
     *
     * @SWG\Delete(
     *     path="/api/user/client-employment-status/{id}",
     *     tags={"Client Employment Status"},
     *     summary="Authorises user via access token and delete client employment status",
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
     * @param ClientEmploymentStatusService $clientEmploymentStatusService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function clientEmploymentStatusDeleteAction(
        $id,
        UtilService $utilService,
        ClientEmploymentStatusService $clientEmploymentStatusService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $response = $clientEmploymentStatusService->deleteClientEmploymentStatusById((int)$id);
            if ($response) {
                return $utilService->makeResponse(Response::HTTP_OK, "Item deleted successfully.");
            }

            return $utilService->makeResponse(Response::HTTP_METHOD_NOT_ALLOWED, "This status is linked with one of clients.");
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_client_employment_status_delete]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }
}