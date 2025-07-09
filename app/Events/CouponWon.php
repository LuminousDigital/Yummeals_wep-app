<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class CouponWon
{
    use Dispatchable, SerializesModels;

    public $user;
    public $couponData;

    public function __construct(User $user, array $couponData)
    {
        $this->user = $user;
        $this->couponData = array_merge($couponData, [
            'username' => $user->username,
            'email' => $user->email
        ]);
    }
}
