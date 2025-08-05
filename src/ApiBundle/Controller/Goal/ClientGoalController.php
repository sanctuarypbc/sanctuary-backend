<?php

namespace App\ApiBundle\Controller\Goal;

use App\ApiBundle\Service\GoalService;
use App\ApiBundle\Service\UtilService;
use App\Enum\CommonEnum;
use App\Enum\GoalEnum;
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
 *
 * Class GoalController
 * @package App\ApiBundle\Controller\Goal
 */
class ClientGoalController extends AbstractController
{
    /**
     * @Route(methods={"POST"}, path="/assign-goal", name="assign_goal_api")
     *
     * @Operation(
     *     tags={"Client Goal"},
     *     summary="Assign goal",
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
     *                  property="goal_ids",
     *                  type="object",
     *                  ),
     *         @SWG\Property(
     *                  property="client_id",
     *                  type="integer",
     *                  ),
     *         @SWG\Property(
     *                  property="action",
     *                  type="string",
     *                  )
     *              ),
     *     )
     * )
     *
     * @param Request $request
     * @param GoalService $goalService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function assignGoalAction(
        Request $request,
        GoalService $goalService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $data = json_decode($request->getContent(), true);

            if (empty($data['goal_ids']) || !is_array($data['goal_ids']) || empty($data['client_id'])) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters missing.");
            }

            $action = !empty($data['action']) ? $data['action'] : GoalEnum::ACTION_ASSIGN;
            $result = $goalService->assignGoal($this->getUser(),  $data, $action);
            if ($result !== true) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $result);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "Goal $action" . "ed successfully.",
                null
            );
        } catch (Exception $exception) {
            $clientApiLogger->error('[create_goal_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }

    /**
     * @Route(methods={"GET"}, path="/client-goal", name="get_client_goal_api")
     *
     * @Operation(
     *     tags={"Client Goal"},
     *     summary="Get client goal",
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
     *         name="client_id",
     *         in="query",
     *         type="integer",
     *         required=false,
     *         description="Client id"
     *      )
     * )
     *
     * @param Request $request
     * @param GoalService $goalService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function getClientGoalAction(
        Request $request,
        GoalService $goalService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $data = $request->query->all();
            $userIsClient = in_array(UserEnum::ROLE_CLIENT, $this->getUser()->getRoles());
            if (!$userIsClient && empty($data['client_id'])) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Client id is required.");
            }

            $result = $goalService->getClientGoals($this->getUser(), $data, $userIsClient);
            if (!is_array($result)) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $result);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "",
                $result
            );
        } catch (Exception $exception) {
            $clientApiLogger->error('[get_client_goal_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }

    /**
     * @Route(methods={"GET"}, path="/client-goal-task", name="get_client_goal_task_api")
     *
     * @Operation(
     *     tags={"Client Goal"},
     *     summary="Get client goal tasks",
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
     *         name="client_goal_id",
     *         in="query",
     *         type="integer",
     *         required=true,
     *         description="Client goal id"
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
     * @param GoalService $goalService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function getClientGoalTasksAction(
        Request $request,
        GoalService $goalService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $data = $request->query->all();
            $userIsClient = in_array(UserEnum::ROLE_CLIENT, $this->getUser()->getRoles());
            if (empty($data['client_goal_id']) || (!$userIsClient && empty($data['client_id']))) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters missing.");
            }

            $result = $goalService->getClientGoalTasks($this->getUser(), $data, $userIsClient);
            if (!is_array($result)) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $result);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "",
                $result
            );
        } catch (Exception $exception) {
            $clientApiLogger->error('[get_client_goal_task_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }

    /**
     * @Route(methods={"POST"}, path="/process-task", name="process_task_api")
     *
     * @Operation(
     *     tags={"Client Goal"},
     *     summary="Process task",
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
     *                  property="client_goal_task_id",
     *                  type="integer",
     *                  ),
     *         @SWG\Property(
     *                  property="completed",
     *                  type="boolean",
     *                  )
     *              ),
     *     )
     * )
     *
     * @param Request $request
     * @param GoalService $goalService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function processTaskAction(
        Request $request,
        GoalService $goalService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $data = json_decode($request->getContent(), true);

            if (empty($data['client_goal_task_id']) || !isset($data['completed'])) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters missing.");
            }

            $result = $goalService->processTask($this->getUser(),  $data);
            if ($result !== true) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $result);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "Action performed successfully.",
                null
            );
        } catch (Exception $exception) {
            $clientApiLogger->error('[process_task_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }
}
