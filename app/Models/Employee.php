<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['full_name'];

    // Relasi dengan MorningReflection (opsional, jika diperlukan)
    public function morningReflections()
    {
        return $this->hasMany(MorningReflection::class);
    }
}