<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Home Wear')</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    @stack('styles')

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('appLayout', () => ({
                sidebarOpen: window.innerWidth >= 1024,
                isDesktop: window.innerWidth >= 1024,

                init() {
                    this.sidebarOpen = this.isDesktop;
                    window.addEventListener('resize', () => {
                        this.isDesktop = window.innerWidth >= 1024;
                        if (this.isDesktop) {
                            this.sidebarOpen = true;
                        }
                    });
                },

                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                },

                closeSidebar() {
                    if (!this.isDesktop) {
                        this.sidebarOpen = false;
                    }
                }
            }));
        });
    </script>
</head>

<body class="bg-gray-100 min-h-screen" x-data="appLayout()" x-init="init()">

    <div class="flex h-screen overflow-hidden">

        {{-- الشريط الجانبي --}}
        <aside x-show="sidebarOpen || isDesktop" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full" :class="{
                'translate-x-0': sidebarOpen || isDesktop,
                'translate-x-full': !sidebarOpen && !isDesktop
            }"
            class="fixed lg:static inset-y-0 right-0 z-[100] w-72 bg-white shadow-2xl lg:shadow-none border-l lg:border-l-0 border-gray-200 flex flex-col">

            {{-- اللوجو --}}
            <div class="h-16 flex items-center justify-center bg-indigo-600 text-white font-bold text-xl relative">
                Home Wear

                <button @click="closeSidebar()" class="absolute left-6 lg:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- القائمة الجانبية --}}
            <nav class="flex-1 overflow-y-auto py-6">
                <ul class="space-y-2 px-4">
                    @php
                        use Illuminate\Support\Facades\Auth;

                        /** @var \App\Models\User|null $user */
                        $user = Auth::user();
                    @endphp

                    @foreach (config('navigation.menu') as $item)
                        @php
                            // لو مفيش يوزر أو مش نشط → متعرضش حاجة
                            if (!$user || (method_exists($user, 'isActive') && !$user->isActive())) {
                                continue;
                            }

                            // الأدوار المسموح لها تشوف العنصر
                            $allowedRoles = $item['roles'] ?? null;
                            if ($allowedRoles && !in_array($user->role, $allowedRoles, true)) {
                                continue;
                            }

                            $hasChildren = !empty($item['children'] ?? []);
                            $itemRoute = $item['route'] ?? null;
                            $itemActive = $item['active'] ?? ($itemRoute ?? '');
                            $itemHref = '#';

                            if ($itemRoute && \Illuminate\Support\Facades\Route::has($itemRoute)) {
                                $itemHref = data_get($item, 'query')
                                    ? route($itemRoute, $item['query'])
                                    : route($itemRoute);
                            }

                            $isItemActive = $itemActive ? request()->routeIs($itemActive) : false;
                        @endphp

                        @if (!$hasChildren)
                            {{-- عنصر بدون أولاد --}}
                            <li>
                                <a href="{{ $itemHref }}" @click="closeSidebar()"
                                    class="block px-6 py-4 rounded-xl text-lg transition
                                                    {{ $isItemActive ? 'bg-indigo-100 text-indigo-700 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                                    <span class="flex items-center gap-3">
                                        @if (!empty($item['icon'] ?? null))
                                            <x-dynamic-component :component="$item['icon']" class="w-5 h-5 text-gray-400" />
                                        @endif
                                        <span>{{ $item['name'] }}</span>
                                    </span>
                                </a>
                            </li>
                        @else
                            @php
                                // نفلتر الأولاد حسب الـ roles
                                $children = collect($item['children'] ?? [])->filter(function ($child) use ($user) {
                                    $childRoles = $child['roles'] ?? null;
                                    if ($childRoles && !in_array($user->role, $childRoles, true)) {
                                        return false;
                                    }
                                    return true;
                                })->values()->all();

                                // لو مفيش أولاد مسموح لهم
                                if (!count($children)) {
                                    continue;
                                }

                                $anyChildActive = collect($children)->contains(function ($child) {
                                    $childRoute = $child['route'] ?? null;
                                    $childActive = $child['active'] ?? $childRoute;
                                    return $childActive ? request()->routeIs($childActive) : false;
                                });
                            @endphp

                            <li x-data="{ open: {{ $anyChildActive ? 'true' : 'false' }} }">
                                <button @click="open = !open"
                                    class="w-full flex items-center justify-between px-6 py-4 rounded-xl text-lg text-gray-700 hover:bg-gray-100 transition">
                                    <span class="flex items-center gap-2">
                                        @if (!empty($item['icon'] ?? null))
                                            <x-dynamic-component :component="$item['icon']" class="w-5 h-5 text-gray-400" />
                                        @endif
                                        <span>{{ $item['name'] }}</span>
                                    </span>
                                    <svg :class="{ 'rotate-180': open }" class="w-5 h-5 transition-transform duration-200"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div x-show="open" x-transition class="mr-8 mt-2 space-y-1">
                                    @foreach ($children as $child)
                                        @php
                                            $childRoute = $child['route'] ?? null;
                                            $childActive = $child['active'] ?? $childRoute;
                                            $childHref = '#';

                                            if ($childRoute && \Illuminate\Support\Facades\Route::has($childRoute)) {
                                                $childHref = data_get($child, 'query')
                                                    ? route($childRoute, $child['query'])
                                                    : route($childRoute);
                                            }

                                            $isChildActive = $childActive ? request()->routeIs($childActive) : false;
                                        @endphp

                                        <a href="{{ $childHref }}" @click="closeSidebar()"
                                            class="block px-8 py-3 text-base rounded-lg
                                                                    {{ $isChildActive ? 'bg-indigo-100 text-indigo-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                                            <span class="flex items-center gap-2">
                                                @if (!empty($child['icon'] ?? null))
                                                    <x-dynamic-component :component="$child['icon']" class="w-4 h-4 text-gray-400" />
                                                @endif
                                                <span>{{ $child['name'] }}</span>
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </li>
                        @endif
                    @endforeach

                    {{-- زر تسجيل الخروج --}}
                    @auth
                        <li class="mt-6 pt-4 border-t border-gray-200">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-6 py-3 rounded-xl text-red-600
                                            hover:bg-red-50 hover:text-red-700 text-lg font-medium transition">
                                    {{-- <x-heroicon-o-arrow-right-start-on-rectangle class="w-5 h-5" /> --}}
                                    <span>تسجيل الخروج</span>
                                </button>
                            </form>
                        </li>
                    @endauth
                </ul>
            </nav>
        </aside>

        {{-- أوفرلاي للموبايل --}}
        <div x-cloak x-show="sidebarOpen && !isDesktop" @click="closeSidebar()" x-transition.opacity
            class="fixed inset-0 bg-black/50 z-[90] lg:hidden">
        </div>

        {{-- محتوى الصفحة --}}
        <div class="flex-1 flex flex-col">
            {{-- هيدر موبايل --}}
            <header class="bg-white shadow h-16 flex items-center justify-between px-6 lg:hidden">
                <button @click="toggleSidebar()" class="text-gray-700 hover:text-gray-900 lg:hidden">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </header>

            <main class="flex-1 overflow-y-auto pt-5 pb-8">
                <div class="max-w-7xl mx-auto p-6">

                    {{-- رسائل النجاح --}}
                    @if (session('success'))
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- رسائل الخطأ --}}
                    @if (session('error'))
                        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif
                    <!-- Page Heading -->
                    @if (isset($header))
                        <header class="bg-white shadow">
                            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endif
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>

</html>