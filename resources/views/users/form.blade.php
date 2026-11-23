@extends('layouts.app')
@section('title', 'User Account')
@section('content')
<main class="page-content"><h1>{{ $user->exists ? 'Edit' : 'Create' }} User</h1>
@if($errors->any())<div role="alert" class="auth-error">Please correct the errors below.</div>@endif
<form class="record-form" method="post" enctype="multipart/form-data" action="{{ $user->exists ? route("users.update", $user) : route("users.store") }}">
@csrf
@if($user->exists) @method("PUT") @endif
<label for="username">Username</label><input id="username" name="username" type="text" maxlength="255" required value="{{ old('username', $user->username) }}">
@error('username')<p class="field-error" role="alert">{{ $message }}</p>@enderror
<label for="full_name">Full Name</label><input id="full_name" name="full_name" type="text" maxlength="255" required value="{{ old('full_name', $user->full_name) }}">
@error('full_name')<p class="field-error" role="alert">{{ $message }}</p>@enderror
@if($user->exists)
<img width="64" height="64" alt="Current avatar" src="{{ $user->avatar ? route('avatars.show', basename($user->avatar)) : asset('images/avatar-placeholder.svg') }}">
<label for="avatar">Profile picture (optional)</label><input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp"><p>JPEG, PNG or WebP, up to 2 MB and 4096 × 4096 pixels. Prepared as a 256 × 256 thumbnail. Leave blank to keep the current image.</p>
@error('avatar')<p class="field-error" role="alert">{{ $message }}</p>@enderror
@endif
<button type="submit">Save User</button> <a class="form-cancel" href="{{ route('users.index') }}">Cancel</a></form></main>
@endsection
