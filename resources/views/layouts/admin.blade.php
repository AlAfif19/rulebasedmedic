<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin DiagnoMed')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f2f6fc] text-slate-950 antialiased">
@php
    $adminLinks = [
        ['key' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'home', 'url' => route('admin.dashboard'), 'active' => request()->routeIs('admin.dashboard')],
        ['key' => 'gejala', 'label' => 'Data Gejala', 'icon' => 'stethoscope', 'url' => route('admin.resource.index', 'gejala'), 'active' => request()->is('admin/gejala*')],
        ['key' => 'penyakit', 'label' => 'Data Penyakit', 'icon' => 'shield', 'url' => route('admin.resource.index', 'penyakit'), 'active' => request()->is('admin/penyakit*')],
        ['key' => 'obat', 'label' => 'Data Obat', 'icon' => 'pill', 'url' => route('admin.resource.index', 'obat'), 'active' => request()->is('admin/obat*')],
        ['key' => 'rule', 'label' => 'Data Rule', 'icon' => 'clipboard', 'url' => route('admin.resource.index', 'rule'), 'active' => request()->is('admin/rule*')],
        ['key' => 'user', 'label' => 'Data User', 'icon' => 'user', 'url' => route('admin.resource.index', 'user'), 'active' => request()->is('admin/user*')],
        ['key' => 'riwayat', 'label' => 'Riwayat', 'icon' => 'history', 'url' => route('admin.resource.index', 'riwayat'), 'active' => request()->is('admin/riwayat*')],
        ['key' => 'pengaturan', 'label' => 'Pengaturan', 'icon' => 'info', 'url' => route('admin.resource.index', 'pengaturan'), 'active' => request()->is('admin/pengaturan*')],
    ];
@endphp
    <div class="min-h-screen lg:flex" data-admin-shell>
        <aside id="admin-sidebar" data-admin-sidebar class="hidden w-56 shrink-0 bg-gradient-to-b from-[#164775] to-[#2d91e6] text-white transition-[width] duration-200 lg:fixed lg:inset-y-0 lg:flex lg:flex-col">
            <div class="flex h-20 items-center justify-between px-5">
                <div data-admin-sidebar-brand>
                    <x-diagnomed.logo light="true" compact="true" />
                </div>
                <button data-admin-sidebar-toggle class="grid h-9 w-9 place-items-center rounded-[6px] bg-white/10 transition hover:bg-white/15" type="button" aria-label="Minimize sidebar" title="Minimize sidebar">
                    <x-diagnomed.icon name="panel-left-close" />
                </button>
            </div>
            <nav class="grid gap-2 px-4 py-3">
                @foreach($adminLinks as $link)
                    <a href="{{ $link['url'] }}" class="admin-link {{ $link['active'] ? 'admin-link-active' : '' }}" title="{{ $link['label'] }}">
                        <x-diagnomed.icon :name="$link['icon']" />
                        <span data-admin-sidebar-label>{{ $link['label'] }}</span>
                    </a>
                    @if($link['key'] === 'rule' || $link['key'] === 'pengaturan')
                        <div class="my-2 h-px bg-white/20"></div>
                    @endif
                @endforeach
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button class="admin-link w-full" type="submit">
                        <x-diagnomed.icon name="logout" />
                        <span data-admin-sidebar-label>Logout</span>
                    </button>
                </form>
            </nav>
            <div class="mt-auto px-4 pb-7">
                <div class="flex items-center gap-3 border-t border-white/20 pt-5">
                    <div class="grid h-10 w-10 place-items-center rounded-full bg-white text-sm font-bold text-[#2385dd]">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="min-w-0" data-admin-sidebar-profile>
                        <p class="truncate text-xs font-bold">{{ auth()->user()->name ?? 'Admin' }}</p>
                        <p class="truncate text-[10px] text-blue-50">Admin</p>
                    </div>
                </div>
            </div>
        </aside>

        <main data-admin-main class="min-h-screen flex-1 transition-[margin] duration-200 lg:ml-56">
            <header class="sticky top-0 z-30 border-b border-[#dce5f1] bg-white/95 backdrop-blur">
                <div class="flex h-20 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <button type="button" data-toggle-target="#admin-mobile-nav" class="grid h-10 w-10 place-items-center rounded-[6px] border border-[#dce5f1] lg:hidden" aria-label="Buka menu">
                            <span class="h-0.5 w-5 bg-slate-950"></span>
                        </button>
                        <div>
                            <h1 class="text-xl font-bold text-slate-950">@yield('page_title', 'Dashboard')</h1>
                            <p class="text-xs text-[#2385dd]">Dashboard &gt; @yield('page_title', 'Dashboard')</p>
                        </div>
                    </div>
                    <div class="hidden items-center gap-4 md:flex">
                        <form method="GET" action="{{ route('admin.resource.index', 'obat') }}" data-admin-global-search data-live-search data-live-search-target="#admin-resource-results" class="flex h-10 w-72 items-center gap-2 rounded-full border border-slate-300 bg-white px-4">
                            <x-diagnomed.icon name="search" class="text-slate-700" />
                            <input type="search" name="q" value="{{ request()->is('admin/obat*') ? request('q') : '' }}" autocomplete="off" class="h-full min-w-0 flex-1 bg-transparent text-xs outline-none" placeholder="Cari data obat">
                        </form>
                        <x-diagnomed.icon name="bell" class="text-slate-900" />
                        <x-diagnomed.icon name="calendar" class="text-slate-900" />
                        <span class="text-xs font-bold text-slate-900">{{ now()->format('d M Y, H:i') }}</span>
                    </div>
                </div>
                <div id="admin-mobile-nav" class="hidden border-t border-[#dce5f1] bg-[#164775] p-4 lg:hidden">
                    <nav class="grid gap-2">
                        <form method="GET" action="{{ route('admin.resource.index', 'obat') }}" data-admin-global-search data-live-search data-live-search-target="#admin-resource-results" class="mb-2 flex h-10 items-center gap-2 rounded-[6px] bg-white px-3">
                            <x-diagnomed.icon name="search" class="h-4 w-4 text-slate-700" />
                            <input type="search" name="q" value="{{ request()->is('admin/obat*') ? request('q') : '' }}" autocomplete="off" class="h-full min-w-0 flex-1 bg-transparent text-sm outline-none" placeholder="Cari data obat">
                        </form>
                        @foreach($adminLinks as $link)
                            <a href="{{ $link['url'] }}" class="admin-link {{ $link['active'] ? 'admin-link-active' : '' }}">
                                <x-diagnomed.icon :name="$link['icon']" />
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </nav>
                </div>
            </header>

            <div class="p-4 sm:p-6 lg:p-8">
                @if(session('success'))
                    <div class="mb-5 rounded-[8px] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-5 rounded-[8px] border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ $errors->first() }}</div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
