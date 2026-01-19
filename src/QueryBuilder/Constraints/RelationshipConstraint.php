<?php

declare(strict_types=1);

namespace Accelade\Filters\QueryBuilder\Constraints;

use Accelade\Filters\Concerns\HasOptions;
use Accelade\Filters\QueryBuilder\Constraint;
use Accelade\Filters\QueryBuilder\Operator;
use Closure;
use Illuminate\Database\Eloquent\Builder;

/**
 * Relationship constraint for filtering based on related models.
 */
class RelationshipConstraint extends Constraint
{
    use HasOptions;

    protected ?string $relationship = null;

    protected bool $multiple = false;

    protected bool $searchable = false;

    protected ?Closure $modifyQueryUsing = null;

    protected string $titleAttribute = 'name';

    protected function setUp(): void
    {
        $this->icon = 'link';

        $this->operators = [
            Operator::make('has', 'Has', function ($query, $column, $value) {
                return $this->applyRelationshipFilter($query, 'has', $value);
            })->inputType('select'),

            Operator::make('does_not_have', 'Does not have', function ($query, $column, $value) {
                return $this->applyRelationshipFilter($query, 'doesntHave', $value);
            })->inputType('select'),

            Operator::make('has_min', 'Has at least', function ($query, $column, $value) {
                $count = (int) ($value['count'] ?? 1);

                return $query->has($this->getRelationshipName(), '>=', $count);
            })->inputType('number'),

            Operator::make('has_max', 'Has at most', function ($query, $column, $value) {
                $count = (int) ($value['count'] ?? 1);

                return $query->has($this->getRelationshipName(), '<=', $count);
            })->inputType('number'),

            Operator::make('has_none', 'Has none', function ($query, $column, $value) {
                return $query->doesntHave($this->getRelationshipName());
            })->requiresValue(false),

            Operator::make('has_any', 'Has any', function ($query, $column, $value) {
                return $query->has($this->getRelationshipName());
            })->requiresValue(false),
        ];
    }

    /**
     * Set the relationship name.
     */
    public function relationship(string $relationship): static
    {
        $this->relationship = $relationship;

        return $this;
    }

    /**
     * Get the relationship name.
     */
    public function getRelationshipName(): string
    {
        return $this->relationship ?? $this->name;
    }

    /**
     * Set the title attribute for display.
     */
    public function titleAttribute(string $attribute): static
    {
        $this->titleAttribute = $attribute;

        return $this;
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

    /**
     * Modify the relationship query.
     */
    public function modifyQueryUsing(Closure $callback): static
    {
        $this->modifyQueryUsing = $callback;

        return $this;
    }

    /**
     * Apply the relationship filter.
     */
    protected function applyRelationshipFilter(Builder $query, string $method, mixed $value): Builder
    {
        $relationship = $this->getRelationshipName();

        if ($this->modifyQueryUsing !== null) {
            return $query->{$method}($relationship, function ($q) use ($value) {
                ($this->modifyQueryUsing)($q, $value);
            });
        }

        if (is_array($value)) {
            return $query->whereHas($relationship, function ($q) use ($value) {
                $q->whereIn('id', $value);
            });
        }

        if ($value !== null) {
            return $query->whereHas($relationship, function ($q) use ($value) {
                $q->where('id', $value);
            });
        }

        return $query->{$method}($relationship);
    }

    public function getType(): string
    {
        return 'relationship';
    }

    public function getSettings(): array
    {
        return [
            'relationship' => $this->getRelationshipName(),
            'multiple' => $this->multiple,
            'searchable' => $this->searchable,
            'titleAttribute' => $this->titleAttribute,
            'options' => $this->getFormattedOptions(),
        ];
    }
}
