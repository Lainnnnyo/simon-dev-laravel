@extends('layouts.app')
@section('title', 'Reset password')
@section('content')
<main class="auth-shell"><aside><p class="eyebrow">BYTE & BREW / YOUR WORKSPACE</p><h1>Stay curious.<br>Stay <em>connected.</em></h1><p>Coffee brings us together.<br>Your account keeps things connected.</p></aside><section class="auth-card"><p class="eyebrow">WELCOME TO THE COUNTER</p><h2>Reset password</h2><p>Enter your account email to request a reset link.</p><form method="post" action="{{ route('password.email') }}">@csrf<label for="email">Email</label><input id="email" name="email" type="email" required autocomplete="email" value="{{ old('email') }}" maxlength="255">@error('email')<p class="field-error" role="alert">{{ $message }}</p>@enderror<button type="submit">Send reset link</button></form></section></main>
@endsection
