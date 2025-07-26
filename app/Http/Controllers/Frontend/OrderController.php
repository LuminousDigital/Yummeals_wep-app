<?php

namespace App\Http\Controllers\Frontend;


use App\Http\Resources\UserOrderResource;
use Exception;
use App\Models\FrontendOrder;
use App\Http\Requests\OrderRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Requests\PaginateRequest;
use App\Services\FrontendOrderService;
use App\Services\OtpManagerService;
use App\Http\Requests\OrderStatusRequest;
use App\Http\Resources\OrderDetailsResource;
use App\Events\SendOrderOtp;

class OrderController extends Controller
{
    private FrontendOrderService $frontendOrderService;
    private OtpManagerService $otpManagerService;

    public function __construct(FrontendOrderService $frontendOrderService, OtpManagerService $otpManagerService)
    {
        $this->frontendOrderService = $frontendOrderService;
        $this->otpManagerService = $otpManagerService;
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return UserOrderResource::collection($this->frontendOrderService->myOrder($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(OrderRequest $request): \Illuminate\Http\Response | OrderDetailsResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new OrderDetailsResource($this->frontendOrderService->myOrderStore($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(FrontendOrder $frontendOrder): \Illuminate\Http\Response|OrderDetailsResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {

        try {
            return new OrderDetailsResource($this->frontendOrderService->show($frontendOrder));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changeStatus(FrontendOrder $frontendOrder, OrderStatusRequest $request): \Illuminate\Http\Response | OrderDetailsResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new OrderDetailsResource($this->frontendOrderService->changeStatus($frontendOrder, $request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function resendOtp(FrontendOrder $order)
    {
        $otpData = $this->otpManagerService->generateOrderOtp($order);

        $order->update([
            'otp_code' => $otpData['otp_code'],
            'otp_expires_at' => $otpData['otp_expires_at'],
        ]);

        SendOrderOtp::dispatch([
            'email' => $order->user->email,
            'otp'   => $otpData['otp_code'],
            'order_id' => $order->id
        ]);

        return response(['status' => true, 'message' => 'OTP resent successfully.']);
    }
}
