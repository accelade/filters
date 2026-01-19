@props([
    'filters' => [],
    'columns' => 1,
    'deferred' => true,
    'applyLabel' => 'Apply',
    'resetLabel' => 'Reset',
    'inline' => false,
    'vertical' => false,
])

@php
    $gridClass = match($columns) {
        1 => 'grid-cols-1',
        2 => 'grid-cols-1 sm:grid-cols-2',
        3 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
        4 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
        default => "grid-cols-1 sm:grid-cols-2 lg:grid-cols-{$columns}",
    };
@endphp

<div
    class="filter-form"
    data-filter-form
    @if(!$deferred) data-filter-auto-submit @endif
>
    <div class="grid {{ $gridClass }} gap-4 {{ $vertical ? '' : '' }}">
        @foreach($filters as $filter)
            <div class="filter-field" data-filter-field="{{ $filter->getName() }}">
                {!! $filter->render() !!}
            </div>
        @endforeach
    </div>

    <div class="filter-actions flex items-center justify-between mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
        <button
            type="button"
            class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
            data-filter-reset
        >
            {{ $resetLabel }}
        </button>

        @if($deferred)
            <button
                type="submit"
                class="px-4 py-2 text-sm font-medium rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                data-filter-apply
            >
                {{ $applyLabel }}
            </button>
        @endif
    </div>
</div>
