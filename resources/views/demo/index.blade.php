@extends('accelade::components.layouts.demo')

@section('title', 'Filters Demo')

@section('content')
<div class="space-y-12">
    {{-- Header --}}
    <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Filters Demo</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Demonstration of Accelade Filters package with multiple layouts, data table, and JSON response.
        </p>
    </div>

    {{-- Section 1: Dropdown Layout --}}
    <section class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">1. Dropdown Layout</h2>
            <span class="text-sm text-gray-500 dark:text-gray-400">FilterLayout::Dropdown</span>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <form method="GET" action="{{ route('filters.demo') }}" class="space-y-4">
                <x-filters::filter-panel :panel="$dropdownPanel" />

                <div class="text-xs text-gray-500 dark:text-gray-400">
                    Click the "Filters" button to open the dropdown panel.
                </div>
            </form>
        </div>
    </section>

    {{-- Section 2: Above Content Collapsible Layout --}}
    <section class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">2. Above Content (Collapsible)</h2>
            <span class="text-sm text-gray-500 dark:text-gray-400">FilterLayout::AboveContentCollapsible</span>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <form method="GET" action="{{ route('filters.demo') }}" class="space-y-4">
                <x-filters::filter-panel :panel="$aboveContentPanel" />
            </form>
        </div>
    </section>

    {{-- Section 3: Inline Layout --}}
    <section class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">3. Inline Layout</h2>
            <span class="text-sm text-gray-500 dark:text-gray-400">FilterLayout::Inline</span>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <form method="GET" action="{{ route('filters.demo') }}">
                <x-filters::filter-panel :panel="$inlinePanel" />
            </form>
        </div>
    </section>

    {{-- Section 4: Query Builder --}}
    <section class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">4. Query Builder</h2>
            <span class="text-sm text-gray-500 dark:text-gray-400">QueryBuilderFilter</span>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Build complex queries with AND/OR grouping and multiple constraint types.
            </p>
            <form method="GET" action="{{ route('filters.demo') }}">
                <x-filters::filter-panel :panel="$queryBuilderPanel" />
            </form>
        </div>
    </section>

    {{-- Section 5: Results Table --}}
    <section class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Filtered Results</h2>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                {{ $data->count() }} of {{ count($rawData) }} records
            </span>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Salary</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Verified</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($data as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $row['id'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $row['name'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $row['email'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'active' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                            'inactive' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$row['status']] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($row['status']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ ucfirst($row['role']) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">${{ number_format($row['salary']) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($row['is_verified'])
                                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $row['created_at'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No results found</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your filters to find what you're looking for.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- Section 6: JSON Response --}}
    <section class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Current Filter State (JSON)</h2>
            <button
                type="button"
                onclick="copyToClipboard()"
                class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
            >
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                Copy
            </button>
        </div>

        <div class="bg-gray-900 rounded-lg shadow-sm border border-gray-700 overflow-hidden">
            <pre id="json-output" class="p-4 text-sm text-gray-100 overflow-x-auto"><code>{{ json_encode([
    'filters' => $filterValues,
    'result_count' => $data->count(),
    'total_count' => count($rawData),
    'data' => $data->values(),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
        </div>
    </section>

    {{-- Section 7: Code Example --}}
    <section class="space-y-4">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Usage Example</h2>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <x-accelade::code-block language="php">
use Accelade\Filters\Components\TextFilter;
use Accelade\Filters\Components\SelectFilter;
use Accelade\Filters\Components\BooleanFilter;
use Accelade\Filters\Enums\FilterLayout;
use Accelade\Filters\FilterPanel;
use Accelade\Filters\QueryBuilder\QueryBuilderFilter;
use Accelade\Filters\QueryBuilder\Constraints\TextConstraint;
use Accelade\Filters\QueryBuilder\Constraints\NumberConstraint;

// Create a filter panel with dropdown layout
$panel = FilterPanel::make()
    ->layout(FilterLayout::Dropdown)
    ->columns(2)
    ->showIndicators()
    ->filters([
        TextFilter::make('search')
            ->label('Search')
            ->placeholder('Search...'),

        SelectFilter::make('status')
            ->label('Status')
            ->options([
                'active' => 'Active',
                'inactive' => 'Inactive',
            ]),

        BooleanFilter::make('is_verified')
            ->label('Verified'),
    ]);

// Apply filters to query
$query = User::query();
$panel->setFilterValues($request->all());
$users = $panel->applyToQuery($query)->paginate();

// Create a query builder filter
$queryBuilder = QueryBuilderFilter::make('advanced')
    ->constraints([
        TextConstraint::make('name')->label('Name'),
        TextConstraint::make('email')->label('Email'),
        NumberConstraint::make('salary')->label('Salary'),
    ]);
            </x-accelade::code-block>
        </div>
    </section>

    {{-- Section 8: Blade Template Example --}}
    <section class="space-y-4">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Blade Template</h2>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <x-accelade::code-block language="html">
{{-- Dropdown Layout --}}
<x-filters::filter-panel :panel="$panel" />

{{-- Or with individual props --}}
<x-filters::filter-panel
    layout="above-content-collapsible"
    :columns="2"
    :showIndicators="true"
/>

{{-- Query Builder --}}
<x-filters::query-builder
    :filter="$queryBuilderFilter"
    name="query"
/>
            </x-accelade::code-block>
        </div>
    </section>
</div>

<script>
function copyToClipboard() {
    const jsonOutput = document.getElementById('json-output').textContent;
    navigator.clipboard.writeText(jsonOutput).then(() => {
        // Show brief notification
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<svg class="w-4 h-4 mr-1.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>Copied!';
        setTimeout(() => btn.innerHTML = originalText, 2000);
    });
}
</script>
@endsection
