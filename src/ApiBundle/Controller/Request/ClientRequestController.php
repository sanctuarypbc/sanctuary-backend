<?php

namespace App\ApiBundle\Controller\Request;

use App\ApiBundle\Service\ClientRequestService;
use App\ApiBundle\Service\UtilService;
use App\Entity\ClientRequest;
use App\Enum\CommonEnum;
use App\Enum\UserEnum;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Psr\Log\LoggerInterface;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;

/**
 * Class ClientRequestController
 * @package App\ApiBundle\Controller\Request
 */
class ClientRequestController extends AbstractController
{
    /**
     * @Route(methods={"PUT"}, path="/client-request/{id}", name="update_client_request_api")
     *
     * @Operation(
     *     tags={"Client Request"},
     *     summary="Update client request",
     *     @SWG\Parameter(
     *       type="string",
     *       name="Authorization",
     *       in="header",
     *       required=true,
     *       description="Bearer your_token"
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *              type="object",
     *              example={"code": 200, "message" : "message", "status":"success"}
     *          )
     *      ),
     *      @SWG\Response(
     *          response=400,
     *          description="Missing some required params"
     *      ),
     *      @SWG\Response(
     *          response=403,
     *          description="Invalid Token provided"
     *      ),
     *      @SWG\Response(
     *          response=500,
     *          description="Some server error"
     *      ),
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
     *                  property="status",
     *                  type="integer",
     *                  ),
     *             @SWG\Property(
     *                  property="title",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="description",
     *                  type="string",
     *                  )
     *              ),
     *     )
     * )
     *
     * @param $id
     * @param Request $request
     * @param ClientRequestService $clientRequestService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function editClientRequestAction(
        $id,
        Request $request,
        ClientRequestService $clientRequestService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $data = json_decode($request->getContent(), true);
            $result = $clientRequestService->updateClientRequest((int) $id, $data);

            if ($result !== true) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $result);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "CLinet request updated successfully."
            );
        } catch (\Exception $exception) {
            $clientApiLogger->error('[update_client_request_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }

    /**
     * @Route(methods={"POST"}, path="/client-request", name="create_client_request_api")
     *
     * @Operation(
     *     tags={"Client Request"},
     *     summary="Create client request",
     *     @SWG\Parameter(
     *       type="string",
     *       name="Authorization",
     *       in="header",
     *       required=true,
     *       description="Bearer your_token"
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *              type="object",
     *              example={"code": 200, "data": "{'token' : 'token here', 'id' : 'id here'}", "message" : "message", "status":"success"}
     *          )
     *      ),
     *      @SWG\Response(
     *          response=400,
     *          description="Missing some required params"
     *      ),
     *      @SWG\Response(
     *          response=403,
     *          description="Invalid Token provided"
     *      ),
     *      @SWG\Response(
     *          response=500,
     *          description="Some server error"
     *      ),
     *      @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                  property="request_id",
     *                  type="integer",
     *                  )
     *              ),
     *     )
     * )
     *
     * @param Request $request
     * @param ClientRequestService $clientRequestService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function createClientRequestAction(
        Request $request,
        ClientRequestService $clientRequestService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            if (!in_array(UserEnum::ROLE_CLIENT, $this->getUser()->getRoles())) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "You don't have rights to perform this action.");
            }

            $data = json_decode($request->getContent(), true);
            if (empty($data['request_id'])) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Request id is required.");
            }

            $response = $clientRequestService->createClientRequest($this->getUser(), $data);
            if (!$response instanceof ClientRequest) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $response);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "Client request created successfully.",
                ['id' => $response->getId()]
            );
        } catch (Exception $exception) {
            $clientApiLogger->error('[create_request_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }

    /**
     * @Route(methods={"DELETE"}, path="/client-request/{id}", name="delete_client_request_api")
     *
     * @Operation(
     *     tags={"Client Request"},
     *     summary="Delete client request",
     *     @SWG\Parameter(
     *       type="string",
     *       name="Authorization",
     *       in="header",
     *       required=true,
     *       description="Bearer your_token"
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *              type="object",
     *              example={"code": 200, "data": "{'token' : 'token here', 'id' : 'id here'}", "message" : "message", "status":"success"}
     *          )
     *      ),
     *      @SWG\Response(
     *          response=400,
     *          description="Missing some required params"
     *      ),
     *      @SWG\Response(
     *          response=403,
     *          description="Invalid Token provided"
     *      ),
     *      @SWG\Response(
     *          response=500,
     *          description="Some server error"
     *      ),
     *      @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer",
     *         required=true,
     *         description="Id"
     *      ),
     * )
     *
     * @param $id
     * @param ClientRequestService $clientRequestService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function deleteClientRequestAction(
        $id,
        ClientRequestService $clientRequestService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            if (!in_array(UserEnum::ROLE_CLIENT, $this->getUser()->getRoles())) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "You don't have rights to perform this action.");
            }

            $result = $clientRequestService->deleteClientRequest($this->getUser(), (int) $id);
            if ($result !== true) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $result);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "CLinet request deleted successfully.",
                null
            );
        } catch (Exception $exception) {
            $clientApiLogger->error('[delete_client_request_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }

    /**
     * @Route(methods={"GET"}, path="/client-request", name="get_client_request_api")
     *
     * @Operation(
     *     tags={"Client Request"},
     *     summary="Get client request",
     *     @SWG\Parameter(
     *       type="string",
     *       name="Authorization",
     *       in="header",
     *       required=true,
     *       description="Bearer your_token"
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *              type="object",
     *              example={"code": 200, "data": "{'token' : 'token here', 'id' : 'id here'}", "message" : "message", "status":"success"}
     *          )
     *      ),
     *      @SWG\Response(
     *          response=400,
     *          description="Missing some required params"
     *      ),
     *      @SWG\Response(
     *          response=403,
     *          description="Invalid Token provided"
     *      ),
     *      @SWG\Response(
     *          response=500,
     *          description="Some server error"
     *      ),
     *      @SWG\Parameter(
     *         name="id",
     *         in="query",
     *         type="integer",
     *         required=false,
     *         description="Id"
     *      ),
     *      @SWG\Parameter(
     *         name="client_id",
     *         in="query",
     *         type="integer",
     *         required=false,
     *         description="Client id"
     *      )
     * )
     *
     * @param Request $request
     * @param ClientRequestService $clientRequestService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function getClientRequestAction(
        Request $request,
        ClientRequestService $clientRequestService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $data = $request->query->all();
            $result = $clientRequestService->getClientRequests($this->getUser(), $data);
            if (!is_array($result)) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $result);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "",
                $result
            );
        } catch (Exception $exception) {
            $clientApiLogger->error('[get_client_request_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }
}
