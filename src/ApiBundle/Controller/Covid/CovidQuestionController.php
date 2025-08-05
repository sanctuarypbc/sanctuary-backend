<?php

namespace App\ApiBundle\Controller\Covid;

use App\ApiBundle\Service\CovidQuestionService;
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
 * Class CovidQuestionController
 * @package App\ApiBundle\Controller\Request
 */
class CovidQuestionController extends AbstractController
{
    /**
     * @Route(methods={"PUT"}, path="/covid-question/{id}", name="update_covid_question_api")
     *
     * @Operation(
     *     tags={"Covid"},
     *     summary="Update covid question",
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
     *                  property="text",
     *                  type="string",
     *                  )
     *              ),
     *     )
     * )
     *
     * @param $id
     * @param Request $request
     * @param CovidQuestionService $covidQuestionService
     * @param UtilService $utilService
     * @param LoggerInterface $adminApiLogger
     * @return JsonResponse
     */
    public function editCovidQuestionAction(
        $id,
        Request $request,
        CovidQuestionService $covidQuestionService,
        UtilService $utilService,
        LoggerInterface $adminApiLogger
    ) {
        try {
            if (!$this->isGranted(UserEnum::ROLE_SUPER_ADMIN)) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "You don't have rights to perform this action.");
            }
            
            $data = json_decode($request->getContent(), true);
            if (empty($data['text'])) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Question text is required.");
            }

            $result = $covidQuestionService->updateCovidQuestion((int) $id, $data);

            if ($result !== true) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $result);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "Covid question updated successfully."
            );
        } catch (\Exception $exception) {
            $adminApiLogger->error('[update_covid_question_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }

    /**
     * @Route(methods={"POST"}, path="/covid-question", name="create_covid_question_api")
     *
     * @Operation(
     *     tags={"Covid"},
     *     summary="Create covid question",
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
     *                  property="text",
     *                  type="string",
     *                  )
     *              ),
     *     )
     * )
     *
     * @param Request $request
     * @param CovidQuestionService $covidQuestionService
     * @param UtilService $utilService
     * @param LoggerInterface $adminApiLogger
     * @return JsonResponse
     */
    public function createQuestionAction(
        Request $request,
        CovidQuestionService $covidQuestionService,
        UtilService $utilService,
        LoggerInterface $adminApiLogger
    ) {
        try {
            if (!$this->isGranted(UserEnum::ROLE_SUPER_ADMIN)) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "You don't have rights to perform this action.");
            }

            $data = json_decode($request->getContent(), true);

            if (empty($data['text'])) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Question text is required.");
            }

            $covidQuestionService->createCovidQuestion($data);

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "Covid question created successfully.",
                null
            );
        } catch (Exception $exception) {
            $adminApiLogger->error('[create_covid_question_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }

    /**
     * @Route(methods={"DELETE"}, path="/covid-question/{id}", name="delete_covid_question_api")
     *
     * @Operation(
     *     tags={"Covid"},
     *     summary="Delete covid question",
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
     * @param CovidQuestionService $covidQuestionService
     * @param UtilService $utilService
     * @param LoggerInterface $adminApiLogger
     * @return JsonResponse
     */
    public function deleteQuestionAction(
        $id,
        CovidQuestionService $covidQuestionService,
        UtilService $utilService,
        LoggerInterface $adminApiLogger
    ) {
        try {
            if (!$this->isGranted(UserEnum::ROLE_SUPER_ADMIN)) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "You don't have rights to perform this action.");
            }

            $result = $covidQuestionService->deleteCovidQuestion((int) $id);
            if ($result !== true) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $result);
            }

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "Covid question deleted successfully.",
                null
            );
        } catch (Exception $exception) {
            $adminApiLogger->error('[delete_covid_question_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }

    /**
     * @Route(methods={"GET"}, path="/covid-question", name="get_covid_question_api")
     *
     * @Operation(
     *     tags={"Covid"},
     *     summary="Get covid question",
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
     * @param CovidQuestionService $covidQuestionService
     * @param UtilService $utilService
     * @param LoggerInterface $adminApiLogger
     * @return JsonResponse
     */
    public function getQuestionAction(
        Request $request,
        CovidQuestionService $covidQuestionService,
        UtilService $utilService,
        LoggerInterface $adminApiLogger
    ) {
        try {
            $data = $request->query->all();
            $result = $covidQuestionService->getCovidQuestions($data);

            return $utilService->makeResponse(
                Response::HTTP_OK,
                "",
                $result
            );
        } catch (Exception $exception) {
            $adminApiLogger->error('[get_covid_question_api]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, CommonEnum::INTERNAL_SERVER_ERROR_TEXT);
        }
    }
}
