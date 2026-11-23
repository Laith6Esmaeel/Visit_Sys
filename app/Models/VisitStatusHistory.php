<?php

namespace App\Models;

use App\Enums\VisitStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_record_id',
        'employee_id',
        'status',
        'notes',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => VisitStatus::class,
            'changed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (VisitStatusHistory $history) {
            $history->visitRecord()->update([
                'current_status' => $history->status,
            ]);
        });
    }

    public function visitRecord(): BelongsTo
    {
        return $this->belongsTo(VisitRecord::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}