@extends('emails.layouts.base')

@section('title','Order Notification')

@section('content')
    <h1 style="margin:0 0 10px 0;font-size:22px;line-height:1.3;color:#111827;">Order Notification</h1>
    <p style="margin:0 0 8px 0;font-size:14px;line-height:1.6;color:#111827;">Order ID : {{$orderId}}</p>
    <p style="margin:0 0 16px 0;font-size:14px;line-height:1.6;color:#374151;">{{$message}}</p>
@endsection
