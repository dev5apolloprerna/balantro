@extends('layouts.mailer')

@section('content')
    <!-- resources/views/emails/password_otp.blade.php -->
    <p>Hi {{ $name }},</p>
    <p>Your one-time password (OTP) to reset your account password is:</p>
    <h2 style="letter-spacing:2px">{{ $otp }}</h2>
    <p>This OTP will expire in {{ $expiryMinutes }} minutes.</p>
    <p>If you didn’t request this, you can ignore this email.</p>
@endsection
