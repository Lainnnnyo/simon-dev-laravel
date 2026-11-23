@extends('layouts.app')
@section('title', 'Record sale')
@section('content')
<main class="page-content"><p class="eyebrow">THE COUNTER / NEW SALE</p><h1>One more <em>good cup.</em></h1><form class="record-form" method="post" action="{{ route('sales.store') }}">@csrf<input type="hidden" name="request_key" value="{{ old('request_key',$requestKey) }}">
<label for="product_id">Product</label><select id="product_id" name="product_id" required><option value="">Choose a product</option>@foreach($products as $p)<option value="{{ $p->id }}" data-price="{{ $p->price }}" @selected(old('product_id')==$p->id)>{{ $p->name }} — PHP {{ $p->price }} — {{ $p->stock_quantity }} in stock</option>@endforeach</select>
<label for="customer_id">Customer (optional)</label><select id="customer_id" name="customer_id"><option value="">Walk-in customer</option>@foreach($customers as $c)<option value="{{ $c->id }}" @selected(old('customer_id')==$c->id)>{{ $c->full_name }}</option>@endforeach</select>
<label for="quantity">Quantity</label><input id="quantity" name="quantity" type="number" min="1" max="10000" step="1" required value="{{ old('quantity',1) }}">
<p>Estimated total</p><output id="estimate" class="estimate" aria-live="polite">PHP 0.00</output><p>The saved total uses the current product price when you submit.</p><button type="submit">Record sale</button><a class="form-cancel" href="{{ route('sales.index') }}">History</a></form></main>
<script>const p=document.getElementById('product_id'),q=document.getElementById('quantity'),o=document.getElementById('estimate');function estimate(){const value=Number(p.selectedOptions[0]?.dataset.price||0)*Number(q.value||0);o.textContent='PHP '+(Number.isFinite(value)?value:0).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2});}p.addEventListener('change',estimate);q.addEventListener('input',estimate);estimate();</script>
@endsection
