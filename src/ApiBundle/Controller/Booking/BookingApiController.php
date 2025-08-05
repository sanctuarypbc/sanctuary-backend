<?php

namespace App\ApiBundle\Controller\Booking;

use App\ApiBundle\Service\BookingService;
use App\ApiBundle\Service\UtilService;
use App\Entity\Facility;
use App\Enum\ClientEnum;
use App\Enum\CommonEnum;
use App\Enum\UserEnum;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Get;
use Swagger\Annotations as SWG;

/**
 * Class BookingApiController
 * @package App\ApiBundle\Controller\Booking
 */
class BookingApiController extends AbstractController
{
    /**
     * @Get(methods={"GET"}, path="/booking", options={ "method_prefix" = false })
     *
     * @SWG\Get(
     *     path="/api/user/booking",
     *     tags={"Booking"},
     *     summary="Get Booking list",
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
     *      ),
     *      @SWG\Parameter(
     *         name="facility_user_id",
     *         in="query",
     *         type="integer",
     *         required=false,
     *         description="Facility user id"
     *      ),
     *     @SWG\Parameter(
     *         name="from_date",
     *         in="query",
     *         type="string",
     *         description="Format: yyyy-mm-dd",
     *         required=false,
     *     ),
     *     @SWG\Parameter(
     *         name="to_date",
     *         in="query",
     *         type="string",
     *         description="Format: yyyy-mm-dd",
     *         required=false,
     *     ),
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
     *      ),
     *     @SWG\Parameter(
     *          name="search",
     *          in="query",
     *          type="string",
     *          required=false,
     *          description="Search user first and last name"
     *      )
     * )
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getBookingAction(
        Request $request,
        UtilService $utilService,
        BookingService $bookingService,
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

            $response = $bookingService->getBookingOfClient($this->getUser(), $data);
            if ($response['status'] === false) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $response['message']);
            }
            return $utilService->makeResponse(Response::HTTP_OK, null, $response);
        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_booking]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }

    /**
     * @Post(methods={"POST"}, path="/booking",options={ "method_prefix" = false })
     *
     * @SWG\Post(
     *     path="/api/user/booking",
     *     tags={"Booking"},
     *     summary="Authorises user via access token add booking",
     *     @SWG\Parameter(
     *         ref="#parameters/authorization"
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                  property="client_id",
     *                  type="integer",
     *                  ),
     *             @SWG\Property(
     *                  property="room_number",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="facility_inventory_type_id",
     *                  type="integer",
     *                  ),
     *             @SWG\Property(
     *                  property="notes",
     *                  type="string",
     *                  ),
     *             @SWG\Property(
     *                  property="check_in",
     *                  type="datetime",
     *                  ),
     *             @SWG\Property(
     *                  property="check_out",
     *                  type="datetime",
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
     * @param LoggerInterface $clientApiLogger
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createBookingAction(
        Request $request,
        UtilService $utilService,
        LoggerInterface $clientApiLogger,
        BookingService $bookingService
    ) {
        try {
            $data = json_decode($request->getContent(), true);
            $validRequest = $utilService->checkRequiredFieldsByRequestedData($data, ClientEnum::CREATE_BOOKING_API_REQUIRED_FIELDS);

            if (!$validRequest) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Some required parameters are missing.");
            }

            if ((!empty($data['check_in']) && \DateTime::createFromFormat('Y-m-d H:i:s', $data['check_in']) === FALSE) ||
                (!empty($data['check_out']) && \DateTime::createFromFormat('Y-m-d H:i:s', $data['check_out']) === FALSE)) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Invalid date format provided");
            }

            if (!$this->getUser()->getFacility() instanceof Facility) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Facility against this user doesn't exist.");
            }

            $response = $bookingService->createBookFacility($data);
            if ($response['status'] === false) {
                return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, $response['message']);
            }

            return $utilService->makeResponse(Response::HTTP_OK, "Booking has been created successfully");

        } catch (\Exception $exception) {
            $clientApiLogger->error('[api_booking]: ' . $exception->getMessage());
            return $utilService->makeResponse(Response::HTTP_INTERNAL_SERVER_ERROR, "Something went wrong.");
        }
    }
}