@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<main class="page-content"><p class="eyebrow">WORKSPACE / SIGNED IN</p><h1>Welcome back,<br><em>{{ auth()->user()->full_name ?: auth()->user()->name }}.</em></h1><p class="lead">Your next connection is just a click away.</p><div class="feature-grid two"><article class="feature-card"><p class="eyebrow">THE REGULARS</p><h2>Customers</h2><p>View, create, update, and delete customer records.</p><a class="button-link" href="{{ route('customers.index') }}">Manage customers ↗</a></article><article class="feature-card"><p class="eyebrow">THE WORKSPACE</p><h2>Users</h2><p>Manage accounts and personalize profile pictures.</p><a class="button-link" href="{{ route('users.index') }}">Manage users ↗</a></article></div></main>
@endsection
