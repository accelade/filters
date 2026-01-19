@props([
    'filter' => null,
    'name' => null,
    'label' => null,
    'value' => null,
    'minDate' => null,
    'maxDate' => null,
    'withTime' => false,
    'fromKey' => 'from',
    'toKey' => 'to',
])

@php
    if ($filter) {
        $name = $filter->getName();
        $label = $filter->getLabel();
        $value = $filter->getValue() ?? [];
        $minDate = $filter->getMinDate();
        $maxDate = $filter->getMaxDate();
        $withTime = $filter->hasTime();
        $fromKey = $filter->getFromKey();
        $toKey = $filter->getToKey();
    }

    $fromValue = $value[$fromKey] ?? null;
    $toValue = $value[$toKey] ?? null;
    $inputType = $withTime ? 'datetime-local' : 'date';
@endphp

<div {{ $attributes->merge(['class' => 'filter-field date-range-filter']) }}>
    @if($label)
        <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
            {{ $label }}
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
        <div>
            <label for="{{ $name }}_{{ $fromKey }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">From</label>
            <input
                type="{{ $inputType }}"
                name="{{ $name }}[{{ $fromKey }}]"
                id="{{ $name }}_{{ $fromKey }}"
                @if($fromValue) value="{{ $fromValue }}" @endif
                @if($minDate) min="{{ $minDate }}" @endif
                @if($maxDate) max="{{ $maxDate }}" @endif
                class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
            >
        </div>
        <div>
            <label for="{{ $name }}_{{ $toKey }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">To</label>
            <input
                type="{{ $inputType }}"
                name="{{ $name }}[{{ $toKey }}]"
                id="{{ $name }}_{{ $toKey }}"
                @if($toValue) value="{{ $toValue }}" @endif
                @if($minDate) min="{{ $minDate }}" @endif
                @if($maxDate) max="{{ $maxDate }}" @endif
                class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
            >
        </div>
    </div>
</div>
