<?php

declare(strict_types=1);

namespace Accelade\Filters\Components;

use Accelade\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Number filter component.
 */
class NumberFilter extends Filter
{
    protected string $operator = '=';

    protected ?float $min = null;

    protected ?float $max = null;

    protected ?float $step = null;

    /**
     * Set the operator.
     */
    public function operator(string $operator): static
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * Filter for greater than or equal.
     */
    public function greaterThanOrEqual(): static
    {
        $this->operator = '>=';

        return $this;
    }

    /**
     * Filter for less than or equal.
     */
    public function lessThanOrEqual(): static
    {
        $this->operator = '<=';

        return $this;
    }

    /**
     * Filter for greater than.
     */
    public function greaterThan(): static
    {
        $this->operator = '>';

        return $this;
    }

    /**
     * Filter for less than.
     */
    public function lessThan(): static
    {
        $this->operator = '<';

        return $this;
    }

    /**
     * Set minimum value.
     */
    public function min(float $min): static
    {
        $this->min = $min;

        return $this;
    }

    /**
     * Get minimum value.
     */
    public function getMin(): ?float
    {
        return $this->min;
    }

    /**
     * Set maximum value.
     */
    public function max(float $max): static
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Get maximum value.
     */
    public function getMax(): ?float
    {
        return $this->max;
    }

    /**
     * Set step value.
     */
    public function step(float $step): static
    {
        $this->step = $step;

        return $this;
    }

    /**
     * Get step value.
     */
    public function getStep(): ?float
    {
        return $this->step;
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
        $numericValue = is_numeric($value) ? (float) $value : null;

        if ($numericValue === null) {
            return $query;
        }

        return $query->where($column, $this->operator, $numericValue);
    }

    /**
     * Get the view name.
     */
    public function getView(): string
    {
        return 'accelade::filters.number';
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'operator' => $this->operator,
            'min' => $this->min,
            'max' => $this->max,
            'step' => $this->step,
        ]);
    }
}
