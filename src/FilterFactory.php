<?php

declare(strict_types=1);

namespace Accelade\Filters;

use Accelade\Filters\Components\BooleanFilter;
use Accelade\Filters\Components\DateFilter;
use Accelade\Filters\Components\DateRangeFilter;
use Accelade\Filters\Components\NumberFilter;
use Accelade\Filters\Components\SelectFilter;
use Accelade\Filters\Components\TextFilter;

/**
 * Factory for creating filter instances.
 */
class FilterFactory
{
    /**
     * Create a text filter.
     */
    public function text(string $name): TextFilter
    {
        return TextFilter::make($name);
    }

    /**
     * Create a select filter.
     */
    public function select(string $name): SelectFilter
    {
        return SelectFilter::make($name);
    }

    /**
     * Create a boolean filter.
     */
    public function boolean(string $name): BooleanFilter
    {
        return BooleanFilter::make($name);
    }

    /**
     * Create a date filter.
     */
    public function date(string $name): DateFilter
    {
        return DateFilter::make($name);
    }

    /**
     * Create a date range filter.
     */
    public function dateRange(string $name): DateRangeFilter
    {
        return DateRangeFilter::make($name);
    }

    /**
     * Create a number filter.
     */
    public function number(string $name): NumberFilter
    {
        return NumberFilter::make($name);
    }
}
