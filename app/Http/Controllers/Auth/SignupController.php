<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Activity;
use App\Enums\Ask;
use App\Http\Requests\SignupPhoneRequest;
use App\Libraries\AppLibrary;
use Carbon\Carbon;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\OtpManagerService;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignupRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\VerifyPhoneRequest;
use App\Enums\Role as EnumRole;
use Smartisan\Settings\Facades\Settings;
use App\Models\ReferralBonus;
use App\Events\ReferralSignedUp;
use App\Models\ReferralTransaction;
use App\Models\Currency;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use App\Mail\RaffleQualification;
use App\Services\NotificationSenderService;


class SignupController extends Controller
{

    private OtpManagerService $otpManagerService;

    public function __construct(OtpManagerService $otpManagerService)
    {
        $this->otpManagerService = $otpManagerService;
    }

    public function otp(
        SignupPhoneRequest $request
    ): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            $this->otpManagerService->otp($request);
            return response(['status' => true, 'message' => trans("all.message.check_your_phone_for_code")]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
            // return response(['status' => true, 'message' => trans("all.message.check_your_phone_for_code")]);
            // return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function verify(
        VerifyPhoneRequest $request
    ): \Illuminate\Http\Response | array | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            $this->otpManagerService->verify($request);
            return response(['status' => true, 'message' => trans("all.message.otp_verify_success")], 201);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function register(SignupRequest $request)
    {
        $flag = false;
        $otp = DB::table('otps')->where([
            ['phone', $request->post('phone')]
        ]);

        if (env('DEMO')) {
            $flag = true;
        } else {
            if (Settings::group('site')->get('site_phone_verification') == Activity::DISABLE) {
                $otp?->delete();
                $flag = true;
            } else {
                if (!$otp->exists()) {
                    $flag = true;
                }
            }
        }

        if ($flag) {
            $user = User::where(['phone' => $request->post('phone'), 'is_guest' => Ask::YES])->first();
            $name = AppLibrary::name($request->post('first_name'), $request->post('last_name'));

            $referrer = null;
            $rawReferral = $request->input('referral_code');
            $code = is_string($rawReferral) ? strtoupper(trim($rawReferral)) : '';
            if ($code !== '') {
                $referrer = User::whereNotNull('referral_code')
                    ->where('referral_code', $code)
                    ->first();
                Log::info('[Signup] referral code resolved', [
                    'code' => $code,
                    'referrer_found' => (bool) $referrer,
                    'referrer_id' => $referrer?->id,
                ]);
            }

            $wasGuest = false;
            if ($user) {
                $wasGuest = ($user->is_guest == Ask::YES);
                $user->name = $name;
                $user->username = Str::slug($name);
                $user->email = $request->post('email');
                $user->password = Hash::make($request->post('password'));
                $user->is_guest = Ask::NO;
                $user->referral_code = $user->referral_code ?? User::generateUniqueReferralCode($name);
                $user->referred_by = $referrer?->id;
                $user->save();
            } else {
                $user = User::create([
                    'name' => $name,
                    'username' => Str::slug($name),
                    'email' => $request->post('email'),
                    'phone' => $request->post('phone'),
                    'country_code' => $request->post('country_code'),
                    'branch_id' => 0,
                    'email_verified_at' => Carbon::now()->getTimestamp(),
                    'is_guest' => Ask::NO,
                    'password' => Hash::make($request->post('password')),
                    'referral_code' => User::generateUniqueReferralCode($name),
                    'referred_by' => $referrer?->id
                ]);
                $user->assignRole(EnumRole::CUSTOMER);
            }
            
            try {
                if (!empty($user->email) && ($user->wasRecentlyCreated || $wasGuest)) {
                    Mail::to($user->email)->send(new WelcomeMail($user->name));
                }
            } catch (\Throwable $e) {
                   Log::info('Mail Sending Error', [
                        'error' => $e,
                    ]);
            }

            if ($referrer) {
                if ($referrer->id !== $user->id) {
                    Log::info('[Signup] referral flow', [
                        'action' => 'dispatch_event_and_process_bonus',
                        'referrer_id' => $referrer->id,
                        'referee_id'  => $user->id,
                    ]);
                    ReferralSignedUp::dispatch([
                        'referrer_id' => $referrer->id,
                        'referee_id'  => $user->id,
                    ]);
                    $this->processReferralBonus($user, $referrer);
                }
            }

            return response([
                'status' => true,
                'message' => trans('all.message.register_successfully'),
                'data' => [
                    'user' => $user,
                    'token' => $user->createToken('auth_token')->plainTextToken
                ]
            ], 201);
        }

        return response([
            'status' => false,
            'message' => trans('all.message.code_is_invalid')
        ], 422);
    }

    protected function processReferralBonus(User $newUser, User $referrer)
    {
        $referralBonus = (float) Settings::group('referral')->get('signup_bonus', 10);
        $refereeBonus = (float) Settings::group('referral')->get('referee_bonus', 0);

        $currencyId   = (int) Settings::group('site')->get('site_default_currency', 1);
        $currencyCode = optional(Currency::find($currencyId))->code ?? env('CURRENCY', 'NGN');

        Log::info('[Signup] processReferralBonus start', [
            'referrer_id'    => $referrer->id,
            'referee_id'     => $newUser->id,
            'referral_bonus' => $referralBonus,
            'referee_bonus'  => $refereeBonus,
            'currency'       => $currencyCode,
        ]);

        $referrerBonusRecord = ReferralBonus::create([
            'referrer_id' => $referrer->id,
            'referee_id' => $newUser->id,
            'amount' => $referralBonus,
            'currency' => $currencyCode,
            'status' => 'pending',
            'notes' => 'New user signup bonus'
        ]);

        $beforeTotalReferrals = (int) $referrer->total_referrals;
        DB::transaction(function () use ($referrer, $referralBonus, $referrerBonusRecord) {
            $referrer->increment('referral_balance', $referralBonus);
            $referrer->increment('total_referrals');
            Log::info('[Signup] referrer credited', [
                'referrer_id' => $referrer->id,
                'amount'      => $referralBonus,
            ]);

            ReferralTransaction::create([
                'user_id' => $referrer->id,
                'amount' => $referralBonus,
                'type' => 'referral_bonus',
                'status' => 'completed',
                'meta' => [
                    'description' => 'Referral bonus for new signup',
                    'referral_bonus_id' => $referrerBonusRecord->id,
                    'referee_id' => $referrerBonusRecord->referee_id
                ]
            ]);
        });

        try {
            $referrer->refresh();
            Log::info('[Signup] referrer totals changed', [
                'referrer_id' => $referrer->id,
                'before' => $beforeTotalReferrals,
                'after'  => (int) $referrer->total_referrals,
            ]);
        } catch (\Throwable $e) {
            Log::info('[Signup] totals refresh failed', ['message' => $e->getMessage()]);
        }

        // Notify referrer exactly on 2nd referral
        try {
            $referrer->refresh();
            if ((int) $referrer->total_referrals === 2) {
                if (!empty($referrer->email)) {
                    Mail::to($referrer->email)->send(new RaffleQualification($referrer->name));
                }
                app(NotificationSenderService::class)->sendToUser(
                    $referrer,
                    'Raffle Qualification',
                    'Congrats! You now qualify for our raffle draw. Stay tuned for announcements.',
                    null,
                    'referral'
                );
            }
        } catch (\Throwable $e) {}

        if ($refereeBonus > 0) {
            $refereeBonusRecord = ReferralBonus::create([
                'referrer_id' => $referrer->id,
                'referee_id' => $newUser->id,
                'amount' => $refereeBonus,
                'currency' => $currencyCode,
                'status' => 'completed',
                'notes' => 'Welcome bonus for using referral code'
            ]);

            DB::transaction(function () use ($newUser, $refereeBonus, $refereeBonusRecord, $currencyCode) {
                $wallet = $newUser->wallet()->firstOrCreate([], ['balance' => 0, 'currency' => $currencyCode]);
                $wallet->increment('balance', $refereeBonus);
                Log::info('[Signup] referee credited', [
                    'referee_id' => $newUser->id,
                    'amount'     => $refereeBonus,
                ]);

                ReferralTransaction::create([
                    'user_id' => $newUser->id,
                    'amount' => $refereeBonus,
                    'type' => 'referral_welcome_bonus',
                    'status' => 'completed',
                    'meta' => [
                        'description' => 'Welcome bonus for using referral code',
                        'referral_bonus_id' => $refereeBonusRecord->id,
                        'referrer_id' => $refereeBonusRecord->referrer_id
                    ]
                ]);
            });
        }
    }
}

