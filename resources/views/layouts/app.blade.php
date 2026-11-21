<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>@yield('title') | Simon Dev POS</title><link rel="stylesheet" href="{{ asset('css/site.css') }}"></head>
<body><header class="site-header"><a class="brand" href="{{ route('home') }}">SIMON<span>_DEV</span></a><nav aria-label="Main navigation">
@foreach (['home' => 'Home', 'about' => 'About', 'customers' => 'Customers', 'users' => 'Users'] as $name => $label)
<a href="{{ route($name) }}" @if(request()->routeIs($name)) aria-current="page" @endif>{{ $label }}</a>
@endforeach
</nav></header>@yield('content')</body></html>
