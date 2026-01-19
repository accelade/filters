@props(['prefix' => 'a'])

<div class="space-y-6">
    <div class="prose dark:prose-invert max-w-none">
        <p class="text-gray-600 dark:text-gray-400">
            Number filters allow filtering by numeric values with optional min/max constraints.
            Supports comparison operators like equals, greater than, less than, and between.
        </p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Number Filter Examples</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Price</label>
                <input type="number" name="price" placeholder="Enter price..." min="0" step="0.01" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Quantity</label>
                <input type="number" name="quantity" placeholder="Min quantity..." min="0" max="10000" step="1" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Minimum Rating</label>
                <input type="number" name="rating" placeholder="1-5" min="1" max="5" step="0.5" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500">
            </div>
        </div>
    </div>

    <x-accelade::code-block language="php" title="Number Filter">
use Accelade\Filters\Components\NumberFilter;

// Basic number filter
NumberFilter::make('price')
    ->label('Price')
    ->min(0)
    ->max(10000)
    ->step(0.01);

// Greater than or equal
NumberFilter::make('min_quantity')
    ->label('Min Quantity')
    ->greaterThanOrEqual()
    ->min(0);

// Less than or equal
NumberFilter::make('max_price')
    ->label('Max Price')
    ->lessThanOrEqual()
    ->max(10000);

// Integer only
NumberFilter::make('stock')
    ->label('Stock')
    ->step(1)
    ->min(0);
    </x-accelade::code-block>
</div>
