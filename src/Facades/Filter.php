<?php

declare(strict_types=1);

namespace Accelade\Filters\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Accelade\Filters\Components\TextFilter text(string $name)
 * @method static \Accelade\Filters\Components\SelectFilter select(string $name)
 * @method static \Accelade\Filters\Components\BooleanFilter boolean(string $name)
 * @method static \Accelade\Filters\Components\DateFilter date(string $name)
 * @method static \Accelade\Filters\Components\DateRangeFilter dateRange(string $name)
 * @method static \Accelade\Filters\Components\NumberFilter number(string $name)
 *
 * @see \Accelade\Filters\FilterFactory
 */
class Filter extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'accelade.filter';
    }
}
