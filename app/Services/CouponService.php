<?php

namespace App\Services;



use App\Models\OrderCoupon;
use Carbon\Carbon;
use Exception;
use App\Models\Coupon;
use App\Libraries\AppLibrary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\CouponRequest;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\CouponCheckRequest;

class CouponService
{
    public $coupon;
    protected $couponFilter = [
        'name',
        'code',
        'discount',
        'discount_type',
        'start_date',
        'end_date',
        'minimum_order',
        'maximum_discount',
        'limit_per_user',
    ];

    protected $exceptFilter = [
        'excepts'
    ];

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'desc';

            return Coupon::where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->couponFilter)) {
                        if ($key == "start_date") {
                            $start_date  = Date('Y-m-d', strtotime($request));
                            $query->whereDate($key, '=', $start_date);
                        } else if ($key == "end_date") {
                            $end_date  = Date('Y-m-d', strtotime($request));
                            $query->whereDate($key, '=', $end_date);
                        } else {
                            $query->where($key, 'like', '%' . $request . '%');
                        }
                    }

                    if (in_array($key, $this->exceptFilter)) {
                        $explodes = explode('|', $request);
                        if (is_array($explodes)) {
                            foreach ($explodes as $explode) {
                                $query->where('id', '!=', $explode);
                            }
                        }
                    }
                }
            })->orderBy($orderColumn, $orderType)->$method(
                $methodValue
            );
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function store(CouponRequest $request)
    {
        try {
            $this->coupon = Coupon::create([
                'name'             => $request->name,
                'description'      => $request->description,
                'code'             => $request->code,
                'discount'         => $request->discount,
                'discount_type'    => $request->discount_type,
                'start_date'       => !blank($request->start_date) ? date(
                    'Y-m-d H:i:s',
                    strtotime($request->start_date)
                ) : "",
                'end_date'         => !blank($request->end_date) ? date(
                    'Y-m-d H:i:s',
                    strtotime($request->end_date)
                ) : "",
                'minimum_order'    => $request->minimum_order,
                'maximum_discount' => $request->maximum_discount,
                'limit_per_user'   => $request->limit_per_user,
            ]);
            if ($request->image) {
                $this->coupon->addMedia($request->image)->toMediaCollection('coupon');
            }
            return $this->coupon;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update(CouponRequest $request, Coupon $coupon)
    {
        try {
            DB::transaction(function () use ($request, $coupon) {
                $this->coupon             = $coupon;
                $coupon->name             = $request->name;
                $coupon->description      = $request->description;
                $coupon->code             = $request->code;
                $coupon->discount         = $request->discount;
                $coupon->discount_type    = $request->discount_type;
                $coupon->start_date       = !blank($request->start_date) ? date(
                    'Y-m-d H:i:s',
                    strtotime($request->start_date)
                ) : null;
                $coupon->end_date         = !blank($request->end_date) ? date(
                    'Y-m-d H:i:s',
                    strtotime($request->end_date)
                ) : null;
                $coupon->minimum_order    = $request->minimum_order;
                $coupon->maximum_discount = $request->maximum_discount;
                $coupon->limit_per_user   = $request->limit_per_user;
                $coupon->save();
                if ($request->image) {
                    $coupon->media()->delete();
                    $coupon->addMedia($request->image)->toMediaCollection('coupon');
                }
            });
            return $this->coupon;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function destroy(Coupon $coupon)
    {
        try {
            $coupon->delete();
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function show(Coupon $coupon): Coupon
    {
        try {
            return $coupon;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function couponDateWise(): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return Coupon::all()->filter(function ($item) {
                if (Carbon::now()->between($item->start_date, $item->end_date)) {
                    return $item;
                }
            });
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    //     public function couponChecking(CouponCheckRequest $request)
    //     {
    //         try {
    //             $coupon = Coupon::where(['code' => $request->code])->first();
    //             if ($coupon) {
    //                 if ($coupon->minimum_order > $request->total) {
    //                     throw new Exception(trans('all.message.minimum_order_amount') . AppLibrary::currencyAmountFormat($coupon->minimum_order), 422);
    //                 } else {
    //                     if (strtotime($coupon->end_date) >= strtotime(Carbon::now())) {
    //                         $ordered_coupon_count = OrderCoupon::where(['user_id' => auth()->user()->id, 'coupon_id' => $coupon->id])->count();
    //                         if ($coupon->limit_per_user <= $ordered_coupon_count) {
    //                             throw new Exception(trans('all.message.coupon_limit_exceeded'), 422);
    //                         }
    //                         return $coupon;
    //                     } else {
    //                         throw new Exception(trans('all.message.coupon_date_expired'), 422);
    //                     }
    //                 }
    //             } else {
    //                 throw new Exception(trans('all.message.coupon_not_exist'), 422);
    //             }
    //         } catch (Exception $exception) {
    //             Log::info($exception->getMessage());
    //             throw new Exception($exception->getMessage(), 422);
    //         }
    //     }
    // }
    // public function couponChecking(CouponCheckRequest $request)
    // {
    //     try {
    //         $coupon = Coupon::where(['code' => $request->code])->first();
    //         if ($coupon) {
    //             if ($coupon->minimum_order > $request->total) {
    //                 throw new Exception(trans('all.message.minimum_order_amount') . AppLibrary::currencyAmountFormat($coupon->minimum_order), 422);
    //             } else {
    //                 if (strtotime($coupon->end_date) >= strtotime(Carbon::now())) {
    //                     $ordered_coupon_count = OrderCoupon::where(['user_id' => auth()->user()->id, 'coupon_id' => $coupon->id])->count();
    //                     if ($coupon->limit_per_user <= $ordered_coupon_count) {
    //                         throw new Exception(trans('all.message.coupon_limit_exceeded'), 422);
    //                     }
    //                     return $coupon;
    //                 } else {
    //                     throw new Exception(trans('all.message.coupon_date_expired'), 422);
    //                 }
    //             }
    //         } else {
    //             throw new Exception(trans('all.message.coupon_not_exist'), 422);
    //         }
    //     } catch (Exception $exception) {
    //         Log::info($exception->getMessage());
    //         throw new Exception($exception->getMessage(), 422);
    //     }
    // }

    public function couponChecking(CouponCheckRequest $request)
    {
        try {
            $coupons = Coupon::where('code', $request->code)
                ->orderBy('start_date', 'desc')
                ->get();

            if ($coupons->isEmpty()) {
                throw new Exception('Coupon does not exist', 422);
            }

            $validCoupon = null;

            foreach ($coupons as $coupon) {
                if (strtotime($coupon->start_date) > time() || strtotime($coupon->end_date) < time()) {
                    continue;
                }

                // Check minimum order requirement
                if ($coupon->minimum_order > $request->total) {
                    continue;
                }

                // Check usage limits
                $usedTotal = OrderCoupon::where('user_id', auth()->id())
                    ->where('coupon_id', $coupon->id)
                    ->count();

                $usedToday = OrderCoupon::where('user_id', auth()->id())
                    ->where('coupon_id', $coupon->id)
                    ->whereDate('created_at', date('Y-m-d'))
                    ->count();

                if ($coupon->limit_per_user && $usedTotal >= $coupon->limit_per_user) {
                    continue;
                }

                if ($coupon->daily_limit_per_user && $usedToday >= $coupon->daily_limit_per_user) {
                    continue;
                }

                // Check winning coupon eligibility
                if ($coupon->is_winning_coupon && $coupon->user_id !== auth()->id()) {
                    continue;
                }

                $validCoupon = $coupon;
                break; // Use the first valid coupon we find
            }

            if (!$validCoupon) {
                // Determine which specific validation failed
                $coupon = $coupons->first();
                if ($coupon->minimum_order > $request->total) {
                    throw new Exception('Minimum order not met', 422);
                }
                if (strtotime($coupon->end_date) < time()) {
                    throw new Exception('Coupon expired', 422);
                }
                if ($coupon->is_winning_coupon && $coupon->user_id !== auth()->id()) {
                    throw new Exception('Not eligible for this winning coupon', 422);
                }
                throw new Exception('Coupon usage limits exceeded', 422);
            }

            return $validCoupon;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw $e;
        }
    }
}
