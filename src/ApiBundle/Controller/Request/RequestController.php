<?php

namespace App\ApiBundle\Controller\Request;

use App\ApiBundle\Service\RequestService;
use App\ApiBundle\Service\UtilService;
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
 * Class RequestController
 * @package App\ApiBundle\Controller\User
 */
class RequestController extends AbstractController
{
    /**
     * @Route(methods={"PUT"}, path="/request/{id}", name="update_request_api")
     *
     * @Operation(
     *     tags={"Request"},
     *     summary="Update request",
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
     * @param RequestService $requestService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function editRequestAction(
        $id,
        Request $request,
        RequestService $requestService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $data = json_decode($request->getContent(), true);
            if (empty($data['title']) && empty($data['description'])) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Missing required parameters.");
            }

            $result = $requestService->updateRequest($this->getUser(), (int) $id, $data);

            if (!is_array($result)) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $result);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "Request updated successfully.",
                $result
            );
        } catch (\Exception $exception) {
            $clientApiLogger->error('[update_request_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }

    /**
     * @Route(methods={"POST"}, path="/request", name="create_request_api")
     *
     * @Operation(
     *     tags={"Request"},
     *     summary="Create request",
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
     *                  property="title",
     *                  type="string",
     *                  ),
     *         @SWG\Property(
     *                  property="description",
     *                  type="string",
     *                  )
     *              ),
     *     )
     * )
     *
     * @param Request $request
     * @param RequestService $requestService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function createRequestAction(
        Request $request,
        RequestService $requestService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $data = json_decode($request->getContent(), true);

            if (empty($data['title'])) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Title is required.");
            }

            $request =  $requestService->createRequest($this->getUser(),  $data);

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "Request created successfully.",
                $request
            );
        } catch (Exception $exception) {
            $clientApiLogger->error('[create_request_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }

    /**
     * @Route(methods={"DELETE"}, path="/request/{id}", name="delete_request_api")
     *
     * @Operation(
     *     tags={"Request"},
     *     summary="Delete request",
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
     * @param RequestService $requestService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function deleteRequestAction(
        $id,
        RequestService $requestService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $result = $requestService->deleteRequest($this->getUser(), (int) $id);
            if ($result !== true) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $result);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "Request deleted successfully.",
                null
            );
        } catch (Exception $exception) {
            $clientApiLogger->error('[delete_request_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }

    /**
     * @Route(methods={"GET"}, path="/request", name="get_request_api")
     *
     * @Operation(
     *     tags={"Request"},
     *     summary="Get request",
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
     *      )
     * )
     *
     * @param Request $request
     * @param RequestService $requestService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function getRequestAction(
        Request $request,
        RequestService $requestService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $data = $request->query->all();
            $result = $requestService->getRequests($this->getUser(), $data);

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "",
                $result
            );
        } catch (Exception $exception) {
            $clientApiLogger->error('[get_request_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }
}
