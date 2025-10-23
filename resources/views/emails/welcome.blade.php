@extends('emails.layouts.base')

@section('title','Welcome')

@section('content')
    <h1 style="margin:0 0 10px 0;font-size:24px;line-height:1.25;color:#111827;">Welcome, {{ $name }} 👋</h1>
    <p style="margin:0 0 16px 0;font-size:15px;line-height:1.6;color:#374151;">
        We're excited to have you on board. Explore menus, place orders, and enjoy exclusive offers tailored for you.
    </p>
    <div style="padding:6px 0 18px 0;">
        <a href="{{ config('app.url').'/home' }}" style="display:inline-block;background:#F25B0A;color:#ffffff;text-decoration:none;padding:12px 20px;border-radius:8px;font-weight:600;font-size:14px;">Start ordering</a>
    </div>
@endsection
