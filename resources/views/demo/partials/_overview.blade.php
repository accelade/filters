@props(['prefix' => 'a'])

@php
    use App\Models\User;
    use Accelade\Filters\Components\TextFilter;
    use Accelade\Filters\Components\SelectFilter;
    use Accelade\Filters\Components\DateFilter;
    use Accelade\Filters\Components\BooleanFilter;
    use Accelade\Filters\Components\NumberFilter;
    use Accelade\Filters\Components\DateRangeFilter;
    use Accelade\Filters\FilterPanel;
    use Accelade\Filters\Enums\FilterLayout;
    use Accelade\Filters\Enums\FilterWidth;

    // Create filters with all features
    $textFilter = TextFilter::make('search')
        ->label('Search')
        ->placeholder('Search by name or email...')
        ->column('name')
        ->setValue(request('search'));

    $selectFilter = SelectFilter::make('email_domain')
        ->label('Email Domain')
        ->placeholder('All domains')
        ->options([
            'gmail.com' => 'Gmail',
            'yahoo.com' => 'Yahoo',
            'outlook.com' => 'Outlook',
            'hotmail.com' => 'Hotmail',
            'example.com' => 'Example',
            'example.net' => 'Example.net',
            'example.org' => 'Example.org',
        ])
        ->searchable()
        ->native(false)
        ->setValue(request('email_domain'));

    $booleanFilter = BooleanFilter::make('email_verified')
        ->label('Email Verified')
        ->nullable()
        ->setValue(request('email_verified'));

    $dateFilter = DateFilter::make('created_at')
        ->label('Registered After')
        ->native(false)
        ->displayFormat('M j, Y')
        ->from()
        ->setValue(request('created_at'));

    $numberFilter = NumberFilter::make('id')
        ->label('User ID')
        ->placeholder('Enter ID...')
        ->min(1)
        ->setValue(request('id'));

    // Create filter panel
    $panel = FilterPanel::make()
        ->layout(FilterLayout::AboveContentCollapsible)
        ->width(FilterWidth::Full)
        ->columns(3)
        ->showIndicators()
        ->triggerLabel('Filter Users')
        ->applyLabel('Apply Filters')
        ->resetLabel('Clear All')
        ->filters([
            $textFilter,
            $selectFilter,
            $booleanFilter,
            $dateFilter,
            $numberFilter,
        ]);

    // Apply filters to query
    $query = User::query();

    // Apply text search
    if ($search = request('search')) {
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    // Apply email domain filter
    if ($domain = request('email_domain')) {
        $query->where('email', 'like', "%@{$domain}");
    }

    // Apply email verified filter
    if (request()->has('email_verified') && request('email_verified') !== '') {
        if (request('email_verified') === '1' || request('email_verified') === 'true') {
            $query->whereNotNull('email_verified_at');
        } else {
            $query->whereNull('email_verified_at');
        }
    }

    // Apply date filter
    if ($date = request('created_at')) {
        $query->where('created_at', '>=', $date);
    }

    // Apply user ID filter
    if ($id = request('id')) {
        $query->where('id', $id);
    }

    // Get paginated results
    $users = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

    // Get stats
    $totalUsers = User::count();
    $verifiedUsers = User::whereNotNull('email_verified_at')->count();
    $recentUsers = User::where('created_at', '>=', now()->subDays(30))->count();
@endphp

<div class="space-y-6">
    <div class="prose dark:prose-invert max-w-none">
        <p class="text-gray-600 dark:text-gray-400">
            Filters provide reusable components for filtering data in tables, grids, and custom views.
            This demo shows all filter types working together with real user data from the database.
        </p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalUsers) }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Total Users</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($verifiedUsers) }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Verified Users</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($recentUsers) }}</div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Last 30 Days</div>
        </div>
    </div>

    {{-- Filter Panel with User Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <form method="GET" class="p-6" data-filter-target="users-table">
            <x-filters::filter-panel :panel="$panel" />
        </form>

        {{-- Results Table - wrapped in rehydrate for partial updates --}}
        <x-accelade::rehydrate id="users-table" :preserveScroll="true">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Verified</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Registered</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $user->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-medium text-sm">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->email_verified_at)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Verified
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $user->created_at->format('M j, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="mt-2 text-sm">No users found matching your filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $users->links() }}
            </div>
        @endif
        </x-accelade::rehydrate>
    </div>

    {{-- Current Filters State --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Filter State (Debug)</h4>
        <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg text-xs overflow-x-auto"><code>{{ json_encode([
            'request' => request()->except(['page']),
            'panel' => $panel->toArray(),
            'results' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
            ],
        ], JSON_PRETTY_PRINT) }}</code></pre>
    </div>

    {{-- Code Example --}}
    <x-accelade::code-block language="php" title="Usage Example">
use App\Models\User;
use Accelade\Filters\Components\TextFilter;
use Accelade\Filters\Components\SelectFilter;
use Accelade\Filters\Components\DateFilter;
use Accelade\Filters\Components\BooleanFilter;
use Accelade\Filters\FilterPanel;
use Accelade\Filters\Enums\FilterLayout;

// Create filters with enhanced features
$panel = FilterPanel::make()
    ->layout(FilterLayout::AboveContentCollapsible)
    ->columns(3)
    ->showIndicators()
    ->filters([
        TextFilter::make('search')
            ->label('Search')
            ->placeholder('Search by name or email...'),

        SelectFilter::make('email_domain')
            ->label('Email Domain')
            ->options([
                'gmail.com' => 'Gmail',
                'yahoo.com' => 'Yahoo',
            ])
            ->searchable()       // Enable search
            ->native(false),     // Use enhanced dropdown

        BooleanFilter::make('email_verified')
            ->label('Email Verified')
            ->nullable(),

        DateFilter::make('created_at')
            ->label('Registered After')
            ->native(false)      // Use date picker
            ->displayFormat('M j, Y')
            ->from(),
    ]);

// Apply to query
$panel->setFilterValues($request->all());
$users = $panel->applyToQuery(User::query())->paginate(10);
    </x-accelade::code-block>
</div>
