<?php

declare(strict_types=1);

namespace Accelade\Filters\QueryBuilder\Constraints;

use Accelade\Filters\QueryBuilder\Constraint;
use Accelade\Filters\QueryBuilder\Operator;

/**
 * Number constraint for filtering numeric columns.
 */
class NumberConstraint extends Constraint
{
    protected bool $isInteger = false;

    protected ?int $min = null;

    protected ?int $max = null;

    protected ?float $step = null;

    protected function setUp(): void
    {
        $this->icon = 'hash';

        $this->operators = [
            Operator::make('equals', 'Equals', function ($query, $column, $value) {
                return $query->where($column, '=', $value);
            })->inputType('number'),

            Operator::make('not_equals', 'Does not equal', function ($query, $column, $value) {
                return $query->where($column, '!=', $value);
            })->inputType('number'),

            Operator::make('greater_than', 'Greater than', function ($query, $column, $value) {
                return $query->where($column, '>', $value);
            })->inputType('number'),

            Operator::make('greater_than_or_equal', 'Greater than or equal to', function ($query, $column, $value) {
                return $query->where($column, '>=', $value);
            })->inputType('number'),

            Operator::make('less_than', 'Less than', function ($query, $column, $value) {
                return $query->where($column, '<', $value);
            })->inputType('number'),

            Operator::make('less_than_or_equal', 'Less than or equal to', function ($query, $column, $value) {
                return $query->where($column, '<=', $value);
            })->inputType('number'),

            Operator::make('between', 'Between', function ($query, $column, $value) {
                if (is_array($value) && count($value) === 2) {
                    return $query->whereBetween($column, $value);
                }

                return $query;
            })->inputType('number-range'),

            Operator::make('is_blank', 'Is blank', function ($query, $column, $value) {
                return $query->whereNull($column);
            })->requiresValue(false),

            Operator::make('is_not_blank', 'Is not blank', function ($query, $column, $value) {
                return $query->whereNotNull($column);
            })->requiresValue(false),
        ];
    }

    /**
     * Set as integer-only constraint.
     */
    public function integer(bool $integer = true): static
    {
        $this->isInteger = $integer;

        return $this;
    }

    /**
     * Set minimum value.
     */
    public function min(?int $min): static
    {
        $this->min = $min;

        return $this;
    }

    /**
     * Set maximum value.
     */
    public function max(?int $max): static
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Set step value.
     */
    public function step(?float $step): static
    {
        $this->step = $step;

        return $this;
    }

    public function getType(): string
    {
        return 'number';
    }

    public function getSettings(): array
    {
        return [
            'integer' => $this->isInteger,
            'min' => $this->min,
            'max' => $this->max,
            'step' => $this->step ?? ($this->isInteger ? 1 : 0.01),
        ];
    }
}
