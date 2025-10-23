<?php

namespace App\Listeners;

use App\Events\ReferralSignedUp;
use App\Models\User;
use App\Services\NotificationSenderService;
use Illuminate\Support\Facades\Log;
use Smartisan\Settings\Facades\Settings;

class SendReferralPushNotification
{
    public function handle(ReferralSignedUp $event): void
    {
        try {
            Log::info('[ReferralSignedUp] received', $event->info);
            $referrer = User::find($event->info['referrer_id'] ?? 0);
            $referee  = User::find($event->info['referee_id'] ?? 0);
            Log::info('[ReferralSignedUp] context', [
                'referrer_id' => $referrer?->id,
                'referee_id'  => $referee?->id,
                'referrer_total_referrals' => $referrer?->total_referrals,
            ]);

            $referralBonus = (float) Settings::group('referral')->get('signup_bonus', 10);
            $refereeBonus  = (float) Settings::group('referral')->get('referee_bonus', 0);
            $symbol        = (string) Settings::group('site')->get('site_default_currency_symbol', '$');
            $decimals      = (int) Settings::group('site')->get('site_digit_after_decimal_point', 2);

            $sender = app(NotificationSenderService::class);

            if ($referrer) {
                Log::info('[ReferralSignedUp] notify referrer', ['referrer_id' => $referrer->id]);
                $sender->sendToUser(
                    $referrer,
                    'Referral Bonus',
                    sprintf(
                        'Great news! %s just signed up using your referral code. You earned %s%s.',
                        $referee?->name ?? 'Someone',
                        $symbol,
                        number_format($referralBonus, $decimals)
                    ),
                    null,
                    'referral'
                );
            }

            if ($refereeBonus > 0 && $referee) {
                Log::info('[ReferralSignedUp] notify referee', ['referee_id' => $referee->id]);
                $sender->sendToUser(
                    $referee,
                    'Welcome Bonus',
                    sprintf(
                        'Welcome! You received a %s%s bonus for using a referral code.',
                        $symbol,
                        number_format($refereeBonus, $decimals)
                    ),
                    null,
                    'referral'
                );
            }
        } catch (\Throwable $e) {
            Log::info($e->getMessage());
        }
    }
}
