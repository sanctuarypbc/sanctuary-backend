<?php

namespace App\ApiBundle\Controller\Newsfeed;

use App\ApiBundle\Service\NewsfeedService;
use App\ApiBundle\Service\UtilService;
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
 * Class NewsfeedApiController
 * @package App\ApiBundle\Controller\Newsfeed
 */
class NewsfeedApiController extends AbstractController
{
    /**
     * @Route(methods={"POST"}, path="/newsfeed/{id}", name="update_newsfeed_api")
     *
     * @Operation(
     *     tags={"Newsfeed"},
     *     summary="Update newsfeed",
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
     *         name="request",
     *         in="formData",
     *         type="string",
     *         required=false,
     *         description="Json encoded request"
     *     ),
     *      @SWG\Parameter(
     *         name="files[]",
     *         in="formData",
     *         type="file",
     *         required=false,
     *         description="Newsfeed files",
     *      )
     * )
     *
     * @param $id
     * @param Request $request
     * @param NewsfeedService $newsfeedService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function editNewsfeedAction(
        $id,
        Request $request,
        NewsfeedService $newsfeedService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        if (!$this->isGranted(UserEnum::ROLE_SUPER_ADMIN)) {
            return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "You don't have rights to perform this action.");
        }

        try {
            $data = json_decode($request->get('request'), true);
            $files = $request->files->get('files', null);

            if (empty($data) && empty($files)) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters missing.");
            }

            $result = $newsfeedService->updateNewsfeed((int)$id, $data, $files);

            if ($result !== true) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $result);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "Newsfeed updated successfully."
            );
        } catch (\Exception $exception) {
            $clientApiLogger->error('[update_newsfeed_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }

    /**
     * @Route(methods={"POST"}, path="/newsfeed", name="create_newsfeed_api")
     *
     * @Operation(
     *     tags={"Newsfeed"},
     *     summary="Create newsfeed",
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
     *     @SWG\Parameter(
     *         name="request",
     *         in="formData",
     *         type="string",
     *         required=false,
     *         description="Json encoded request"
     *     ),
     *      @SWG\Parameter(
     *         name="files[]",
     *         in="formData",
     *         type="file",
     *         required=false,
     *         description="Newsfeed files",
     *      )
     * )
     *
     * @param Request $request
     * @param NewsfeedService $newsfeedService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function createNewsfeedAction(
        Request $request,
        NewsfeedService $newsfeedService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        if (!$this->isGranted(UserEnum::ROLE_SUPER_ADMIN)) {
            return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "You don't have rights to perform this action.");
        }

        try {
            $data = json_decode($request->get('request'), true);
            $files = $request->files->get('files', null);

            if (empty($data) && empty($files)) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters missing.");
            }

            $response = $newsfeedService->createNewsfeed($data, $files);
            if ($response !== true) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $response);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "Newsfeed created successfully."
            );
        } catch (Exception $exception) {
            $clientApiLogger->error('[create_newsfeed_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }

    /**
     * @Route(methods={"DELETE"}, path="/newsfeed/{id}", name="delete_newsfeed_api")
     *
     * @Operation(
     *     tags={"Newsfeed"},
     *     summary="Delete newsfeed",
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
     * @param NewsfeedService $newsfeedService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function deleteNewsfeedAction(
        $id,
        NewsfeedService $newsfeedService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        if (!$this->isGranted(UserEnum::ROLE_SUPER_ADMIN)) {
            return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "You don't have rights to perform this action.");
        }

        try {
            $result = $newsfeedService->deleteNewsfeed((int) $id);
            if ($result !== true) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $result);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "Newsfeed deleted successfully.",
                null
            );
        } catch (Exception $exception) {
            $clientApiLogger->error('[delete_newsfeed_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }
    
    /**
     * @Route(methods={"DELETE"}, path="/newsfeed-file/{id}", name="delete_newsfeed_file_api")
     *
     * @Operation(
     *     tags={"Newsfeed"},
     *     summary="Delete newsfeed file",
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
     * @param NewsfeedService $newsfeedService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function deleteNewsfeedFileAction(
        $id,
        NewsfeedService $newsfeedService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        if (!$this->isGranted(UserEnum::ROLE_SUPER_ADMIN)) {
            return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "You don't have rights to perform this action.");
        }

        try {
            $result = $newsfeedService->deleteNewsfeedFile((int) $id);
            if ($result !== true) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $result);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "Newsfeed file deleted successfully.",
                null
            );
        } catch (Exception $exception) {
            $clientApiLogger->error('[delete_newsfeed_file_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }

    /**
     * @Route(methods={"GET"}, path="/newsfeed", name="get_newsfeed_api")
     *
     * @Operation(
     *     tags={"Newsfeed"},
     *     summary="Get newsfeed",
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
     * @param NewsfeedService $newsfeedService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function getNewsfeedAction(
        Request $request,
        NewsfeedService $newsfeedService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $data = $request->query->all();
            $result = $newsfeedService->getNewsfeeds($this->getUser(), $data);

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "",
                $result
            );
        } catch (Exception $exception) {
            $clientApiLogger->error('[get_newsfeed_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }
}
