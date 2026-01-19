<?php

declare(strict_types=1);

namespace Accelade\Filters\QueryBuilder\Constraints;

use Accelade\Filters\QueryBuilder\Constraint;
use Accelade\Filters\QueryBuilder\Operator;

/**
 * Boolean constraint for filtering boolean columns.
 */
class BooleanConstraint extends Constraint
{
    protected string $trueLabel = 'Yes';

    protected string $falseLabel = 'No';

    protected function setUp(): void
    {
        $this->icon = 'check-circle';

        $this->operators = [
            Operator::make('is_true', 'Is true', function ($query, $column, $value) {
                return $query->where($column, '=', true);
            })->requiresValue(false),

            Operator::make('is_false', 'Is false', function ($query, $column, $value) {
                return $query->where($column, '=', false);
            })->requiresValue(false),
        ];
    }

    /**
     * Set labels for true/false.
     */
    public function labels(string $trueLabel, string $falseLabel): static
    {
        $this->trueLabel = $trueLabel;
        $this->falseLabel = $falseLabel;

        return $this;
    }

    /**
     * Set the true label.
     */
    public function trueLabel(string $label): static
    {
        $this->trueLabel = $label;

        return $this;
    }

    /**
     * Set the false label.
     */
    public function falseLabel(string $label): static
    {
        $this->falseLabel = $label;

        return $this;
    }

    public function getType(): string
    {
        return 'boolean';
    }

    public function getSettings(): array
    {
        return [
            'trueLabel' => $this->trueLabel,
            'falseLabel' => $this->falseLabel,
        ];
    }
}
