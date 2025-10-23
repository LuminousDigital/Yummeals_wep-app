<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CouponNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $isAdmin;

    public function __construct(array $data, bool $isAdmin = false)
    {
        $this->data = $data;
        $this->isAdmin = $isAdmin;
    }

    public function build()
    {
        return $this->subject($this->isAdmin ? 'New Coupon Winner' : 'Congratulations! You Won a Coupon')
                   ->view('emails.couponNotification')
                   ->with([
                       'data' => $this->data,
                       'isAdmin' => $this->isAdmin,
                   ]);
    }
}
