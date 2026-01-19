<?php

declare(strict_types=1);

namespace Accelade\Filters\QueryBuilder\Constraints;

use Accelade\Filters\QueryBuilder\Constraint;
use Accelade\Filters\QueryBuilder\Operator;

/**
 * Text constraint for filtering text/string columns.
 */
class TextConstraint extends Constraint
{
    protected function setUp(): void
    {
        $this->icon = 'font';

        $this->operators = [
            Operator::make('contains', 'Contains', function ($query, $column, $value) {
                return $query->where($column, 'like', "%{$value}%");
            }),

            Operator::make('not_contains', 'Does not contain', function ($query, $column, $value) {
                return $query->where($column, 'not like', "%{$value}%");
            }),

            Operator::make('starts_with', 'Starts with', function ($query, $column, $value) {
                return $query->where($column, 'like', "{$value}%");
            }),

            Operator::make('ends_with', 'Ends with', function ($query, $column, $value) {
                return $query->where($column, 'like', "%{$value}");
            }),

            Operator::make('equals', 'Equals', function ($query, $column, $value) {
                return $query->where($column, '=', $value);
            }),

            Operator::make('not_equals', 'Does not equal', function ($query, $column, $value) {
                return $query->where($column, '!=', $value);
            }),

            Operator::make('is_blank', 'Is blank', function ($query, $column, $value) {
                return $query->where(function ($q) use ($column) {
                    $q->whereNull($column)->orWhere($column, '=', '');
                });
            })->requiresValue(false),

            Operator::make('is_not_blank', 'Is not blank', function ($query, $column, $value) {
                return $query->whereNotNull($column)->where($column, '!=', '');
            })->requiresValue(false),
        ];
    }

    public function getType(): string
    {
        return 'text';
    }
}
