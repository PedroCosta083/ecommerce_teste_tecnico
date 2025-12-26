<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    /**
     * Status finais (não permitem alteração)
     */
    public function isFinal(): bool
    {
        return match ($this) {
            self::DELIVERED,
            self::CANCELLED => true,
            default => false,
        };
    }

    /**
     * Status que permitem avanço no fluxo
     */
    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::PENDING => in_array($next, [self::PROCESSING, self::CANCELLED]),
            self::PROCESSING => in_array($next, [self::SHIPPED, self::CANCELLED]),
            self::SHIPPED => in_array($next, [self::DELIVERED]),
            self::DELIVERED,
            self::CANCELLED => false,
        };
    }

    /**
     * Lista simples para validações (FormRequest)
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
