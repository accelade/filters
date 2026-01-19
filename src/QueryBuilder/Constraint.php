<?php

declare(strict_types=1);

namespace Accelade\Filters\QueryBuilder;

use Closure;
use Illuminate\Database\Eloquent\Builder;

/**
 * Base constraint class for QueryBuilder filter.
 */
abstract class Constraint
{
    protected string $name;

    protected string|Closure|null $label = null;

    protected ?string $icon = null;

    protected ?string $column = null;

    /** @var array<Operator> */
    protected array $operators = [];

    protected bool $isNullable = false;

    /**
     * Create a new constraint instance.
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->setUp();
    }

    /**
     * Create a new constraint instance.
     */
    public static function make(string $name): static
    {
        return new static($name);
    }

    /**
     * Set up the constraint with default operators.
     */
    abstract protected function setUp(): void;

    /**
     * Get the constraint name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the label.
     */
    public function label(string|Closure $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the label.
     */
    public function getLabel(): string
    {
        if ($this->label instanceof Closure) {
            return ($this->label)();
        }

        return $this->label ?? str_replace('_', ' ', ucfirst($this->name));
    }

    /**
     * Set the icon.
     */
    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get the icon.
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * Set the database column.
     */
    public function column(string $column): static
    {
        $this->column = $column;

        return $this;
    }

    /**
     * Get the database column.
     */
    public function getColumn(): string
    {
        return $this->column ?? $this->name;
    }

    /**
     * Override operators.
     *
     * @param  array<Operator>  $operators
     */
    public function operators(array $operators): static
    {
        $this->operators = $operators;

        return $this;
    }

    /**
     * Get all operators.
     *
     * @return array<Operator>
     */
    public function getOperators(): array
    {
        return $this->operators;
    }

    /**
     * Enable nullable filtering.
     */
    public function nullable(bool $nullable = true): static
    {
        $this->isNullable = $nullable;

        return $this;
    }

    /**
     * Check if nullable.
     */
    public function isNullable(): bool
    {
        return $this->isNullable;
    }

    /**
     * Apply the constraint to the query.
     */
    public function apply(Builder $query, string $operatorName, mixed $value): Builder
    {
        $operator = $this->findOperator($operatorName);

        if ($operator === null) {
            return $query;
        }

        return $operator->apply($query, $this->getColumn(), $value);
    }

    /**
     * Find an operator by name.
     */
    protected function findOperator(string $name): ?Operator
    {
        foreach ($this->operators as $operator) {
            if ($operator->getName() === $name) {
                return $operator;
            }
        }

        return null;
    }

    /**
     * Get the constraint type for JS.
     */
    abstract public function getType(): string;

    /**
     * Get additional settings for JS.
     */
    public function getSettings(): array
    {
        return [];
    }

    /**
     * Convert to array for JS.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->getLabel(),
            'icon' => $this->icon,
            'column' => $this->getColumn(),
            'type' => $this->getType(),
            'nullable' => $this->isNullable,
            'operators' => array_map(fn (Operator $op) => $op->toArray(), $this->operators),
            'settings' => $this->getSettings(),
        ];
    }
}
