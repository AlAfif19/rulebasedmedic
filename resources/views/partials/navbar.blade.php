@php
    $links = [
        ['label' => 'Beranda', 'route' => auth()->check() && auth()->user()->role === 'masyarakat' ? 'user.dashboard' : 'landing', 'icon' => 'home', 'active' => ['landing', 'user.dashboard']],
        ['label' => 'Cek Gejala', 'route' => 'consultation.index', 'icon' => 'stethoscope', 'active' => ['consultation.*']],
        ['label' => 'Riwayat', 'route' => 'history.index', 'icon' => 'history', 'active' => ['history.*']],
        ['label' => 'Informasi', 'route' => 'information', 'icon' => 'info', 'active' => ['information']],
    ];
@endphp

<header class="sticky top-0 z-40 border-b border-[#dce5f1] bg-white/95 backdrop-blur">
    <div class="dm-shell">
        <div class="flex h-20 items-center justify-between gap-4">
            <a href="{{ route('landing') }}" class="shrink-0">
                <x-diagnomed.logo />
            </a>

            <nav class="hidden items-center gap-5 lg:flex">
                @foreach($links as $link)
                    @php($canVisit = Route::has($link['route']) && (!in_array($link['route'], ['consultation.index', 'history.index'], true) || auth()->check()))
                    @if($canVisit)
                        <a href="{{ route($link['route']) }}" class="dm-nav-link {{ request()->routeIs($link['active']) ? 'dm-nav-link-active' : '' }}">
                            <x-diagnomed.icon :name="$link['icon']" />
                            {{ $link['label'] }}
                        </a>
                    @endif
                @endforeach
            </nav>

            <div class="hidden items-center gap-3 lg:flex">
                <div class="flex h-9 w-44 items-center gap-2 rounded-full border border-slate-800/30 bg-white px-3">
                    <x-diagnomed.icon name="search" class="h-4 w-4 text-slate-700" />
                    <span class="text-xs text-slate-500">Cari</span>
                </div>
                <button type="button" class="grid h-10 w-10 place-items-center rounded-full text-slate-800 hover:bg-blue-50" aria-label="Notifikasi">
                    <x-diagnomed.icon name="bell" />
                </button>

                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="dm-btn-light">Dashboard</a>
                    @else
                        <div class="flex items-center gap-2">
                            <div class="grid h-9 w-9 place-items-center overflow-hidden rounded-full bg-blue-100 text-xs font-bold text-blue-700">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="text-sm font-semibold text-slate-900">{{ Str::limit(auth()->user()->username, 12) }}</span>
                        </div>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="dm-btn-primary min-h-9 px-4 text-xs">Login</a>
                    <a href="{{ route('register') }}" class="dm-btn-light min-h-9 px-4 text-xs text-[#2385dd]">Registrasi</a>
                @endauth
            </div>

            <button type="button" data-toggle-target="#mobile-nav" class="grid h-11 w-11 place-items-center rounded-[6px] border border-[#dce5f1] bg-white lg:hidden" aria-label="Buka menu">
                <span class="h-0.5 w-5 bg-slate-900"></span>
                <span class="mt-1 h-0.5 w-5 bg-slate-900"></span>
                <span class="mt-1 h-0.5 w-5 bg-slate-900"></span>
            </button>
        </div>

        <div id="mobile-nav" class="hidden pb-4 lg:hidden">
            <nav class="grid gap-2">
                @foreach($links as $link)
                    @php($canVisit = Route::has($link['route']) && (!in_array($link['route'], ['consultation.index', 'history.index'], true) || auth()->check()))
                    @if($canVisit)
                        <a href="{{ route($link['route']) }}" class="flex min-h-11 items-center gap-3 rounded-[6px] px-3 text-sm font-semibold {{ request()->routeIs($link['active']) ? 'bg-blue-50 text-[#2385dd]' : 'text-slate-800' }}">
                            <x-diagnomed.icon :name="$link['icon']" />
                            {{ $link['label'] }}
                        </a>
                    @endif
                @endforeach
                @guest
                    <div class="grid grid-cols-2 gap-2 pt-2">
                        <a href="{{ route('login') }}" class="dm-btn-primary">Login</a>
                        <a href="{{ route('register') }}" class="dm-btn-light text-[#2385dd]">Registrasi</a>
                    </div>
                @else
                    <form method="POST" action="{{ route('logout') }}" class="pt-2">
                        @csrf
                        <button type="submit" class="dm-btn-light w-full">Logout</button>
                    </form>
                @endguest
            </nav>
        </div>
    </div>
</header>
