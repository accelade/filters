<?php

declare(strict_types=1);

namespace Accelade\Filters;

use Accelade\Filters\Enums\FilterLayout;
use Accelade\Filters\Enums\FilterWidth;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Filter Panel manages a collection of filters with layout options.
 */
class FilterPanel
{
    /** @var array<Filter> */
    protected array $filters = [];

    protected FilterLayout $layout = FilterLayout::Dropdown;

    protected FilterWidth $width = FilterWidth::Medium;

    protected int $columns = 1;

    protected ?string $maxHeight = null;

    protected bool $persistInSession = false;

    protected bool $deferFilters = true;

    protected bool $showIndicators = true;

    protected bool $collapsed = false;

    protected string|Closure|null $triggerLabel = null;

    protected string|Closure|null $triggerIcon = null;

    protected string|Closure|null $applyLabel = null;

    protected string|Closure|null $resetLabel = null;

    protected ?string $sessionKey = null;

    protected ?Closure $filterFormSchema = null;

    protected array $activeFilters = [];

    /**
     * Create a new filter panel instance.
     */
    public function __construct()
    {
        $this->setUp();
    }

    /**
     * Create a new filter panel instance.
     */
    public static function make(): static
    {
        return new static;
    }

    /**
     * Set up the filter panel.
     */
    protected function setUp(): void
    {
        $this->triggerLabel = 'Filters';
        $this->applyLabel = 'Apply';
        $this->resetLabel = 'Reset';
    }

    /**
     * Set the filters.
     *
     * @param  array<Filter>  $filters
     */
    public function filters(array $filters): static
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * Add a filter.
     */
    public function addFilter(Filter $filter): static
    {
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * Get all filters.
     *
     * @return array<Filter>
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Get visible filters.
     *
     * @return array<Filter>
     */
    public function getVisibleFilters(): array
    {
        return array_filter($this->filters, fn (Filter $filter) => ! $filter->isHidden());
    }

    /**
     * Get active filters.
     *
     * @return array<Filter>
     */
    public function getActiveFilters(): array
    {
        return array_filter($this->filters, fn (Filter $filter) => $filter->isActive());
    }

    /**
     * Set the layout.
     */
    public function layout(FilterLayout $layout): static
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * Get the layout.
     */
    public function getLayout(): FilterLayout
    {
        return $this->layout;
    }

    /**
     * Set the form width.
     */
    public function width(FilterWidth $width): static
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get the form width.
     */
    public function getWidth(): FilterWidth
    {
        return $this->width;
    }

    /**
     * Set the number of columns.
     */
    public function columns(int $columns): static
    {
        $this->columns = max(1, $columns);

        return $this;
    }

    /**
     * Get the number of columns.
     */
    public function getColumns(): int
    {
        return $this->columns;
    }

    /**
     * Set the maximum height for scrolling.
     */
    public function maxHeight(?string $maxHeight): static
    {
        $this->maxHeight = $maxHeight;

        return $this;
    }

    /**
     * Get the maximum height.
     */
    public function getMaxHeight(): ?string
    {
        return $this->maxHeight;
    }

    /**
     * Enable session persistence.
     */
    public function persistInSession(bool $persist = true, ?string $key = null): static
    {
        $this->persistInSession = $persist;
        $this->sessionKey = $key;

        return $this;
    }

    /**
     * Check if persisting in session.
     */
    public function isPersistingInSession(): bool
    {
        return $this->persistInSession;
    }

    /**
     * Disable deferred filtering (apply filters immediately).
     */
    public function deferFilters(bool $defer = true): static
    {
        $this->deferFilters = $defer;

        return $this;
    }

    /**
     * Check if filters are deferred.
     */
    public function isDeferred(): bool
    {
        return $this->deferFilters;
    }

    /**
     * Show/hide filter indicators.
     */
    public function showIndicators(bool $show = true): static
    {
        $this->showIndicators = $show;

        return $this;
    }

    /**
     * Check if indicators should be shown.
     */
    public function shouldShowIndicators(): bool
    {
        return $this->showIndicators;
    }

    /**
     * Set the collapsed state.
     */
    public function collapsed(bool $collapsed = true): static
    {
        $this->collapsed = $collapsed;

        return $this;
    }

    /**
     * Check if collapsed.
     */
    public function isCollapsed(): bool
    {
        return $this->collapsed;
    }

    /**
     * Set the trigger button label.
     */
    public function triggerLabel(string|Closure $label): static
    {
        $this->triggerLabel = $label;

        return $this;
    }

    /**
     * Get the trigger label.
     */
    public function getTriggerLabel(): string
    {
        if ($this->triggerLabel instanceof Closure) {
            return ($this->triggerLabel)();
        }

        return $this->triggerLabel ?? 'Filters';
    }

    /**
     * Set the trigger icon.
     */
    public function triggerIcon(string|Closure $icon): static
    {
        $this->triggerIcon = $icon;

        return $this;
    }

    /**
     * Get the trigger icon.
     */
    public function getTriggerIcon(): ?string
    {
        if ($this->triggerIcon instanceof Closure) {
            return ($this->triggerIcon)();
        }

        return $this->triggerIcon;
    }

    /**
     * Set the apply button label.
     */
    public function applyLabel(string|Closure $label): static
    {
        $this->applyLabel = $label;

        return $this;
    }

    /**
     * Get the apply label.
     */
    public function getApplyLabel(): string
    {
        if ($this->applyLabel instanceof Closure) {
            return ($this->applyLabel)();
        }

        return $this->applyLabel ?? 'Apply';
    }

    /**
     * Set the reset button label.
     */
    public function resetLabel(string|Closure $label): static
    {
        $this->resetLabel = $label;

        return $this;
    }

    /**
     * Get the reset label.
     */
    public function getResetLabel(): string
    {
        if ($this->resetLabel instanceof Closure) {
            return ($this->resetLabel)();
        }

        return $this->resetLabel ?? 'Reset';
    }

    /**
     * Set a custom filter form schema.
     */
    public function filterFormSchema(Closure $schema): static
    {
        $this->filterFormSchema = $schema;

        return $this;
    }

    /**
     * Get the custom filter form schema.
     */
    public function getFilterFormSchema(): ?Closure
    {
        return $this->filterFormSchema;
    }

    /**
     * Set filter values from request or array.
     */
    public function setFilterValues(array $values): static
    {
        foreach ($this->filters as $filter) {
            $name = $filter->getName();
            if (array_key_exists($name, $values)) {
                $filter->setValue($values[$name]);
            }
        }

        return $this;
    }

    /**
     * Get all filter values.
     */
    public function getFilterValues(): array
    {
        $values = [];

        foreach ($this->filters as $filter) {
            $values[$filter->getName()] = $filter->getValue();
        }

        return $values;
    }

    /**
     * Apply all filters to a query.
     */
    public function applyToQuery(Builder $query): Builder
    {
        foreach ($this->filters as $filter) {
            $value = $filter->getValue();

            if ($filter->isActive()) {
                $query = $filter->apply($query, $value);
            }
        }

        return $query;
    }

    /**
     * Get filter indicators for display.
     */
    public function getIndicators(): array
    {
        $indicators = [];

        foreach ($this->getActiveFilters() as $filter) {
            $indicators[] = [
                'name' => $filter->getName(),
                'label' => $filter->getLabel(),
                'value' => $filter->getValue(),
                'formatted' => $this->formatIndicatorValue($filter),
            ];
        }

        return $indicators;
    }

    /**
     * Format the indicator value for display.
     */
    protected function formatIndicatorValue(Filter $filter): string
    {
        $value = $filter->getValue();

        if (is_array($value)) {
            return implode(', ', array_map(fn ($v) => (string) $v, $value));
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        return (string) $value;
    }

    /**
     * Check if there are active filters.
     */
    public function hasActiveFilters(): bool
    {
        return count($this->getActiveFilters()) > 0;
    }

    /**
     * Reset all filters to default values.
     */
    public function reset(): static
    {
        foreach ($this->filters as $filter) {
            $filter->setValue($filter->getDefault());
        }

        return $this;
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'layout' => $this->layout->value,
            'width' => $this->width->value,
            'columns' => $this->columns,
            'maxHeight' => $this->maxHeight,
            'deferred' => $this->deferFilters,
            'showIndicators' => $this->showIndicators,
            'collapsed' => $this->collapsed,
            'triggerLabel' => $this->getTriggerLabel(),
            'applyLabel' => $this->getApplyLabel(),
            'resetLabel' => $this->getResetLabel(),
            'filters' => array_map(fn (Filter $f) => $f->toArray(), $this->filters),
            'activeFilters' => array_map(fn (Filter $f) => $f->toArray(), $this->getActiveFilters()),
            'indicators' => $this->getIndicators(),
            'hasActiveFilters' => $this->hasActiveFilters(),
        ];
    }

    /**
     * Convert to JSON.
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }

    /**
     * Render the filter panel.
     */
    public function render(): View
    {
        return view('filters::components.filter-panel', [
            'panel' => $this,
        ]);
    }
}
