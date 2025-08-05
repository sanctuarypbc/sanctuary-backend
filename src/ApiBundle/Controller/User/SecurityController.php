<?php

namespace App\ApiBundle\Controller\User;

use App\ApiBundle\Service\LoginService;
use App\ApiBundle\Service\UserService;
use App\ApiBundle\Service\UtilService;
use App\Entity\User;
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
 * Class SecurityController
 * @package App\ApiBundle\Controller\User
 */
class SecurityController extends AbstractController
{
    /**
     * @Route(methods={"POST"}, path="/onboarding", name="onboarding_api")
     *
     * @Operation(
     *     tags={"Onboarding"},
     *     summary="Receive phone number and send verification code",
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
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                  property="phone",
     *                  type="string",
     *                  )
     *              ),
     *     )
     * )
     *
     * @param Request $request
     * @param LoginService $loginService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @param UtilService $userService
     * @return JsonResponse
     */
    public function loginAction(
        Request $request,
        LoginService $loginService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger,
        UserService $userService
    ) {
        try {
            $data = json_decode($request->getContent(), true);

            if (empty($data['phone'])) {
                $phone = $request->request->get('phone');
            } else {
                $phone = $data['phone'];
            }
            $user = $userService->checkUserByPhoneNumber($phone);
            if (!$user instanceof  User) {
                return $utilService->makeResponse(Response::HTTP_CONFLICT, "You are not authorized");
            }
            $result = $loginService->loginUser($phone);

            if ($result === false) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Invalid phone number provided.");
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "SMS sent."
            );
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_onboarding]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }

    /**
     * @Route(methods={"POST"}, path="/verify-code", name="verification_api")
     *
     * @Operation(
     *     tags={"Onboarding"},
     *     summary="Verify code sent via sms",
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
     *                  property="phone",
     *                  type="string",
     *                  ),
     *         @SWG\Property(
     *                  property="code",
     *                  type="string",
     *                  )
     *              ),
     *     )
     * )
     *
     * @param Request $request
     * @param LoginService $loginService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     * @throws \Exception
     */
    public function verifyAction(
        Request $request,
        LoginService $loginService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $data = json_decode($request->getContent(), true);

            if (empty($data['phone'])) {
                $phone = $request->request->get('phone');
                $code = $request->request->get('code');
            } else {
                $phone = $data['phone'];
                $code = $data['code'] ?? "";
            }

            if (empty($phone) || empty($code)) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Invalid Parameters.");
            }

            $result = $loginService->verifyCode($phone, $code);

            if (!is_array($result)) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $result);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "Login successfully.",
                $result
            );
        } catch (Exception $exception) {
            $clientApiLogger->error('[api_verify_code]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }
}
