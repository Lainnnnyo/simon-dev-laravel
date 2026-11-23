@extends('layouts.app')
@section('title', 'Customer Accounts')
@section('content')
<main class="page-content"><h1>Customer Accounts</h1>
@if(session('status'))<p role="status" class="notice">{{ session('status') }}</p>@endif
<a class="button-link" href="{{ route('customers.create') }}">Create Customer</a><div class="table-scroll"><table><thead><tr><th scope="col">Id</th><th scope="col">Full Name</th><th scope="col">Email</th><th scope="col">Phone</th><th scope="col">Action</th></tr></thead><tbody>
@forelse($customers as $record)<tr><td>{{ $record->id }}</td><td>{{ $record->full_name }}</td><td>{{ $record->email }}</td><td>{{ $record->phone }}</td><td><a href="{{ route('customers.edit', $record) }}">Edit</a></td></tr>
@empty<tr><td colspan="5">No accounts available.</td></tr>@endforelse
</tbody></table></div></main>
@endsection
