<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disease extends Model
{
    protected $fillable = ['code', 'name', 'description', 'solution', 'severity', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function medicines()
    {
        return $this->hasMany(Medicine::class);
    }
}
