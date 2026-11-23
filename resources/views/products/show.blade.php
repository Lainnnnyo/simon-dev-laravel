@extends('layouts.app')
@section('title', 'Product details')
@section('content')
<main class="page-content"><p class="eyebrow">THE MENU / DETAILS</p><h1>{{ $product->name }}</h1><section class="project">@if($product->image)<img class="detail-image" src="{{ route('product-images.show',basename($product->image)) }}" alt="{{ $product->name }}">@endif<dl><dt>Price</dt><dd>PHP {{ $product->price }}</dd><dt>Available stock</dt><dd>{{ $product->stock_quantity }}</dd><dt>Status</dt><dd>{{ $product->is_archived ? 'Archived' : 'Active' }}</dd></dl><div class="actions"><a class="button-link" href="{{ route('products.edit',$product) }}">Edit product</a><a href="{{ route('products.index') }}">Back to products</a></div></section></main>
@endsection
