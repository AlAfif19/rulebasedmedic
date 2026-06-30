<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DiagnoMed')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="dm-page antialiased">
    @include('partials.navbar')

    <main>
        @if(session('success'))
            <div class="dm-shell mt-4">
                <div class="rounded-[8px] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
            </div>
        @endif
        @if($errors->any())
            <div class="dm-shell mt-4">
                <div class="rounded-[8px] border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ $errors->first() }}</div>
            </div>
        @endif

        @yield('content')
    </main>

    @include('partials.footer')
</body>
</html>
