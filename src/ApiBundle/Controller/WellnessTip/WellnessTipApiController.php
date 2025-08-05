<?php

namespace App\ApiBundle\Controller\WellnessTip;

use App\ApiBundle\Service\WellnessTipService;
use App\ApiBundle\Service\UtilService;
use App\Enum\WellnessTipEnum;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use Swagger\Annotations as SWG;

/**
 * Class WellnessTipApiController
 * @package App\ApiBundle\Controller\WellnessTip
 */
class WellnessTipApiController extends AbstractController
{

    /**
     * @Post("/wellnesstip", name="api_wellnesstip_create",  options={ "method_prefix" = false })
     *
     * @SWG\Post(
     *     path="/api/user/wellnesstip",
     *     tags={"WellnessTip"},
     *     summary="Authorises user via access token and create WellnessTip",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *
     *     @SWG\Parameter(
     *         ref="#parameters/authorization"
     *     ),
     *     @SWG\Parameter(
     *         name="heading",
     *         in="formData",
     *         type="string",
     *         required=true,
     *         description="Heading of Wellness Tip"
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="formData",
     *         type="string",
     *         required=true,
     *         description="description of Wellness Tip"
     *     ),
     *     @SWG\Parameter(
     *         name="icon",
     *         in="formData",
     *         type="integer",
     *         required=true,
     *         description="icon of Wellness Tip"
     *     ),
     *     @SWG\Parameter(
     *         name="image",
     *         in="formData",
     *         type="file",
     *         description="Image (png/jpeg)"
     *     ),
     *     @SWG\Parameter(
     *         name="media",
     *         in="formData",
     *         type="file",
     *         description="Audio or Video (mp3/mp4)"
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
     * @param LoggerInterface $wellnessTipApiLogger
     * @param WellnessTipService $wellnessTipService
     * @return JsonResponse
     */
    public function createWellnessTipAction(
        Request $request,
        UtilService $utilService,
        LoggerInterface $wellnessTipApiLogger,
        WellnessTipService $wellnessTipService
    ) {
        try {
            $data[WellnessTipEnum::PARM_HEADING] = $request->get((WellnessTipEnum::PARM_HEADING), true);
            $data[WellnessTipEnum::PARM_BODY] = $request->get((WellnessTipEnum::PARM_BODY), true);
            $data[WellnessTipEnum::PARM_ICON] = $request->get((WellnessTipEnum::PARM_ICON), true);
            $wellnessTipImage = $request->files->get(WellnessTipEnum::PARM_IMAGE);
            $wellnessTipMedia = $request->files->get(WellnessTipEnum::PARM_MEDIA);
            $validRequest = $utilService->checkRequiredFieldsByRequestedData($data, WellnessTipEnum::WELLNESSTIP_API_REQUIRED_FIELDS);

            if (!$validRequest) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, WellnessTipEnum::ERROR_MESSAGE_BAD_REQUEST);
            }

            $isValidImageResponse = !empty($wellnessTipImage) ? $utilService->isValidImage($wellnessTipImage) : true;
            if ($isValidImageResponse !== true) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $isValidImageResponse);
            }

            $isValidMediaResponse = !empty($wellnessTipMedia) ? $utilService->isValidMedia($wellnessTipMedia) : true;
            if ($isValidMediaResponse !== true) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $isValidMediaResponse);
            }

            $response = $wellnessTipService->createWellnessTip($this->getUser(), $data, $wellnessTipImage, $wellnessTipMedia);
            if ($response[WellnessTipEnum::STATUS] === false) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $response[WellnessTipEnum::MESSAGE]);
            }
            return $utilService->makeResponse(Response::HTTP_OK, $response[WellnessTipEnum::MESSAGE]);
        } catch (\Exception $exception) {
            $wellnessTipApiLogger->error('[api_create_WellnessTip]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, WellnessTipEnum::ERROR_MESSAGE_INTERNAL_SERVER);
        }
    }

    /**
     * @Get("/wellness-tip", name="api_wellness_tip_list",  options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/wellness-tip",
     *     tags={"WellnessTip"},
     *     summary="Authorises user via access token and list wellnessTip",
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
     *     @SWG\Parameter(
     *         name="range",
     *         in="query",
     *         type="integer",
     *         required=false,
     *         description="range"
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
     * @param LoggerInterface $wellnessTipApiLogger
     * @param WellnessTipService $wellnessTipService
     * @return JsonResponse
     */
    public function wellnessTipListAction(
        Request $request,
        UtilService $utilService,
        LoggerInterface $wellnessTipApiLogger,
        WellnessTipService $wellnessTipService
    ) {
        try {
            $data = $request->query->all();
            $wellnessTips = $wellnessTipService->getWellnessTip($data);
            return $utilService->makeResponse(Response::HTTP_OK, null, $wellnessTips);
        } catch (\Exception $exception) {
            $wellnessTipApiLogger->error('[api_wellness-tip_list]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, WellnessTipEnum::ERROR_MESSAGE_INTERNAL_SERVER);
        }
    }

    /**
     * @Delete("/wellness-tip/{id}", name="api_wellness_tip_delete",  options={ "method_prefix" = false })
     *
     * @SWG\Delete(
     *     path="/api/user/wellness-tip/{id}",
     *     tags={"WellnessTip"},
     *     summary="Authorises user via access token and delete wellnessTip",
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
     * @param WellnessTipService $wellnessTipService
     * @param LoggerInterface $wellnessTipApiLogger
     * @return JsonResponse
     */
    public function wellnessTipDeleteAction(
        $id,
        UtilService $utilService,
        WellnessTipService $wellnessTipService,
        LoggerInterface $wellnessTipApiLogger
    ) {
        try {
            $result = $wellnessTipService->deleteWellnessTipById($this->getUser(), (int)$id);
            if($result ===true) {
                return $utilService->makeResponse(Response::HTTP_OK, "WellnessTip deleted successfully.");
            }
            return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $result);
        } catch (\Exception $exception) {
            $wellnessTipApiLogger->error('[api_wellnessTip_delete]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, WellnessTipEnum::ERROR_MESSAGE_INTERNAL_SERVER);
        }
    }

    /**
     * @Post("/wellnesstip/{id}", name="api_wellnesstip_edit",  options={ "method_prefix" = false })
     *
     * @SWG\Post(
     *     path="/api/user/wellnesstip/{id}",
     *     tags={"WellnessTip"},
     *     summary="Authorises user via access token and create WellnessTip",
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
     *         description="id of Wellness Tip"
     *     ),
     *     @SWG\Parameter(
     *         name="heading",
     *         in="formData",
     *         type="string",
     *         required=true,
     *         description="Heading of Wellness Tip"
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="formData",
     *         type="string",
     *         required=true,
     *         description="Detail of Wellness Tip"
     *     ),
     *     @SWG\Parameter(
     *         name="icon",
     *         in="formData",
     *         type="integer",
     *         required=true,
     *         description="icon of Wellness Tip"
     *     ),
     *     @SWG\Parameter(
     *         name="image",
     *         in="formData",
     *         type="file",
     *         description="Image"
     *     ),
     *     @SWG\Parameter(
     *         name="deleteImage",
     *         in="formData",
     *         type="integer",
     *         description="Delete exsiting Image"
     *     ),
     *     @SWG\Parameter(
     *         name="media",
     *         in="formData",
     *         type="file",
     *         description="Audio or Video"
     *     ),
     *     @SWG\Parameter(
     *         name="deleteMedia",
     *         in="formData",
     *         type="integer",
     *         description="delete exising Media"
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
     * @param LoggerInterface $wellnessTipApiLogger
     * @param WellnessTipService $wellnessTipService
     * @return JsonResponse
     */
    public function editWellnessTipAction(
        $id,
        Request $request,
        UtilService $utilService,
        LoggerInterface $wellnessTipApiLogger,
        WellnessTipService $wellnessTipService
    ) {
        try {
            $data[WellnessTipEnum::PARM_HEADING] = $request->get((WellnessTipEnum::PARM_HEADING), true);
            $data[WellnessTipEnum::PARM_BODY] = $request->get((WellnessTipEnum::PARM_BODY), true);
            $data[WellnessTipEnum::PARM_ICON] = $request->get((WellnessTipEnum::PARM_ICON), true);
            $data[WellnessTipEnum::PARM_DELETE_IMAGE] = $request->get((WellnessTipEnum::PARM_DELETE_IMAGE), true);
            $data[WellnessTipEnum::PARM_DELETE_MEDIA] = $request->get((WellnessTipEnum::PARM_DELETE_MEDIA), true);
            $wellnessTipImage = $request->files->get(WellnessTipEnum::PARM_IMAGE);
            $wellnessTipMedia = $request->files->get(WellnessTipEnum::PARM_MEDIA);
            $validRequest = $utilService->checkRequiredFieldsByRequestedData($data, WellnessTipEnum::WELLNESSTIP_API_REQUIRED_FIELDS);

            if (!$validRequest) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, WellnessTipEnum::ERROR_MESSAGE_BAD_REQUEST);
            }

            $isValidImageResponse = !empty($wellnessTipImage)?$utilService->isValidImage($wellnessTipImage):true;
            if($isValidImageResponse !== true) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $isValidImageResponse);
            }

            $isValidMediaResponse = !empty($wellnessTipMedia)?$utilService->isValidMedia($wellnessTipMedia):true;
            if($isValidMediaResponse !== true) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $isValidMediaResponse);
            }

            $response = $wellnessTipService->updateWellnessTip($this->getUser(), $id, $data, $wellnessTipImage, $wellnessTipMedia);
            if ($response === true) {
                return $utilService->makeResponse(Response::HTTP_OK, 'WellnessTip Updated successfully');
            }
            return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $response);
        } catch (\Exception $exception) {
            $wellnessTipApiLogger->error('[api_update_WellnessTip]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, WellnessTipEnum::ERROR_MESSAGE_INTERNAL_SERVER);
        }
    }
}
