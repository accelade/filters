# Boolean Filter

Boolean filters allow filtering by true/false values with customizable labels.

## Basic Usage

```php
use Accelade\Filters\Components\BooleanFilter;

BooleanFilter::make('is_active')
    ->label('Active')
    ->trueLabel('Yes')
    ->falseLabel('No');
```

## Nullable Boolean

Include an "All" option:

```php
BooleanFilter::make('is_verified')
    ->label('Verified')
    ->nullable();
```

## Blade Component

```blade
<x-accelade::filters.boolean
    name="is_active"
    label="Active"
/>
```
