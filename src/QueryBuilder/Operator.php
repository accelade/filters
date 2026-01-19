<?php

declare(strict_types=1);

namespace Accelade\Filters\QueryBuilder;

use Closure;
use Illuminate\Database\Eloquent\Builder;

/**
 * Operator for QueryBuilder constraints.
 */
class Operator
{
    protected string $name;

    protected string $label;

    protected Closure $applyCallback;

    protected bool $requiresValue = true;

    protected string $inputType = 'text';

    /**
     * Create a new operator instance.
     */
    public function __construct(string $name, string $label, Closure $applyCallback)
    {
        $this->name = $name;
        $this->label = $label;
        $this->applyCallback = $applyCallback;
    }

    /**
     * Create a new operator instance.
     */
    public static function make(string $name, string $label, Closure $applyCallback): static
    {
        return new static($name, $label, $applyCallback);
    }

    /**
     * Get the operator name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the operator label.
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Set whether the operator requires a value.
     */
    public function requiresValue(bool $requires = true): static
    {
        $this->requiresValue = $requires;

        return $this;
    }

    /**
     * Check if operator requires a value.
     */
    public function doesRequireValue(): bool
    {
        return $this->requiresValue;
    }

    /**
     * Set the input type for value entry.
     */
    public function inputType(string $type): static
    {
        $this->inputType = $type;

        return $this;
    }

    /**
     * Get the input type.
     */
    public function getInputType(): string
    {
        return $this->inputType;
    }

    /**
     * Apply the operator to the query.
     */
    public function apply(Builder $query, string $column, mixed $value): Builder
    {
        return ($this->applyCallback)($query, $column, $value);
    }

    /**
     * Convert to array for JS.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'requiresValue' => $this->requiresValue,
            'inputType' => $this->inputType,
        ];
    }
}
