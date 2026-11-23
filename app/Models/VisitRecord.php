<?php

namespace App\Models;

use App\Enums\VisitStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use app\Models\Visitor;

class VisitRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_id',
        'directorate_id',
        'room_id',
        'visit_reason',
        'badge_number',
        'visitor_type',
        'current_status',
        'check_in',
        'check_out',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'current_status' => VisitStatus::class,
            'check_in' => 'datetime',
            'check_out' => 'datetime',
        ];
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function directorate(): BelongsTo
    {
        return $this->belongsTo(Directorate::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(VisitStatusHistory::class);
    }
}