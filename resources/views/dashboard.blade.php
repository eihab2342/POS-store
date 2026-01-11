@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
@php
    // بيانات ديناميكية فقط
    $todaySales = \App\Models\Sale::whereDate('created_at', today())->sum('total');
    $totalProducts = \App\Models\ProductVariant::count();
    $totalStock = \App\Models\ProductVariant::sum('stock_qty');
    $lowStockCount = \App\Models\ProductVariant::where('stock_qty', '<', 10)->count();
    $todayProfit = \App\Models\Sale::whereDate('created_at', today())->sum('paid');
    $todaySalesCount = \App\Models\Sale::whereDate('created_at', today())->count();
    $totalCustomers = \App\Models\Customer::count();
    $totalAdmins = \App\Models\User::count();
    $totalReturns = \App\Models\SaleReturn::count();
    $totalBalance = \App\Models\Balance::sum('cash_amount');

    $recentSales = \App\Models\Sale::with('customer')->latest()->take(5)->get();
@endphp

<style>
:root {
    --primary: #3b82f6;
    --primary-dark: #2563eb;
    --primary-light: #dbeafe;
    --secondary: #10b981;
    --secondary-dark: #059669;
    --secondary-light: #d1fae5;
    --danger: #ef4444;
    --danger-dark: #dc2626;
    --danger-light: #fee2e2;
    --warning: #f59e0b;
    --warning-dark: #d97706;
    --warning-light: #fef3c7;
    --info: #8b5cf6;
    --info-dark: #7c3aed;
    --info-light: #ede9fe;
    --success: #22c55e;
    --success-dark: #16a34a;
    --success-light: #dcfce7;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
}

.dashboard-bg {
    background: linear-gradient(135deg, var(--gray-50) 0%, var(--gray-100) 100%);
    min-height: 100vh;
}

.decorative-circle-1 {
    position: absolute;
    top: -10%;
    right: -5%;
    width: 40%;
    height: 40%;
    border-radius: 50%;
    background: linear-gradient(45deg, var(--primary-light) 0%, transparent 70%);
    opacity: 0.3;
    filter: blur(40px);
}

.decorative-circle-2 {
    position: absolute;
    bottom: -10%;
    left: -5%;
    width: 40%;
    height: 40%;
    border-radius: 50%;
    background: linear-gradient(45deg, var(--info-light) 0%, transparent 70%);
    opacity: 0.3;
    filter: blur(40px);
}

.stat-card-primary {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(59, 130, 246, 0.2);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card-primary:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(59, 130, 246, 0.3);
}

.stat-card-primary::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100%;
    height: 4px;
    background: rgba(255, 255, 255, 0.3);
    transition: height 0.3s ease;
}

.stat-card-primary:hover::before {
    height: 6px;
}

.stat-card-white {
    background: white;
    color: var(--gray-800);
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    border: 1px solid var(--gray-100);
    transition: all 0.3s ease;
}

.stat-card-white:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    border-color: var(--gray-200);
}

.stat-card-success {
    background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
    color: white;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(34, 197, 94, 0.2);
    transition: all 0.3s ease;
}

.stat-card-success:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(34, 197, 94, 0.3);
}

.quick-action-card {
    background: white;
    border: 1px solid var(--gray-100);
    border-radius: 0.75rem;
    padding: 1rem;
    transition: all 0.3s ease;
}

.quick-action-card:hover {
    background: linear-gradient(90deg, var(--primary-light) 0%, white 100%);
    border-color: var(--primary);
    transform: translateX(-4px);
}

.quick-action-icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.625rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
    transition: transform 0.3s ease;
}

.quick-action-card:hover .quick-action-icon {
    transform: scale(1.1);
}

.nav-card {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    border: 1px solid var(--gray-100);
    transition: all 0.4s ease;
    position: relative;
    overflow: hidden;
}

.nav-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
}

.nav-card-header {
    padding: 1.5rem;
    color: white;
    position: relative;
}

.nav-card-indicator {
    position: absolute;
    top: 0;
    right: 0;
    width: 100%;
    height: 3px;
    background: rgba(255, 255, 255, 0.3);
    transition: height 0.3s ease;
}

.nav-card:hover .nav-card-indicator {
    height: 5px;
}

.sale-item {
    background: var(--gray-50);
    border: 1px solid var(--gray-100);
    border-radius: 0.75rem;
    padding: 1rem;
    transition: all 0.3s ease;
}

.sale-item:hover {
    background: var(--gray-100);
    border-color: var(--gray-200);
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.time-card {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    border: 1px solid var(--gray-200);
    border-radius: 0.75rem;
    padding: 0.5rem 1rem;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out forwards;
}

.delay-100 {
    animation-delay: 0.1s;
}

.delay-200 {
    animation-delay: 0.2s;
}

.delay-300 {
    animation-delay: 0.3s;
}

.gradient-text {
    background: linear-gradient(45deg, var(--primary), var(--info));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
</style>

<div class="dashboard-bg py-8 relative overflow-hidden">
    <div class="decorative-circle-1"></div>
    <div class="decorative-circle-2"></div>

    <div class="container mx-auto px-4 relative z-10">

        <!-- Header Section -->
        <div class="mb-8 animate-fade-in-up">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-4xl font-bold" style="color: var(--gray-800); margin-bottom: 0.5rem;">
                        مرحباً، <span class="gradient-text">{{ auth()->user()->name }}</span> 👋
                    </h1>
                    <p style="color: var(--gray-600); font-size: 1.125rem;">إليك نظرة عامة على نشاط اليوم</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="time-card">
                        <i class="far fa-calendar" style="color: var(--primary); margin-left: 0.5rem;"></i>
                        <span style="color: var(--gray-600); font-size: 0.875rem;">{{ now()->format('d/m/Y') }}</span>
                    </div>
                    <div class="time-card">
                        <i class="far fa-clock" style="color: var(--primary); margin-left: 0.5rem;"></i>
                        <span style="color: var(--gray-600); font-size: 0.875rem;" id="current-time"></span>
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->user()->role == 'manager')

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

            <!-- Total Sales Card -->
            <div class="stat-card-primary animate-fade-in-up">
                <div class="flex items-center justify-between mb-4">
                    <span style="font-size: 0.875rem; font-weight: 500; opacity: 0.9;">اليوم</span>
                    <i class="fas fa-chart-line" style="font-size: 1.125rem; opacity: 0.8;"></i>
                </div>
                <h3 style="font-size: 2.25rem; font-weight: 700; margin-bottom: 0.5rem;">
                    {{ number_format($todaySales, 2) }} <span style="font-size: 1.25rem;">ج.م</span>
                </h3>
                <p style="font-size: 0.875rem; opacity: 0.9;">إجمالي المبيعات</p>
                <div style="margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid rgba(255, 255, 255, 0.2);">
                    <div class="flex items-center justify-between" style="font-size: 0.75rem;">
                        {{-- <span style="opacity: 0.8;">مقارنة بالأمس</span>
                        <span style="background: rgba(255, 255, 255, 0.2); padding: 0.25rem 0.5rem; border-radius: 9999px;">
                            <i class="fas fa-arrow-up" style="font-size: 0.625rem; margin-left: 0.25rem;"></i> 12%
                        </span> --}}
                    </div>
                </div>
            </div>

            <!-- Total Products Card -->
            <div class="stat-card-white animate-fade-in-up delay-100">
                <div class="flex items-center justify-between mb-4">
                    <span style="font-size: 0.875rem; font-weight: 500; color: var(--gray-500);">الكل</span>
                    <i class="fas fa-boxes" style="font-size: 1.125rem; color: var(--gray-400);"></i>
                </div>
                <h3 style="font-size: 2.25rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--gray-800);">
                    {{ number_format($totalProducts) }}
                </h3>
                <p style="font-size: 0.875rem; color: var(--gray-500);">إجمالي المنتجات</p>
                <div style="margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid var(--gray-100);">
                    <div style="font-size: 0.75rem; color: var(--gray-400); display: flex; align-items: center; gap: 0.25rem;">
                        <i class="fas fa-cubes" style="margin-left: 0.25rem;"></i>
                        <span>{{ number_format($totalStock) }} قطعة في المخزون</span>
                    </div>
                </div>
            </div>

            <!-- Low Stock Card -->
            <div class="stat-card-white animate-fade-in-up delay-200">
                <div class="flex items-center justify-between mb-4">
                    <span style="font-size: 0.875rem; font-weight: 500; color: var(--gray-500);">تنبيه</span>
                    <i class="fas fa-exclamation-triangle" style="font-size: 1.125rem; color: var(--warning);"></i>
                </div>
                <h3 style="font-size: 2.25rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--gray-800);">
                    {{ $lowStockCount }}
                </h3>
                <p style="font-size: 0.875rem; color: var(--gray-500);">منتج منخفض المخزون</p>
                <div style="margin-top: 1rem;">
                    <a href="{{ route('low.index') }}" style="font-size: 0.75rem; color: var(--warning-dark); display: flex; align-items: center; gap: 0.25rem;">
                        <span>عرض التفاصيل</span>
                        <i class="fas fa-arrow-left" style="font-size: 0.625rem; margin-right: 0.5rem;"></i>
                    </a>
                </div>
            </div>

            <!-- Total Profit Card -->
            <div class="stat-card-success animate-fade-in-up delay-300">
                <div class="flex items-center justify-between mb-4">
                    <span style="font-size: 0.875rem; font-weight: 500; opacity: 0.9;">اليوم</span>
                    <i class="fas fa-coins" style="font-size: 1.125rem; opacity: 0.8;"></i>
                </div>
                <h3 style="font-size: 2.25rem; font-weight: 700; margin-bottom: 0.5rem;">
                    {{ number_format($todayProfit, 2) }} <span style="font-size: 1.25rem;">ج.م</span>
                </h3>
                <p style="font-size: 0.875rem; opacity: 0.9;">صافي الربح</p>
                <div style="margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid rgba(255, 255, 255, 0.2);">
                    <div class="flex items-center justify-between" style="font-size: 0.75rem;">
                        {{-- <span style="opacity: 0.8;">مقارنة بالأمس</span>
                        <span style="background: rgba(255, 255, 255, 0.2); padding: 0.25rem 0.5rem; border-radius: 9999px;">
                            <i class="fas fa-arrow-up" style="font-size: 0.625rem; margin-left: 0.25rem;"></i> 8%
                        </span> --}}
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

            <!-- Quick Actions -->
            <div class="lg:col-span-1">
                <div class="stat-card-white animate-fade-in-up">
                    <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--gray-800); margin-bottom: 1.5rem; display: flex; align-items: center;">
                        <i class="fas fa-bolt" style="color: var(--warning); margin-left: 0.5rem;"></i>
                        إجراءات سريعة
                    </h2>

                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <a href="{{ route('sales.invoice') }}" class="quick-action-card">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div class="quick-action-icon" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);">
                                        <i class="fas fa-cash-register"></i>
                                    </div>
                                    <div>
                                        <h3 style="font-weight: 600; color: var(--gray-800);">فاتورة جديدة</h3>
                                        <p style="font-size: 0.75rem; color: var(--gray-600); margin-top: 0.25rem;">إنشاء فاتورة بيع جديدة</p>
                                    </div>
                                </div>
                                <i class="fas fa-arrow-left" style="color: var(--primary);"></i>
                            </div>
                        </a>

                        <a href="{{ route('variants.create') }}" class="quick-action-card">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div class="quick-action-icon" style="background: linear-gradient(135deg, var(--info) 0%, var(--info-dark) 100%);">
                                        <i class="fas fa-plus-circle"></i>
                                    </div>
                                    <div>
                                        <h3 style="font-weight: 600; color: var(--gray-800);">منتج جديد</h3>
                                        <p style="font-size: 0.75rem; color: var(--gray-600); margin-top: 0.25rem;">إضافة منتج للمخزون</p>
                                    </div>
                                </div>
                                <i class="fas fa-arrow-left" style="color: var(--info);"></i>
                            </div>
                        </a>

                        <a href="{{ route('suppliers.index') }}" class="quick-action-card">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div class="quick-action-icon" style="background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);">
                                        <i class="fas fa-truck"></i>
                                    </div>
                                    <div>
                                        <h3 style="font-weight: 600; color: var(--gray-800);">الموردين</h3>
                                        <p style="font-size: 0.75rem; color: var(--gray-600); margin-top: 0.25rem;">إدارة الموردين والمشتريات</p>
                                    </div>
                                </div>
                                <i class="fas fa-arrow-left" style="color: var(--success);"></i>
                            </div>
                        </a>

                        <a href="{{ route('profits.index') }}" class="quick-action-card">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div class="quick-action-icon" style="background: linear-gradient(135deg, var(--warning) 0%, var(--warning-dark) 100%);">
                                        <i class="fas fa-chart-pie"></i>
                                    </div>
                                    <div>
                                        <h3 style="font-weight: 600; color: var(--gray-800);">تقرير الأرباح</h3>
                                        <p style="font-size: 0.75rem; color: var(--gray-600); margin-top: 0.25rem;">عرض الأرباح والخسائر</p>
                                    </div>
                                </div>
                                <i class="fas fa-arrow-left" style="color: var(--warning);"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Sales -->
            <div class="lg:col-span-2">
                <div class="stat-card-white animate-fade-in-up delay-100">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                        <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--gray-800); display: flex; align-items: center;">
                            <i class="fas fa-history" style="color: var(--primary); margin-left: 0.5rem;"></i>
                            آخر المبيعات
                        </h2>
                        <a href="{{ route('sales.index') }}" style="color: var(--primary); font-size: 0.875rem; font-weight: 500; display: flex; align-items: center;">
                            عرض الكل
                            <i class="fas fa-arrow-left" style="margin-right: 0.25rem;"></i>
                        </a>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        @foreach($recentSales as $sale)
                        <div class="sale-item">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <div style="background: linear-gradient(135deg, var(--primary-light) 0%, var(--gray-100) 100%); padding: 0.75rem; border-radius: 0.5rem; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);">
                                        <i class="fas fa-receipt" style="color: var(--primary);"></i>
                                    </div>
                                    <div>
                                        <h4 style="font-weight: 600; color: var(--gray-800);">فاتورة #{{ $sale->id }}</h4>
                                        <div style="display: flex; align-items: center; gap: 1rem; font-size: 0.875rem; color: var(--gray-600); margin-top: 0.25rem;">
                                            <span style="display: flex; align-items: center;">
                                                <i class="far fa-user" style="margin-left: 0.25rem; font-size: 0.75rem;"></i>
                                                {{ $sale->customer->name ?? 'عميل نقدي' }}
                                            </span>
                                            <span style="display: flex; align-items: center;">
                                                <i class="far fa-clock" style="margin-left: 0.25rem; font-size: 0.75rem;"></i>
                                                {{ $sale->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align: left;">
                                    <div style="font-size: 1.125rem; font-weight: 700; color: var(--gray-800);">{{ number_format($sale->total_price, 2) }} <span style="font-size: 0.875rem;">ج.م</span></div>
                                    <span class="status-badge" style="background: var(--success-light); color: var(--success-dark); margin-top: 0.25rem;">
                                        <i class="fas fa-check-circle" style="margin-left: 0.25rem;"></i>
                                        مكتمل
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Navigation Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Products Card -->
            <a href="{{ route('variants.index') }}" class="nav-card animate-fade-in-up">
                <div class="nav-card-indicator"></div>
                <div class="nav-card-header" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);">
                    <div style="display: flex; align-items: center; justify-content: space-between; color: white; margin-bottom: 1rem;">
                        <i class="fas fa-boxes" style="font-size: 2.25rem; opacity: 0.9;"></i>
                        <span style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500;">
                            {{ $totalProducts }}
                        </span>
                    </div>
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: white; margin-bottom: 0.5rem;">الأصناف والمنتجات</h2>
                    <p style="color: rgba(255, 255, 255, 0.8); font-size: 0.875rem;">إدارة المخزون والأسعار</p>
                </div>
                <div style="padding: 1.5rem;">
                    <div style="display: flex; align-items: center; color: var(--primary); font-weight: 500; gap: 0.5rem;">
                        <span>إدارة المنتجات</span>
                        <i class="fas fa-arrow-left"></i>
                    </div>
                </div>
            </a>

            <!-- Sales Card -->
            <a href="{{ route('sales.index') }}" class="nav-card animate-fade-in-up delay-100">
                <div class="nav-card-indicator"></div>
                <div class="nav-card-header" style="background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);">
                    <div style="display: flex; align-items: center; justify-content: space-between; color: white; margin-bottom: 1rem;">
                        <i class="fas fa-shopping-cart" style="font-size: 2.25rem; opacity: 0.9;"></i>
                        <span style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500;">
                            {{ $todaySalesCount }}
                        </span>
                    </div>
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: white; margin-bottom: 0.5rem;">المبيعات</h2>
                    <p style="color: rgba(255, 255, 255, 0.8); font-size: 0.875rem;">عرض وإدارة الفواتير</p>
                </div>
                <div style="padding: 1.5rem;">
                    <div style="display: flex; align-items: center; color: var(--success); font-weight: 500; gap: 0.5rem;">
                        <span>عرض الفواتير</span>
                        <i class="fas fa-arrow-left"></i>
                    </div>
                </div>
            </a>

            <!-- Returns Card -->
            <a href="{{ route('returns.index') }}" class="nav-card animate-fade-in-up delay-200">
                <div class="nav-card-indicator"></div>
                <div class="nav-card-header" style="background: linear-gradient(135deg, var(--danger) 0%, var(--danger-dark) 100%);">
                    <div style="display: flex; align-items: center; justify-content: space-between; color: white; margin-bottom: 1rem;">
                        <i class="fas fa-undo" style="font-size: 2.25rem; opacity: 0.9;"></i>
                        <span style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500;">
                            {{ $totalReturns }}
                        </span>
                    </div>
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: white; margin-bottom: 0.5rem;">المرتجعات</h2>
                    <p style="color: rgba(255, 255, 255, 0.8); font-size: 0.875rem;">إدارة مرتجعات العملاء</p>
                </div>
                <div style="padding: 1.5rem;">
                    <div style="display: flex; align-items: center; color: var(--danger); font-weight: 500; gap: 0.5rem;">
                        <span>عرض المرتجعات</span>
                        <i class="fas fa-arrow-left"></i>
                    </div>
                </div>
            </a>

            <!-- Customers Card -->
            <a href="{{ route('customers.index') }}" class="nav-card animate-fade-in-up">
                <div class="nav-card-indicator"></div>
                <div class="nav-card-header" style="background: linear-gradient(135deg, var(--info) 0%, var(--info-dark) 100%);">
                    <div style="display: flex; align-items: center; justify-content: space-between; color: white; margin-bottom: 1rem;">
                        <i class="fas fa-users" style="font-size: 2.25rem; opacity: 0.9;"></i>
                        <span style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500;">
                            {{ $totalCustomers }}
                        </span>
                    </div>
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: white; margin-bottom: 0.5rem;">العملاء</h2>
                    <p style="color: rgba(255, 255, 255, 0.8); font-size: 0.875rem;">إدارة بيانات العملاء</p>
                </div>
                <div style="padding: 1.5rem;">
                    <div style="display: flex; align-items: center; color: var(--info); font-weight: 500; gap: 0.5rem;">
                        <span>إدارة العملاء</span>
                        <i class="fas fa-arrow-left"></i>
                    </div>
                </div>
            </a>

            <!-- Admins Card -->
            <a href="{{ route('admins.index') }}" class="nav-card animate-fade-in-up delay-100">
                <div class="nav-card-indicator"></div>
                <div class="nav-card-header" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                    <div style="display: flex; align-items: center; justify-content: space-between; color: white; margin-bottom: 1rem;">
                        <i class="fas fa-user-shield" style="font-size: 2.25rem; opacity: 0.9;"></i>
                        <span style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500;">
                            {{ $totalAdmins }}
                        </span>
                    </div>
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: white; margin-bottom: 0.5rem;">المديرين</h2>
                    <p style="color: rgba(255, 255, 255, 0.8); font-size: 0.875rem;">إدارة الحسابات والصلاحيات</p>
                </div>
                <div style="padding: 1.5rem;">
                    <div style="display: flex; align-items: center; color: #6366f1; font-weight: 500; gap: 0.5rem;">
                        <span>إدارة المديرين</span>
                        <i class="fas fa-arrow-left"></i>
                    </div>
                </div>
            </a>

            <!-- Balances Card -->
            <a href="{{ route('balances.index') }}" class="nav-card animate-fade-in-up delay-200">
                <div class="nav-card-indicator"></div>
                <div class="nav-card-header" style="background: linear-gradient(135deg, var(--warning) 0%, var(--warning-dark) 100%);">
                    <div style="display: flex; align-items: center; justify-content: space-between; color: white; margin-bottom: 1rem;">
                        <i class="fas fa-wallet" style="font-size: 2.25rem; opacity: 0.9;"></i>
                        <span style="background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500;">
                            {{ number_format($totalBalance, 0) }}
                        </span>
                    </div>
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: white; margin-bottom: 0.5rem;">الأرصدة</h2>
                    <p style="color: rgba(255, 255, 255, 0.8); font-size: 0.875rem;">متابعة الأرصدة والمدفوعات</p>
                </div>
                <div style="padding: 1.5rem;">
                    <div style="display: flex; align-items: center; color: var(--warning); font-weight: 500; gap: 0.5rem;">
                        <span>عرض الأرصدة</span>
                        <i class="fas fa-arrow-left"></i>
                    </div>
                </div>
            </a>
        </div>

        @else
        <!-- Admin View -->
        <div class="stat-card-white animate-fade-in-up" style="text-align: center;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 5rem; height: 5rem; background: linear-gradient(135deg, var(--primary-light) 0%, var(--gray-100) 100%); border-radius: 9999px; margin-bottom: 1rem;">
                <i class="fas fa-user-tie" style="font-size: 2.25rem; color: var(--primary);"></i>
            </div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--gray-800); margin-bottom: 0.5rem;">مرحباً بك، {{ auth()->user()->name }}</h2>
            <p style="color: var(--gray-600); margin-bottom: 1.5rem;">يمكنك الوصول إلى نقطة البيع وإدارة الفواتير</p>
            <a href="{{ route('sales.invoice') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%); color: white; padding: 0.75rem 2rem; border-radius: 0.75rem; font-weight: 600; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3); transition: all 0.3s ease;">
                <i class="fas fa-cash-register"></i>
                <span>فتح نقطة البيع</span>
            </a>
        </div>
        @endif

    </div>
</div>

<script>
// Update current time
function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('ar-EG', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });
    document.getElementById('current-time').textContent = timeString;
}
updateTime();
setInterval(updateTime, 1000);
</script>
@endsection