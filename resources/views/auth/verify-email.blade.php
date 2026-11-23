@extends('layouts.app')
@section('title', 'Verify email')
@section('content')
<main class="page-content"><h1>Check your inbox.</h1><p>Follow the email verification link to continue.</p><form method="post" action="{{ route('verification.send') }}">@csrf<button type="submit">Resend verification email</button></form></main>
@endsection
