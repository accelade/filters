# Filters Package

This package provides filter components for Accelade tables and grids.

## Package Overview

- **Namespace**: `Accelade\Filters`
- **Service Provider**: `FiltersServiceProvider`
- **Facade**: `Filter`
- **View Namespace**: `filters::` and `accelade::`

## Key Components

### Filter Types
- `TextFilter` - Text/search input
- `SelectFilter` - Dropdown select
- `BooleanFilter` - Yes/No toggle
- `DateFilter` - Single date picker
- `DateRangeFilter` - Date range (from/to)
- `NumberFilter` - Numeric input

### Usage
```php
use Accelade\Filters\Components\TextFilter;
use Accelade\Filters\Components\SelectFilter;

$filters = [
    TextFilter::make('search')
        ->label('Search')
        ->placeholder('Search...'),

    SelectFilter::make('status')
        ->label('Status')
        ->options(['active' => 'Active', 'inactive' => 'Inactive']),
];
```

### Blade Components
```blade
<x-accelade::filters.text name="search" label="Search" />
<x-accelade::filters.select name="status" :options="$options" />
```

## Testing
```bash
cd packages/filters
composer test
```

## Dependencies
- `accelade/accelade`
- `accelade/query-builder`
