<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Smartisan\Settings\Facades\Settings;

class ReferralSettingsSeeder extends Seeder
{
    public function run()
    {
        Settings::group('referral')->set([
            'signup_bonus' => 10.00,
            'referee_bonus' => 5.00,
            'minimum_withdrawal' => 10.00,
            'is_active' => true,
            'require_kyc' => false,
            'tiers' => [
                ['level' => 1, 'percentage' => 100],
                ['level' => 2, 'percentage' => 20],
                ['level' => 3, 'percentage' => 10]
            ]
        ]);
    }
}