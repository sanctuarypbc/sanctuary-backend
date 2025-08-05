<?php

namespace App\ApiBundle\Controller\Goal;

use App\ApiBundle\Service\GoalService;
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
 * Class GoalController
 * @package App\ApiBundle\Controller\Goal
 */
class GoalController extends AbstractController
{
    /**
     * @Route(methods={"PUT"}, path="/goal/{id}", name="update_goal_api")
     *
     * @Operation(
     *     tags={"Goal"},
     *     summary="Update goal",
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
     * @param GoalService $goalService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function editGoalAction(
        $id,
        Request $request,
        GoalService $goalService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $data = json_decode($request->getContent(), true);
            if (empty($data['title']) && empty($data['description'])) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Missing required parameters.");
            }

            $result = $goalService->updateGoal($this->getUser(), (int) $id, $data);

            if ($result !== true) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $result);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "Goal updated successfully."
            );
        } catch (\Exception $exception) {
            $clientApiLogger->error('[update_goal_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }

    /**
     * @Route(methods={"POST"}, path="/goal", name="create_goal_api")
     *
     * @Operation(
     *     tags={"Goal"},
     *     summary="Create goal",
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
     *                  ),
     *         @SWG\Property(
     *                  property="tasks",
     *                  type="object"
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
    public function createGoalAction(
        Request $request,
        GoalService $goalService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $data = json_decode($request->getContent(), true);

            if (empty($data['title'])) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Title is required.");
            }

            $result = $goalService->createGoal($this->getUser(),  $data);
            if ($result !== true) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $result);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "Goal created successfully.",
                null
            );
        } catch (Exception $exception) {
            $clientApiLogger->error('[create_goal_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }

    /**
     * @Route(methods={"DELETE"}, path="/goal/{id}", name="delete_goal_api")
     *
     * @Operation(
     *     tags={"Goal"},
     *     summary="Delete goal",
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
     * @param GoalService $goalService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function deleteGoalAction(
        $id,
        GoalService $goalService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $result = $goalService->deleteGoal($this->getUser(), (int) $id);
            if ($result !== true) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $result);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "Goal deleted successfully.",
                null
            );
        } catch (Exception $exception) {
            $clientApiLogger->error('[delete_goal_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }

    /**
     * @Route(methods={"GET"}, path="/goal", name="get_goal_api")
     *
     * @Operation(
     *     tags={"Goal"},
     *     summary="Get goal",
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
     * @param GoalService $goalService
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function getGoalAction(
        Request $request,
        GoalService $goalService,
        UtilService $utilService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $data = $request->query->all();
            if (!empty($data['per_page']) && $data['per_page'] > CommonEnum::PER_PAGE_MAX) {
                return $utilService->makeResponse(
                    Response::HTTP_BAD_REQUEST,
                    "per_page page can not exceed the limit " . CommonEnum::PER_PAGE_MAX . "."
                );
            }

            $result = $goalService->getGoals($this->getUser(), $data);
            if (!is_array($result)) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $result);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "",
                $result
            );
        } catch (Exception $exception) {
            $clientApiLogger->error('[get_goal_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }
}
