@extends('layouts.app')
@section('title', 'User Accounts')
@section('content')
<main class="page-content"><h1>User Accounts</h1>
@if(session('status'))<p role="status" class="notice">{{ session('status') }}</p>@endif
<a class="button-link" href="{{ route('users.create') }}">Create User</a><div class="table-scroll"><table><thead><tr><th scope="col">Avatar</th><th scope="col">Id</th><th scope="col">Username</th><th scope="col">Full Name</th><th scope="col">Action</th></tr></thead><tbody>
@forelse($users as $record)<tr><td><img width="64" height="64" style="border-radius:12px;object-fit:cover" alt="User avatar" src="{{ $record->avatar ? route('avatars.show', basename($record->avatar)) : asset('images/avatar-placeholder.svg') }}"></td><td>{{ $record->id }}</td><td>{{ $record->username }}</td><td>{{ $record->full_name }}</td><td><a href="{{ route('users.edit', $record) }}">Edit</a></td></tr>
@empty<tr><td colspan="5">No accounts available.</td></tr>@endforelse
</tbody></table></div></main>
@endsection
