# Filter Layouts

Filter layouts control how your filters are presented to users. Choose from dropdown menus, modal dialogs, inline forms, sidebars, and collapsible panels.

## Available Layouts

### Dropdown (Default)

A compact trigger button that reveals filters in a dropdown panel.

```php
use Accelade\Filters\Enums\FilterLayout;
use Accelade\Filters\FilterPanel;

$panel = FilterPanel::make()
    ->layout(FilterLayout::Dropdown)
    ->filters([...]);
```

### Modal

Opens filters in a centered modal dialog for a focused experience.

```php
$panel = FilterPanel::make()
    ->layout(FilterLayout::Modal)
    ->filters([...]);
```

### Above Content

Displays filters in a panel above your main content.

```php
$panel = FilterPanel::make()
    ->layout(FilterLayout::AboveContent)
    ->filters([...]);
```

### Above Content (Collapsible)

Same as above content, but can be collapsed to save space.

```php
$panel = FilterPanel::make()
    ->layout(FilterLayout::AboveContentCollapsible)
    ->collapsed() // Start collapsed
    ->filters([...]);
```

### Below Content

Displays filters below your main content.

```php
$panel = FilterPanel::make()
    ->layout(FilterLayout::BelowContent)
    ->filters([...]);
```

### Sidebar

A fixed sidebar for persistent filter visibility.

```php
$panel = FilterPanel::make()
    ->layout(FilterLayout::Sidebar)
    ->maxHeight('600px')
    ->filters([...]);
```

### Sidebar (Collapsible)

A sidebar that can be collapsed.

```php
$panel = FilterPanel::make()
    ->layout(FilterLayout::SidebarCollapsible)
    ->filters([...]);
```

### Inline

Filters displayed inline, ideal for simple filtering needs.

```php
$panel = FilterPanel::make()
    ->layout(FilterLayout::Inline)
    ->filters([...]);
```

## Layout Configuration

### Width

Control the width of filter panels:

```php
use Accelade\Filters\Enums\FilterWidth;

$panel = FilterPanel::make()
    ->width(FilterWidth::Large)    // 32rem
    ->width(FilterWidth::ExtraLarge)  // 36rem
    ->width(FilterWidth::Full);     // 100%
```

Available widths: `ExtraSmall`, `Small`, `Medium`, `Large`, `ExtraLarge`, `TwoExtraLarge`, `ThreeExtraLarge`, `FourExtraLarge`, `FiveExtraLarge`, `SixExtraLarge`, `Full`.

### Columns

Arrange filters in multiple columns:

```php
$panel = FilterPanel::make()
    ->columns(2)  // Two column layout
    ->filters([...]);
```

### Max Height

Set a maximum height with scrolling:

```php
$panel = FilterPanel::make()
    ->maxHeight('400px')
    ->filters([...]);
```

## Blade Usage

```blade
{{-- Using FilterPanel object --}}
<x-filters::filter-panel :panel="$panel" />

{{-- Or with individual props --}}
<x-filters::filter-panel
    layout="dropdown"
    :columns="2"
    width="lg"
/>
```
