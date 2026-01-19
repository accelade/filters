<?php

declare(strict_types=1);

namespace Accelade\Filters\Components;

use Accelade\Filters\Concerns\HasOptions;
use Accelade\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Select/dropdown filter component.
 */
class SelectFilter extends Filter
{
    use HasOptions;

    protected bool $multiple = false;

    protected bool $searchable = false;

    protected bool $native = true;

    /**
     * Enable multiple selection.
     */
    public function multiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * Check if multiple selection is enabled.
     */
    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    /**
     * Enable searchable dropdown.
     */
    public function searchable(bool $searchable = true): static
    {
        $this->searchable = $searchable;

        return $this;
    }

    /**
     * Check if searchable.
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    /**
     * Use native select element.
     */
    public function native(bool $native = true): static
    {
        $this->native = $native;

        return $this;
    }

    /**
     * Check if using native select.
     */
    public function isNative(): bool
    {
        return $this->native;
    }

    /**
     * Apply the filter.
     */
    public function apply(Builder $query, mixed $value): Builder
    {
        if ($value === null || $value === '' || $value === []) {
            return $query;
        }

        $column = $this->getColumn();

        if ($this->multiple && is_array($value)) {
            return $query->whereIn($column, $value);
        }

        return $query->where($column, $value);
    }

    /**
     * Get the view name.
     */
    public function getView(): string
    {
        return 'accelade::filters.select';
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'options' => $this->getOptions(),
            'multiple' => $this->multiple,
            'searchable' => $this->searchable,
            'native' => $this->native,
        ]);
    }
}
