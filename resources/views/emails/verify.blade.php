@extends('emails.layouts.base')

@section('title','Email Verification')

@section('content')
    <h1 style="margin:0 0 10px 0;font-size:22px;line-height:1.3;color:#111827;">Email Verification</h1>
    <p style="margin:0 0 8px 0;font-size:15px;line-height:1.6;color:#374151;">Thank you for signing up.</p>
    <p style="margin:0 0 16px 0;font-size:15px;line-height:1.6;color:#374151;">Your six-digit code is</p>
    <div style="margin:0 0 16px 0;font-size:20px;font-weight:700;background:#f3f4f6;display:inline-block;border-radius:8px;padding:10px 14px;color:#111827;">{{$pin}}</div>
@endsection
