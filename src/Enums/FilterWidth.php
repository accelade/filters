<?php

declare(strict_types=1);

namespace Accelade\Filters\Enums;

/**
 * Filter form width options.
 */
enum FilterWidth: string
{
    case ExtraSmall = 'xs';
    case Small = 'sm';
    case Medium = 'md';
    case Large = 'lg';
    case ExtraLarge = 'xl';
    case TwoExtraLarge = '2xl';
    case ThreeExtraLarge = '3xl';
    case FourExtraLarge = '4xl';
    case FiveExtraLarge = '5xl';
    case SixExtraLarge = '6xl';
    case Full = 'full';

    /**
     * Get CSS max-width value.
     */
    public function getMaxWidth(): string
    {
        return match ($this) {
            self::ExtraSmall => '20rem',
            self::Small => '24rem',
            self::Medium => '28rem',
            self::Large => '32rem',
            self::ExtraLarge => '36rem',
            self::TwoExtraLarge => '42rem',
            self::ThreeExtraLarge => '48rem',
            self::FourExtraLarge => '56rem',
            self::FiveExtraLarge => '64rem',
            self::SixExtraLarge => '72rem',
            self::Full => '100%',
        };
    }

    /**
     * Get Tailwind class.
     */
    public function getTailwindClass(): string
    {
        return match ($this) {
            self::ExtraSmall => 'max-w-xs',
            self::Small => 'max-w-sm',
            self::Medium => 'max-w-md',
            self::Large => 'max-w-lg',
            self::ExtraLarge => 'max-w-xl',
            self::TwoExtraLarge => 'max-w-2xl',
            self::ThreeExtraLarge => 'max-w-3xl',
            self::FourExtraLarge => 'max-w-4xl',
            self::FiveExtraLarge => 'max-w-5xl',
            self::SixExtraLarge => 'max-w-6xl',
            self::Full => 'max-w-full',
        };
    }
}
