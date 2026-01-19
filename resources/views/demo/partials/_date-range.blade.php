@props(['prefix' => 'a'])

<div class="space-y-6">
    <div class="prose dark:prose-invert max-w-none">
        <p class="text-gray-600 dark:text-gray-400">
            Date range filters allow filtering by a date range with from/to values.
            Perfect for filtering records within a specific time period.
        </p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Date Range Filter Examples</h4>
        <div class="space-y-6">
            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Created Between</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">From</label>
                        <input type="date" name="created_at[from]" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">To</label>
                        <input type="date" name="created_at[to]" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Updated Between</label>
                <div class="flex gap-2">
                    <input type="date" name="updated_at[from]" placeholder="From" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                    <input type="date" name="updated_at[to]" placeholder="To" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                </div>
            </div>
        </div>
    </div>

    <x-accelade::code-block language="php" title="Date Range Filter">
use Accelade\Filters\Components\DateRangeFilter;

// Basic date range
DateRangeFilter::make('created_at')
    ->label('Created Between');

// Custom from/to labels
DateRangeFilter::make('validity')
    ->label('Valid Period')
    ->keys('start', 'end');

// With time
DateRangeFilter::make('event_time')
    ->label('Event Time')
    ->withTime();

// With constraints
DateRangeFilter::make('report_period')
    ->label('Report Period')
    ->minDate(now()->subYear()->format('Y-m-d'))
    ->maxDate(now()->format('Y-m-d'));
    </x-accelade::code-block>
</div>
