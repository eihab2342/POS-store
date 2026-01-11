@extends('layouts.app')
@section('title', 'تفاصيل المرتجع #' . $return->id)

@section('content')
<div class="max-w-4xl mx-auto mt-10 px-4 mb-10 font-sans">
    <nav class="flex mb-6 text-sm" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2 space-x-reverse">
            <li><a href="{{ route('returns.index') }}" class="text-gray-400 hover:text-blue-700 transition-colors font-medium">المرتجعات</a></li>
            <li class="flex items-center">
                <svg class="w-4 h-4 text-gray-300 mx-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                <span class="text-black font-bold">عرض تفاصيل السند</span>
            </li>
        </ol>
    </nav>

    <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
        
        <div class="bg-black px-8 py-10 text-white flex justify-between items-center relative">
            <div class="absolute bottom-0 left-0 w-full h-1.5 bg-blue-600"></div>
            
            <div class="z-10">
                <span class="inline-block bg-white text-black text-[11px] font-black px-3 py-1 rounded mb-4 uppercase tracking-wider">
                    Official Return Document
                </span>
                <h1 class="text-4xl text-black font-extrabold tracking-tight">
                    مرتجع رقم <span class="text-black">#{{ $return->id }}</span>
                </h1>
                
                <div class="flex items-center mt-4 text-black font-semibold bg-white/5 px-4 py-2 rounded-lg border border-white/10">
                    <svg class="w-5 h-5 ml-2 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>{{ $return->created_at }}</span>
                    {{-- <span class="mx-3 text-black">|</span> --}}
                    {{-- <span>{{ $return->created_at }}</span> --}}
                </div>
            </div>

            <div class="hidden md:block">
                <div class="bg-blue-600 p-5 rounded-xl shadow-lg shadow-blue-900/20">
                    <svg class="w-12 h-12 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="p-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                
                <div class="space-y-10">
                    <div class="flex items-start space-x-5 space-x-reverse">
                        <div class="bg-blue-50 p-3 rounded-lg border border-blue-100">
                            <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase mb-1">بيانات الفاتورة</p>
                            <p class="text-xl font-bold text-black italic">Invoice #{{ $return->sale_id }}</p>
                            <p class="text-blue-700 font-bold mt-1 underline decoration-2 underline-offset-4">العميل: {{ $return->sale->customer->name ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-5 space-x-reverse">
                        <div class="bg-blue-50 p-3 rounded-lg border border-blue-100">
                            <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 11m8 4V5"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase mb-1">المنتج والكمية</p>
                            <p class="text-xl font-bold text-black tracking-tight leading-none">{{ $return->variant->variant_name ?? '-' }}</p>
                            <div class="mt-3 inline-block bg-black text-white px-4 py-1 rounded text-sm font-bold">
                                الكمية: {{ $return->returned_qty }} قطع
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-10 md:border-r md:pr-10 border-gray-100">
                    <div class="flex items-start space-x-5 space-x-reverse">
                        <div class="bg-green-50 p-3 rounded-lg border border-green-100">
                            <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase mb-1">سبب المرتجع</p>
                            <p class="text-gray-800 font-medium leading-relaxed bg-green-50/50 p-3 rounded-lg border-r-4 border-green-600">
                                {{ $return->reason ?? 'لم يتم ذكر سبب محدد' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-5 space-x-reverse">
                        <div class="bg-green-50 p-3 rounded-lg border border-green-100">
                            <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase mb-1">حالة التوثيق</p>
                            <p class="text-xl font-bold text-black uppercase tracking-tighter">{{ $return->user->name ?? '-' }}</p>
                            <p class="text-green-700 text-xs font-bold mt-1 flex items-center">
                                <span class="w-2 h-2 bg-green-600 rounded-full ml-1"></span>
                                تم المراجعة بواسطة النظام
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-16 pt-8 border-t-2 border-black flex flex-col sm:flex-row gap-4 items-center justify-between print:hidden">
                <a href="{{ route('returns.index') }}" class="flex items-center text-black hover:text-blue-700 font-black transition-all">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m7 7l-7-7 7-7"></path></svg>
                    الرجوع للقائمة
                </a>
                
                <div class="flex items-center space-x-4 space-x-reverse">
                    <!-- <button onclick="window.print()" class="px-6 py-3 bg-white text-black border-2 border-black font-black hover:bg-black hover:text-white transition-all">
                        طباعة السند (P)
                    </button>
-->
                    <a href="{{ route('returns.edit', $return) }}" class="px-10 py-3 bg-blue-700 text-white font-black hover:bg-blue-800 transition-all shadow-lg shadow-blue-200">
                        تعديل البيانات
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* تحسين الخط للعربي */
    @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap');
    .font-sans { font-family: 'Cairo', sans-serif !important; }

    @media print {
        body { background: white !important; padding: 0 !important; }
        nav, .mt-16, button, .shadow-2xl, .bg-blue-600.p-5 { display: none !important; }
        .bg-black { background: black !important; -webkit-print-color-adjust: exact; }
        .bg-white { border: none !important; }
        .text-blue-500 { color: #2563eb !important; }
        .p-10 { padding: 20px 0 !important; }
        .md\:border-r { border: none !important; }
    }
</style>
@endsection