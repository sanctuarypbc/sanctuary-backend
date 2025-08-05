<?php

namespace App\ApiBundle\Controller\ActivityLog;

use App\ApiBundle\Service\ActivityLogService;
use App\ApiBundle\Service\UtilService;
use Psr\Log\LoggerInterface;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Get;

/**
 * Class ActivityLogApiController
 * @package App\ApiBundle\Controller\User
 */
class ActivityLogApiController extends AbstractController
{
    /**
     * @Get("/activity-log", name="api_activity_log_list",  options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/activity-log",
     *     tags={"Activity Log"},
     *     summary="Authorises user via access token and returns activity log list",
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
     *         description="Enter advocate id",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="client_id",
     *         in="query",
     *         type="integer",
     *         description="Enter requested client id",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="from_date",
     *         in="query",
     *         type="string",
     *         description="Format: yyyy-mm-dd",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="to_date",
     *         in="query",
     *         type="string",
     *         description="Format: yyyy-mm-dd",
     *         required=false,
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
     * @param LoggerInterface $adminApiLogger
     * @param ActivityLogService $activityLogService
     * @return JsonResponse
     */
    public function getActivityLogsAction(
        Request $request,
        UtilService $utilService,
        LoggerInterface $adminApiLogger,
        ActivityLogService $activityLogService
    ) {
        try {
            $fromDate = $request->get('from_date', null);
            $toDate = $request->get('to_date', null);
            $advocateId = $request->get('id', null);
            $clientId = $request->get('client_id', null);
            $logs = $activityLogService->getActivityLogsOnClient($fromDate, $toDate, $advocateId, $clientId);
            if ($logs === false) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Date range exceeded 30 days");
            }
            $data = $activityLogService->makeActivityLogsResponse($logs);
            return $utilService->makeResponse(Response::HTTP_OK, null, $data);
        } catch (\Exception $exception) {
            $adminApiLogger->error('[api_activity_log_list]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }
}
