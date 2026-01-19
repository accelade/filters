@props(['prefix' => 'a'])

<div class="space-y-6">
    <div class="prose dark:prose-invert max-w-none">
        <p class="text-gray-600 dark:text-gray-400">
            Boolean filters allow filtering by true/false values with customizable labels.
            Supports nullable option for "All" state.
        </p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Boolean Filter Examples</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Active Status</label>
                <select name="is_active" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                    <option value="">All</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Verified</label>
                <select name="is_verified" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                    <option value="">Any</option>
                    <option value="1">Verified</option>
                    <option value="0">Not Verified</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Featured</label>
                <select name="is_featured" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                    <option value="">All Items</option>
                    <option value="1">Featured</option>
                    <option value="0">Regular</option>
                </select>
            </div>
        </div>
    </div>

    <x-accelade::code-block language="php" title="Boolean Filter">
use Accelade\Filters\Components\BooleanFilter;

// Basic boolean
BooleanFilter::make('is_active')
    ->label('Active')
    ->trueLabel('Yes')
    ->falseLabel('No');

// Nullable boolean (with "All" option)
BooleanFilter::make('is_verified')
    ->label('Verified')
    ->nullable()
    ->trueLabel('Verified')
    ->falseLabel('Not Verified');
    </x-accelade::code-block>
</div>
