@extends('emails.layouts.base')

@section('title','Raffle Qualification')

@section('content')
    <h1 style="margin:0 0 10px 0;font-size:22px;line-height:1.3;color:#111827;">You're in, {{ $name }}! 🎉</h1>
    <p style="margin:0 0 16px 0;font-size:15px;line-height:1.6;color:#374151;">You now qualify for our raffle draw after your 2nd successful referral. Stay tuned—winners are announced soon!</p>
    <div style="padding:6px 0 18px 0;">
        <a href="{{ config('app.url').'/referrals' }}" style="display:inline-block;background:#F25B0A;color:#ffffff;text-decoration:none;padding:12px 20px;border-radius:8px;font-weight:600;font-size:14px;">View referrals</a>
    </div>
@endsection
