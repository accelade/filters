# Filter Panel

The Filter Panel is a container for managing collections of filters with layout options, indicators, and form submission handling.

## Basic Usage

```php
use Accelade\Filters\Components\TextFilter;
use Accelade\Filters\Components\SelectFilter;
use Accelade\Filters\FilterPanel;

$panel = FilterPanel::make()
    ->filters([
        TextFilter::make('search')
            ->label('Search')
            ->placeholder('Search...'),

        SelectFilter::make('status')
            ->label('Status')
            ->options([
                'active' => 'Active',
                'inactive' => 'Inactive',
            ]),
    ]);
```

## Filter Indicators

Show active filter badges that users can click to remove:

```php
$panel = FilterPanel::make()
    ->showIndicators()
    ->filters([...]);
```

Indicators display the filter label and current value, with a remove button for each.

## Deferred vs Immediate Filtering

### Deferred (Default)

Users must click "Apply" to submit filters:

```php
$panel = FilterPanel::make()
    ->deferFilters()  // Default
    ->applyLabel('Apply Filters')
    ->resetLabel('Clear')
    ->filters([...]);
```

### Immediate

Filters submit automatically on change:

```php
$panel = FilterPanel::make()
    ->deferFilters(false)
    ->filters([...]);
```

## Custom Labels

Customize button and trigger labels:

```php
$panel = FilterPanel::make()
    ->triggerLabel('Filter Results')
    ->applyLabel('Search')
    ->resetLabel('Clear All')
    ->filters([...]);
```

## Session Persistence

Persist filter values across page loads:

```php
$panel = FilterPanel::make()
    ->persistInSession(true, 'users-filters')
    ->filters([...]);
```

## Applying to Queries

Apply active filters to an Eloquent query:

```php
// In your controller
$panel = FilterPanel::make()
    ->filters([...])
    ->setFilterValues($request->all());

$users = $panel->applyToQuery(User::query())->paginate();
```

## Getting Filter State

```php
// Get all filter values
$values = $panel->getFilterValues();

// Get active filters only
$activeFilters = $panel->getActiveFilters();

// Check if any filters are active
if ($panel->hasActiveFilters()) {
    // ...
}

// Get indicators for display
$indicators = $panel->getIndicators();

// Reset all filters
$panel->reset();
```

## Convert to Array/JSON

```php
// For API responses or JavaScript
$array = $panel->toArray();
$json = $panel->toJson();
```

## Blade Component

```blade
{{-- Pass the panel object --}}
<x-filters::filter-panel :panel="$panel" />

{{-- Full example with form --}}
<form method="GET" action="{{ route('users.index') }}">
    <x-filters::filter-panel :panel="$panel" />
</form>
```

## Complete Example

```php
use Accelade\Filters\Components\TextFilter;
use Accelade\Filters\Components\SelectFilter;
use Accelade\Filters\Components\BooleanFilter;
use Accelade\Filters\Components\DateFilter;
use Accelade\Filters\Enums\FilterLayout;
use Accelade\Filters\Enums\FilterWidth;
use Accelade\Filters\FilterPanel;

$panel = FilterPanel::make()
    ->layout(FilterLayout::AboveContentCollapsible)
    ->width(FilterWidth::Large)
    ->columns(3)
    ->showIndicators()
    ->triggerLabel('Filters')
    ->applyLabel('Apply')
    ->resetLabel('Reset')
    ->filters([
        TextFilter::make('search')
            ->label('Search')
            ->placeholder('Search users...'),

        SelectFilter::make('role')
            ->label('Role')
            ->options([
                'admin' => 'Administrator',
                'user' => 'User',
            ]),

        BooleanFilter::make('verified')
            ->label('Email Verified'),

        DateFilter::make('created_at')
            ->label('Registered'),
    ]);
```
