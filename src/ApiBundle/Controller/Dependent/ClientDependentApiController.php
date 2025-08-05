<?php

namespace App\ApiBundle\Controller\Dependent;

use App\ApiBundle\Service\DependentService;
use App\ApiBundle\Service\UtilService;
use App\Enum\DependentEnum;
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
 * Class ClientDependentApiController
 * @package App\ApiBundle\Controller\Dependent
 */
class ClientDependentApiController extends AbstractFOSRestController
{
    /**
     * @Get("/client/{clientId}/dependent", name="api_client_dependent_list",  options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/client/{clientId}/dependent",
     *     tags={"Dependent"},
     *     summary="Authorises user via access token and returns client dependent list",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *
     *     @SWG\Parameter(
     *         ref="#parameters/authorization"
     *     ),
     *     @SWG\Parameter(
     *         name="clientId",
     *         in="path",
     *         type="integer",
     *         required=true,
     *     ),
     *     @SWG\Parameter(
     *         name="id",
     *         in="query",
     *         type="integer",
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
     * @param $clientId
     * @param Request $request
     * @param UtilService $utilService
     * @param DependentService $dependentService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function clientDependentListAction(
        $clientId,
        Request $request,
        UtilService $utilService,
        DependentService $dependentService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $dependentId = $request->get('id', null);
            $clientDependents = $dependentService->getClientDependents($clientId, $dependentId);
            if (isset($clientDependents['status']) && $clientDependents['status'] === false) {
                return $utilService->makeResponse(Response::HTTP_NOT_FOUND, $clientDependents['message']);
            }
            return $utilService->makeResponse(Response::HTTP_OK, null, $clientDependents);
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_client_dependents_list]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $exception->getMessage());
        }
    }

    /**
     * @Post("/client/{clientId}/dependent", name="api_client_dependent_create",  options={ "method_prefix" = false })
     *
     * @SWG\Post(
     *     path="/api/user/client/{clientId}/dependent",
     *     tags={"Dependent"},
     *     summary="Authorises user via access token and create client dependents",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *
     *     @SWG\Parameter(
     *         ref="#parameters/authorization"
     *     ),
     *     @SWG\Parameter(
     *         name="clientId",
     *         in="path",
     *         type="integer",
     *         required=true,
     *         description="Client Id"
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                  property="phone",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="first_name",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="last_name",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="age",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="gender",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="parent",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="clothing_size",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="shoe_size",
     *                  type="string"
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
     * @param $clientId
     * @param Request $request
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @param DependentService $dependentService
     * @return JsonResponse
     */
    public function createClientDependentAction(
        $clientId,
        Request $request,
        UtilService $utilService,
        LoggerInterface $clientApiLogger,
        DependentService $dependentService
    ) {
        try {
            $dependentsData = json_decode($request->getContent(), true);
            if(count($dependentsData) === 0) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Invalid request.");
            }
            foreach ($dependentsData as $dependentsDatum) {
                $validRequest = $utilService->checkRequiredFieldsByRequestedData($dependentsDatum, DependentEnum::CREATE_DEPENDENT_API_REQUIRED_FIELDS);
                if (!$validRequest) {
                    return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Some required parameters are missing.");
                }
            }

            $response = $dependentService->createClientDependentsByRequestData((int)$clientId, $dependentsData);
            if ($response['status'] === false) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $response['message']);
            }
            return $utilService->makeResponse(Response::HTTP_OK, $response['message']);
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_create_client_dependent]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Put("/client/{clientId}/dependent/{id}", name="api_client_dependent_updaate",  options={ "method_prefix" = false })
     *
     * @SWG\Put(
     *     path="/api/user/client/{clientId}/dependent/{id}",
     *     tags={"Dependent"},
     *     summary="Authorises user via access token and update client dependent",
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
     *         description="Dependent Id"
     *     ),
     *     @SWG\Parameter(
     *         name="clientId",
     *         in="path",
     *         type="integer",
     *         required=true,
     *         description="Client Id"
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                  property="phone",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="first_name",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="last_name",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="age",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="gender",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="parent",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="clothing_size",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="shoe_size",
     *                  type="string"
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
     * @param $clientId
     * @param $id
     * @param Request $request
     * @param UtilService $utilService
     * @param LoggerInterface $clientApiLogger
     * @param DependentService $dependentService
     * @return JsonResponse
     */
    public function editClientDependentAction(
        $clientId,
        $id,
        Request $request,
        UtilService $utilService,
        LoggerInterface $clientApiLogger,
        DependentService $dependentService
    ) {
        try {
            $data = json_decode($request->getContent(), true);
            $validRequest = $utilService->checkIfRequestHasFieldsToUpdate($data, DependentEnum::DEPENDENT_POSSIBLE_UPDATE_FIELDS_ALL);
            if (!$validRequest) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Some parameters required.");
            }

            $response = $dependentService->updateClientDependentByRequest((int)$id, (int)$clientId, $data);
            if ($response['status'] === false) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $response['message']);
            }

            return $utilService->makeResponse(Response::HTTP_OK, $response['message']);
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_update_client_dependent]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }


    /**
     *
     * @Delete("/client/{clientId}/dependent/{id}", name="api_client_dependent_delete",  options={ "method_prefix" = false })
     *
     * @SWG\Delete(
     *     path="/api/user/client/{clientId}/dependent/{id}",
     *     tags={"Dependent"},
     *     summary="Authorises user via access token and delete a client dependent",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *
     *     @SWG\Parameter(
     *         ref="#parameters/authorization"
     *     ),
     *     @SWG\Parameter(
     *         name="clientId",
     *         in="path",
     *         type="integer",
     *         required=true,
     *         description="Client Id"
     *     ),
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         type="integer",
     *         required=true,
     *         description="Dependent Id"
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
     * @param $clientId
     * @param $id
     * @param UtilService $utilService
     * @param DependentService $dependentService
     * @param LoggerInterface $clientApiLogger
     * @return JsonResponse
     */
    public function clientDependentDeleteAction(
        $clientId,
        $id,
        UtilService $utilService,
        DependentService $dependentService,
        LoggerInterface $clientApiLogger
    ) {
        try {
            $response = $dependentService->deleteClientDependentById((int)$clientId, (int)$id);
            if(isset($response['status']) && $response['status'] === false) {
                return $utilService->makeResponse(Response::HTTP_NOT_FOUND, $response['message']);
            }
            return $utilService->makeResponse(Response::HTTP_OK, "Client dependent is deleted successfully.");
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_client_dependent_delete]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }
}