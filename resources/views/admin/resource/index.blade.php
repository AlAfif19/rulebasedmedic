@extends('layouts.admin')
@section('title', $config['title'])
@section('page_title', $config['title'])
@section('content')
<section class="dm-card overflow-hidden">
    <div class="flex flex-col justify-between gap-4 border-b border-[#dce5f1] bg-[#f8fbff] px-5 py-4 sm:flex-row sm:items-center">
        <div class="flex items-center gap-3">
            <div class="grid h-8 w-8 place-items-center rounded-full bg-blue-100 text-[#2385dd]">
                <x-diagnomed.icon name="info" />
            </div>
            <p class="text-sm font-semibold text-slate-700">Kelola {{ strtolower($config['title']) }} yang digunakan dalam sistem</p>
        </div>
        @unless($config['readonly'] ?? false)
            <a class="dm-btn-primary min-h-9 px-4" href="{{ route('admin.resource.create', $resource) }}">
                <x-diagnomed.icon name="plus" />
                Tambah {{ Str::after($config['title'], 'Data ') ?: 'Data' }}
            </a>
        @endunless
    </div>

    <form method="GET" data-auto-submit-search data-search-delay="350" class="grid gap-3 border-b border-[#dce5f1] px-5 py-4 lg:grid-cols-[1fr_220px_180px_auto]">
        <div class="flex h-10 items-center gap-2 rounded-[6px] border border-[#c8d4e4] bg-white px-3">
            <x-diagnomed.icon name="search" class="h-4 w-4 text-slate-500" />
            <input class="h-full flex-1 bg-transparent text-sm outline-none" type="search" name="q" value="{{ request('q') }}" autocomplete="off" placeholder="Cari data atau kode">
        </div>
        <select class="dm-input h-10" name="category">
            <option value="">Semua Kategori</option>
            @foreach($items->pluck('category')->filter()->unique()->values() as $category)
                <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
            @endforeach
        </select>
        <select class="dm-input h-10" name="status">
            <option value="">Semua Status</option>
            <option value="active" @selected(request('status') === 'active')>Aktif</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
        </select>
        <div class="flex gap-2">
            <button class="dm-btn-light h-10 px-4 text-[#2385dd]" type="submit"><x-diagnomed.icon name="filter" /> Filter</button>
            <a class="dm-btn-light h-10 px-4" href="{{ route('admin.resource.index', $resource) }}"><x-diagnomed.icon name="reset" /> Reset</a>
        </div>
    </form>

    <div class="overflow-x-auto p-5">
        <table class="dm-table overflow-hidden rounded-[8px] border border-[#dce5f1]">
            <thead>
                <tr>
                    <th class="dm-th">No</th>
                    @foreach($config['columns'] as $label)
                        <th class="dm-th">{{ $label }}</th>
                    @endforeach
                    <th class="dm-th">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td class="dm-td">{{ $items->firstItem() + $loop->index }}</td>
                        @foreach($config['columns'] as $key => $label)
                            <td class="dm-td">
                                @php($value = data_get($item, $key))
                                @if($key === 'disease_id')
                                    {{ optional($item->disease)->name ?? $value }}
                                @elseif($key === 'user_id')
                                    {{ optional($item->user)->name ?? $value }}
                                @elseif(in_array($key, ['category', 'severity', 'status'], true))
                                    <x-diagnomed.badge :tone="$value">{{ $value ?: '-' }}</x-diagnomed.badge>
                                @elseif($key === 'is_active')
                                    <x-diagnomed.badge :tone="$value ? 'aktif' : 'nonaktif'">{{ $value ? 'Ya' : 'Tidak' }}</x-diagnomed.badge>
                                @elseif(is_array($value))
                                    {{ Str::limit(implode(', ', $value), 70) }}
                                @elseif($key === 'created_at' && $value)
                                    {{ $value->format('d M Y H:i') }}
                                @else
                                    {{ Str::limit((string) $value, 90) }}
                                @endif
                            </td>
                        @endforeach
                        <td class="dm-td">
                            <div class="flex items-center gap-2">
                                @unless($config['readonly'] ?? false)
                                    <a class="grid h-8 w-8 place-items-center rounded-[6px] border border-blue-200 text-[#2385dd]" href="{{ route('admin.resource.edit', [$resource, $item->id]) }}" aria-label="Edit">
                                        <x-diagnomed.icon name="edit" />
                                    </a>
                                @endunless
                                <form method="POST" action="{{ route('admin.resource.destroy', [$resource, $item->id]) }}" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="grid h-8 w-8 place-items-center rounded-[6px] border border-red-200 text-red-500" type="submit" aria-label="Hapus">
                                        <x-diagnomed.icon name="trash" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($config['columns']) + 2 }}" class="p-8 text-center text-sm text-slate-500">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-[#dce5f1] px-5 py-4">
        <x-diagnomed.pagination :paginator="$items" />
    </div>
</section>
@endsection
