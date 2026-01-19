@props(['prefix' => 'a'])

@php
    use Accelade\Filters\QueryBuilder\QueryBuilderFilter;
    use Accelade\Filters\QueryBuilder\Constraints\TextConstraint;
    use Accelade\Filters\QueryBuilder\Constraints\NumberConstraint;
    use Accelade\Filters\QueryBuilder\Constraints\BooleanConstraint;
    use Accelade\Filters\QueryBuilder\Constraints\DateConstraint;
    use Accelade\Filters\QueryBuilder\Constraints\SelectConstraint;
    use Accelade\Filters\FilterPanel;
    use Accelade\Filters\Enums\FilterLayout;

    $queryBuilder = QueryBuilderFilter::make('advanced_filters')
        ->label('Advanced Filters')
        ->constraints([
            TextConstraint::make('name')
                ->label('Name')
                ->icon('user'),

            TextConstraint::make('email')
                ->label('Email')
                ->icon('envelope'),

            NumberConstraint::make('age')
                ->label('Age')
                ->icon('calculator'),

            SelectConstraint::make('status')
                ->label('Status')
                ->icon('flag')
                ->options([
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                    'pending' => 'Pending',
                ])
                ->searchable(),

            SelectConstraint::make('department')
                ->label('Department')
                ->options([
                    'engineering' => 'Engineering',
                    'sales' => 'Sales',
                    'marketing' => 'Marketing',
                    'hr' => 'Human Resources',
                ])
                ->multiple(),

            BooleanConstraint::make('is_verified')
                ->label('Email Verified'),

            DateConstraint::make('created_at')
                ->label('Created Date'),
        ]);

    // Set value from request if available
    if (request()->has('advanced_filters')) {
        $queryBuilder->setValue(request('advanced_filters'));
    }

    $panel = FilterPanel::make()
        ->layout(FilterLayout::AboveContent)
        ->filters([$queryBuilder]);
@endphp

<div class="space-y-6">
    <div class="prose dark:prose-invert max-w-none">
        <p class="text-gray-600 dark:text-gray-400">
            The Query Builder filter allows users to create complex, nested filtering conditions with AND/OR grouping - similar to database query builders.
        </p>
    </div>

    {{-- Query Builder Demo --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Interactive Query Builder</h4>
        <form method="GET">
            <x-filters::filter-panel :panel="$panel" />
        </form>

        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Query Structure (JSON)</h5>
            <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg text-xs overflow-x-auto"><code>{{ json_encode($queryBuilder->toArray(), JSON_PRETTY_PRINT) }}</code></pre>
        </div>
    </div>

    {{-- Available Constraints --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Available Constraint Types</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h5 class="font-medium text-gray-900 dark:text-white mb-2">TextConstraint</h5>
                <p class="text-sm text-gray-600 dark:text-gray-400">For text/string fields</p>
                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">contains, equals, starts_with, ends_with, is_blank</p>
            </div>
            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h5 class="font-medium text-gray-900 dark:text-white mb-2">NumberConstraint</h5>
                <p class="text-sm text-gray-600 dark:text-gray-400">For numeric fields</p>
                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">equals, greater_than, less_than, between</p>
            </div>
            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h5 class="font-medium text-gray-900 dark:text-white mb-2">BooleanConstraint</h5>
                <p class="text-sm text-gray-600 dark:text-gray-400">For true/false fields</p>
                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">is_true, is_false</p>
            </div>
            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h5 class="font-medium text-gray-900 dark:text-white mb-2">DateConstraint</h5>
                <p class="text-sm text-gray-600 dark:text-gray-400">For date/datetime fields</p>
                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">is, is_after, is_before, is_between, is_month, is_year</p>
            </div>
            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h5 class="font-medium text-gray-900 dark:text-white mb-2">SelectConstraint</h5>
                <p class="text-sm text-gray-600 dark:text-gray-400">For predefined options</p>
                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">is, is_not, searchable, multiple</p>
            </div>
            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h5 class="font-medium text-gray-900 dark:text-white mb-2">RelationshipConstraint</h5>
                <p class="text-sm text-gray-600 dark:text-gray-400">For related model attributes</p>
                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">relationship(), titleAttribute()</p>
            </div>
        </div>
    </div>

    <x-accelade::code-block language="php" title="Query Builder Filter">
use Accelade\Filters\QueryBuilder\QueryBuilderFilter;
use Accelade\Filters\QueryBuilder\Constraints\TextConstraint;
use Accelade\Filters\QueryBuilder\Constraints\NumberConstraint;
use Accelade\Filters\QueryBuilder\Constraints\SelectConstraint;
use Accelade\Filters\FilterPanel;
use Accelade\Filters\Enums\FilterLayout;

$queryBuilder = QueryBuilderFilter::make('advanced_filters')
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
            ])
            ->searchable(),
    ]);

$panel = FilterPanel::make()
    ->layout(FilterLayout::AboveContent)
    ->filters([$queryBuilder]);

// Apply to query
$panel->setFilterValues($request->all());
$users = $panel->applyToQuery(User::query())->paginate();
    </x-accelade::code-block>

    <x-accelade::code-block language="json" title="Query Structure Example">
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

// Translates to: name CONTAINS 'John' AND (status = 'active' OR age > 18)
    </x-accelade::code-block>
</div>
