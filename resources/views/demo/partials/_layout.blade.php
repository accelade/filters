@props(['prefix' => 'a'])

@php
    use Accelade\Filters\Components\TextFilter;
    use Accelade\Filters\Components\SelectFilter;
    use Accelade\Filters\Components\BooleanFilter;
    use Accelade\Filters\Enums\FilterLayout;
    use Accelade\Filters\Enums\FilterWidth;
    use Accelade\Filters\FilterPanel;

    $filters = [
        TextFilter::make('search')
            ->label('Search')
            ->placeholder('Search...'),

        SelectFilter::make('status')
            ->label('Status')
            ->placeholder('All statuses')
            ->options([
                'active' => 'Active',
                'inactive' => 'Inactive',
                'pending' => 'Pending',
            ]),

        BooleanFilter::make('verified')
            ->label('Verified'),
    ];

    $dropdownPanel = FilterPanel::make()
        ->layout(FilterLayout::Dropdown)
        ->width(FilterWidth::Large)
        ->columns(2)
        ->showIndicators()
        ->filters($filters);

    $collapsiblePanel = FilterPanel::make()
        ->layout(FilterLayout::AboveContentCollapsible)
        ->columns(3)
        ->showIndicators()
        ->filters($filters);

    $inlinePanel = FilterPanel::make()
        ->layout(FilterLayout::Inline)
        ->filters($filters);
@endphp

<div class="space-y-6">
    <div class="prose dark:prose-invert max-w-none">
        <p class="text-gray-600 dark:text-gray-400">
            Filter layouts control how filters are presented to users. Choose from dropdown menus, modal dialogs, inline forms, sidebars, and collapsible panels.
        </p>
    </div>

    {{-- Dropdown Layout --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Dropdown Layout</h4>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">A compact trigger button that reveals filters in a dropdown panel.</p>
        <form>
            <x-filters::filter-panel :panel="$dropdownPanel" />
        </form>
    </div>

    {{-- Above Content Collapsible --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Above Content (Collapsible)</h4>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Displays filters above content with the ability to collapse.</p>
        <form>
            <x-filters::filter-panel :panel="$collapsiblePanel" />
        </form>
    </div>

    {{-- Inline Layout --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Inline Layout</h4>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Filters displayed inline for simple filtering needs.</p>
        <form>
            <x-filters::filter-panel :panel="$inlinePanel" />
        </form>
    </div>

    <x-accelade::code-block language="php" title="Filter Layouts">
use Accelade\Filters\Enums\FilterLayout;
use Accelade\Filters\Enums\FilterWidth;
use Accelade\Filters\FilterPanel;

// Dropdown layout (default)
$panel = FilterPanel::make()
    ->layout(FilterLayout::Dropdown)
    ->width(FilterWidth::Large)
    ->filters([...]);

// Above content, collapsible
$panel = FilterPanel::make()
    ->layout(FilterLayout::AboveContentCollapsible)
    ->collapsed()  // Start collapsed
    ->filters([...]);

// Modal dialog
$panel = FilterPanel::make()
    ->layout(FilterLayout::Modal)
    ->filters([...]);

// Sidebar
$panel = FilterPanel::make()
    ->layout(FilterLayout::Sidebar)
    ->maxHeight('600px')
    ->filters([...]);

// Inline
$panel = FilterPanel::make()
    ->layout(FilterLayout::Inline)
    ->filters([...]);
    </x-accelade::code-block>
</div>
