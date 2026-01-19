<?php

declare(strict_types=1);

namespace Accelade\Filters\Components;

use Accelade\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Text/search filter component.
 */
class TextFilter extends Filter
{
    protected string $operator = 'like';

    protected bool $caseSensitive = false;

    /**
     * Set the operator.
     */
    public function operator(string $operator): static
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * Use exact match.
     */
    public function exact(): static
    {
        $this->operator = '=';

        return $this;
    }

    /**
     * Use contains match (default).
     */
    public function contains(): static
    {
        $this->operator = 'like';

        return $this;
    }

    /**
     * Use starts with match.
     */
    public function startsWith(): static
    {
        $this->operator = 'starts_with';

        return $this;
    }

    /**
     * Use ends with match.
     */
    public function endsWith(): static
    {
        $this->operator = 'ends_with';

        return $this;
    }

    /**
     * Set case sensitivity.
     */
    public function caseSensitive(bool $caseSensitive = true): static
    {
        $this->caseSensitive = $caseSensitive;

        return $this;
    }

    /**
     * Get the operator.
     */
    public function getOperator(): string
    {
        return $this->operator;
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

        return match ($this->operator) {
            '=' => $query->where($column, $value),
            'starts_with' => $this->applyLike($query, $column, "{$value}%"),
            'ends_with' => $this->applyLike($query, $column, "%{$value}"),
            default => $this->applyLike($query, $column, "%{$value}%"),
        };
    }

    /**
     * Apply a LIKE query.
     */
    protected function applyLike(Builder $query, string $column, string $value): Builder
    {
        if ($this->caseSensitive) {
            return $query->where($column, 'LIKE', $value);
        }

        return $query->whereRaw("LOWER({$column}) LIKE ?", [strtolower($value)]);
    }

    /**
     * Get the view name.
     */
    public function getView(): string
    {
        return 'accelade::filters.text';
    }
}
