<?php

namespace App\ApiBundle\Controller\Advocate;

use App\ApiBundle\Service\AdvocateServiceTypeService;
use App\ApiBundle\Service\UtilService;
use App\Entity\AdvocateServiceType;
use Psr\Log\LoggerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use Swagger\Annotations as SWG;

/**
 * Class AdvocateServiceTypeApiController
 * @package App\ApiBundle\Controller\Advocate
 */
class AdvocateServiceTypeApiController extends AbstractFOSRestController
{
    /**
     * @Get("/advocate-service-type", name="api_advocate_service_type_list",  options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/advocate-service-type",
     *     tags={"Advocate Service Type"},
     *     summary="Authorises user via access token and list advocate service types",
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
     * @param Request $request
     * @param UtilService $utilService
     * @param AdvocateServiceTypeService $advocateServiceTypeService
     * @param LoggerInterface $advocateApiLogger
     * @return JsonResponse
     */
    public function advocateServiceTypeListAction(
        Request $request,
        UtilService $utilService,
        AdvocateServiceTypeService $advocateServiceTypeService,
        LoggerInterface $advocateApiLogger
    ) {
        try {
            $advocateServiceTypeId = $request->get('id');
            $name = $request->get('name');
            $data = $advocateServiceTypeService->getAdvocateServiceTypeList($advocateServiceTypeId, $name);
            return $utilService->makeResponse(Response::HTTP_OK, null, $data);
        } catch (\Exception $exception) {
            $advocateApiLogger->error('[api_advocate_service_type_list]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Post("/advocate-service-type", name="api_advocate_service_type_add",  options={ "method_prefix" = false })
     *
     * @SWG\Post(
     *     path="/api/user/advocate-service-type",
     *     tags={"Advocate Service Type"},
     *     summary="Authorises user via access token and create an advocate service type",
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
     *                  property="name",
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
     * @param AdvocateServiceTypeService $advocateServiceTypeService
     * @param LoggerInterface $advocateApiLogger
     * @return JsonResponse
     */
    public function advocateServiceTypeAddAction(
        Request $request,
        UtilService $utilService,
        AdvocateServiceTypeService $advocateServiceTypeService,
        LoggerInterface $advocateApiLogger
    ) {
        try {
            $data = json_decode($request->getContent(), true);
            $name = isset($data['name']) ? $data['name'] : null;
            if (empty($name)) {
                return $utilService
                    ->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters missing.");
            }
            $advocateServiceTypeService->addAdvocateServiceType($name);
            return $utilService->makeResponse(Response::HTTP_OK, "Item added successfully.");
        } catch (\Exception $exception) {
            $advocateApiLogger->error('[api_advocate_service_type_add]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Put("/advocate-service-type/{id}", name="api_advocate_service_type_edit",  options={ "method_prefix" = false })
     *
     * @SWG\Put(
     *     path="/api/user/advocate-service-type/{id}",
     *     tags={"Advocate Service Type"},
     *     summary="Authorises user via access token and update an advocate service type",
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
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                  property="name",
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
     * @param $id
     * @param Request $request
     * @param UtilService $utilService
     * @param AdvocateServiceTypeService $advocateServiceTypeService
     * @param LoggerInterface $advocateApiLogger
     * @return JsonResponse
     */
    public function advocateServiceTypeEditAction(
        $id,
        Request $request,
        UtilService $utilService,
        AdvocateServiceTypeService $advocateServiceTypeService,
        LoggerInterface $advocateApiLogger
    ) {
        try {
            $typeObj = $advocateServiceTypeService->getAdvocateServiceTypeById($id);
            if (!$typeObj instanceof AdvocateServiceType) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Item doesn't exist.");
            }
            $data = json_decode($request->getContent(), true);
            $name = isset($data['name']) ? $data['name'] : null;
            if (empty($name) || empty($id)) {
                return $utilService
                    ->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters missing.");
            }
            $advocateServiceTypeService->updateAdvocateServiceTypeById((int)$id, $name);
            return $utilService->makeResponse(Response::HTTP_OK, "Item updated successfully.");
        } catch (\Exception $exception) {
            $advocateApiLogger->error('[api_advocate_service_type_edit]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Delete("/advocate-service-type/{id}", name="api_advocate_service_type_delete",  options={ "method_prefix" = false })
     *
     * @SWG\Delete(
     *     path="/api/user/advocate-service-type/{id}",
     *     tags={"Advocate Service Type"},
     *     summary="Authorises user via access token and delete an advocate service type",
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
     * @param AdvocateServiceTypeService $advocateServiceTypeService
     * @param LoggerInterface $advocateApiLogger
     * @return JsonResponse
     */
    public function advocateServiceTypeDeleteAction(
        $id,
        UtilService $utilService,
        AdvocateServiceTypeService $advocateServiceTypeService,
        LoggerInterface $advocateApiLogger
    ) {
        try {
            $response = $advocateServiceTypeService->deleteAdvocateServiceTypeById((int)$id);
            if ($response) {
                return $utilService->makeResponse(Response::HTTP_OK, "Item deleted successfully.");
            }

            return $utilService->makeResponse(Response::HTTP_METHOD_NOT_ALLOWED, "This type is linked with one of advocates.");
        } catch (\Exception $exception) {
            $advocateApiLogger->error('[api_advocate_service_type_delete]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }
}