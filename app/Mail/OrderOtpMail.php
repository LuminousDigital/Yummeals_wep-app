<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $orderId;

    public function __construct($otp, $orderId)
    {
        $this->otp = $otp;
        $this->orderId = $orderId;
    }

    public function build()
    {
        return $this->subject("Confirm Your Order #{$this->orderId}")
            ->view('emails.order-otp')
            ->with([
                'otp' => $this->otp,
                'orderId' => $this->orderId,
            ]);
    }
}
