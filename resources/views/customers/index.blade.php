@extends('layouts.app')
@section('title', 'Customer Accounts')
@section('content')
<main class="page-content"><p class="eyebrow">SIMON DEV POS</p><h1>Customer Accounts</h1><p>Sample accounts for the POS foundation.</p>
<div class="table-scroll"><table><caption>Five sample customer accounts</caption><thead><tr><th scope="col">ID</th><th scope="col">Full Name</th><th scope="col">Email</th><th scope="col">Phone</th></tr></thead><tbody>
@foreach ($customers as $record)
<tr><td>{{ $record['id'] }}</td><td>{{ $record['full_name'] }}</td><td>{{ $record['email'] }}</td><td>{{ $record['phone'] }}</td></tr>
@endforeach
</tbody></table></div></main>
@endsection
