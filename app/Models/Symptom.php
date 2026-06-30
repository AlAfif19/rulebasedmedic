<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Symptom extends Model
{
    protected $fillable = ['code', 'name', 'description', 'category', 'duration', 'body_location', 'frequency', 'weight', 'is_active'];

    protected function casts(): array
    {
        return ['weight' => 'float', 'is_active' => 'boolean'];
    }
}
