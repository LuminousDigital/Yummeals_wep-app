<?php

namespace App\Http\Controllers\Frontend;


use App\Enums\Activity;
use App\Enums\Ask;
use App\Enums\PaymentStatus;
use App\Http\Requests\PaymentRequest;
use App\Libraries\AppLibrary;
use App\Models\Currency;
use App\Models\Order;
use App\Models\PaymentGateway;
use App\Models\ThemeSetting;
use App\Services\PaymentManagerService;
use App\Services\OtpManagerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Smartisan\Settings\Facades\Settings;
use App\Enums\OrderStatus;
use App\Enums\PaymentGateway as EnumsPaymentGateway;
use App\Events\SendOrderSms;
use App\Events\SendOrderMail;
use App\Events\SendOrderPush;
use App\Models\FrontendOrder;
use Illuminate\Support\Facades\Auth;
use App\Events\SendOrderGotPush;
use App\Events\SendOrderGotMail;
use App\Events\SendOrderGotSms;
use App\Events\SendOrderOtp;
use App\Enums\OrderType;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    private PaymentManagerService $paymentManagerService;
    private OtpManagerService $otpManagerService;

    public function __construct(PaymentManagerService $paymentManagerService, OtpManagerService $otpManagerService)
    {
        $this->paymentManagerService = $paymentManagerService;
        $this->otpManagerService = $otpManagerService;
    }

    // public function index(
    //     Order $order
    // ): \Illuminate\Contracts\View\Factory | \Illuminate\Contracts\View\View | \Illuminate\Contracts\Foundation\Application | \Illuminate\Http\RedirectResponse {
    //     $credit          = false;
    //     // $paymentGateways = PaymentGateway::with('gatewayOptions')->whereNotIn('id', [1])->where(['status' => Activity::ENABLE])->get();
    //     $paymentGateways = PaymentGateway::with('gatewayOptions')->where(['status' => Activity::ENABLE])->get();
    //     $company         = Settings::group('company')->all();
    //     $logo            = ThemeSetting::where(['key' => 'theme_logo'])->first();
    //     $faviconLogo     = ThemeSetting::where(['key' => 'theme_favicon_logo'])->first();
    //     $currency        = Currency::findOrFail(Settings::group('site')->get('site_default_currency'));
    //     if ($order?->user?->balance >= $order->total) {
    //         $credit = true;
    //     }

    //     if (blank($order->transaction) && $order->payment_status === PaymentStatus::UNPAID) {
    //         return view('payment', [
    //             'company'         => $company,
    //             'logo'            => $logo,
    //             'currency'        => $currency,
    //             'faviconLogo'     => $faviconLogo,
    //             'paymentGateways' => $paymentGateways,
    //             'order'           => $order,
    //             'creditAmount'    => AppLibrary::currencyAmountFormat($order?->user?->balance),
    //             'credit'          => $credit
    //         ]);
    //     }
    //     return redirect()->route('home')->with('error', trans('all.message.payment_canceled'));
    // }

    public function index(Order $order): \Illuminate\Contracts\View\Factory | \Illuminate\Contracts\View\View | \Illuminate\Contracts\Foundation\Application | \Illuminate\Http\RedirectResponse
    {
        $credit = false;

        $paymentGatewaysQuery = PaymentGateway::with('gatewayOptions')
            ->where('status', Activity::ENABLE);

        if ($order->status === OrderStatus::PENDING) {

            $paymentGatewaysQuery->whereNotIn('slug', ['cash-on-delivery']);
        } else {

            $paymentGatewaysQuery->whereNotIn('slug', ['cash']);
        }

        $paymentGateways = $paymentGatewaysQuery->get();

        $company     = Settings::group('company')->all();
        $logo        = ThemeSetting::where('key', 'theme_logo')->first();
        $faviconLogo = ThemeSetting::where('key', 'theme_favicon_logo')->first();
        $currency    = Currency::findOrFail(Settings::group('site')->get('site_default_currency'));

        if ($order?->user?->balance >= $order->total) {
            $credit = true;
        }

        if (blank($order->transaction) && $order->payment_status === PaymentStatus::UNPAID) {
            return view('payment', [
                'company'         => $company,
                'logo'            => $logo,
                'currency'        => $currency,
                'faviconLogo'     => $faviconLogo,
                'paymentGateways' => $paymentGateways,
                'order'           => $order,
                'creditAmount'    => AppLibrary::currencyAmountFormat($order?->user?->balance),
                'credit'          => $credit
            ]);
        }

        return redirect()->route('home')->with('error', trans('all.message.payment_canceled'));
    }


    public function payment(Order $order, PaymentRequest $request)
    {
        $orderDatetime = Carbon::now();

        if ($request->paymentMethod === "cash") {
            return redirect()->route('payment.successful', ['order' => $order->id, 'otp' => $order->otp, 'otp_expiry' => $order->otp_expiry, 'paymentMethod' => 'cash']);
        }

        if ($order->order_type === OrderType::DELIVERY) {
            $otpData = $this->otpManagerService->generateOrderOtp($order);

            $order->update([
                'otp_code'         => $otpData['otp_code'],
                'otp_expires_at'   => $otpData['otp_expires_at'],
            ]);

            event(new SendOrderOtp([
                'email'     => $order->user->email,
                'otp'       => $otpData['otp_code'],
                'order_id'  => $order->id
            ]));
        }

        if ($request->paymentMethod === "cash-on-delivery") {
            $order->update([
                'status'           => OrderStatus::PENDING,
                'order_datetime'   => $orderDatetime->toDateTimeString(),
            ]);

            return redirect()->route('payment.successful', ['order' => $order->id, 'paymentMethod' => 'cash-on-delivery']);
        }

        if ($this->paymentManagerService->gateway($request->paymentMethod)->status()) {
            $className = 'App\\Http\\PaymentGateways\\PaymentRequests\\' . ucfirst($request->paymentMethod);
            $gateway   = new $className;
            $request->validate($gateway->rules());
            $payment = $this->paymentManagerService->gateway($request->paymentMethod)->payment($order, $request);

            $preparationTime = (int) Settings::group('order_setup')->get('order_setup_food_preparation_time');

            $deliveryStart = $orderDatetime->copy()->addMinutes($preparationTime);
            $deliveryEnd = $deliveryStart->copy()->addMinutes(30);

            $deliveryTimeString = $deliveryStart->format('H:i') . ' - ' . $deliveryEnd->format('H:i');

            $order->update([
                // 'is_advance_order' => Ask::NO,
                // 'payment_method'   => EnumsPaymentGateway::E_WALLET,
                'status'           => OrderStatus::PENDING,
                'order_datetime'   => $orderDatetime->toDateTimeString(),
                'delivery_time'    => $deliveryTimeString,
            ]);

            SendOrderMail::dispatch(['order_id' => $order->id, 'status' => OrderStatus::PENDING]);
            SendOrderSms::dispatch(['order_id' => $order->id, 'status' => OrderStatus::PENDING]);
            SendOrderPush::dispatch(['order_id' => $order->id, 'status' => OrderStatus::PENDING]);
            SendOrderGotMail::dispatch(['order_id' => $order->id]);
            SendOrderGotSms::dispatch(['order_id' => $order->id]);
            SendOrderGotPush::dispatch(['order_id' => $order->id]);

            return $payment;
        } else {
            return redirect()->route('payment.index', ['order' => $order])->with(
                'error',
                trans('all.message.payment_gateway_disable')
            );
        }
    }

    public function success(PaymentGateway $paymentGateway, Order $order, Request $request)
    {
        return $this->paymentManagerService->gateway($paymentGateway->slug)->success($order, $request);
    }

    public function fail(PaymentGateway $paymentGateway, Order $order, Request $request)
    {
        return $this->paymentManagerService->gateway($paymentGateway->slug)->fail($order, $request);
    }

    public function cancel(PaymentGateway $paymentGateway, Order $order, Request $request)
    {
        return $this->paymentManagerService->gateway($paymentGateway->slug)->cancel($order, $request);
    }

    public function successful(
        Order $order
    ): \Illuminate\Contracts\View\Factory | \Illuminate\Contracts\View\View | \Illuminate\Contracts\Foundation\Application | \Illuminate\Http\RedirectResponse {
        $company     = Settings::group('company')->all();
        $logo        = ThemeSetting::where(['key' => 'theme_logo'])->first();
        $faviconLogo = ThemeSetting::where(['key' => 'theme_favicon_logo'])->first();

        if (!blank($order->transaction)) {
            return view('paymentSuccess', [
                'company'     => $company,
                'logo'        => $logo,
                'faviconLogo' => $faviconLogo,
                'order'       => $order,
            ]);
        }
        return redirect()->route('home')->with('error', trans('all.message.payment_canceled'));
    }
}
