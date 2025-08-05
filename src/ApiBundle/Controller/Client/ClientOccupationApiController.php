<?php

namespace App\ApiBundle\Controller\Client;

use App\ApiBundle\Service\ClientOccupationService;
use App\ApiBundle\Service\UtilService;
use App\Entity\ClientOccupation;
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
 * Class ClientOccupationApiController
 * @package App\ApiBundle\Controller\Client
 */
class ClientOccupationApiController extends AbstractController
{
    /**
     * @Get("/client-occupation", name="api_client_occupation_list",  options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/client-occupation",
     *     tags={"Client Occupation"},
     *     summary="Authorises user via access token and list client occupation",
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
     * @param ClientOccupationService $clientOccupationService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function clientOccupationListAction(
        Request $request,
        UtilService $utilService,
        ClientOccupationService $clientOccupationService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $clientOccupationId = $request->get('id');
            $name = $request->get('name');
            $data = $clientOccupationService->getClientOccupationList($clientOccupationId, $name);
            return $utilService->makeResponse(Response::HTTP_OK, null, $data);
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_client_occupation_list]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Post("/client-occupation", name="api_client_occupation_add",  options={ "method_prefix" = false })
     *
     * @SWG\Post(
     *     path="/api/user/client-occupation",
     *     tags={"Client Occupation"},
     *     summary="Authorises user via access token and create client occupation",
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
     * @param ClientOccupationService $clientOccupationService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function clientOccupationAddAction(
        Request $request,
        UtilService $utilService,
        ClientOccupationService $clientOccupationService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $data = json_decode($request->getContent(), true);
            $name = isset($data['name']) ? $data['name'] : null;
            if (empty($name)) {
                return $utilService
                    ->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters missing.");
            }
            $clientOccupationService->addClientOccupation($name);
            return $utilService->makeResponse(Response::HTTP_OK, "Item added successfully.");
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_client_occupation_add]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Put("/client-occupation/{id}", name="api_client_occupation_edit",  options={ "method_prefix" = false })
     *
     * @SWG\Put(
     *     path="/api/user/client-occupation/{id}",
     *     tags={"Client Occupation"},
     *     summary="Authorises user via access token and update client occupation",
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
     * @param ClientOccupationService $clientOccupationService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function clientOccupationEditAction(
        $id,
        Request $request,
        UtilService $utilService,
        ClientOccupationService $clientOccupationService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $typeObj = $clientOccupationService->getClientOccupationById($id);
            if (!$typeObj instanceof ClientOccupation) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Item doesn't exist.");
            }
            $data = json_decode($request->getContent(), true);
            $name = isset($data['name']) ? $data['name'] : null;
            if (empty($name) || empty($id)) {
                return $utilService
                    ->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters missing.");
            }
            $clientOccupationService->updateClientOccupationById((int)$id, $name);
            return $utilService->makeResponse(Response::HTTP_OK, "Item updated successfully.");
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_client_occupation_edit]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Delete("/client-occupation/{id}", name="api_client_occupation_delete",  options={ "method_prefix" = false })
     *
     * @SWG\Delete(
     *     path="/api/user/client-occupation/{id}",
     *     tags={"Client Occupation"},
     *     summary="Authorises user via access token and delete client occupation",
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
     * @param ClientOccupationService $clientOccupationService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function clientOccupationDeleteAction(
        $id,
        UtilService $utilService,
        ClientOccupationService $clientOccupationService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $clientOccupationService->deleteClientOccupationById((int)$id);
            return $utilService->makeResponse(Response::HTTP_OK, "Item deleted successfully.");
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_client_occupation_delete]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }
}