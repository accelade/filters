<?php

declare(strict_types=1);

namespace Accelade\Filters\QueryBuilder\Constraints;

use Accelade\Filters\Concerns\HasOptions;
use Accelade\Filters\QueryBuilder\Constraint;
use Accelade\Filters\QueryBuilder\Operator;

/**
 * Select constraint for filtering with predefined options.
 */
class SelectConstraint extends Constraint
{
    use HasOptions;

    protected bool $multiple = false;

    protected bool $searchable = false;

    protected function setUp(): void
    {
        $this->icon = 'list';

        $this->operators = [
            Operator::make('is', 'Is', function ($query, $column, $value) {
                if (is_array($value)) {
                    return $query->whereIn($column, $value);
                }

                return $query->where($column, '=', $value);
            })->inputType('select'),

            Operator::make('is_not', 'Is not', function ($query, $column, $value) {
                if (is_array($value)) {
                    return $query->whereNotIn($column, $value);
                }

                return $query->where($column, '!=', $value);
            })->inputType('select'),

            Operator::make('is_blank', 'Is blank', function ($query, $column, $value) {
                return $query->whereNull($column);
            })->requiresValue(false),

            Operator::make('is_not_blank', 'Is not blank', function ($query, $column, $value) {
                return $query->whereNotNull($column);
            })->requiresValue(false),
        ];
    }

    /**
     * Allow multiple selection.
     */
    public function multiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * Enable search in options.
     */
    public function searchable(bool $searchable = true): static
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function getType(): string
    {
        return 'select';
    }

    public function getSettings(): array
    {
        return [
            'multiple' => $this->multiple,
            'searchable' => $this->searchable,
            'options' => $this->getFormattedOptions(),
        ];
    }
}
