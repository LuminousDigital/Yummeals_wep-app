<?php

namespace App\Listeners;

use App\Events\CouponWon;
use App\Mail\CouponNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendCouponNotifications implements ShouldQueue
{
    public function handle(CouponWon $event)
    {
        // Send to winner
        Mail::to($event->user->email)->send(
            new CouponNotification($event->couponData)
        );

        // Send to admin
        Mail::to(config('mail.admin_address'))->send(
            new CouponNotification($event->couponData, true)
        );
    }
}
