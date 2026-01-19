# Date Range Filter

Date range filters allow filtering by a date range with from/to values.

## Basic Usage

```php
use Accelade\Filters\Components\DateRangeFilter;

DateRangeFilter::make('created_at')
    ->label('Created Between')
    ->fromLabel('From')
    ->toLabel('To');
```

## Blade Component

```blade
<x-accelade::filters.date-range
    name="created_at"
    label="Date Range"
/>
```
