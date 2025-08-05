<?php

namespace App\ApiBundle\Controller\InventoryAssignment;

use App\ApiBundle\Service\ClientInventoryAssignmentService;
use App\ApiBundle\Service\UtilService;
use App\Enum\FacilityInventoryEnum;
use Psr\Log\LoggerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Post;
use Swagger\Annotations as SWG;

/**
 * Class InventoryAssignmentApiController
 * @package App\ApiBundle\Controller\InventoryAssignment
 * @IsGranted("ROLE_FACILITY", message="You don't have rights to access this resource")
 */
class InventoryAssignmentApiController extends AbstractFOSRestController
{
    /**
     * @Post("/inventory-assignment", name="api_inventory_assignment",  options={ "method_prefix" = false })
     *
     * @SWG\Post(
     *     path="/api/user/inventory-assignment",
     *     tags={"Inventory Assignment"},
     *     summary="Authorises user via access token and assign inventory to client",
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
     *              @SWG\Property(
     *                  property="assigned_at",
     *                  type="datetime",
     *                  description="Format: 2000-01-01 00:00:00"
     *                  ),
     *              @SWG\Property(
     *                  property="client_id",
     *                  type="integer",
     *                  ),
     *              @SWG\Property(
     *                  property="inventory_data",
     *                  type="json",
     *                  ),
     * )
     *     ),
     * @SWG\Response(
     *          response=200,
     *          description="Success",
     *      ),
     * @SWG\Response(
     *          response=401,
     *          description="Unauthorized request",
     *      ),
     * @SWG\Response(
     *          response=403,
     *          description="Access denied",
     *      ),
     * @SWG\Response(
     *          response=422,
     *          description="Validation error",
     *      ),
     * @SWG\Response(
     *          response=500,
     *          description="Internal server error",
     *      )
     * )
     *
     * @param Request $request
     * @param UtilService $utilService
     * @param ClientInventoryAssignmentService $clientInventoryAssignmentService
     * @param LoggerInterface $facilityApiLogger
     * @return JsonResponse
     */
    public function clientInventoryAssignmentAction(
        Request $request,
        UtilService $utilService,
        ClientInventoryAssignmentService $clientInventoryAssignmentService,
        LoggerInterface $facilityApiLogger
    ) {
        try {
            $data = json_decode($request->getContent(), true);
            $validRequest = $utilService->checkRequiredFieldsByRequestedData($data, FacilityInventoryEnum::INVENTORY_ASSIGNMENT_POSSIBLE_FIELDS);
            if (!$validRequest) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Some required parameters are missing.");
            }

            if (\DateTime::createFromFormat('Y-m-d H:i:s', $data['assigned_at']) === FALSE) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Invalid date format provided");
            }

            $response = $clientInventoryAssignmentService->assignInventoryToClient($data);
            if (!$response['status']) {
                return $utilService->makeResponse(Response::HTTP_NOT_FOUND, $response['message']);
            }
            return $utilService->makeResponse(Response::HTTP_OK, $response['message']);
        } catch (\Exception $exception) {
            $facilityApiLogger->error('[api_client_inventory_assignment]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Post("/client-inventory-action", name="api_client_inventory_action",  options={ "method_prefix" = false })
     *
     * @SWG\Post(
     *     path="/api/user/client-inventory-action",
     *     tags={"Inventory Assignment"},
     *     summary="Authorises user via access token and save client check-in and check-out time",
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
     *              @SWG\Property(
     *                  property="time",
     *                  type="datetime",
     *                  description="Format: 2000-01-01 00:00:00"
     *                  ),
     *              @SWG\Property(
     *                  property="type",
     *                  type="string",
     *                  description="Value should be check-in or check-out"
     *                  ),
     *              @SWG\Property(
     *                  property="client_id",
     *                  type="integer",
     *                  ),
     * )
     *     ),
     * @SWG\Response(
     *          response=200,
     *          description="Success",
     *      ),
     * @SWG\Response(
     *          response=401,
     *          description="Unauthorized request",
     *      ),
     * @SWG\Response(
     *          response=403,
     *          description="Access denied",
     *      ),
     * @SWG\Response(
     *          response=422,
     *          description="Validation error",
     *      ),
     * @SWG\Response(
     *          response=500,
     *          description="Internal server error",
     *      )
     * )
     *
     * @param Request $request
     * @param UtilService $utilService
     * @param ClientInventoryAssignmentService $clientInventoryAssignmentService
     * @param LoggerInterface $facilityApiLogger
     * @return JsonResponse
     */
    public function clientInventoryActionAction(
        Request $request,
        UtilService $utilService,
        ClientInventoryAssignmentService $clientInventoryAssignmentService,
        LoggerInterface $facilityApiLogger
    ) {
        try {
            $data = json_decode($request->getContent(), true);
            $validRequest = $utilService->checkRequiredFieldsByRequestedData($data, FacilityInventoryEnum::CLIENT_INVENTORY_ACTION_API_POSSIBLE_FIELDS);
            if (!$validRequest) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Some required parameters are missing.");
            }

            if (\DateTime::createFromFormat('Y-m-d H:i:s', $data['time']) === FALSE) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Invalid date format provided");
            }
            $response = $clientInventoryAssignmentService->saveClientInventoryAction($this->getUser(), $data);
            if (!$response['status']) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $response['message']);
            }
            return $utilService->makeResponse(Response::HTTP_OK, $response['message']);
        } catch (\Exception $exception) {
            $facilityApiLogger->error('[api_client_inventory_assignment]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }
}