@props(['prefix' => 'a'])

<div class="space-y-6">
    <div class="prose dark:prose-invert max-w-none">
        <p class="text-gray-600 dark:text-gray-400">
            Date filters allow filtering by a single date value.
            Supports date-only and datetime formats with optional min/max constraints.
        </p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Date Filter Examples</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Created Date</label>
                <input type="date" name="created_at" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">From Date</label>
                <input type="date" name="start_date" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Published At (with time)</label>
                <input type="datetime-local" name="published_at" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
            </div>
        </div>
    </div>

    <x-accelade::code-block language="php" title="Date Filter">
use Accelade\Filters\Components\DateFilter;

// Basic date filter
DateFilter::make('created_at')
    ->label('Created Date');

// Date from (>=)
DateFilter::make('start_date')
    ->label('From')
    ->from();

// Date until (<=)
DateFilter::make('end_date')
    ->label('To')
    ->until();

// With time
DateFilter::make('published_at')
    ->label('Published At')
    ->withTime();

// With min/max constraints
DateFilter::make('birth_date')
    ->label('Birth Date')
    ->minDate('1900-01-01')
    ->maxDate(now()->format('Y-m-d'));
    </x-accelade::code-block>
</div>
