@props([
    'filter' => null,
    'name' => null,
    'label' => null,
    'value' => null,
    'minDate' => null,
    'maxDate' => null,
    'withTime' => false,
    'native' => true,
    'displayFormat' => null,
    'placeholder' => null,
])

@php
    if ($filter) {
        $name = $filter->getName();
        $label = $filter->getLabel();
        $value = $filter->getValue();
        $minDate = $filter->getMinDate();
        $maxDate = $filter->getMaxDate();
        $withTime = $filter->hasTime();
        $native = $filter->isNative();
        $displayFormat = $filter->getDisplayFormat();
        $placeholder = $filter->getPlaceholder() ?? ($withTime ? 'Select date and time...' : 'Select date...');
    }

    // Build flatpickr options for enhanced date picker
    $flatpickrOptions = [
        'enableTime' => $withTime,
        'dateFormat' => $withTime ? 'Y-m-d H:i' : 'Y-m-d',
        'altInput' => true,
        'altFormat' => $displayFormat ?? ($withTime ? 'F j, Y h:i K' : 'F j, Y'),
        'allowInput' => true,
    ];

    if ($minDate) {
        $flatpickrOptions['minDate'] = $minDate;
    }
    if ($maxDate) {
        $flatpickrOptions['maxDate'] = $maxDate;
    }

    $containerClasses = 'relative rounded-lg border border-gray-300 bg-white shadow-sm transition-all duration-150 focus-within:border-primary-500 focus-within:ring-2 focus-within:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-800';
    $inputClasses = 'block w-full px-3 py-2 text-sm bg-transparent text-gray-900 placeholder-gray-400 border-0 focus:ring-0 focus:outline-none dark:text-gray-100 dark:placeholder-gray-500';
@endphp

<div {{ $attributes->merge(['class' => 'filter-field date-filter']) }}
    @if(!$native)
        data-flatpickr="{{ json_encode($flatpickrOptions) }}"
    @endif
>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
            {{ $label }}
        </label>
    @endif

    @if($native)
        {{-- Native browser date input --}}
        <div class="{{ $containerClasses }}">
            <input
                type="{{ $withTime ? 'datetime-local' : 'date' }}"
                name="{{ $name }}"
                id="{{ $name }}"
                @if($value) value="{{ $value }}" @endif
                @if($minDate) min="{{ $minDate }}" @endif
                @if($maxDate) max="{{ $maxDate }}" @endif
                class="{{ $inputClasses }}"
            >
        </div>
    @else
        {{-- Flatpickr enhanced date picker --}}
        <div class="{{ $containerClasses }} flex items-center">
            <input
                type="text"
                name="{{ $name }}"
                id="{{ $name }}"
                @if($value) value="{{ $value }}" @endif
                placeholder="{{ $placeholder }}"
                class="date-picker-input flatpickr-input {{ $inputClasses }} pe-10"
                autocomplete="off"
            >
            <button type="button" class="date-picker-toggle absolute inset-y-0 end-0 flex items-center pe-3 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300" tabindex="-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </button>
        </div>
    @endif
</div>
