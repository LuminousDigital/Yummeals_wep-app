<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendSmsCode
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $info;
    public function __construct($info)
    {
        $this->info = $info;
    }
}


// namespace App\Events;

// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Broadcasting\InteractsWithSockets;
// use Illuminate\Foundation\Events\Dispatchable;
// use Illuminate\Queue\SerializesModels;
// use Illuminate\Support\Facades\Log;

// class SendSmsCode implements ShouldQueue
// {
//     use Dispatchable, InteractsWithSockets, SerializesModels;

//     public $info;

//     public function __construct($info)
//     {
//         $this->info = $info;
//     }

//     public function failed(\Throwable $exception)
// {
//     Log::error('SMS sending failed', [
//         'error' => $exception->getMessage(),
//         'info' => $this->info
//     ]);
// }
// }
