@extends('layouts.app')
@section('title', 'User Accounts')
@section('content')
<main class="page-content"><p class="eyebrow">SIMON DEV POS</p><h1>User Accounts</h1><p>{{ $users->count() }} accounts</p>
<div class="table-scroll"><table><caption>User account directory</caption><thead><tr><th scope="col">Id</th><th scope="col">Username</th><th scope="col">Full Name</th></tr></thead><tbody>
@forelse ($users as $record)
<tr><td>{{ $record->id }}</td><td>{{ $record->username }}</td><td>{{ $record->full_name }}</td></tr>
@empty
<tr><td colspan="3">No accounts available.</td></tr>
@endforelse
</tbody></table></div></main>
@endsection
