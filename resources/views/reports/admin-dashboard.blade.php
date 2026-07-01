@include('reports.partials.report-styles')

<div class="report-header">
    <h1>{{ $title }}</h1>
    <p class="muted">{{ $subtitle }}</p>
    <p>Dibuat: {{ $generated_at->format('d M Y H:i') }} WIB</p>
</div>

<div class="summary">
    @foreach($counts as $label => $value)
        <div class="summary-item">
            <div class="summary-label">{{ $label }}</div>
            <div class="summary-value">{{ $value }}</div>
        </div>
    @endforeach
</div>

@include('reports.partials.sections', ['sections' => $sections])
