@component('mail::message')
# Confirm Your Order

Your One-Time Password (OTP) is **{{ $otp }}**

This code will expire in a few minutes.

Please do not share this OTP with anyone. It is required to verify your cash-on-delivery order.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
