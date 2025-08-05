<?php

namespace App\ApiBundle\Controller\User;

use App\ApiBundle\Service\UserService;
use App\ApiBundle\Service\UtilService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Get;
use Swagger\Annotations as SWG;

/**
 * Class UserApiController
 * @package App\ApiBundle\Controller\Client
 */
class UserApiController extends AbstractController
{
    /**
     * @Get("/user", name="api_user_list",  options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/user",
     *     tags={"User"},
     *     summary="Authorises user via access token and list user",
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
     * @param LoggerInterface $adminApiLogger
     * @param UserService $userService
     * @return JsonResponse
     */
    public function getClientAction(
        UtilService $utilService,
        LoggerInterface $adminApiLogger,
        UserService $userService
    ) {
        try {
            $users = $userService->getAllUsers();
            $data = $userService->makeUsersResponse($users);
            return $utilService->makeResponse(Response::HTTP_OK, null, $data);
        } catch (\Exception $exception) {
            $adminApiLogger->error('[api_user_list]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }
}
