@extends('layouts.app')
@section('title', 'Home')
@section('content')
<main class="home"><p class="eyebrow">WELCOME TO MY HOMEPAGE</p><h1>Hi, I'm Simon Andrei Dimaculangan.</h1><p>I enjoy learning to code and building projects with HTML, CSS, and Java.</p>
<section class="project"><p class="eyebrow">FEATURED PROJECT</p><h2>Point-of-Sale System</h2><p>A foundation for organizing customer and user accounts.</p><a class="button-link" href="{{ route('customers') }}">Customer Accounts</a><a class="button-link" href="{{ route('users') }}">User Accounts</a></section></main>
@endsection
