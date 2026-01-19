<?php

declare(strict_types=1);

namespace Accelade\Filters;

use Accelade\QueryBuilder\Contracts\FilterInterface;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Traits\Conditionable;

/**
 * Base filter class.
 */
abstract class Filter implements FilterInterface
{
    use Conditionable;

    protected string $name;

    protected string|Closure|null $label = null;

    protected mixed $value = null;

    protected mixed $defaultValue = null;

    protected string|Closure|null $placeholder = null;

    protected bool $isHidden = false;

    protected ?string $column = null;

    protected array $extraAttributes = [];

    /**
     * Create a new filter instance.
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->setUp();
    }

    /**
     * Create a new filter instance.
     */
    public static function make(string $name): static
    {
        return new static($name);
    }

    /**
     * Set up the filter.
     */
    protected function setUp(): void
    {
        // Override in subclasses
    }

    /**
     * Get the filter name.
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
     * Set the placeholder.
     */
    public function placeholder(string|Closure $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * Get the placeholder.
     */
    public function getPlaceholder(): ?string
    {
        if ($this->placeholder instanceof Closure) {
            return ($this->placeholder)();
        }

        return $this->placeholder;
    }

    /**
     * Set the default value.
     */
    public function default(mixed $value): static
    {
        $this->defaultValue = $value;

        return $this;
    }

    /**
     * Get the default value.
     */
    public function getDefault(): mixed
    {
        return $this->defaultValue;
    }

    /**
     * Set the value.
     */
    public function setValue(mixed $value): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the value.
     */
    public function getValue(): mixed
    {
        return $this->value ?? $this->defaultValue;
    }

    /**
     * Check if the filter is active.
     */
    public function isActive(): bool
    {
        $value = $this->getValue();

        return $value !== null && $value !== '' && $value !== [];
    }

    /**
     * Hide the filter.
     */
    public function hidden(bool $condition = true): static
    {
        $this->isHidden = $condition;

        return $this;
    }

    /**
     * Check if hidden.
     */
    public function isHidden(): bool
    {
        return $this->isHidden;
    }

    /**
     * Add extra HTML attributes.
     */
    public function extraAttributes(array $attributes): static
    {
        $this->extraAttributes = array_merge($this->extraAttributes, $attributes);

        return $this;
    }

    /**
     * Get extra attributes.
     */
    public function getExtraAttributes(): array
    {
        return $this->extraAttributes;
    }

    /**
     * Apply the filter to the query.
     */
    abstract public function apply(Builder $query, mixed $value): Builder;

    /**
     * Get the view name for rendering.
     */
    abstract public function getView(): string;

    /**
     * Render the filter.
     */
    public function render(): string
    {
        if ($this->isHidden()) {
            return '';
        }

        return view($this->getView(), [
            'filter' => $this,
        ])->render();
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'type' => class_basename(static::class),
            'name' => $this->name,
            'label' => $this->getLabel(),
            'column' => $this->getColumn(),
            'value' => $this->getValue(),
            'default' => $this->defaultValue,
            'placeholder' => $this->getPlaceholder(),
            'active' => $this->isActive(),
            'hidden' => $this->isHidden,
        ];
    }
}
