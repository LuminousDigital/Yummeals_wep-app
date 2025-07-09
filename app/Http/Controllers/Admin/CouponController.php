<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CouponExport;
use Exception;
use App\Services\CouponService;
use App\Http\Requests\CouponRequest;
use App\Http\Requests\PaginateRequest;
<<<<<<< HEAD
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use Maatwebsite\Excel\Facades\Excel;
=======
use App\Http\Resources\CouponCheckResource;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\MealCouponService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
>>>>>>> d38913bcf1d8d577a7729a1b02ad0194e20e5551


class CouponController extends AdminController
{

    private CouponService $couponService;
<<<<<<< HEAD

    public function __construct(CouponService $coupon)
    {
        parent::__construct();
        $this->couponService = $coupon;
=======
    private MealCouponService $mealCouponService;

    public function __construct(CouponService $coupon, MealCouponService $mealCouponService)
    {
        parent::__construct();
        $this->couponService = $coupon;
        $this->mealCouponService = $mealCouponService;
>>>>>>> d38913bcf1d8d577a7729a1b02ad0194e20e5551
        $this->middleware(['permission:coupons'])->only('index', 'export');
        $this->middleware(['permission:coupons_create'])->only('store');
        $this->middleware(['permission:coupons_edit'])->only('update');
        $this->middleware(['permission:coupons_delete'])->only('destroy');
        $this->middleware(['permission:coupons_show'])->only('show');
    }

    public function index(PaginateRequest $request
    ) : \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return CouponResource::collection($this->couponService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(CouponRequest $request) : CouponResource | \Illuminate\Http\Response
    {
        try {
            return new CouponResource($this->couponService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function show(Coupon $coupon
    ) : CouponResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new CouponResource($this->couponService->show($coupon));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function update(
        CouponRequest $request,
        Coupon $coupon
    ) : CouponResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new CouponResource($this->couponService->update($request, $coupon));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Coupon $coupon
    ) : \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            $this->couponService->destroy($coupon);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request
    ) : \Illuminate\Http\Response | \Symfony\Component\HttpFoundation\BinaryFileResponse | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return Excel::download(new CouponExport($this->couponService, $request), 'Coupons.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
<<<<<<< HEAD
=======

    // public function checkBonusEligibility(Request $request)
    // {
    //     try {
    //         $user = auth()->user();
    //         if (!$user) {
    //             throw new Exception('User not authenticated', 401);
    //         }

    //         $result = $this->mealCouponService->handleOrderCoupon($user);

    //         if ($result['status']) {
    //             return response()->json([
    //                 'status' => true,
    //                 'message' => $result['message'],
    //                 'coupon_code' => $result['coupon']->code,
    //                 'eligibility_type' => $result['type'],
    //             ]);
    //         }

    //         return response()->json([
    //             'status' => false,
    //             'message' => $result['message']
    //         ], $result['code'] ?? 400);

    //     } catch (Exception $e) {
    //         Log::error('Coupon eligibility check failed: ' . $e->getMessage());
    //         return response()->json([
    //             'status' => false,
    //             'message' => $e->getMessage()
    //         ], $e->getCode() ?: 500);
    //     }
    // }

    public function checkBonusEligibility(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                throw new Exception('User not authenticated', 401);
            }

            $result = $this->mealCouponService->handleOrderCoupon($user);

            if ($result['status']) {
                return response()->json([
                    'status' => true,
                    'message' => $result['message'],
                    'coupon_code' => $result['coupon']->code,
                    'eligibility_type' => $result['type'],
                ]);
            }

            // Modified response when coupon exists
            if (isset($result['coupon'])) {
                return response()->json([
                    'status' => false,
                    'message' => $result['message'],
                    'existing_coupon' => new CouponCheckResource($result["coupon"])
                ], $result['code'] ?? 400);
            }

            return response()->json([
                'status' => false,
                'message' => $result['message']
            ], $result['code'] ?? 400);

        } catch (Exception $e) {
            Log::error('Coupon eligibility check failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }
>>>>>>> d38913bcf1d8d577a7729a1b02ad0194e20e5551
}
