<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'room_id',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(VisitStatusHistory::class);
    }
    public function user(): \Illuminate\Database\Eloquent\Relations\HasOne
{
    return $this->hasOne(User::class);
}
}