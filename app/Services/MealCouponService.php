<?php

namespace App\Services;

use App\Events\CouponWon;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Enums\TaxType;
use Illuminate\Support\Facades\DB;

class MealCouponService
{
    const FREE_MEAL_PREFIX = 'FREEMEAL';
    const WEEKLY_WINNER_PREFIX = 'WEEKWINNER';
    const MONTHLY_WINNER_PREFIX = 'MONTHWINNER';
    const LEGENDARY_PREFIX = 'LEGEND365';

    const FREE_MEAL_DISCOUNT = 100;
    const FREE_MEAL_VALID_MINUTES = 30;
    const WEEKLY_WINNER_VALID_DAYS = 2;
    const MONTHLY_WINNER_VALID_DAYS = 30;
    const LEGENDARY_VALID_DAYS = 365;

    public function handleOrderCoupon(User $user): array
    {
        DB::beginTransaction();
        try {
            $timezone = config('app.timezone');
            $today = now($timezone)->toDateString();

            $count = Order::where('user_id', $user->id)
                ->whereDate('created_at', $today)
                ->lockForUpdate()
                ->count();

            $existingCoupon = Coupon::where('user_id', $user->id)
                ->where('is_winning_coupon', true)
                ->whereDate('created_at', $today)
                ->where('end_date', '>', now($timezone))
                ->lockForUpdate()
                ->first();

            if ($existingCoupon) {
                DB::commit();
                return [
                    'status' => false,
                    'message' => 'You already have an active coupon for today',
                    'coupon' => $existingCoupon
                ];
            }

            if ($count === 0) {
                $dailySuffix = now()->format('Ymd');
                $c = $this->createFreeCoupon($user, self::FREE_MEAL_PREFIX . '-1');
                DB::commit();
                return [
                    'status' => true,
                    'coupon' => $c,
                    'type' => 'first_order',
                    'message' => 'First order coupon generated'
                ];
            }

            if ($count === 9 && rand(0, 1)) {
                $dailySuffix = now()->format('Ymd');
                $c = $this->createFreeCoupon($user, self::FREE_MEAL_PREFIX . '-10');
                DB::commit();
                return [
                    'status' => true,
                    'coupon' => $c,
                    'type' => 'tenth_order',
                    'message' => 'Tenth order bonus coupon generated'
                ];
            }

            DB::commit();
            return [
                'status' => false,
                'message' => 'Not eligible for any coupons at this time',
                'code' => 400
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to handle order coupon: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => 'An error occurred while processing your coupon request',
                'code' => 500
            ];
        }
    }

    protected function createFreeCoupon(User $user, string $code): Coupon
    {
        try {
            $coupon = Coupon::create([
                'user_id' => $user->id,
                'name' => 'Free Meal',
                'code' => $code,
                'discount' => self::FREE_MEAL_DISCOUNT,
                'discount_type' => TaxType::PERCENTAGE,
                'start_date' => now(),
                'end_date' => now()->addMinutes(self::FREE_MEAL_VALID_MINUTES),
                'limit_per_user' => 1,
                'daily_limit_per_user' => 1,
                'is_reusable' => false,
                'is_winning_coupon' => true,
            ]);

            event(new CouponWon($user, [
                'coupon_type' => 'Free Meal',
                'coupon_code' => $code,
                'valid_until' => $coupon->end_date->format('Y-m-d H:i'),
            ]));

            return $coupon;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw $e;
        }
    }

    public function assignWeeklyWinnerCoupon(User $u): void
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = $now->month;

        $firstDayOfMonth = $now->copy()->startOfMonth();
        $weekOfMonth = intval(ceil(($now->day + $firstDayOfMonth->dayOfWeek) / 7));

        $code = self::WEEKLY_WINNER_PREFIX . "-{$weekOfMonth}";

        $this->assignWinnerIfNotExists(
            $u,
            'Weekly Winner',
            $code,
            self::WEEKLY_WINNER_VALID_DAYS,
            3,
            6,
            $now->copy()->startOfWeek(),
            $now->copy()->endOfWeek()
        );
    }


    public function assignMonthlyWinnerCoupon(User $u): void
    {
        $now = Carbon::now();
        $monthAbbr = strtoupper($now->format('M'));
        $year = $now->year;
        $code = self::MONTHLY_WINNER_PREFIX . "-{$monthAbbr}";

        $this->assignWinnerIfNotExists(
            $u,
            'Monthly Winner',
            $code,
            self::MONTHLY_WINNER_VALID_DAYS,
            1,
            30,
            $now->copy()->startOfMonth(),
            $now->copy()->endOfMonth()
        );
    }

    public function assignAllTimeWinnerCoupon(User $u): void
    {
        $now = Carbon::now();
        $year = $now->year;
        $code = self::LEGENDARY_PREFIX . '-' .  $year;

        $this->assignWinnerIfNotExists(
            $u,
            'Legendary Winner',
            $code,
            self::LEGENDARY_VALID_DAYS,
            1,
            365,
            $now->copy()->startOfYear(),
            $now->copy()->endOfYear()
        );
    }

    // protected function assignWinnerIfNotExists(
    //     User $u,
    //     string $name,
    //     string $code,
    //     int $validDays,
    //     int $daily,
    //     int $total,
    //     Carbon $start,
    //     Carbon $end
    // ): void {
    //     try {
    //         if (Coupon::where('user_id', $u->id)
    //             ->where('is_winning_coupon', true)
    //             ->where('name', $name)
    //             ->whereBetween('created_at', [$start, $end])
    //             ->exists()
    //         ) {
    //             return;
    //         }

    //         $coupon = Coupon::create([
    //             'user_id' => $u->id,
    //             'name' => $name,
    //             'code' => $code,
    //             'discount' => 100,
    //             'discount_type' => TaxType::PERCENTAGE,
    //             'start_date' => now(),
    //             'end_date' => now()->addDays($validDays),
    //             'limit_per_user' => $total,
    //             'daily_limit_per_user' => $daily,
    //             'is_reusable' => true,
    //             'is_winning_coupon' => true,
    //         ]);

    //         event(new CouponWon($u, [
    //             'coupon_type' => $name,
    //             'coupon_code' => $code,
    //             'discount' => $coupon->discount,
    //             'valid_until' => $coupon->end_date->format('Y-m-d H:i'),
    //         ]));
    //     } catch (Exception $e) {
    //         Log::error("assignWinner error: {$e->getMessage()}");
    //     }
    // }

    protected function assignWinnerIfNotExists(
        User $u,
        string $name,
        string $code,
        int $validDays,
        int $daily,
        int $total,
        Carbon $start,
        Carbon $end
    ): void {
        try {
            $existingCoupon = Coupon::where('user_id', $u->id)
                ->where('is_winning_coupon', true)
                ->where('name', $name)
                ->whereBetween('created_at', [$start, $end])
                ->first();

            if ($existingCoupon) {
                Log::info("User {$u->id} already has a {$name} coupon for this period");
                return;
            }

            DB::beginTransaction();

            $coupon = Coupon::create([
                'user_id' => $u->id,
                'name' => $name,
                'code' => $code,
                'discount' => 100,
                'discount_type' => TaxType::PERCENTAGE,
                'start_date' => now(),
                'end_date' => now()->addDays($validDays),
                'limit_per_user' => $total,
                'daily_limit_per_user' => $daily,
                'is_reusable' => true,
                'is_winning_coupon' => true,
            ]);

            event(new CouponWon($u, [
                'coupon_type' => $name,
                'coupon_code' => $code,
                'discount' => $coupon->discount,
                'valid_until' => $coupon->end_date->format('Y-m-d H:i'),
            ]));

            DB::commit();

            Log::info("Successfully assigned {$name} coupon to user {$u->id}");
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("assignWinner error for user {$u->id}: {$e->getMessage()}");
        }
    }
}
