@extends('layouts.app')
@section('content')
    <div class="py-12">
        <div class="max-w-5xl mx-auto">
            @include('product.form', ['variant' => $variant])
        </div>
    </div>
@endsection