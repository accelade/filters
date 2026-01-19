@props(['prefix' => 'a'])

<div class="space-y-6">
    <div class="prose dark:prose-invert max-w-none">
        <p class="text-gray-600 dark:text-gray-400">
            Text filters allow users to search and filter data using text input.
            Supports various matching modes like contains, exact, starts with, and ends with.
        </p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Text Filter Examples</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Basic Text Filter</label>
                <input type="text" name="search" placeholder="Search..." class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">With Debounce</label>
                <input type="text" name="search_debounced" placeholder="Type to search (debounced)..." class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500">
            </div>
        </div>
        <p class="mt-2 text-xs text-gray-500">Search triggers after 300ms of no typing</p>
    </div>

    <x-accelade::code-block language="php" title="Text Filter">
use Accelade\Filters\Components\TextFilter;

// Basic text filter
TextFilter::make('search')
    ->label('Search')
    ->placeholder('Search users...');

// Exact match
TextFilter::make('email')
    ->label('Email')
    ->exact();

// Starts with
TextFilter::make('name')
    ->label('Name')
    ->startsWith();
    </x-accelade::code-block>
</div>
