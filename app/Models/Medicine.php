<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $fillable = [
        'code', 'disease_id', 'name', 'category', 'dosage', 'usage_rule',
        'side_effects', 'contraindication', 'warning', 'description', 'image_path', 'price', 'price_unit', 'is_active'
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'price' => 'integer',
        ];
    }

    public function disease()
    {
        return $this->belongsTo(Disease::class);
    }
}
