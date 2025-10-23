@component('mail::message')
# Welcome, {{ $name }} 👋

We're excited to have you on board. Explore menus, place orders, and enjoy exclusive offers tailored for you.

@component('mail::button', ['url' => config('app.url').'/home'])
Start ordering
@endcomponent

Thanks,
{{ config('app.name') }}
@endcomponent