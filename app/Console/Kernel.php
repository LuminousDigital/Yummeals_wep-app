<?php

namespace App\Console;

<<<<<<< HEAD
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

=======
use App\Models\TaskLog;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Throwable;

class Kernel extends ConsoleKernel
{
    const FREQUENCY_WEEKLY = 'weekly';
    const FREQUENCY_MONTHLY = 'monthly';
    const FREQUENCY_QUARTERLY = 'quarterly';
    const FREQUENCY_YEARLY = 'yearly';

    const WEEKLY_RUN_TIME = '0:00';
    const MONTHLY_RUN_TIME = '1:00';
    const QUARTERLY_RUN_TIME = '2:00';
    const YEARLY_RUN_TIME = '3:00';

    const TASK_SUCCESS = 'success';
    const TASK_FAILURE = 'failure';

    protected function schedule(Schedule $schedule)
    {
        $this->scheduleWeeklyWinners($schedule);
        // $this->scheduleMonthlyWinners($schedule); // Uncomment if needed
        $this->scheduleQuarterlyWinners($schedule);
        $this->scheduleYearlyWinners($schedule);
    }

    protected function scheduleWeeklyWinners(Schedule $schedule): void
    {
        $this->scheduleWinnerCommand(
            $schedule,
            self::FREQUENCY_WEEKLY,
            fn($cmd) => $cmd->weeklyOn(1, self::WEEKLY_RUN_TIME)
        );
    }

    protected function scheduleMonthlyWinners(Schedule $schedule): void
    {
        $this->scheduleWinnerCommand(
            $schedule,
            self::FREQUENCY_MONTHLY,
            fn($cmd) => $cmd->monthlyOn(1, self::MONTHLY_RUN_TIME)
        );
    }

    protected function scheduleQuarterlyWinners(Schedule $schedule): void
    {
        $this->scheduleWinnerCommand(
            $schedule,
            self::FREQUENCY_QUARTERLY,
            fn($cmd) => $cmd->cron('0 '.explode(':', self::QUARTERLY_RUN_TIME)[0].' 1 1,4,7,10 *')
        );
    }

    protected function scheduleYearlyWinners(Schedule $schedule): void
    {
        $this->scheduleWinnerCommand(
            $schedule,
            self::FREQUENCY_YEARLY,
            fn($cmd) => $cmd->yearlyOn(1, 1, self::YEARLY_RUN_TIME)
        );
    }

    protected function scheduleWinnerCommand(
        Schedule $schedule,
        string $type,
        callable $scheduleMethod
    ): void {
        $command = $schedule->command('coupon:assign-winners --type='.$type);
        $scheduleMethod($command)
            ->withoutOverlapping()
            ->onSuccess(function () use ($type) {
                $this->logTaskSuccess($type);
            })
            ->onFailure(function (Throwable $exception) use ($type) {
                $this->logTaskFailure($type, $exception);
            });
    }

    protected function logTaskSuccess(string $type): void
    {
        TaskLog::create([
            'task_name' => 'coupon:assign-winners --type='.$type,
            'status' => self::TASK_SUCCESS,
            'message' => ucfirst($type).' coupon assignment completed successfully.',
            'run_at' => now(),
        ]);
    }

    protected function logTaskFailure(string $type, Throwable $exception): void
    {
        TaskLog::create([
            'task_name' => 'coupon:assign-winners --type='.$type,
            'status' => self::TASK_FAILURE,
            'message' => $exception->getMessage(),
            'run_at' => now(),
        ]);
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
>>>>>>> d38913bcf1d8d577a7729a1b02ad0194e20e5551
        require base_path('routes/console.php');
    }
}
