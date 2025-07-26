<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendOrderOtp
{
    use Dispatchable, SerializesModels;

    public $info;

    public function __construct(array $info)
    {
        $this->info = $info;
    }
}
