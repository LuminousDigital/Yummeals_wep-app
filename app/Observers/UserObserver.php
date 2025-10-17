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
    }

    public function updating(User $user)
    {
        $user->branch_id = $this->setBranch($user->branch_id);
    }
}
