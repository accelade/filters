<?php

declare(strict_types=1);

namespace Accelade\Filters\Components;

use Accelade\Filters\Filter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Date filter component.
 */
class DateFilter extends Filter
{
    protected string $operator = '=';

    protected ?string $format = 'Y-m-d';

    protected ?string $minDate = null;

    protected ?string $maxDate = null;

    protected bool $withTime = false;

    protected bool $native = true;

    protected ?string $displayFormat = null;

    /**
     * Set the operator.
     */
    public function operator(string $operator): static
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * Filter for dates on or after.
     */
    public function from(): static
    {
        $this->operator = '>=';

        return $this;
    }

    /**
     * Filter for dates on or before.
     */
    public function until(): static
    {
        $this->operator = '<=';

        return $this;
    }

    /**
     * Set the date format.
     */
    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get the date format.
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Set minimum selectable date.
     */
    public function minDate(string $date): static
    {
        $this->minDate = $date;

        return $this;
    }

    /**
     * Get minimum date.
     */
    public function getMinDate(): ?string
    {
        return $this->minDate;
    }

    /**
     * Set maximum selectable date.
     */
    public function maxDate(string $date): static
    {
        $this->maxDate = $date;

        return $this;
    }

    /**
     * Get maximum date.
     */
    public function getMaxDate(): ?string
    {
        return $this->maxDate;
    }

    /**
     * Include time in the filter.
     */
    public function withTime(bool $withTime = true): static
    {
        $this->withTime = $withTime;
        if ($withTime) {
            $this->format = 'Y-m-d H:i:s';
        }

        return $this;
    }

    /**
     * Check if time is included.
     */
    public function hasTime(): bool
    {
        return $this->withTime;
    }

    /**
     * Use native browser date input.
     */
    public function native(bool $native = true): static
    {
        $this->native = $native;

        return $this;
    }

    /**
     * Check if using native input.
     */
    public function isNative(): bool
    {
        return $this->native;
    }

    /**
     * Set the display format for the date picker.
     */
    public function displayFormat(string $format): static
    {
        $this->displayFormat = $format;

        return $this;
    }

    /**
     * Get the display format.
     */
    public function getDisplayFormat(): ?string
    {
        return $this->displayFormat;
    }

    /**
     * Apply the filter.
     */
    public function apply(Builder $query, mixed $value): Builder
    {
        if ($value === null || $value === '') {
            return $query;
        }

        $column = $this->getColumn();

        try {
            $date = Carbon::parse($value);

            if (! $this->withTime) {
                $date = $date->startOfDay();
            }

            return match ($this->operator) {
                '>=' => $query->where($column, '>=', $date),
                '<=' => $query->where($column, '<=', $date->endOfDay()),
                '>' => $query->where($column, '>', $date),
                '<' => $query->where($column, '<', $date),
                default => $query->whereDate($column, $date),
            };
        } catch (\Exception $e) {
            return $query;
        }
    }

    /**
     * Get the view name.
     */
    public function getView(): string
    {
        return 'accelade::filters.date';
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'operator' => $this->operator,
            'format' => $this->format,
            'min_date' => $this->minDate,
            'max_date' => $this->maxDate,
            'with_time' => $this->withTime,
            'native' => $this->native,
            'display_format' => $this->displayFormat,
        ]);
    }
}
