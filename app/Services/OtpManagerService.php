<?php

namespace App\Services;

use App\Events\SendSmsCode;
use Exception;
use App\Models\Otp;
use App\Events\SendOrderOtp;
use App\Models\Order;
use App\Models\FrontendOrder;
use App\Jobs\SendSmsJob;
use App\Enums\OtpType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Smartisan\Settings\Facades\Settings;
use App\Http\Requests\VerifyPhoneRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OtpManagerService
{

    /**
     * @throws Exception
     */
    public function otp(Request $request): bool
    {
        try {
            $otp = DB::table('otps')->where([
                ['phone', $request->post('phone')],
                ['code', $request->post('code')],
            ]);

            if ($otp->exists()) {
                $otp->delete();
            }

            if (OtpType::SMS == Settings::group('otp')->get('otp_type') || OtpType::BOTH == Settings::group('otp')->get(
                'otp_type'
            )) {
                $token = rand(
                    pow(10, (int)Settings::group('otp')->get('otp_digit_limit') - 1),
                    pow(10, (int)Settings::group('otp')->get('otp_digit_limit')) - 1
                );
            } else {
                $token = rand(pow(10, 4 - 1), pow(10, 4) - 1);
            }

            $otp = Otp::create([
                'phone' => $request->phone,
                'code' => $request->code,
                'token' => $token,
                'created_at' => now(),
            ]);

            // if (!blank($otp)) {
            //     SendSmsJob::dispatch(
            //         ['phone' => $request->post('phone'), 'code' => $request->post('code'), 'token' => $token]
            //     );
            // }
            if (!blank($otp)) {
                SendSmsCode::dispatch(
                    ['phone' => $request->post('phone'), 'code' => $request->post('code'), 'token' => $token]
                );
            }

            return true;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function verify(VerifyPhoneRequest $request): bool
    {
        try {
            if (env('DEMO')) {
                return true;
            }

            $otp = DB::table('otps')->where([
                ['phone', $request->post('phone')],
                ['token', $request->post('token')],
            ]);
            if ($otp->exists()) {
                $difference = Carbon::now()->diffInSeconds($otp->first()->created_at);
                if ($difference > (int)Settings::group('otp')->get('otp_expire_time') * 60) {
                    throw new Exception(trans('all.message.code_is_expired'), 422);
                } else {
                    $otp->delete();
                    return true;
                }
            } else {
                throw new Exception(trans('all.message.code_is_invalid'), 422);
            }
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * Generate and send OTP for an order.
     */
    public function generateOrderOtp(FrontendOrder|Order $order): array
    {
        try {
            $length = (int)Settings::group('otp')->get('order_otp_digit_limit', 6);
            $otp = $this->generateOtpToken($length);
            $expiry = Carbon::now()->addMinutes((int)Settings::group('otp')->get('order_otp_expiry_time', 30));

            // $order->update([
            //     'otp_code'       => $otp,
            //     'otp_expires_at' => $expiry,
            // ]);

            // SendOrderOtp::dispatch([
            //     'email'    => $order->user->email,
            //     'otp'      => $otp,
            //     'order_id' => $order->id,
            // ]);
            return ['otp_code' => $otp, 'otp_expires_at' => $expiry];
        } catch (Exception $e) {
            Log::error("Order OTP generation failed: " . $e->getMessage());
            throw new Exception("Failed to generate OTP for order", 500);
        }
    }

    /**
     * Verify the OTP for an order.
     */
    public function verifyOrderOtp(Order $order, int|string $otp): bool
    {
        if (!$order->otp_code || !$order->otp_expires_at) {
            throw new Exception("No OTP assigned to this order", 400);
        }

        if ($order->otp_code != $otp) {
            throw new Exception("Invalid OTP", 422);
        }

        if (Carbon::parse($order->otp_expires_at)->isPast()) {
            throw new Exception("OTP has expired", 422);
        }

        return true;
    }

    /**
     * Generate an OTP token based on settings.
     */
    protected function generateOtpToken(int $length): int
    {
        return rand(pow(10, $length - 1), pow(10, $length) - 1);
    }
}
