<?php

declare(strict_types=1);

namespace Accelade\Filters\Components;

use Accelade\Filters\Filter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

/**
 * Date range filter component.
 */
class DateRangeFilter extends Filter
{
    protected ?string $format = 'Y-m-d';

    protected ?string $minDate = null;

    protected ?string $maxDate = null;

    protected bool $withTime = false;

    protected string $fromKey = 'from';

    protected string $toKey = 'to';

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
     * Set the from/to keys.
     */
    public function keys(string $fromKey, string $toKey): static
    {
        $this->fromKey = $fromKey;
        $this->toKey = $toKey;

        return $this;
    }

    /**
     * Get the from key.
     */
    public function getFromKey(): string
    {
        return $this->fromKey;
    }

    /**
     * Get the to key.
     */
    public function getToKey(): string
    {
        return $this->toKey;
    }

    /**
     * Check if the filter is active.
     */
    public function isActive(): bool
    {
        $value = $this->getValue();

        if (! is_array($value)) {
            return false;
        }

        return ! empty($value[$this->fromKey]) || ! empty($value[$this->toKey]);
    }

    /**
     * Apply the filter.
     */
    public function apply(Builder $query, mixed $value): Builder
    {
        if (! is_array($value)) {
            return $query;
        }

        $column = $this->getColumn();
        $from = $value[$this->fromKey] ?? null;
        $to = $value[$this->toKey] ?? null;

        if ($from) {
            try {
                $fromDate = Carbon::parse($from);
                if (! $this->withTime) {
                    $fromDate = $fromDate->startOfDay();
                }
                $query->where($column, '>=', $fromDate);
            } catch (\Exception $e) {
                Log::debug("DateRangeFilter: Invalid 'from' date format", ['value' => $from, 'error' => $e->getMessage()]);
            }
        }

        if ($to) {
            try {
                $toDate = Carbon::parse($to);
                if (! $this->withTime) {
                    $toDate = $toDate->endOfDay();
                }
                $query->where($column, '<=', $toDate);
            } catch (\Exception $e) {
                Log::debug("DateRangeFilter: Invalid 'to' date format", ['value' => $to, 'error' => $e->getMessage()]);
            }
        }

        return $query;
    }

    /**
     * Get the view name.
     */
    public function getView(): string
    {
        return 'accelade::filters.date-range';
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'format' => $this->format,
            'min_date' => $this->minDate,
            'max_date' => $this->maxDate,
            'with_time' => $this->withTime,
            'from_key' => $this->fromKey,
            'to_key' => $this->toKey,
        ]);
    }
}
