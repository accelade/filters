<?php

declare(strict_types=1);

namespace Accelade\Filters\QueryBuilder\Constraints;

use Accelade\Filters\QueryBuilder\Constraint;
use Accelade\Filters\QueryBuilder\Operator;
use Carbon\Carbon;

/**
 * Date constraint for filtering date/datetime columns.
 */
class DateConstraint extends Constraint
{
    protected bool $withTime = false;

    protected ?string $minDate = null;

    protected ?string $maxDate = null;

    protected string $format = 'Y-m-d';

    protected function setUp(): void
    {
        $this->icon = 'calendar';

        $this->operators = [
            Operator::make('is', 'Is', function ($query, $column, $value) {
                $date = Carbon::parse($value);

                return $query->whereDate($column, '=', $date);
            })->inputType('date'),

            Operator::make('is_not', 'Is not', function ($query, $column, $value) {
                $date = Carbon::parse($value);

                return $query->whereDate($column, '!=', $date);
            })->inputType('date'),

            Operator::make('is_after', 'Is after', function ($query, $column, $value) {
                $date = Carbon::parse($value);

                return $query->whereDate($column, '>', $date);
            })->inputType('date'),

            Operator::make('is_on_or_after', 'Is on or after', function ($query, $column, $value) {
                $date = Carbon::parse($value);

                return $query->whereDate($column, '>=', $date);
            })->inputType('date'),

            Operator::make('is_before', 'Is before', function ($query, $column, $value) {
                $date = Carbon::parse($value);

                return $query->whereDate($column, '<', $date);
            })->inputType('date'),

            Operator::make('is_on_or_before', 'Is on or before', function ($query, $column, $value) {
                $date = Carbon::parse($value);

                return $query->whereDate($column, '<=', $date);
            })->inputType('date'),

            Operator::make('is_between', 'Is between', function ($query, $column, $value) {
                if (is_array($value) && count($value) === 2) {
                    $from = Carbon::parse($value[0])->startOfDay();
                    $to = Carbon::parse($value[1])->endOfDay();

                    return $query->whereBetween($column, [$from, $to]);
                }

                return $query;
            })->inputType('date-range'),

            Operator::make('is_month', 'Is in month', function ($query, $column, $value) {
                return $query->whereMonth($column, '=', $value);
            })->inputType('month'),

            Operator::make('is_year', 'Is in year', function ($query, $column, $value) {
                return $query->whereYear($column, '=', $value);
            })->inputType('year'),

            Operator::make('is_blank', 'Is blank', function ($query, $column, $value) {
                return $query->whereNull($column);
            })->requiresValue(false),

            Operator::make('is_not_blank', 'Is not blank', function ($query, $column, $value) {
                return $query->whereNotNull($column);
            })->requiresValue(false),
        ];
    }

    /**
     * Enable time selection.
     */
    public function withTime(bool $withTime = true): static
    {
        $this->withTime = $withTime;
        $this->format = $withTime ? 'Y-m-d H:i:s' : 'Y-m-d';

        return $this;
    }

    /**
     * Set minimum date.
     */
    public function minDate(?string $date): static
    {
        $this->minDate = $date;

        return $this;
    }

    /**
     * Set maximum date.
     */
    public function maxDate(?string $date): static
    {
        $this->maxDate = $date;

        return $this;
    }

    /**
     * Set date format.
     */
    public function format(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function getType(): string
    {
        return 'date';
    }

    public function getSettings(): array
    {
        return [
            'withTime' => $this->withTime,
            'minDate' => $this->minDate,
            'maxDate' => $this->maxDate,
            'format' => $this->format,
        ];
    }
}
