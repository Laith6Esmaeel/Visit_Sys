<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'national_id',
        'first_name',
        'last_name',
        'father_name',
        'mother_name',
        'address',
        'phone',
    ];

    public function visitRecords(): HasMany
    {
        return $this->hasMany(VisitRecord::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->father_name} {$this->last_name}");
    }
}
