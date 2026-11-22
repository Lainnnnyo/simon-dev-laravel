@extends('layouts.app')
@section('title', 'Customer Accounts')
@section('content')
<main class="page-content"><p class="eyebrow">SIMON DEV POS</p><h1>Customer Accounts</h1><p>{{ $customers->count() }} accounts</p>
<div class="table-scroll"><table><caption>Customer account directory</caption><thead><tr><th scope="col">Id</th><th scope="col">Full Name</th><th scope="col">Email</th><th scope="col">Phone</th></tr></thead><tbody>
@forelse ($customers as $record)
<tr><td>{{ $record->id }}</td><td>{{ $record->full_name }}</td><td>{{ $record->email }}</td><td>{{ $record->phone }}</td></tr>
@empty
<tr><td colspan="4">No accounts available.</td></tr>
@endforelse
</tbody></table></div></main>
@endsection
