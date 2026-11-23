@extends('layouts.app')
@section('title', 'User details')
@section('content')
<main class="page-content"><p class="eyebrow">WORKSPACE / RECORD DETAILS</p><h1>User details</h1><section class="project"><img class="avatar" width="48" height="48" alt="User avatar" src="{{ $user->avatar ? route('avatars.show', basename($user->avatar)) : asset('images/avatar-placeholder.svg') }}"><dl><dt>Username</dt><dd>{{ $user->username }}</dd><dt>Full Name</dt><dd>{{ $user->full_name }}</dd></dl><div class="actions"><a class="button-link" href="{{ route('users.edit', $user) }}">Edit</a><a href="{{ route('users.index') }}">Back to list</a>@if(!auth()->user()->is($user))<form method="post" action="{{ route('users.destroy', $user) }}" data-confirm="Delete this record? This cannot be undone.">@csrf @method('DELETE')<button class="delete-button" type="submit">Delete</button></form>@else<span class="muted">You</span>@endif</div></section></main>
@endsection
