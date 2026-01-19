@props([
    'panel' => null,
    'layout' => 'dropdown',
    'columns' => 1,
    'width' => 'md',
    'maxHeight' => null,
    'deferred' => true,
    'showIndicators' => true,
    'collapsed' => false,
])

@php
    use Accelade\Filters\Enums\FilterLayout;

    $panelInstance = $panel;
    $layoutEnum = $panelInstance?->getLayout() ?? FilterLayout::tryFrom($layout) ?? FilterLayout::Dropdown;
    $columnsCount = $panelInstance?->getColumns() ?? $columns;
    $widthValue = $panelInstance?->getWidth()?->getMaxWidth() ?? match($width) {
        'xs' => '20rem', 'sm' => '24rem', 'md' => '28rem', 'lg' => '32rem',
        'xl' => '36rem', '2xl' => '42rem', '3xl' => '48rem', default => '28rem'
    };
    $maxHeightValue = $panelInstance?->getMaxHeight() ?? $maxHeight;
    $isDeferred = $panelInstance?->isDeferred() ?? $deferred;
    $showIndicatorsValue = $panelInstance?->shouldShowIndicators() ?? $showIndicators;
    $isCollapsed = $panelInstance?->isCollapsed() ?? $collapsed;
    $filters = $panelInstance?->getVisibleFilters() ?? [];
    $activeFilters = $panelInstance?->getActiveFilters() ?? [];
    $hasActiveFilters = count($activeFilters) > 0;
    $indicators = $panelInstance?->getIndicators() ?? [];

    $triggerLabel = $panelInstance?->getTriggerLabel() ?? 'Filters';
    $applyLabel = $panelInstance?->getApplyLabel() ?? 'Apply';
    $resetLabel = $panelInstance?->getResetLabel() ?? 'Reset';

    $panelId = 'filter-panel-' . uniqid();
    $panelConfig = json_encode([
        'layout' => $layoutEnum->value,
        'deferred' => $isDeferred,
        'collapsed' => $isCollapsed,
        'filters' => array_map(fn($f) => $f->toArray(), $filters),
    ]);
@endphp

<div
    data-accelade
    data-accelade-filter-panel
    data-filter-panel-id="{{ $panelId }}"
    data-filter-panel-config="{{ $panelConfig }}"
    class="filter-panel {{ $layoutEnum->getContainerClass() }}"
    {{ $attributes }}
>
    {{-- Filter Indicators --}}
    @if($showIndicatorsValue && $hasActiveFilters)
        <div class="filter-indicators flex flex-wrap gap-2 mb-4">
            @foreach($indicators as $indicator)
                <span
                    class="filter-indicator inline-flex items-center gap-1.5 px-2.5 py-1 text-sm font-medium rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300"
                    data-filter-indicator="{{ $indicator['name'] }}"
                >
                    <span class="indicator-label">{{ $indicator['label'] }}:</span>
                    <span class="indicator-value">{{ $indicator['formatted'] }}</span>
                    <button
                        type="button"
                        class="indicator-remove ml-1 hover:text-indigo-600 dark:hover:text-indigo-200"
                        data-filter-remove="{{ $indicator['name'] }}"
                        aria-label="Remove filter"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </span>
            @endforeach

            @if(count($indicators) > 1)
                <button
                    type="button"
                    class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                    data-filter-reset-all
                >
                    Clear all
                </button>
            @endif
        </div>
    @endif

    {{-- Dropdown Layout --}}
    @if($layoutEnum === FilterLayout::Dropdown)
        <div class="filter-dropdown relative" data-filter-dropdown>
            <button
                type="button"
                class="filter-trigger inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                data-filter-trigger
                aria-expanded="false"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <span>{{ $triggerLabel }}</span>
                @if($hasActiveFilters)
                    <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-semibold rounded-full bg-indigo-500 text-white">
                        {{ count($activeFilters) }}
                    </span>
                @endif
                <svg class="w-4 h-4 transition-transform" data-filter-chevron fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div
                class="filter-dropdown-content absolute z-50 mt-2 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800 hidden"
                style="max-width: {{ $widthValue }}; {{ $maxHeightValue ? "max-height: {$maxHeightValue}; overflow-y: auto;" : '' }}"
                data-filter-content
            >
                @include('filters::components.partials.filter-form', [
                    'filters' => $filters,
                    'columns' => $columnsCount,
                    'deferred' => $isDeferred,
                    'applyLabel' => $applyLabel,
                    'resetLabel' => $resetLabel,
                ])
            </div>
        </div>
    @endif

    {{-- Modal Layout --}}
    @if($layoutEnum === FilterLayout::Modal)
        <button
            type="button"
            class="filter-trigger inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
            data-filter-modal-trigger
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            <span>{{ $triggerLabel }}</span>
            @if($hasActiveFilters)
                <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-semibold rounded-full bg-indigo-500 text-white">
                    {{ count($activeFilters) }}
                </span>
            @endif
        </button>

        <x-accelade::modal name="filter-modal-{{ $panelId }}" maxWidth="2xl">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">{{ $triggerLabel }}</h3>
                @include('filters::components.partials.filter-form', [
                    'filters' => $filters,
                    'columns' => $columnsCount,
                    'deferred' => $isDeferred,
                    'applyLabel' => $applyLabel,
                    'resetLabel' => $resetLabel,
                ])
            </div>
        </x-accelade::modal>
    @endif

    {{-- Above/Below Content Layout --}}
    @if(in_array($layoutEnum, [FilterLayout::AboveContent, FilterLayout::AboveContentCollapsible, FilterLayout::BelowContent]))
        <div
            class="filter-content-panel rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 {{ $layoutEnum === FilterLayout::BelowContent ? 'mt-4' : 'mb-4' }}"
            data-filter-collapsible="{{ $layoutEnum->isCollapsible() ? 'true' : 'false' }}"
        >
            @if($layoutEnum->isCollapsible())
                <button
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50"
                    data-filter-collapse-toggle
                    aria-expanded="{{ $isCollapsed ? 'false' : 'true' }}"
                >
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        {{ $triggerLabel }}
                        @if($hasActiveFilters)
                            <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-semibold rounded-full bg-indigo-500 text-white">
                                {{ count($activeFilters) }}
                            </span>
                        @endif
                    </span>
                    <svg class="w-4 h-4 transition-transform {{ $isCollapsed ? '' : 'rotate-180' }}" data-filter-chevron fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            @endif

            <div
                class="filter-form-container p-4 {{ $layoutEnum->isCollapsible() ? 'border-t border-gray-200 dark:border-gray-700' : '' }} {{ $isCollapsed ? 'hidden' : '' }}"
                data-filter-form-container
            >
                @include('filters::components.partials.filter-form', [
                    'filters' => $filters,
                    'columns' => $columnsCount,
                    'deferred' => $isDeferred,
                    'applyLabel' => $applyLabel,
                    'resetLabel' => $resetLabel,
                    'inline' => true,
                ])
            </div>
        </div>
    @endif

    {{-- Sidebar Layout --}}
    @if(in_array($layoutEnum, [FilterLayout::Sidebar, FilterLayout::SidebarCollapsible]))
        <aside
            class="filter-sidebar w-64 shrink-0 rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800"
            data-filter-collapsible="{{ $layoutEnum->isCollapsible() ? 'true' : 'false' }}"
        >
            @if($layoutEnum->isCollapsible())
                <button
                    type="button"
                    class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50"
                    data-filter-collapse-toggle
                    aria-expanded="{{ $isCollapsed ? 'false' : 'true' }}"
                >
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        {{ $triggerLabel }}
                    </span>
                    <svg class="w-4 h-4 transition-transform {{ $isCollapsed ? '' : 'rotate-180' }}" data-filter-chevron fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            @else
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        {{ $triggerLabel }}
                    </h3>
                </div>
            @endif

            <div
                class="filter-form-container p-4 {{ $isCollapsed ? 'hidden' : '' }}"
                style="{{ $maxHeightValue ? "max-height: {$maxHeightValue}; overflow-y: auto;" : '' }}"
                data-filter-form-container
            >
                @include('filters::components.partials.filter-form', [
                    'filters' => $filters,
                    'columns' => 1,
                    'deferred' => $isDeferred,
                    'applyLabel' => $applyLabel,
                    'resetLabel' => $resetLabel,
                    'vertical' => true,
                ])
            </div>
        </aside>
    @endif

    {{-- Inline Layout --}}
    @if($layoutEnum === FilterLayout::Inline)
        <div class="filter-inline flex flex-wrap items-end gap-4">
            @foreach($filters as $filter)
                <div class="filter-item min-w-[150px]">
                    {!! $filter->render() !!}
                </div>
            @endforeach

            @if($isDeferred)
                <button
                    type="button"
                    class="px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    data-filter-apply
                >
                    {{ $applyLabel }}
                </button>
            @endif

            @if($hasActiveFilters)
                <button
                    type="button"
                    class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                    data-filter-reset
                >
                    {{ $resetLabel }}
                </button>
            @endif
        </div>
    @endif
</div>
