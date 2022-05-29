@extends('layouts.main')

@section('content')
    <div class="verification-mail-box">
        <div class="w-50">
            <div class="mb-4 text">
                {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
            </div>

            <div class="mt-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn btn-save ">{{ __('Resend Verification Email') }}</button>
                </form>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mt-4 text-sm green-text blod-text">
                    {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                </div>
            @endif
        </div>
    </div>
@endsection
