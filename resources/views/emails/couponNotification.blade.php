@extends('emails.layouts.base')

@section('title', $isAdmin ? 'New Coupon Winner Notification' : 'Congratulations!')

@section('content')
    <h1 style="margin:0 0 10px 0;font-size:22px;line-height:1.3;color:#111827;">{{ $isAdmin ? 'New Coupon Winner Notification' : 'Congratulations!' }}</h1>
    @if ($isAdmin)
        <p style="margin:0 0 8px 0;font-size:14px;line-height:1.6;color:#111827;font-weight:600;">Winner Details:</p>
        <ul style="margin:0 0 16px 0;padding-left:18px;font-size:14px;line-height:1.6;color:#374151;">
            <li>Name: {{ $data['username'] }}</li>
            <li>Email: {{ $data['email'] }}</li>
            <li>Coupon Type: {{ $data['coupon_type'] }}</li>
        </ul>
    @else
        <p style="margin:0 0 10px 0;font-size:15px;line-height:1.6;color:#374151;">You've won a {{ $data['coupon_type'] }} coupon!</p>
        <p style="margin:0 0 6px 0;font-size:14px;line-height:1.6;color:#111827;font-weight:600;">Coupon Details:</p>
        <ul style="margin:0 0 12px 0;padding-left:18px;font-size:14px;line-height:1.6;color:#374151;">
            <li>Code: {{ $data['coupon_code'] }}</li>
            <li>Discount: {{ 100 }}%</li>
            <li>Valid Until: {{ $data['valid_until'] }}</li>
        </ul>
        <p style="margin:0 0 16px 0;font-size:14px;line-height:1.6;color:#374151;">Use this coupon during checkout to redeem your reward!</p>
    @endif
@endsection
