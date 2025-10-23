<?php

namespace App\Observers;

use App\Models\User;
use App\Traits\DefaultAccessModelTrait;
use Illuminate\Support\Str;

class UserObserver
{
    use DefaultAccessModelTrait;

    public function creating(User $user)
    {
        $user->branch_id = $this->setBranch($user->branch_id);
        if (empty($user->password)) {
            $user->password = bcrypt(Str::random(24));
        }
        // Ensure referral code exists on creation (e.g., social signup path)
        if (empty($user->referral_code)) {
            $base = $user->username ?: ($user->name ?: 'user');
            $user->referral_code = User::generateUniqueReferralCode($base);
        }
    }

    public function updating(User $user)
    {
        $user->branch_id = $this->setBranch($user->branch_id);
        // Ensure referral code exists on update (e.g., social upsert path)
        if (empty($user->referral_code)) {
            $base = $user->username ?: ($user->name ?: 'user');
            $user->referral_code = User::generateUniqueReferralCode($base);
        }
    }
}
