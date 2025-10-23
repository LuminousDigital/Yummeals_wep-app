<?php

namespace App\Observers;

use App\Enums\Ask;
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
        // Ensure referral code exists on creation but skip for guests
        if (empty($user->referral_code) && (int) $user->is_guest !== Ask::YES) {
            $base = $user->username ?: ($user->name ?: 'user');
            $user->referral_code = User::generateUniqueReferralCode($base);
        }
    }

    public function updating(User $user)
    {
        $user->branch_id = $this->setBranch($user->branch_id);
        // Ensure referral code exists on update but skip for guests
        if (empty($user->referral_code) && (int) $user->is_guest !== Ask::YES) {
            $base = $user->username ?: ($user->name ?: 'user');
            $user->referral_code = User::generateUniqueReferralCode($base);
        }
    }
}
