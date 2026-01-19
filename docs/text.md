# Text Filter

Text filters allow users to search and filter data using text input.

## Basic Usage

```php
use Accelade\Filters\Components\TextFilter;

TextFilter::make('search')
    ->label('Search')
    ->placeholder('Search users...');
```

## With Debounce

```php
TextFilter::make('search')
    ->label('Search')
    ->placeholder('Type to search...')
    ->debounce(300);
```

## Blade Component

```blade
<x-accelade::filters.text
    name="search"
    label="Search"
    placeholder="Search..."
/>
```
