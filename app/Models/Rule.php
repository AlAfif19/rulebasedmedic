<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    protected $fillable = [
        'code', 'disease_id', 'symptom_codes', 'medicine_codes', 'cf_value',
        'method', 'description', 'is_active'
    ];

    protected function casts(): array
    {
        return [
            'symptom_codes' => 'array',
            'medicine_codes' => 'array',
            'cf_value' => 'float',
            'is_active' => 'boolean',
        ];
    }

    public function disease()
    {
        return $this->belongsTo(Disease::class);
    }
}
