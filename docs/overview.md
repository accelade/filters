# Filters

The Filters package provides reusable filter components for tables and grids.

## Installation

```bash
composer require accelade/filters
```

## Available Filters

- **TextFilter** - Text/search input filter
- **SelectFilter** - Dropdown/select filter
- **BooleanFilter** - Yes/No toggle filter
- **DateFilter** - Single date filter
- **DateRangeFilter** - Date range (from/to) filter
- **NumberFilter** - Numeric value filter

## Basic Usage

```php
use Accelade\Filters\Components\TextFilter;
use Accelade\Filters\Components\SelectFilter;

$filters = [
    TextFilter::make('name')
        ->label('Name')
        ->placeholder('Search by name...'),

    SelectFilter::make('status')
        ->label('Status')
        ->options([
            'active' => 'Active',
            'inactive' => 'Inactive',
        ]),
];
```

## Using with Query Builder

```php
use Accelade\QueryBuilder\QueryBuilder;
use Accelade\Filters\Components\TextFilter;

$builder = QueryBuilder::for(User::class)
    ->filters([
        TextFilter::make('name'),
        TextFilter::make('email'),
    ])
    ->fromRequest()
    ->paginate();
```

## Blade Component Usage

```blade
<x-accelade::filters.text
    name="search"
    label="Search"
    placeholder="Search..."
/>

<x-accelade::filters.select
    name="status"
    label="Status"
    :options="['active' => 'Active', 'inactive' => 'Inactive']"
/>
```
