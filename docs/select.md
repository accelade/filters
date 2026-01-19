# Select Filter

Select filters provide a dropdown for selecting from predefined options.

## Basic Usage

```php
use Accelade\Filters\Components\SelectFilter;

SelectFilter::make('status')
    ->label('Status')
    ->options([
        'active' => 'Active',
        'pending' => 'Pending',
        'inactive' => 'Inactive',
    ])
    ->placeholder('All Status');
```

## Multiple Selection

```php
SelectFilter::make('roles')
    ->label('Roles')
    ->multiple()
    ->options(Role::pluck('name', 'id'));
```

## Blade Component

```blade
<x-accelade::filters.select
    name="status"
    label="Status"
    :options="$options"
/>
```
