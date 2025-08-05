<?php

namespace App\ApiBundle\Controller\Facility;

use App\ApiBundle\Service\FacilityTypeService;
use App\ApiBundle\Service\UtilService;
use App\Entity\FacilityType;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use Swagger\Annotations as SWG;

/**
 * Class FacilityTypeApiController
 * @package App\ApiBundle\Controller\Client
 */
class FacilityTypeApiController extends AbstractController
{
    /**
     * @Get("/facility-type", name="api_facility_type_list",  options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/facility-type",
     *     tags={"Facility Type"},
     *     summary="Authorises user via access token and list facility type",
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
     * @param FacilityTypeService $facilityTypeService
     * @param LoggerInterface $facilityApiLogger
     * @return JsonResponse
     */
    public function facilityTypeListAction(
        Request $request,
        UtilService $utilService,
        FacilityTypeService $facilityTypeService,
        LoggerInterface $facilityApiLogger
    ) {
        try {
            $facilityTypeId = $request->get('id');
            $name = $request->get('name');
            $data = $facilityTypeService->getFacilityTypeList($facilityTypeId, $name);
            return $utilService->makeResponse(Response::HTTP_OK, null, $data);
        } catch (\Exception $exception) {
            $facilityApiLogger->error('[api_facility_type_list]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Post("/facility-type", name="api_facility_type_add",  options={ "method_prefix" = false })
     *
     * @SWG\Post(
     *     path="/api/user/facility-type",
     *     tags={"Facility Type"},
     *     summary="Authorises user via access token and create facility type",
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
     * @param FacilityTypeService $facilityTypeService
     * @param LoggerInterface $facilityApiLogger
     * @return JsonResponse
     */
    public function facilityTypeAddAction(
        Request $request,
        UtilService $utilService,
        FacilityTypeService $facilityTypeService,
        LoggerInterface $facilityApiLogger
    ) {
        try {
            $data = json_decode($request->getContent(), true);
            $name = isset($data['name']) ? $data['name'] : null;
            if (empty($name)) {
                return $utilService
                    ->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters missing.");
            }
            $facilityTypeService->addFacilityType($name);
            return $utilService->makeResponse(Response::HTTP_OK, "Item added successfully.");
        } catch (\Exception $exception) {
            $facilityApiLogger->error('[api_facility_type_add]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Put("/facility-type/{id}", name="api_facility_type_edit",  options={ "method_prefix" = false })
     *
     * @SWG\Put(
     *     path="/api/user/facility-type/{id}",
     *     tags={"Facility Type"},
     *     summary="Authorises user via access token and update facility type",
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
     * @param FacilityTypeService $facilityTypeService
     * @param LoggerInterface $facilityApiLogger
     * @return JsonResponse
     */
    public function facilityTypeEditAction(
        $id,
        Request $request,
        UtilService $utilService,
        FacilityTypeService $facilityTypeService,
        LoggerInterface $facilityApiLogger
    ) {
        try {
            $typeObj = $facilityTypeService->getFacilityTypeById($id);
            if (!$typeObj instanceof FacilityType) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Item doesn't exist.");
            }
            $data = json_decode($request->getContent(), true);
            $name = isset($data['name']) ? $data['name'] : null;
            if (empty($name) || empty($id)) {
                return $utilService
                    ->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters missing.");
            }
            $facilityTypeService->updateFacilityTypeById((int)$id, $name);
            return $utilService->makeResponse(Response::HTTP_OK, "Item updated successfully.");
        } catch (\Exception $exception) {
            $facilityApiLogger->error('[api_facility_type_edit]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Delete("/facility-type/{id}", name="api_facility_type_delete",  options={ "method_prefix" = false })
     *
     * @SWG\Delete(
     *     path="/api/user/facility-type/{id}",
     *     tags={"Facility Type"},
     *     summary="Authorises user via access token and delete facility type",
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
     * @param FacilityTypeService $facilityTypeService
     * @param LoggerInterface $facilityApiLogger
     * @return JsonResponse
     */
    public function facilityTypeDeleteAction(
        $id,
        UtilService $utilService,
        FacilityTypeService $facilityTypeService,
        LoggerInterface $facilityApiLogger
    ) {
        try {
            $response = $facilityTypeService->deleteFacilityTypeById((int)$id);
            if ($response) {
                return $utilService->makeResponse(Response::HTTP_OK, "Item deleted successfully.");
            }
            return $utilService->makeResponse(Response::HTTP_METHOD_NOT_ALLOWED, "This type is linked with one of facilities.");
        } catch (\Exception $exception) {
            $facilityApiLogger->error('[api_facility_type_delete]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }
}