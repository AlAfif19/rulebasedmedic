@extends('layouts.admin')
@section('title', ($item ? 'Edit ' : 'Tambah ').$config['title'])
@section('page_title', ($item ? 'Edit ' : 'Tambah ').$config['title'])
@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ $item ? route('admin.resource.update', [$resource, $item->id]) : route('admin.resource.store', $resource) }}" class="dm-card p-5" @if($resource === 'obat') data-upload-form @endif>
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
            <div class="{{ in_array($field, ['description','solution','usage_rule','side_effects','contraindication','warning','address','value','image_path'], true) ? 'md:col-span-2' : '' }}">
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
                @elseif($field === 'image_path')
                    <div class="grid gap-3 md:grid-cols-[220px_1fr] md:items-start">
                        <div class="overflow-hidden rounded-[8px] border border-[#dce5f1] bg-[#f8fbff]">
                            @if($value)
                                <img data-upload-preview src="{{ asset($value) }}" alt="Preview gambar obat" class="h-36 w-full object-contain p-3">
                            @else
                                <div data-upload-empty class="grid h-36 place-items-center text-xs font-semibold text-slate-500">Belum ada gambar</div>
                                <img data-upload-preview src="" alt="Preview gambar obat" class="hidden h-36 w-full object-contain p-3">
                            @endif
                        </div>
                        <div>
                            <label class="grid min-h-36 cursor-pointer place-items-center rounded-[8px] border-2 border-dashed border-blue-200 bg-blue-50 px-4 py-5 text-center hover:bg-blue-100">
                                <input data-upload-file type="file" name="image_file" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="sr-only">
                                <span>
                                    <span class="block text-sm font-bold text-slate-950">Upload atau drag gambar obat</span>
                                    <span data-upload-idle-text class="mt-2 block text-xs leading-5 text-slate-600">Format JPG, PNG, WebP, atau SVG. Maksimal 1 MB. Jika tidak memilih file, gambar lama tetap dipakai.</span>
                                </span>
                            </label>
                            <input name="image_path" class="dm-input mt-3" value="{{ $value }}" placeholder="assets/images/medicine-box.svg">
                            <p class="mt-2 text-xs text-slate-500">Path ini bisa diisi manual jika gambar sudah ada di folder public.</p>
                        </div>
                    </div>
                @elseif(in_array($field, ['symptom_codes','medicine_codes'], true))
                    <input name="{{ $field }}" class="dm-input" value="{{ is_array($value) ? implode(', ', $value) : $value }}" placeholder="Pisahkan kode dengan koma, contoh: G001, G009, G011">
                @elseif($field === 'price')
                    <input type="number" name="price" min="0" step="500" class="dm-input" value="{{ $value ?: 5000 }}" placeholder="Contoh: 5000">
                @elseif($field === 'password')
                    <x-diagnomed.password-field name="password" placeholder="Isi hanya jika ingin mengubah password" autocomplete="new-password" />
                @else
                    <input name="{{ $field }}" class="dm-input" value="{{ $value }}">
                @endif
            </div>
        @endforeach
    </div>

    <div class="mt-6 flex flex-col gap-3 sm:flex-row">
        <button class="dm-btn-primary" type="submit" @if($resource === 'obat') data-upload-submit data-default-label="Simpan" @endif>Simpan</button>
        <a href="{{ route('admin.resource.index', $resource) }}" class="dm-btn-light">Batal</a>
    </div>
    @if($resource === 'obat')
        <div data-upload-status class="mt-4 hidden rounded-[8px] border border-blue-100 bg-blue-50 px-4 py-3 text-sm font-semibold text-blue-800">
            Gambar siap dipreview.
        </div>
    @endif
</form>
@endsection
