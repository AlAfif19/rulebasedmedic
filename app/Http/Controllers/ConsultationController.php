<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Symptom;
use App\Services\ExpertSystemService;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function index()
    {
        $symptoms = Symptom::query()->where('is_active', true)->orderBy('code')->get()->groupBy('category');
        return view('user.consultation.index', compact('symptoms'));
    }

    public function diagnose(Request $request, ExpertSystemService $service)
    {
        $data = $request->validate([
            'symptoms' => ['required', 'array', 'min:1'],
            'symptoms.*' => ['string', 'exists:symptoms,code'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $result = $service->analyze($data['symptoms']);

        $consultation = Consultation::create([
            'user_id' => auth()->id(),
            'disease_id' => optional($result['disease'])->id,
            'method' => $result['method'],
            'selected_symptom_codes' => $data['symptoms'],
            'result_payload' => [
                'disease' => optional($result['disease'])->only(['code', 'name', 'severity', 'solution']),
                'method_scores' => $result['method_scores'],
                'medicines' => $result['medicines']->map->only([
                    'code',
                    'name',
                    'category',
                    'dosage',
                    'usage_rule',
                    'side_effects',
                    'contraindication',
                    'warning',
                    'description',
                    'image_path',
                ])->values(),
                'matched_rule' => $result['matched_rule'] ? [
                    'code' => $result['matched_rule']['rule']->code,
                    'required_symptoms' => $result['matched_rule']['required_symptoms'],
                    'matched_symptoms' => $result['matched_rule']['matched_symptoms'],
                    'missing_symptoms' => $result['matched_rule']['missing_symptoms'],
                    'match_ratio' => $result['matched_rule']['match_ratio'],
                    'method_scores' => $result['matched_rule']['method_scores'],
                    'parallel_score' => $result['matched_rule']['parallel_score'],
                ] : null,
            ],
            'confidence_score' => $result['confidence_score'],
            'status' => $result['disease'] ? 'matched' : 'unmatched',
            'notes' => $data['notes'] ?? null,
            'recommendation_summary' => $result['disease']
                ? $result['disease']->name.' - '.$result['medicines']->pluck('name')->join(', ')
                : $result['message'],
        ]);

        return redirect()->route('consultation.show', $consultation)->with('success', 'Hasil rekomendasi berhasil dibuat.');
    }

    public function history(Request $request)
    {
        $baseQuery = Consultation::query()
            ->with('disease')
            ->where('user_id', auth()->id());

        $summary = [
            'total' => (clone $baseQuery)->count(),
            'diseases' => (clone $baseQuery)->whereNotNull('disease_id')->distinct('disease_id')->count('disease_id'),
            'medicines' => (clone $baseQuery)->get()->sum(fn ($item) => count(data_get($item->result_payload, 'medicines', []))),
            'latest' => optional((clone $baseQuery)->latest()->first())->created_at,
        ];

        $query = clone $baseQuery;

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($inner) use ($search) {
                $inner->where('recommendation_summary', 'like', "%{$search}%")
                    ->orWhereHas('disease', fn ($disease) => $disease->where('name', 'like', "%{$search}%"));
            });
        }

        if ($dateFrom = $request->date('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->date('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $request->string('sort')->toString() === 'oldest' ? $query->oldest() : $query->latest();

        $latestHistory = (clone $baseQuery)->latest()->first();
        $histories = $query->paginate(10)->withQueryString();

        return view('user.history.index', compact('histories', 'summary', 'latestHistory'));
    }

    public function show(Consultation $consultation)
    {
        abort_unless($consultation->user_id === auth()->id() || auth()->user()->role === 'admin', 403);
        $symptoms = Symptom::query()->whereIn('code', $consultation->selected_symptom_codes ?? [])->get();

        return view('user.consultation.show', compact('consultation', 'symptoms'));
    }
}
