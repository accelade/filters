# Query Builder Filter

The Query Builder filter allows users to create complex, nested filtering conditions with AND/OR grouping - similar to database query builders.

## Basic Usage

```php
use Accelade\Filters\QueryBuilder\QueryBuilderFilter;
use Accelade\Filters\QueryBuilder\Constraints\TextConstraint;
use Accelade\Filters\QueryBuilder\Constraints\NumberConstraint;
use Accelade\Filters\QueryBuilder\Constraints\SelectConstraint;

$filter = QueryBuilderFilter::make('advanced_filters')
    ->label('Advanced Filters')
    ->constraints([
        TextConstraint::make('name')
            ->label('Name'),

        TextConstraint::make('email')
            ->label('Email'),

        NumberConstraint::make('age')
            ->label('Age'),

        SelectConstraint::make('status')
            ->label('Status')
            ->options([
                'active' => 'Active',
                'inactive' => 'Inactive',
            ]),
    ]);
```

## Available Constraints

### TextConstraint

For text/string fields with operators like contains, equals, starts with, ends with.

```php
use Accelade\Filters\QueryBuilder\Constraints\TextConstraint;

TextConstraint::make('name')
    ->label('Name')
    ->column('users.name');  // Optional: specify column
```

**Operators:** `contains`, `not_contains`, `equals`, `not_equals`, `starts_with`, `ends_with`, `is_blank`, `is_not_blank`

### NumberConstraint

For numeric fields with comparison operators.

```php
use Accelade\Filters\QueryBuilder\Constraints\NumberConstraint;

NumberConstraint::make('age')
    ->label('Age');
```

**Operators:** `equals`, `not_equals`, `greater_than`, `greater_than_or_equal`, `less_than`, `less_than_or_equal`, `between`, `is_blank`, `is_not_blank`

### BooleanConstraint

For true/false fields.

```php
use Accelade\Filters\QueryBuilder\Constraints\BooleanConstraint;

BooleanConstraint::make('is_active')
    ->label('Active');
```

**Operators:** `is_true`, `is_false`

### DateConstraint

For date/datetime fields with date-specific operators.

```php
use Accelade\Filters\QueryBuilder\Constraints\DateConstraint;

DateConstraint::make('created_at')
    ->label('Created Date');
```

**Operators:** `is`, `is_not`, `is_after`, `is_on_or_after`, `is_before`, `is_on_or_before`, `is_between`, `is_month`, `is_year`, `is_blank`, `is_not_blank`

### SelectConstraint

For fields with predefined options.

```php
use Accelade\Filters\QueryBuilder\Constraints\SelectConstraint;

SelectConstraint::make('category')
    ->label('Category')
    ->options([
        'electronics' => 'Electronics',
        'clothing' => 'Clothing',
        'books' => 'Books',
    ])
    ->searchable()
    ->multiple();
```

**Operators:** `is`, `is_not`, `is_blank`, `is_not_blank`

### RelationshipConstraint

For filtering by related model attributes.

```php
use Accelade\Filters\QueryBuilder\Constraints\RelationshipConstraint;

RelationshipConstraint::make('team')
    ->label('Team')
    ->relationship('team')
    ->titleAttribute('name');
```

## Query Structure

The query builder produces a JSON structure:

```json
{
  "rules": [
    {
      "constraint": "name",
      "operator": "contains",
      "value": "John"
    },
    {
      "rules": [
        {
          "constraint": "status",
          "operator": "is",
          "value": "active"
        },
        {
          "constraint": "age",
          "operator": "greater_than",
          "value": 18
        }
      ],
      "combinator": "or"
    }
  ],
  "combinator": "and"
}
```

This translates to: `name CONTAINS 'John' AND (status = 'active' OR age > 18)`

## Constraint Picker

Configure the constraint picker dropdown:

```php
$filter = QueryBuilderFilter::make('query')
    ->constraintPickerColumns(2)
    ->constraintPickerWidth(FilterWidth::Medium)
    ->constraints([...]);
```

## Using with FilterPanel

```php
use Accelade\Filters\FilterPanel;
use Accelade\Filters\Enums\FilterLayout;

$panel = FilterPanel::make()
    ->layout(FilterLayout::AboveContent)
    ->filters([
        QueryBuilderFilter::make('query')
            ->constraints([
                TextConstraint::make('name')->label('Name'),
                TextConstraint::make('email')->label('Email'),
                NumberConstraint::make('salary')->label('Salary'),
            ]),
    ]);
```

## Applying to Eloquent

The QueryBuilder filter automatically applies conditions to your query:

```php
$panel->setFilterValues($request->all());
$users = $panel->applyToQuery(User::query())->paginate();
```

## Blade Component

```blade
{{-- Standalone usage --}}
<x-filters::query-builder
    :filter="$queryBuilderFilter"
    name="query"
/>

{{-- Or within a FilterPanel --}}
<x-filters::filter-panel :panel="$panel" />
```

## Complete Example

```php
use Accelade\Filters\FilterPanel;
use Accelade\Filters\Enums\FilterLayout;
use Accelade\Filters\QueryBuilder\QueryBuilderFilter;
use Accelade\Filters\QueryBuilder\Constraints\TextConstraint;
use Accelade\Filters\QueryBuilder\Constraints\NumberConstraint;
use Accelade\Filters\QueryBuilder\Constraints\BooleanConstraint;
use Accelade\Filters\QueryBuilder\Constraints\DateConstraint;
use Accelade\Filters\QueryBuilder\Constraints\SelectConstraint;

$panel = FilterPanel::make()
    ->layout(FilterLayout::AboveContent)
    ->filters([
        QueryBuilderFilter::make('advanced')
            ->label('Advanced Filters')
            ->constraints([
                TextConstraint::make('name')
                    ->label('Name'),

                TextConstraint::make('email')
                    ->label('Email'),

                SelectConstraint::make('department')
                    ->label('Department')
                    ->options([
                        'engineering' => 'Engineering',
                        'sales' => 'Sales',
                        'marketing' => 'Marketing',
                    ]),

                NumberConstraint::make('salary')
                    ->label('Salary'),

                BooleanConstraint::make('is_manager')
                    ->label('Is Manager'),

                DateConstraint::make('hired_at')
                    ->label('Hire Date'),
            ]),
    ]);

// In controller
$panel->setFilterValues($request->all());
$employees = $panel->applyToQuery(Employee::query())->paginate();
```
