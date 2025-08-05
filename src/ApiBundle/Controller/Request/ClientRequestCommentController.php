<?php

namespace App\ApiBundle\Controller\Request;

use App\ApiBundle\Service\ClientRequestCommentService;
use App\ApiBundle\Service\UtilService;
use App\Entity\ClientRequestComment;
use App\Enum\CommonEnum;
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
 * Class ClientRequestCommentController
 * @package App\ApiBundle\Controller\Request
 */
class ClientRequestCommentController extends AbstractController
{
    /**
     * @Route(methods={"PUT"}, path="/client-request-comment/{id}", name="update_client_request_comment_api")
     *
     * @Operation(
     *     tags={"Request"},
     *     summary="Update client request comment",
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
     *                  property="text",
     *                  type="string",
     *                  )
     *              ),
     *     )
     * )
     *
     * @param $id
     * @param Request $request
     * @param ClientRequestCommentService $clientRequestCommentService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function editClientRequestCommentAction(
        $id,
        Request $request,
        ClientRequestCommentService $clientRequestCommentService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $data = json_decode($request->getContent(), true);
            if (empty($data['text'])) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Comment text is required.");
            }

            $result = $clientRequestCommentService->updateClientRequestComment($this->getUser(), (int) $id, $data);

            if ($result !== true) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $result);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "Comment updated successfully."
            );
        } catch (\Exception $exception) {
            $clientApiLogger->error('[update_client_request_comment_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }

    /**
     * @Route(methods={"POST"}, path="/client-request-comment", name="create_client_request_comment_api")
     *
     * @Operation(
     *     tags={"Request"},
     *     summary="Create client request comment",
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
     *                  property="text",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="client_request_id",
     *                  type="integer",
     *                  )
     *              ),
     *     )
     * )
     *
     * @param Request $request
     * @param ClientRequestCommentService $clientRequestCommentService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function createClientRequestCommentAction(
        Request $request,
        ClientRequestCommentService $clientRequestCommentService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $data = json_decode($request->getContent(), true);

            if (empty($data['text']) || empty($data['client_request_id'])) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters missing.");
            }

            $clientRequestComment = $clientRequestCommentService->createClientRequestComment($this->getUser(),  $data);
            if (!$clientRequestComment instanceof ClientRequestComment) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $clientRequestComment);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "Comment created successfully.",
                $clientRequestComment->toArray()
            );
        } catch (Exception $exception) {
            $clientApiLogger->error('[create_client_request_comment_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }

    /**
     * @Route(methods={"DELETE"}, path="/client-request-comment/{id}", name="delete_client_request_comment_api")
     *
     * @Operation(
     *     tags={"Request"},
     *     summary="Delete client request comment",
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
     * @param ClientRequestCommentService $clientRequestCommentService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function deleteRequestAction(
        $id,
        ClientRequestCommentService $clientRequestCommentService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $result = $clientRequestCommentService->deleteClientRequestComment($this->getUser(), (int) $id);
            if ($result !== true) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $result);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "Comment deleted successfully.",
                null
            );
        } catch (Exception $exception) {
            $clientApiLogger->error('[delete_client_request_comment_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }

    /**
     * @Route(methods={"GET"}, path="/client-request-comment", name="get_client_request_comment_api")
     *
     * @Operation(
     *     tags={"Request"},
     *     summary="Get client request comment",
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
     *         name="client_request_id",
     *         in="query",
     *         type="integer",
     *         required=true,
     *         description="Client request id"
     *      ),
     *      @SWG\Parameter(
     *         name="id",
     *         in="query",
     *         type="integer",
     *         required=false,
     *         description="Id"
     *      ),
     *      @SWG\Parameter(
     *          name="page",
     *          in="query",
     *          type="integer",
     *          required=false,
     *          description="Page number"
     *      ),
     *      @SWG\Parameter(
     *          name="per_page",
     *          in="query",
     *          type="integer",
     *          required=false,
     *          description="Records per page. Default 50"
     *      )
     * )
     *
     * @param Request $request
     * @param ClientRequestCommentService $clientRequestCommentService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function getRequestAction(
        Request $request,
        ClientRequestCommentService $clientRequestCommentService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $data = $request->query->all();
            if (empty($data['client_request_id'])) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Client request id is required.");
            }

            if (!empty($data['per_page']) && $data['per_page'] > CommonEnum::PER_PAGE_MAX) {
                return $utilService->makeResponse(
                    Response::HTTP_BAD_REQUEST,
                    "per_page page can not exceed the limit " . CommonEnum::PER_PAGE_MAX . "."
                );
            }

            $result = $clientRequestCommentService->getClientRequestComments($this->getUser(), $data);
            if (!is_array($result)) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $result);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "",
                $result
            );
        } catch (Exception $exception) {
            $clientApiLogger->error('[get_client_request_comment_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }
}
