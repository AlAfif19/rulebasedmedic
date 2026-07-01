@include('reports.partials.report-styles')

<div class="report-header">
    <h1>{{ $title }}</h1>
    <p class="muted">{{ $subtitle }}</p>
    <p>Dibuat: {{ $generated_at->format('d M Y H:i') }} WIB</p>
</div>

<p><strong>Nama:</strong> {{ $owner->name }}</p>
<p><strong>Username:</strong> {{ $owner->username }}</p>
<p><strong>Email:</strong> {{ $owner->email }}</p>

<div class="summary">
    <div class="summary-item">
        <div class="summary-label">Total Diagnosis</div>
        <div class="summary-value">{{ $summary['total'] }}</div>
    </div>
    <div class="summary-item">
        <div class="summary-label">Penyakit</div>
        <div class="summary-value">{{ $summary['diseases'] }}</div>
    </div>
    <div class="summary-item">
        <div class="summary-label">Obat</div>
        <div class="summary-value">{{ $summary['medicines'] }}</div>
    </div>
    <div class="summary-item">
        <div class="summary-label">Terakhir</div>
        <div class="summary-value">{{ $summary['latest'] ? $summary['latest']->format('d M Y') : '-' }}</div>
    </div>
</div>

@include('reports.partials.sections', ['sections' => $sections])
