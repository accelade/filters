<?php

declare(strict_types=1);

namespace Accelade\Filters\Components;

use Accelade\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Boolean/toggle filter component.
 */
class BooleanFilter extends Filter
{
    protected ?string $trueLabel = null;

    protected ?string $falseLabel = null;

    protected bool $nullable = false;

    /**
     * Set the label for true value.
     */
    public function trueLabel(string $label): static
    {
        $this->trueLabel = $label;

        return $this;
    }

    /**
     * Get the true label.
     */
    public function getTrueLabel(): string
    {
        return $this->trueLabel ?? 'Yes';
    }

    /**
     * Set the label for false value.
     */
    public function falseLabel(string $label): static
    {
        $this->falseLabel = $label;

        return $this;
    }

    /**
     * Get the false label.
     */
    public function getFalseLabel(): string
    {
        return $this->falseLabel ?? 'No';
    }

    /**
     * Allow null/unset state.
     */
    public function nullable(bool $nullable = true): static
    {
        $this->nullable = $nullable;

        return $this;
    }

    /**
     * Check if nullable.
     */
    public function isNullable(): bool
    {
        return $this->nullable;
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
        $boolValue = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($boolValue === null) {
            return $query;
        }

        return $query->where($column, $boolValue);
    }

    /**
     * Get the view name.
     */
    public function getView(): string
    {
        return 'accelade::filters.boolean';
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'true_label' => $this->getTrueLabel(),
            'false_label' => $this->getFalseLabel(),
            'nullable' => $this->nullable,
        ]);
    }
}
