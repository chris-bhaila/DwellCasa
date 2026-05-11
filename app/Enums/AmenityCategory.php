<?php

namespace App\Enums;

enum AmenityCategory: string
{
    case Utilities      = 'utilities';
    case Comfort        = 'comfort';
    case Bathroom       = 'bathroom';
    case KitchenDining  = 'kitchen_and_dining';
    case Entertainment  = 'entertainment';
    case Safety         = 'safety';
    case Transport      = 'transport';
    case Wellness       = 'wellness';
    case Outdoor        = 'outdoor';
    case Business       = 'business';
    case Accessibility  = 'accessibility';
    case Housekeeping   = 'housekeeping';
    case General        = 'general';

    public function label(): string
    {
        return match($this) {
            self::Utilities     => 'Utilities',
            self::Comfort       => 'Comfort',
            self::Bathroom      => 'Bathroom',
            self::KitchenDining => 'Kitchen & Dining',
            self::Entertainment => 'Entertainment',
            self::Safety        => 'Safety',
            self::Transport     => 'Transport',
            self::Wellness      => 'Wellness',
            self::Outdoor       => 'Outdoor',
            self::Business      => 'Business',
            self::Accessibility => 'Accessibility',
            self::Housekeeping  => 'Housekeeping',
            self::General       => 'General',
        };
    }
}
