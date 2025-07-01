@component('mail::message')
# {{ $isAdmin ? 'New Coupon Winner Notification' : 'Congratulations!' }}

@if($isAdmin)
**Winner Details:**
- Name: {{ $data['username'] }}
- Email: {{ $data['email'] }}
- Coupon Type: {{ $data['coupon_type'] }}
@else
You've won a {{ $data['coupon_type'] }} coupon!

**Coupon Details:**
- Code: {{ $data['coupon_code'] }}
{{-- - Discount: {{ 100 }}% --}}
- Discount: {{ $data['discount'] }}%
- Valid Until: {{ $data['valid_until'] }}
@endif

@if(!$isAdmin)
Use this coupon during checkout to redeem your reward!
@endif

Thanks,
{{ config('app.name') }}
@endcomponent
