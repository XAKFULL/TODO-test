<?php

namespace App\Enums;

enum TaskStatusEnum: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'В ожидании',
            self::IN_PROGRESS => 'В процессе',
            self::COMPLETED => 'Завершено',
        };
    }
}
