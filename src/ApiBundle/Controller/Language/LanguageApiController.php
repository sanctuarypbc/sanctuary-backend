<?php

namespace App\ApiBundle\Controller\Language;

use App\ApiBundle\Service\LanguageService;
use App\ApiBundle\Service\UtilService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Get;
use Swagger\Annotations as SWG;

/**
 * Class LanguageApiController
 * @package App\ApiBundle\Controller\User
 */
class LanguageApiController extends AbstractController
{
    /**
     * @Get("/language", name="api_language_list",  options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/language",
     *     tags={"Language"},
     *     summary="Authorises user via access token and list language",
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
     * @param LanguageService $languageService
     * @return JsonResponse
     */
    public function getClientAction(
        Request $request,
        UtilService $utilService,
        LoggerInterface $adminApiLogger,
        LanguageService $languageService
    ) {
        try {
            $id = $request->get('id', null);
            $data = $languageService->getActiveLanguagesData($id);
            return $utilService->makeResponse(Response::HTTP_OK, null, $data);
        } catch (\Exception $exception) {
            $adminApiLogger->error('[api_language_list]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }
}
