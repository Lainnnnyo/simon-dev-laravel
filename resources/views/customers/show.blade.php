@extends('layouts.app')
@section('title', 'Customer details')
@section('content')
<main class="page-content"><p class="eyebrow">WORKSPACE / RECORD DETAILS</p><h1>Customer details</h1><section class="project"><dl><dt>Full Name</dt><dd>{{ $customer->full_name }}</dd><dt>Email</dt><dd>{{ $customer->email }}</dd><dt>Phone</dt><dd>{{ $customer->phone }}</dd></dl><div class="actions"><a class="button-link" href="{{ route('customers.edit', $customer) }}">Edit</a><a href="{{ route('customers.index') }}">Back to list</a><form method="post" action="{{ route('customers.destroy', $customer) }}" data-confirm="Delete this record? This cannot be undone.">@csrf @method('DELETE')<button class="delete-button" type="submit">Delete</button></form></div></section></main>
@endsection
