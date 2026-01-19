# Number Filter

Number filters allow filtering by numeric values with optional min/max constraints.

## Basic Usage

```php
use Accelade\Filters\Components\NumberFilter;

NumberFilter::make('price')
    ->label('Price')
    ->min(0)
    ->max(10000)
    ->step(0.01);
```

## As Range

```php
NumberFilter::make('quantity')
    ->label('Quantity')
    ->range();
```

## Blade Component

```blade
<x-accelade::filters.number
    name="price"
    label="Price"
    min="0"
    max="10000"
/>
```
