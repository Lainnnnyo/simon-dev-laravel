@extends('layouts.app')
@section('title', 'Product editor')
@section('content')
<main class="page-content"><p class="eyebrow">THE MENU / PRODUCT EDITOR</p><h1>{{ $product->exists ? 'Edit product' : 'New product' }}</h1><form class="record-form" method="post" enctype="multipart/form-data" action="{{ $product->exists ? route('products.update',$product) : route('products.store') }}">@csrf @if($product->exists) @method('PUT')<input type="hidden" name="expected_stock" value="{{ old('expected_stock',$product->stock_quantity) }}">@endif
<label for="name">Product name</label><input id="name" name="name" required maxlength="255" value="{{ old('name',$product->name) }}">
<label for="price">Price (PHP)</label><input id="price" name="price" type="number" min="0.01" max="99999999.99" step="0.01" required value="{{ old('price',$product->price) }}">
<label for="stock_quantity">Stock quantity</label><input id="stock_quantity" name="stock_quantity" type="number" min="0" max="1000000" step="1" required value="{{ old('stock_quantity',$product->stock_quantity??0) }}">
@if($product->image)<img class="product-image" src="{{ route('product-images.show',basename($product->image)) }}" alt="Current product image">@endif
<label for="image">Product image {{ $product->exists ? '(optional replacement)' : '(required)' }}</label><input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" @if(!$product->exists) required @endif><p>JPG, PNG or WebP. Up to 2 MB and 4096 × 4096 pixels. Prepared as an 800 × 800 JPEG.</p>
<input type="hidden" name="is_archived" value="0"><label class="check"><input type="checkbox" name="is_archived" value="1" @checked(old('is_archived',$product->is_archived))> Archived (not available for new sales)</label>
<button>Save product</button><a class="form-cancel" href="{{ route('products.index') }}">Cancel</a></form></main>
@endsection
