<?php

namespace App\ApiBundle\Controller\Facility;

use App\ApiBundle\Service\FacilityInventoryTypeService;
use App\ApiBundle\Service\UtilService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use Swagger\Annotations as SWG;

/**
 * Class FacilityInventoryTypeController
 * @package App\ApiBundle\Controller\Facility
 */
class FacilityInventoryTypeController extends AbstractController
{
    /**
     * @Get("/facility-inventory-type", name="api_facility_inventory_type_list",  options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/facility-inventory-type",
     *     tags={"Facility Inventory Type"},
     *     summary="Authorises user via access token and list facility inventory type",
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
     *     @SWG\Parameter(
     *         name="facility_user_id",
     *         in="query",
     *         type="integer",
     *         required=false,
     *         description="Facility User Id"
     *     ),
     *     @SWG\Parameter(
     *         name="get_inventory_count",
     *         in="query",
     *         type="boolean",
     *         required=false,
     *         description="Get inventory count"
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
     * @param FacilityInventoryTypeService $inventoryTypeService
     * @param LoggerInterface $facilityApiLogger
     * @return JsonResponse
     */
    public function listAction(
        Request $request,
        UtilService $utilService,
        FacilityInventoryTypeService $inventoryTypeService,
        LoggerInterface $facilityApiLogger
    ) {
        try {
            $id = $request->get('id', null);
            $facilityUserId = $request->get('facility_user_id', false);
            $getInventoryCount = $request->get('get_inventory_count', false);
            $data = $inventoryTypeService->getInventoryTypes($id, $getInventoryCount, $facilityUserId);
            if ($data) {
                return $utilService->makeResponse(Response::HTTP_OK, null, $data);
            }
            return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Invalid facility id provided");
        } catch (\Exception $exception) {
            $facilityApiLogger->error('[api_facility_inventory_type_list]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Post("/facility-inventory-type", name="api_facility_inventory_type_add",  options={ "method_prefix" = false })
     *
     * @SWG\Post(
     *     path="/api/user/facility-inventory-type",
     *     tags={"Facility Inventory Type"},
     *     summary="Authorises user via access token and create facility inventory type",
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
     * @param FacilityInventoryTypeService $inventoryTypeService
     * @param LoggerInterface $facilityApiLogger
     * @return JsonResponse
     */
    public function createAction(
        Request $request,
        UtilService $utilService,
        FacilityInventoryTypeService $inventoryTypeService,
        LoggerInterface $facilityApiLogger
    ) {
        try {
            $data = json_decode($request->getContent(), true);
            $name = isset($data['name']) ? $data['name'] : null;
            if (empty($name)) {
                return $utilService
                    ->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters missing.");
            }
            $responseStatus = $inventoryTypeService->createInventoryType($name);
            if ($responseStatus === false) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Facility doesn't exist.");
            }
            return $utilService->makeResponse(Response::HTTP_OK, "Item added successfully.");
        } catch (\Exception $exception) {
            $facilityApiLogger->error('[api_facility_inventory_type_add]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Put("/facility-inventory-type/{id}", name="api_facility_inventory_type_edit",  options={ "method_prefix" = false })
     *
     * @SWG\Put(
     *     path="/api/user/facility-inventory-type/{id}",
     *     tags={"Facility Inventory Type"},
     *     summary="Authorises user via access token and update facility inventory type",
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
     * @param FacilityInventoryTypeService $inventoryTypeService
     * @param LoggerInterface $facilityApiLogger
     * @return JsonResponse
     */
    public function editAction(
        $id,
        Request $request,
        UtilService $utilService,
        FacilityInventoryTypeService $inventoryTypeService,
        LoggerInterface $facilityApiLogger
    ) {
        try {
            $data = json_decode($request->getContent(), true);
            $name = isset($data['name']) ? $data['name'] : null;
            if (empty($name) || empty($id)) {
                return $utilService
                    ->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters missing.");
            }
            if($inventoryTypeService->updateInventoryType((int)$id, $name)) {
                return $utilService->makeResponse(Response::HTTP_OK, "Item updated successfully.");
            }
            return $utilService->makeResponse(Response::HTTP_BAD_REQUEST,
                "Either inventory type does not exist or you don't have sufficient rights to update this item.");
        } catch (\Exception $exception) {
            $facilityApiLogger->error('[api_facility_inventory_type_edit]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Delete("/facility-inventory-type/{id}", name="api_facility_inventory_type_delete",  options={ "method_prefix" = false })
     *
     * @SWG\Delete(
     *     path="/api/user/facility-inventory-type/{id}",
     *     tags={"Facility Inventory Type"},
     *     summary="Authorises user via access token and delete facility inventory type",
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
     * @param FacilityInventoryTypeService $inventoryTypeService
     * @param LoggerInterface $facilityApiLogger
     * @return JsonResponse
     */
    public function facilityTypeDeleteAction(
        $id,
        UtilService $utilService,
        FacilityInventoryTypeService $inventoryTypeService,
        LoggerInterface $facilityApiLogger
    ) {
        try {
            $response = $inventoryTypeService->deleteInventoryType((int)$id);
            if($response['status']) {
                return $utilService->makeResponse(Response::HTTP_OK, "Item deleted successfully.");
            }
            return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $response['message']);
        } catch (\Exception $exception) {
            $facilityApiLogger->error('[api_facility_type_delete]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }
}