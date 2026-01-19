# Accelade Filters

Filter components for Accelade - text, select, date, boolean and custom filters for tables and grids.

## Installation

```bash
composer require accelade/filters
```

## Quick Start

```php
use Accelade\Filters\FilterPanel;
use Accelade\Filters\Components\TextFilter;
use Accelade\Filters\Components\SelectFilter;

$panel = FilterPanel::make()
    ->filters([
        TextFilter::make('search')
            ->label('Search')
            ->placeholder('Search users...'),

        SelectFilter::make('status')
            ->label('Status')
            ->options([
                'active' => 'Active',
                'inactive' => 'Inactive',
            ]),
    ]);
```

## Documentation

- [Overview](docs/overview.md) - Introduction to filter components
- [Text Filter](docs/text.md) - Text/search filter component
- [Select Filter](docs/select.md) - Dropdown/select filter component
- [Boolean Filter](docs/boolean.md) - Boolean/toggle filter component
- [Number Filter](docs/number.md) - Number filter component
- [Date Filter](docs/date.md) - Date filter component
- [Date Range Filter](docs/date-range.md) - Date range filter component
- [Filter Layouts](docs/layout.md) - Different layout options for filter panels
- [Filter Panel](docs/panel.md) - Container for managing filter collections
- [Query Builder](docs/query-builder.md) - Complex nested filtering with AND/OR grouping

## Development

### Building TypeScript

```bash
cd packages/filters
npm install
npm run build
```

### Running Tests

```bash
composer test
```

### Code Quality

```bash
composer format      # Format PHP code with Pint
composer mago        # Run Mago linter
```

## Requirements

- PHP 8.2+
- Laravel 11.0+
- Accelade 1.0+

## License

MIT License. See [LICENSE](LICENSE) for details.
