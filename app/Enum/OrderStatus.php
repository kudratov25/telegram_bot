<?php

namespace App\Enum;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    public static function toArray(): array
    {
        return array_column(OrderStatus::cases(), 'value');
    }
}
