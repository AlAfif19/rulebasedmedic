@extends('layouts.admin')
@section('title', ($item ? 'Edit ' : 'Tambah ').$config['title'])
@section('page_title', ($item ? 'Edit ' : 'Tambah ').$config['title'])
@section('content')
<form method="POST" action="{{ $item ? route('admin.resource.update', [$resource, $item->id]) : route('admin.resource.store', $resource) }}" class="dm-card p-5">
    @csrf
    @if($item)
        @method('PUT')
    @endif

    <div class="mb-5 rounded-[8px] border border-blue-100 bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-800">
        Isi data sesuai basis pengetahuan apoteker. Field kode harus unik dan konsisten dengan rule.
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        @foreach($config['fields'] as $field)
            @php($value = old($field, $item ? data_get($item, $field) : ''))
            <div class="{{ in_array($field, ['description','solution','usage_rule','side_effects','contraindication','warning','address','value'], true) ? 'md:col-span-2' : '' }}">
                <label class="form-label">{{ Str::headline($field) }}</label>
                @if($field === 'disease_id')
                    <select name="disease_id" class="dm-input">
                        <option value="">Pilih Penyakit</option>
                        @foreach($diseases as $disease)
                            <option value="{{ $disease->id }}" @selected((string)$value === (string)$disease->id)>{{ $disease->code }} - {{ $disease->name }}</option>
                        @endforeach
                    </select>
                @elseif($field === 'role')
                    <select name="role" class="dm-input">
                        <option value="masyarakat" @selected($value === 'masyarakat')>Masyarakat</option>
                        <option value="admin" @selected($value === 'admin')>Admin Apoteker</option>
                    </select>
                @elseif($field === 'method')
                    <input type="hidden" name="method" value="parallel">
                    <div class="rounded-[6px] border border-blue-100 bg-blue-50 px-3 py-3 text-sm font-semibold text-blue-800">
                        Paralel: Rule Based, Forward Chaining, Backward Chaining, dan Certainty Factor
                    </div>
                @elseif($field === 'is_active')
                    <label class="inline-flex min-h-11 items-center gap-2 text-sm font-semibold text-slate-700">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-[#2385dd]" @checked((bool)$value || !$item)>
                        Aktif
                    </label>
                @elseif(in_array($field, ['description','solution','usage_rule','side_effects','contraindication','warning','address','value'], true))
                    <textarea name="{{ $field }}" class="dm-textarea" rows="4">{{ $value }}</textarea>
                @elseif(in_array($field, ['symptom_codes','medicine_codes'], true))
                    <input name="{{ $field }}" class="dm-input" value="{{ is_array($value) ? implode(', ', $value) : $value }}" placeholder="Pisahkan kode dengan koma, contoh: G001, G009, G011">
                @elseif($field === 'password')
                    <input type="password" name="password" class="dm-input" placeholder="Isi hanya jika ingin mengubah password">
                @else
                    <input name="{{ $field }}" class="dm-input" value="{{ $value }}">
                @endif
            </div>
        @endforeach
    </div>

    <div class="mt-6 flex flex-col gap-3 sm:flex-row">
        <button class="dm-btn-primary" type="submit">Simpan</button>
        <a href="{{ route('admin.resource.index', $resource) }}" class="dm-btn-light">Batal</a>
    </div>
</form>
@endsection
