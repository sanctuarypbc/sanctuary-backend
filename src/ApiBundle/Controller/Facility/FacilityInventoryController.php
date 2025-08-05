<?php

namespace App\ApiBundle\Controller\Facility;

use App\ApiBundle\Service\FacilityInventoryService;
use App\ApiBundle\Service\UtilService;
use App\Enum\FacilityInventoryEnum;
use App\Enum\UserEnum;
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
 * Class FacilityInventoryController
 * @package App\ApiBundle\Controller\Facility
 */
class FacilityInventoryController extends AbstractController
{
    /**
     * @Get("/facility-inventory", name="api_facility_inventory_list",  options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/facility-inventory",
     *     tags={"Facility Inventory"},
     *     summary="Authorises user via access token and list facility inventory",
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
     *         description="Facility user id"
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
     * @param FacilityInventoryService $inventoryService
     * @param LoggerInterface $facilityApiLogger
     * @return JsonResponse
     */
    public function listAction(
        Request $request,
        UtilService $utilService,
        FacilityInventoryService $inventoryService,
        LoggerInterface $facilityApiLogger
    ) {
        try {
            $id = $request->get('id', null);
            $facilityUserId = $request->get('facility_user_id', null);
            $inventories = $inventoryService->getInventories($id, $facilityUserId);
            if ($inventories === false) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Facility doesn't exist.");
            }
            $data = $inventoryService->makeInventoriesAPIResponse($inventories);
            return $utilService->makeResponse(Response::HTTP_OK, null, $data);
        } catch (\Exception $exception) {
            $facilityApiLogger->error('[api_facility_inventory_list]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    /**
     * @Post("/facility-inventory", name="api_facility_inventory_add",  options={ "method_prefix" = false })
     *
     * @SWG\Post(
     *     path="/api/user/facility-inventory",
     *     tags={"Facility Inventory"},
     *     summary="Authorises user via access token and create facility inventory",
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
     *             @SWG\Property(
     *                  property="capacity",
     *                  type="number",
     *                  ),
     *             @SWG\Property(
     *                  property="total_available",
     *                  type="number",
     *                  ),
     *             @SWG\Property(
     *                  property="inventory_type_id",
     *                  type="integer",
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
     * @param FacilityInventoryService $inventoryService
     * @param LoggerInterface $facilityApiLogger
     * @return JsonResponse
     */
    public function createAction(
        Request $request,
        UtilService $utilService,
        FacilityInventoryService $inventoryService,
        LoggerInterface $facilityApiLogger
    ) {
        try {
            $data = json_decode($request->getContent(), true);
            $name = isset($data['name']) ? $data['name'] : null;
            $inventoryTypeId = isset($data['inventory_type_id']) ? $data['inventory_type_id'] : null;
            if (empty($name) || empty($inventoryTypeId)) {
                return $utilService
                    ->makeResponse(Response::HTTP_BAD_REQUEST, "Required parameters missing.");
            }
            $response = $inventoryService->createInventory($data);
            if ($response['status'] === false) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $response['message']);
            }
            return $utilService->makeResponse(Response::HTTP_OK, $response['message']);
        } catch (\Exception $exception) {
            $facilityApiLogger->error('[api_facility_inventory_add]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Put("/facility-inventory/{id}", name="api_facility_inventory_edit",  options={ "method_prefix" = false })
     *
     * @SWG\Put(
     *     path="/api/user/facility-inventory/{id}",
     *     tags={"Facility Inventory"},
     *     summary="Authorises user via access token and update facility inventory",
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
     *             @SWG\Property(
     *                  property="capacity",
     *                  type="number",
     *                  ),
     *             @SWG\Property(
     *                  property="total_available",
     *                  type="number",
     *                  ),
     *             @SWG\Property(
     *                  property="inventory_type_id",
     *                  type="integer",
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
     * @param $id
     * @param Request $request
     * @param UtilService $utilService
     * @param FacilityInventoryService $inventoryService
     * @param LoggerInterface $facilityApiLogger
     * @return JsonResponse
     */
    public function editAction(
        $id,
        Request $request,
        UtilService $utilService,
        FacilityInventoryService $inventoryService,
        LoggerInterface $facilityApiLogger
    ) {
        try {
            $data = json_decode($request->getContent(), true);
            $validRequest = $utilService->checkIfRequestHasFieldsToUpdate($data, FacilityInventoryEnum::FACILITY_INVENTORY_POSSIBLE_UPDATE_FIELDS_ALL);
            if (!$validRequest) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Some parameters required.");
            }

            $response = $inventoryService->updateInventory((int)$id, $data);
            if($response['status'] === false) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $response['message']);
            }
            return $utilService->makeResponse(Response::HTTP_OK, $response['message']);
        } catch (\Exception $exception) {
            $facilityApiLogger->error('[api_facility_inventory_edit]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Delete("/facility-inventory/{id}", name="api_facility_inventory_delete",  options={ "method_prefix" = false })
     *
     * @SWG\Delete(
     *     path="/api/user/facility-inventory/{id}",
     *     tags={"Facility Inventory"},
     *     summary="Authorises user via access token and delete facility inventory",
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
     * @param FacilityInventoryService $inventoryService
     * @param LoggerInterface $facilityApiLogger
     * @return JsonResponse
     */
    public function facilityInventoryDeleteAction(
        $id,
        UtilService $utilService,
        FacilityInventoryService $inventoryService,
        LoggerInterface $facilityApiLogger
    ) {
        try {
            if($inventoryService->deleteInventory((int)$id)) {
                return $utilService->makeResponse(Response::HTTP_OK, "Item deleted successfully.");
            }
            return $utilService->makeResponse(Response::HTTP_BAD_REQUEST,
                "Either inventory does not exist or you don't have sufficient rights to delete this item.");
        } catch (\Exception $exception) {
            $facilityApiLogger->error('[api_facility_inventory_delete]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }
}