<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum VisitStatus: string implements HasLabel, HasColor
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case ON_HOLD = 'on_hold';
    case TRANSFERRED = 'transferred';
    case REJECTED = 'rejected';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => 'قيد الانتظار',
            self::IN_PROGRESS => 'قيد المعالجة',
            self::COMPLETED => 'منجزة',
            self::ON_HOLD => 'معلّقة / ناقصة أوراق',
            self::TRANSFERRED => 'محوّلة',
            self::REJECTED => 'مرفوضة',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::IN_PROGRESS => 'info',
            self::COMPLETED => 'success',
            self::ON_HOLD => 'warning',
            self::TRANSFERRED => 'primary',
            self::REJECTED => 'danger',
        };
    }
}