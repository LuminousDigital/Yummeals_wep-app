@extends('emails.layouts.base')

@section('title','Reset Password')

@section('content')
    <h1 style="margin:0 0 10px 0;font-size:22px;line-height:1.3;color:#111827;">Reset Password</h1>
    <p style="margin:0 0 12px 0;font-size:15px;line-height:1.6;color:#374151;">Your code is <span style="display:inline-block;font-weight:700;background:#f3f4f6;border-radius:8px;padding:8px 12px;color:#111827;">{{$pin}}</span></p>
    <p style="margin:0 0 16px 0;font-size:14px;line-height:1.6;color:#374151;">
        Please do not share your One Time Code With Anyone.
        You made a request to reset your password. Please
        discard if this wasn't you.
    </p>
@endsection
