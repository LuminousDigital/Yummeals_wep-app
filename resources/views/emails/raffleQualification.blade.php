@component('mail::message')
# You're in, {{ $name }}! 🎉

You now qualify for our raffle draw after your 2nd successful referral. Stay tuned—winners are announced soon!

@component('mail::button', ['url' => config('app.url').'/referrals'])
View referrals
@endcomponent

Thanks,
{{ config('app.name') }}
@endcomponent