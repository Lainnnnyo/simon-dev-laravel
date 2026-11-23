@extends('layouts.app')
@section('title', 'Customer Account')
@section('content')
<main class="page-content"><h1>{{ $customer->exists ? 'Edit' : 'Create' }} Customer</h1>
@if($errors->any())<div role="alert" class="auth-error">Please correct the errors below.</div>@endif
<form class="record-form" method="post" enctype="multipart/form-data" action="{{ $customer->exists ? route("customers.update", $customer) : route("customers.store") }}">
@csrf
@if($customer->exists) @method("PUT") @endif
<label for="full_name">Full Name</label><input id="full_name" name="full_name" type="text" maxlength="255" required value="{{ old('full_name', $customer->full_name) }}">
@error('full_name')<p class="field-error" role="alert">{{ $message }}</p>@enderror
<label for="email">Email</label><input id="email" name="email" type="email" maxlength="255" required value="{{ old('email', $customer->email) }}">
@error('email')<p class="field-error" role="alert">{{ $message }}</p>@enderror
<label for="phone">Phone (optional)</label><input id="phone" name="phone" type="text" maxlength="30" value="{{ old('phone', $customer->phone) }}">
@error('phone')<p class="field-error" role="alert">{{ $message }}</p>@enderror
<button type="submit">Save Customer</button> <a class="form-cancel" href="{{ route('customers.index') }}">Cancel</a></form></main>
@endsection
