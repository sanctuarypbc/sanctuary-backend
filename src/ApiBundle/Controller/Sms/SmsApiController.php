<?php

namespace App\ApiBundle\Controller\Sms;

use App\ApiBundle\Service\SmsService;
use App\ApiBundle\Service\UtilService;
use App\Entity\Sms;
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
 * Class SmsApiController
 * @package App\ApiBundle\Controller\Sms
 */
class SmsApiController extends AbstractController
{
    /**
     * @Route(methods={"POST"}, path="/sms/{id}", name="update_sms_api")
     *
     * @Operation(
     *     tags={"Sms"},
     *     summary="Update sms",
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
     *     )
     * )
     *
     * @param $id
     * @param Request $request
     * @param SmsService $smsService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function editSmsAction(
        $id,
        Request $request,
        SmsService $smsService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        if (!$this->isGranted(UserEnum::ROLE_SUPER_ADMIN)) {
            return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "You don't have rights to perform this action.");
        }

        try {
            $data = json_decode($request->get('request'), true);

            if (empty($data)) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters missing.");
            }

            $result = $smsService->updateSmsInfo((int)$id, $data);

            if ($result !== true) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $result);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "Sms contact updated successfully."
            );
        } catch (\Exception $exception) {
            $clientApiLogger->error('[update_sms_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }

    /**
     * @Route(methods={"POST"}, path="/sms", name="create_sms_api")
     *
     * @Operation(
     *     tags={"Sms"},
     *     summary="Create sms",
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
     *     )
     * )
     *
     * @param Request $request
     * @param SmsService $smsService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function createSmsAction(
        Request $request,
        SmsService $smsService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        if (!$this->isGranted(UserEnum::ROLE_SUPER_ADMIN)) {
            return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "You don't have rights to perform this action.");
        }

        try {
            $data = json_decode($request->get('request'), true);

            if (empty($data)) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters missing.");
            }

            $alreadyExist = $smsService->getSmsByPhone(trim($data['phone_number']));
            if ($alreadyExist instanceof Sms) {
                return $utilService->makeResponse(Response::HTTP_CONFLICT, "Phone number already exist.");
            }

            $response = $smsService->createSmsByRequestData($data);
            if ($response !== true) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, 'Invalid phone number provided.');
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "Sms contact created successfully."
            );
        } catch (Exception $exception) {
            $clientApiLogger->error('[create_sms_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }

    /**
     * @Route(methods={"DELETE"}, path="/sms/{id}", name="delete_sms_api")
     *
     * @Operation(
     *     tags={"Sms"},
     *     summary="Delete sms",
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
     * @param SmsService $smsService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function deleteSmsAction(
        $id,
        SmsService $smsService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        if (!$this->isGranted(UserEnum::ROLE_SUPER_ADMIN)) {
            return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "You don't have rights to perform this action.");
        }

        try {
            $result = $smsService->deleteSms((int) $id);
            if ($result !== true) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $result);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "Sms contact deleted successfully.",
                null
            );
        } catch (Exception $exception) {
            $clientApiLogger->error('[delete_sms_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }

    /**
     * @Route(methods={"GET"}, path="/sms", name="get_sms_api")
     *
     * @Operation(
     *     tags={"Sms"},
     *     summary="Get sms",
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
     * @param SmsService $smsService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function getSmsAction(
        Request $request,
        SmsService $smsService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $data = $request->query->all();
            if(!empty($data)){
                $result = $smsService->getSmsById($data['id']);
                if (!$result instanceof Sms) {
                    return $utilService->makeResponse(Response::HTTP_NOT_FOUND,'Sms contact not found.');
                }
                $datas = $smsService->makeSingleSmsResponse($result);
            }else{
                $result = $smsService->getAllActivePhoneNumber();
                $datas = $smsService->makeSmsApiResponse($result);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "",
                $datas
            );
        } catch (Exception $exception) {
            $clientApiLogger->error('[get_sms_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }
}
