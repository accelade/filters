<?php

declare(strict_types=1);

namespace Accelade\Filters\QueryBuilder;

use Accelade\Filters\Enums\FilterWidth;
use Accelade\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

/**
 * QueryBuilder filter for complex, nested filtering conditions.
 *
 * Allows users to build sophisticated queries with AND/OR grouping,
 * multiple constraint types, and unlimited nesting.
 */
class QueryBuilderFilter extends Filter
{
    /** @var array<Constraint> */
    protected array $constraints = [];

    protected int $constraintPickerColumns = 2;

    protected FilterWidth $constraintPickerWidth = FilterWidth::Medium;

    protected function setUp(): void
    {
        $this->defaultValue = ['rules' => [], 'combinator' => 'and'];
    }

    /**
     * Set the available constraints.
     *
     * @param  array<Constraint>  $constraints
     */
    public function constraints(array $constraints): static
    {
        $this->constraints = $constraints;

        return $this;
    }

    /**
     * Add a constraint.
     */
    public function addConstraint(Constraint $constraint): static
    {
        $this->constraints[] = $constraint;

        return $this;
    }

    /**
     * Get all constraints.
     *
     * @return array<Constraint>
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }

    /**
     * Set columns for constraint picker.
     */
    public function constraintPickerColumns(int $columns): static
    {
        $this->constraintPickerColumns = max(1, $columns);

        return $this;
    }

    /**
     * Set width for constraint picker dropdown.
     */
    public function constraintPickerWidth(FilterWidth $width): static
    {
        $this->constraintPickerWidth = $width;

        return $this;
    }

    /**
     * Apply the filter to the query.
     */
    public function apply(Builder $query, mixed $value): Builder
    {
        if (! is_array($value) || empty($value['rules'])) {
            return $query;
        }

        $rules = $value['rules'];
        $combinator = $value['combinator'] ?? 'and';

        return $this->applyRules($query, $rules, $combinator);
    }

    /**
     * Apply rules recursively.
     *
     * @param  array<array>  $rules
     */
    protected function applyRules(Builder $query, array $rules, string $combinator): Builder
    {
        $method = $combinator === 'or' ? 'orWhere' : 'where';

        return $query->{$method}(function (Builder $q) use ($rules, $combinator) {
            foreach ($rules as $rule) {
                if ($this->isGroup($rule)) {
                    // Nested group
                    $this->applyRules($q, $rule['rules'], $rule['combinator'] ?? 'and');
                } else {
                    // Single rule
                    $this->applyRule($q, $rule, $combinator);
                }
            }
        });
    }

    /**
     * Check if a rule is a group (has nested rules).
     */
    protected function isGroup(array $rule): bool
    {
        return isset($rule['rules']) && is_array($rule['rules']);
    }

    /**
     * Apply a single rule.
     */
    protected function applyRule(Builder $query, array $rule, string $combinator): void
    {
        $constraintName = $rule['constraint'] ?? null;
        $operatorName = $rule['operator'] ?? null;
        $value = $rule['value'] ?? null;

        if ($constraintName === null || $operatorName === null) {
            return;
        }

        $constraint = $this->findConstraint($constraintName);

        if ($constraint === null) {
            return;
        }

        $method = $combinator === 'or' ? 'orWhere' : 'where';

        $query->{$method}(function (Builder $q) use ($constraint, $operatorName, $value) {
            $constraint->apply($q, $operatorName, $value);
        });
    }

    /**
     * Find a constraint by name.
     */
    protected function findConstraint(string $name): ?Constraint
    {
        foreach ($this->constraints as $constraint) {
            if ($constraint->getName() === $name) {
                return $constraint;
            }
        }

        return null;
    }

    /**
     * Check if the filter is active.
     */
    public function isActive(): bool
    {
        $value = $this->getValue();

        return is_array($value) && ! empty($value['rules']);
    }

    /**
     * Get the view name.
     */
    public function getView(): string
    {
        return 'filters::query-builder';
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'constraints' => array_map(fn (Constraint $c) => $c->toArray(), $this->constraints),
            'constraintPickerColumns' => $this->constraintPickerColumns,
            'constraintPickerWidth' => $this->constraintPickerWidth->value,
        ]);
    }
}
