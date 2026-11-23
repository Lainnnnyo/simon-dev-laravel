@extends('layouts.app')
@section('title', 'Confirm password')
@section('content')
<main class="auth-shell"><aside><p class="eyebrow">BYTE & BREW / YOUR WORKSPACE</p><h1>Stay curious.<br>Stay <em>connected.</em></h1><p>Coffee brings us together.<br>Your account keeps things connected.</p></aside><section class="auth-card"><p class="eyebrow">WELCOME TO THE COUNTER</p><h2>Confirm password</h2><p>Confirm your password before continuing.</p><form method="post" action="{{ route('password.confirm') }}">@csrf<label for="password">Password</label><input id="password" name="password" type="password" required autocomplete="current-password" >@error('password')<p class="field-error" role="alert">{{ $message }}</p>@enderror<button type="submit">Confirm</button></form></section></main>
@endsection
