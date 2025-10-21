<?php

namespace App\Listeners;

use App\Events\ReferralSignedUp;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;
use Smartisan\Settings\Facades\Settings;

class SendReferralPushNotification
{
    public function handle(ReferralSignedUp $event): void
    {
        try {
            $referrer = User::find($event->info['referrer_id'] ?? 0);
            $referee  = User::find($event->info['referee_id'] ?? 0);

            $referralBonus = (float) Settings::group('referral')->get('signup_bonus', 10);
            $refereeBonus  = (float) Settings::group('referral')->get('referee_bonus', 0);
            $symbol        = (string) Settings::group('site')->get('site_default_currency_symbol', '$');
            $decimals      = (int) Settings::group('site')->get('site_digit_after_decimal_point', 2);

            $firebase = new FirebaseService();

            if ($referrer && (!empty($referrer->web_token) || !empty($referrer->device_token))) {
                $tokens = array_values(array_filter([$referrer->web_token, $referrer->device_token]));
                $payload = (object) [
                    'title'       => 'Referral Bonus',
                    'description' => sprintf(
                        'Great news! %s just signed up using your referral code. You earned %s%s.',
                        $referee?->name ?? 'Someone',
                        $symbol,
                        number_format($referralBonus, $decimals)
                    ),
                ];
                $firebase->sendNotification($payload, $tokens, 'referral');
            }

            if ($refereeBonus > 0 && $referee && (!empty($referee->web_token) || !empty($referee->device_token))) {
                $tokens = array_values(array_filter([$referee->web_token, $referee->device_token]));
                $payload = (object) [
                    'title'       => 'Welcome Bonus',
                    'description' => sprintf(
                        'Welcome! You received a %s%s bonus for using a referral code.',
                        $symbol,
                        number_format($refereeBonus, $decimals)
                    ),
                ];
                $firebase->sendNotification($payload, $tokens, 'referral');
            }
        } catch (\Throwable $e) {
            Log::info($e->getMessage());
        }
    }
}
