@props(['prefix' => 'a'])

<div class="space-y-6">
    <div class="prose dark:prose-invert max-w-none">
        <p class="text-gray-600 dark:text-gray-400">
            Select filters provide dropdown options for filtering data.
            Supports native selects, searchable dropdowns, and multiple selection.
        </p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Select Filter Examples</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Native Select</label>
                <select name="status" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Searchable Select</label>
                <select name="status_searchable" class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                    <option value="">Search status...</option>
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="inactive">Inactive</option>
                </select>
                <p class="mt-1 text-xs text-gray-500">Use searchable() for enhanced dropdown</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">Multiple Select</label>
                <select name="roles[]" multiple class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
                    <option value="admin">Admin</option>
                    <option value="editor">Editor</option>
                    <option value="viewer">Viewer</option>
                </select>
                <p class="mt-1 text-xs text-gray-500">Hold Ctrl/Cmd to select multiple</p>
            </div>
        </div>
    </div>

    <x-accelade::code-block language="php" title="Select Filter">
use Accelade\Filters\Components\SelectFilter;

// Basic select
SelectFilter::make('status')
    ->label('Status')
    ->options([
        'active' => 'Active',
        'pending' => 'Pending',
        'inactive' => 'Inactive',
    ])
    ->placeholder('All Status');

// Searchable select
SelectFilter::make('category')
    ->label('Category')
    ->searchable()
    ->options(Category::pluck('name', 'id'));

// Multiple selection
SelectFilter::make('roles')
    ->label('Roles')
    ->multiple()
    ->options(Role::pluck('name', 'id'));
    </x-accelade::code-block>
</div>
