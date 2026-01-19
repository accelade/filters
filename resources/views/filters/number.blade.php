@props([
    'filter' => null,
    'name' => null,
    'label' => null,
    'placeholder' => null,
    'value' => null,
    'min' => null,
    'max' => null,
    'step' => null,
])

@php
    if ($filter) {
        $name = $filter->getName();
        $label = $filter->getLabel();
        $placeholder = $filter->getPlaceholder();
        $value = $filter->getValue();
        $min = $filter->getMin();
        $max = $filter->getMax();
        $step = $filter->getStep();
    }
@endphp

<div {{ $attributes->merge(['class' => 'filter-field number-filter']) }}>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
            {{ $label }}
        </label>
    @endif

    <input
        type="number"
        name="{{ $name }}"
        id="{{ $name }}"
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($value !== null) value="{{ $value }}" @endif
        @if($min !== null) min="{{ $min }}" @endif
        @if($max !== null) max="{{ $max }}" @endif
        @if($step !== null) step="{{ $step }}" @endif
        class="block w-full px-3 py-2 text-sm rounded-lg border border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500"
    >
</div>
