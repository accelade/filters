@props(['prefix' => 'a'])

@php
    use Accelade\Filters\Components\TextFilter;
    use Accelade\Filters\Components\SelectFilter;
    use Accelade\Filters\Components\BooleanFilter;
    use Accelade\Filters\Components\DateFilter;
    use Accelade\Filters\Enums\FilterLayout;
    use Accelade\Filters\Enums\FilterWidth;
    use Accelade\Filters\FilterPanel;

    $panel = FilterPanel::make()
        ->layout(FilterLayout::AboveContentCollapsible)
        ->width(FilterWidth::Large)
        ->columns(2)
        ->showIndicators()
        ->triggerLabel('Filters')
        ->applyLabel('Apply Filters')
        ->resetLabel('Clear All')
        ->filters([
            TextFilter::make('search')
                ->label('Search')
                ->placeholder('Search users...')
                ->setValue(request('search')),

            SelectFilter::make('role')
                ->label('Role')
                ->placeholder('All roles')
                ->options([
                    'admin' => 'Administrator',
                    'editor' => 'Editor',
                    'viewer' => 'Viewer',
                ])
                ->setValue(request('role')),

            BooleanFilter::make('verified')
                ->label('Email Verified')
                ->setValue(request('verified')),

            DateFilter::make('created_at')
                ->label('Registered After')
                ->setValue(request('created_at')),
        ]);
@endphp

<div class="space-y-6">
    <div class="prose dark:prose-invert max-w-none">
        <p class="text-gray-600 dark:text-gray-400">
            The Filter Panel is a container for managing collections of filters with layout options, indicators, and form submission handling.
        </p>
    </div>

    {{-- Filter Panel Demo --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Filter Panel with Indicators</h4>
        <form method="GET">
            <x-filters::filter-panel :panel="$panel" />
        </form>

        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Current Filter State</h5>
            <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg text-xs overflow-x-auto"><code>{{ json_encode($panel->toArray(), JSON_PRETTY_PRINT) }}</code></pre>
        </div>
    </div>

    <x-accelade::code-block language="php" title="Filter Panel">
use Accelade\Filters\Components\TextFilter;
use Accelade\Filters\Components\SelectFilter;
use Accelade\Filters\Components\BooleanFilter;
use Accelade\Filters\Enums\FilterLayout;
use Accelade\Filters\Enums\FilterWidth;
use Accelade\Filters\FilterPanel;

$panel = FilterPanel::make()
    ->layout(FilterLayout::AboveContentCollapsible)
    ->width(FilterWidth::Large)
    ->columns(2)
    ->showIndicators()
    ->triggerLabel('Filters')
    ->applyLabel('Apply Filters')
    ->resetLabel('Clear All')
    ->filters([
        TextFilter::make('search')
            ->label('Search')
            ->placeholder('Search users...'),

        SelectFilter::make('role')
            ->label('Role')
            ->options([
                'admin' => 'Administrator',
                'editor' => 'Editor',
            ]),

        BooleanFilter::make('verified')
            ->label('Email Verified'),
    ]);

// Apply to query
$panel->setFilterValues($request->all());
$users = $panel->applyToQuery(User::query())->paginate();
    </x-accelade::code-block>
</div>
