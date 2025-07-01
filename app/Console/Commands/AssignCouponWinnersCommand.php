<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\MealCouponService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AssignCouponWinnersCommand extends Command
{
    protected $signature = 'coupon:assign-winners {--type=} {--from=} {--to=}';
    protected $description = 'Assign winning coupons per period';

    const PERIOD_WEEKLY = 'weekly';
    const PERIOD_MONTHLY = 'monthly';
    const PERIOD_QUARTERLY = 'quarterly';
    const PERIOD_YEARLY = 'yearly';
    const PERIOD_CUSTOM = 'custom';

    const TOP_WINNERS_COUNT = 1;

    protected MealCouponService $mealCouponService;

    public function __construct(MealCouponService $mealCouponService)
    {
        parent::__construct();
        $this->mealCouponService = $mealCouponService;
    }

    public function handle()
    {
        try {
            $type = strtolower($this->option('type'));
            $now = Carbon::now();

            switch ($type) {
                case self::PERIOD_WEEKLY:
                    $this->process($now->startOfWeek(), $now->endOfWeek(), self::PERIOD_WEEKLY);
                    break;
                case self::PERIOD_MONTHLY:
                    $this->process($now->startOfMonth(), $now->endOfMonth(), self::PERIOD_MONTHLY);
                    break;
                case self::PERIOD_QUARTERLY:
                    $this->process($now->firstOfQuarter(), $now->endOfQuarter(), self::PERIOD_QUARTERLY);
                    break;
                case self::PERIOD_YEARLY:
                    $this->process($now->startOfYear(), $now->endOfYear(), self::PERIOD_YEARLY);
                    break;
                case self::PERIOD_CUSTOM:
                    $f = $this->option('from');
                    $t = $this->option('to');
                    if (!$f || !$t) return $this->error('Provide --from and --to dates');
                    $this->process(Carbon::parse($f)->startOfDay(), Carbon::parse($t)->endOfDay(), self::PERIOD_CUSTOM);
                    break;
                default:
                    return $this->error('Type: weekly|monthly|quarterly|yearly|custom');
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $this->error($e->getMessage());
        }
    }

    protected function process($start, $end, $type): void
    {
        try {
            $top = User::withCount(['referrals' => fn($q) => $q->whereBetween('created_at', [$start, $end])])
                ->orderByDesc('referrals_count')
                ->take(self::TOP_WINNERS_COUNT)
                ->get();

            foreach ($top as $user) {
                match ($type) {
                    self::PERIOD_WEEKLY => $this->mealCouponService->assignWeeklyWinnerCoupon($user),
                    self::PERIOD_MONTHLY => $this->mealCouponService->assignMonthlyWinnerCoupon($user),
                    default => $this->mealCouponService->assignAllTimeWinnerCoupon($user),
                };
                $this->info("{$type} coupon assigned to: {$user->email}");
            }
        } catch (Exception $e) {
            Log::error("process {$type} error: {$e->getMessage()}");
            $this->error("Error in {$type} assignment");
        }
    }
}
