<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    protected $fillable = [
        'user_id', 'disease_id', 'method', 'selected_symptom_codes', 'result_payload',
        'confidence_score', 'status', 'notes', 'recommendation_summary'
    ];

    protected function casts(): array
    {
        return [
            'selected_symptom_codes' => 'array',
            'result_payload' => 'array',
            'confidence_score' => 'float',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function disease()
    {
        return $this->belongsTo(Disease::class);
    }
}
