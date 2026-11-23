<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Directorate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function visitRecords(): HasMany
    {
        return $this->hasMany(VisitRecord::class);
    }
}