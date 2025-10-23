@extends('emails.layouts.base')

@section('title','Subscriber Notification')

@section('content')
    <h1 style="margin:0 0 10px 0;font-size:22px;line-height:1.3;color:#111827;">Subscriber Notification</h1>
    <p style="margin:0 0 12px 0;font-size:15px;line-height:1.6;color:#374151;">Hello,</p>
    <p style="margin:0 0 8px 0;font-size:14px;line-height:1.6;color:#111827;">Subject : {{ $title }}</p>
    <p style="margin:0 0 16px 0;font-size:14px;line-height:1.6;color:#374151;">{{ $message }}</p>
@endsection
