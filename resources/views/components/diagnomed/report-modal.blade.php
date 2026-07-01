@props([
    'id',
    'title',
    'previewUrl',
    'downloadBaseUrl',
])

<div id="{{ $id }}" data-modal data-report-modal data-report-download-base="{{ $downloadBaseUrl }}" class="fixed inset-0 z-50 hidden bg-slate-950/60 p-4">
    <div class="mx-auto flex h-full max-w-6xl flex-col overflow-hidden rounded-[8px] bg-white shadow-2xl">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#dce5f1] bg-[#f8fbff] px-5 py-4">
            <div>
                <p class="text-xs font-bold uppercase text-[#2385dd]">Preview Laporan</p>
                <h2 class="text-base font-bold text-slate-950">{{ $title }}</h2>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <select data-report-format class="dm-input h-10 w-32 text-xs">
                    <option value="pdf">PDF</option>
                    <option value="doc">DOC</option>
                    <option value="excel">Excel</option>
                    <option value="json">JSON</option>
                    <option value="csv">CSV</option>
                </select>
                <a data-report-download href="{{ $downloadBaseUrl }}/pdf" class="dm-btn-primary h-10 px-4 text-xs">
                    Download
                </a>
                <button type="button" data-modal-close class="dm-btn-light h-10 px-4 text-xs">Tutup</button>
            </div>
        </div>
        <iframe src="{{ $previewUrl }}" title="{{ $title }}" class="min-h-0 flex-1 bg-white"></iframe>
    </div>
</div>
