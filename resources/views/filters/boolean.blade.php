@props([
    'filter' => null,
    'name' => null,
    'label' => null,
    'value' => null,
    'trueLabel' => 'Yes',
    'falseLabel' => 'No',
    'nullable' => false,
])

@php
    if ($filter) {
        $name = $filter->getName();
        $label = $filter->getLabel();
        $value = $filter->getValue();
        $trueLabel = $filter->getTrueLabel();
        $falseLabel = $filter->getFalseLabel();
        $nullable = $filter->isNullable();
    }

    // Normalize value for comparison
    $normalizedValue = match (true) {
        $value === true, $value === '1', $value === 1 => '1',
        $value === false, $value === '0', $value === 0 => '0',
        default => '',
    };
@endphp

<div {{ $attributes->merge(['class' => 'filter-field boolean-filter']) }}>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
            {{ $label }}
        </label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
    >
        @if($nullable)
            <option value="" @selected($normalizedValue === '')>All</option>
        @endif
        <option value="1" @selected($normalizedValue === '1')>{{ $trueLabel }}</option>
        <option value="0" @selected($normalizedValue === '0')>{{ $falseLabel }}</option>
    </select>
</div>
