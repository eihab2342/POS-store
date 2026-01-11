@extends('layouts.app')

@section('title', 'إضافة حركة رصيد')

@section('content')
    <h1 class="text-2xl font-bold text-gray-800 mb-6">إضافة حركة رصيد</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('balances.store') }}" method="POST">
            @php $balance = $balance ?? new \App\Models\Balance(); @endphp
            @include('balances._form', ['balance' => $balance, 'customers' => $customer, 'invoices' => $invoices])
        </form>
    </div>
@endsection