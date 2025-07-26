@component('mail::message')
# Confirm Your Order

Your One-Time Password (OTP) is **{{ $otp }}**

This code will expire in a 30 minutes.

Please do not share this OTP with anyone. It is required to verify your delivery order.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
