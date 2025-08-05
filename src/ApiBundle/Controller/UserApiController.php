<?php

namespace App\ApiBundle\Controller;

use App\ApiBundle\Service\MailService;
use App\ApiBundle\Service\ResetTokenService;
use App\ApiBundle\Service\UserService;
use App\ApiBundle\Service\UtilService;
use Psr\Log\LoggerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use Swagger\Annotations as SWG;

/**
 * Class LoginApiController
 * @package App\Controller
 */
class UserApiController extends AbstractFOSRestController
{
    /**
     * @Get("user/user-detail", name="api_user_detail",  options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/user-detail",
     *     tags={"User"},
     *     summary="Authorises user via access token and returns user detail",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *
     *     @SWG\Parameter(
     *         ref="#parameters/authorization"
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
     * @param UtilService $utilService
     * @param UserService $userService
     * @param LoggerInterface $adminApiLogger
     * @return JsonResponse
     */
    public function index(UtilService $utilService, UserService $userService, LoggerInterface $adminApiLogger)
    {
        $user = $this->getUser();
        if (empty($user)) {
            return $utilService->makeResponse(Response::HTTP_NOT_FOUND, "User not found.");
        }
        try {
            $userData = $userService->getUserDetailResponseByUser($user);
            return $utilService->makeResponse(Response::HTTP_OK, null, $userData);
        } catch (\Exception $e) {
            $adminApiLogger->error('[api_user_detail]: ' . $e->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Post("client/forget-password", name="api_forget_password",  options={ "method_prefix" = false })
     *
     * @SWG\Post(
     *     path="/api/client/forget-password",
     *     tags={"User"},
     *     summary="Authorises client via access token and perform forget password",
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
     *                  property="email",
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
     * @param UserService $userService
     * @param UtilService $utilService
     * @param MailService $mailService
     * @param LoggerInterface $adminApiLogger
     * @return JsonResponse
     */
    public function forgetPasswordAction(
        Request $request,
        UserService $userService,
        UtilService $utilService,
        MailService $mailService,
        LoggerInterface $adminApiLogger
    ) {
        $data = json_decode($request->getContent(), true);
        $email = isset($data['email']) ? $data['email'] : null;
        if (!$email) {
            return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters are missing.");
        }

        $user = $userService->getUserByEmail($email);
        if (!$user) {
            return $utilService->makeResponse(Response::HTTP_OK, "Email sent successfully.");
        }

        try {
            $resetToken = $utilService->makeUserResetToken($user);
            $mailService->sendVerificationCodeEmail($resetToken);

            return $utilService->makeResponse(Response::HTTP_OK, "Email sent successfully.");
        } catch (\Exception $e) {
            $adminApiLogger->error('[api_forget_password]: ' . $e->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Post("client/verify-token", name="api_verify_token",  options={ "method_prefix" = false })
     *
     * @SWG\Post(
     *     path="/api/client/verify-token",
     *     tags={"User"},
     *     summary="Authorises client via access token and verify token",
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
     *                  property="token",
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
     * @param ResetTokenService $resetTokenService
     * @param LoggerInterface $adminApiLogger
     * @return JsonResponse
     */
    public function verifyTokenAction(
        Request $request,
        UtilService $utilService,
        ResetTokenService $resetTokenService,
        LoggerInterface $adminApiLogger
    ) {
        $data = json_decode($request->getContent(), true);
        $token = isset($data['token']) ? $data['token'] : null;
        if (!$token) {
            return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters are missing.");
        }

        $resetToken = $resetTokenService->getResetTokenByToken($token);
        if (empty($resetToken)) {
            return $utilService->makeResponse(Response::HTTP_NOT_FOUND, "Invalid token.");
        }

        try {
            $currentTime = new \DateTime('now');
            if ($resetToken->getExpiry() > $currentTime) {
                return $utilService->makeResponse(Response::HTTP_OK, "Token verified.");
            }
            return $utilService->makeResponse(Response::HTTP_UNAUTHORIZED, "Token expired.");
        } catch (\Exception $e) {
            $adminApiLogger->error('[api_verify_token]: ' . $e->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Post("client/reset-password", name="api_reset_password",  options={ "method_prefix" = false })
     *
     * @SWG\Post(
     *     path="/api/client/reset-password",
     *     tags={"User"},
     *     summary="Authorises client via access token and reset password",
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
     *                  property="token",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="password",
     *                  type="string",
     *                  ),
     *              ),
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
     * @param UserService $userService
     * @param ResetTokenService $resetTokenService
     * @param LoggerInterface $adminApiLogger
     * @return JsonResponse
     */
    public function resetPasswordAction(
        Request $request,
        UtilService $utilService,
        UserService $userService,
        ResetTokenService $resetTokenService,
        LoggerInterface $adminApiLogger
    ) {
        $data = json_decode($request->getContent(), true);
        $token = isset($data['token']) ? $data['token'] : null;
        $password = isset($data['password']) ? $data['password'] : null;
        if (!$token || !$password) {
            return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters are missing.");
        }
        try {
            $tokenObject = $resetTokenService->getResetTokenByToken($token);
            if (!$tokenObject) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Invalid token.");
            }
            if ($tokenObject->getExpiry() < new \DateTime('now')) {
                return $utilService->makeResponse(Response::HTTP_UNAUTHORIZED, "Token expired.");
            }
            $userService->updateUserPassword($tokenObject->getUser(), $password);
            return $utilService->makeResponse(Response::HTTP_OK, "Password changed successfully.");
        } catch (\Exception $e) {
            $adminApiLogger->error('[api_reset_password]: ' . $e->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Post("user/change-password", name="api_change_password",  options={ "method_prefix" = false })
     *
     * @SWG\Post(
     *     path="/api/user/change-password",
     *     tags={"User"},
     *     summary="Authorises user via access token and change password",
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
     *                  property="old_password",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="new_password",
     *                  type="string",
     *                  ),
     *              ),
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
     * @param UserService $userService
     * @param LoggerInterface $adminApiLogger
     * @param EncoderFactory $factory
     * @return JsonResponse
     */
    public function changePasswordAction(
        Request $request,
        UtilService $utilService,
        UserService $userService,
        LoggerInterface $adminApiLogger,
        EncoderFactory $factory
    ) {
        $data = json_decode($request->getContent(), true);
        $newPassword = isset($data['new_password']) ? $data['new_password'] : null;
        $oldPassword = isset($data['old_password']) ? $data['old_password'] : null;
        if (!$newPassword || !$oldPassword) {
            return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters are missing.");
        }
        try {
            $user = $this->getUser();
            if (!$user) {
                return $utilService->makeResponse(Response::HTTP_NOT_FOUND, "User not found.");
            }

            $encoder = $factory->getEncoder($user);
            $passwordMatched = $encoder->isPasswordValid($user->getPassword(), $oldPassword, $user->getSalt());
            if ($passwordMatched) {
                $userService->updateUserPassword($user, $newPassword);
                return $utilService->makeResponse(Response::HTTP_OK, "Password changed successfully.");
            }

            return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Incorrect old password.");
        } catch (\Exception $e) {
            $adminApiLogger->error('[api_change_password]: ' . $e->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }
}
