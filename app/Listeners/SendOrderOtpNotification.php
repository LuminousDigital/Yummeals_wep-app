<?php

namespace App\Listeners;

use App\Events\SendOrderOtp;
use App\Mail\OrderOtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class SendOrderOtpNotification
{
    public function handle(SendOrderOtp $event)
    {
        try {
            Mail::to($event->info['email'])->send(new OrderOtpMail($event->info['otp'], $event->info['order_id']));
            // Log::error('Working:');
        } catch (Exception $e) {
            Log::error('Failed to send OTP email: ' . $e->getMessage());
        }
    }
}
