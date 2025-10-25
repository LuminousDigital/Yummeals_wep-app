@extends('emails.layouts.base')

@section('title','Confirm Your Order')

@section('content')
    <h1 style="margin:0 0 10px 0;font-size:22px;line-height:1.3;color:#111827;">Confirm Your Order</h1>
    <p style="margin:0 0 12px 0;font-size:15px;line-height:1.6;color:#374151;">Your order OTP is <span style="font-weight:700;background:#f3f4f6;border-radius:8px;padding:8px 12px;color:#111827;">{{ $otp }}</span></p>
    {{-- <p style="margin:0 0 12px 0;font-size:14px;line-height:1.6;color:#374151;">This code will expire in a 30 minutes.</p> --}}
    <p style="margin:0 0 16px 0;font-size:14px;line-height:1.6;color:#374151;">Please do not share this OTP with anyone. It is required to verify your delivery order.</p>
@endsection
