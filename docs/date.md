# Date Filter

Date filters allow filtering by a single date value.

## Basic Usage

```php
use Accelade\Filters\Components\DateFilter;

DateFilter::make('created_at')
    ->label('Created Date');
```

## From/To Dates

```php
DateFilter::make('start_date')
    ->label('From')
    ->from();

DateFilter::make('end_date')
    ->label('To')
    ->to();
```

## Blade Component

```blade
<x-accelade::filters.date
    name="created_at"
    label="Date"
/>
```
