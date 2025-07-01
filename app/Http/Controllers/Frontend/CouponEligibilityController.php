<?php

namespace App\Http\Controllers;

use App\Services\MealCouponService;
use Exception;
use Illuminate\Http\Request;

class CouponEligibilityController extends Controller
{
    protected MealCouponService $mealCouponService;

    public function __construct(MealCouponService $mealCouponService)
    {
        $this->mealCouponService = $mealCouponService;
    }

    public function check(Request $request)
    {
        try {
            $result = $this->mealCouponService->handleOrderCoupon(auth()->user());
            if ($result) {
                return response()->json([
                    'status' => true,
                    'message' => 'Eligible for free meal',
                    'coupon_code' => $result['coupon']->code,
                    'eligibility_type' => $result['type'],
                ]);
            }
            return response()->json(['status' => false, 'message' => 'Not eligible']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
