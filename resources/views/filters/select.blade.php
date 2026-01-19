@props([
    'filter' => null,
    'name' => null,
    'label' => null,
    'placeholder' => null,
    'value' => null,
    'options' => [],
    'multiple' => false,
    'searchable' => false,
    'native' => true,
])

@php
    if ($filter) {
        $name = $filter->getName();
        $label = $filter->getLabel();
        $placeholder = $filter->getPlaceholder() ?? 'Select...';
        $value = $filter->getValue();
        $options = $filter->getFormattedOptions();
        $multiple = $filter->isMultiple();
        $searchable = $filter->isSearchable();
        $native = $filter->isNative();
    }

    // Convert options to key-value format if needed
    $formattedOptions = collect($options)->map(function ($item, $key) {
        if (is_array($item) && isset($item['value'], $item['label'])) {
            return $item;
        }
        return ['value' => is_numeric($key) ? $item : $key, 'label' => $item];
    })->values()->toArray();

    // Normalize value for comparison
    $selectedValues = is_array($value) ? $value : ($value !== null ? [$value] : []);

    // Build searchable select options
    $searchableOptions = [
        'searchable' => $searchable,
        'multiple' => $multiple,
        'placeholder' => $placeholder,
        'allowClear' => true,
        'searchPlaceholder' => 'Search...',
        'noResultsText' => 'No results found',
    ];

    $containerClasses = 'relative rounded-lg border border-gray-300 bg-white shadow-sm transition-all duration-150 focus-within:border-primary-500 focus-within:ring-2 focus-within:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-800';
    $selectClasses = 'block w-full px-3 py-2 text-sm bg-transparent text-gray-900 border-0 focus:ring-0 focus:outline-none dark:text-gray-100 appearance-none';
@endphp

<div {{ $attributes->merge(['class' => 'filter-field select-filter']) }}
    @if(!$native)
        data-searchable-select="{{ json_encode($searchableOptions) }}"
    @endif
>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">
            {{ $label }}
        </label>
    @endif

    @if($native)
        {{-- Native Select --}}
        <div class="{{ $containerClasses }} flex items-center">
            {{-- Dropdown arrow icon --}}
            <div class="pointer-events-none absolute inset-y-0 end-0 flex items-center pe-3">
                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>

            <select
                name="{{ $multiple ? $name . '[]' : $name }}"
                id="{{ $name }}"
                @if($multiple) multiple @endif
                class="{{ $selectClasses }} pe-10"
            >
                @if($placeholder && !$multiple)
                    <option value="">{{ $placeholder }}</option>
                @endif

                @foreach($formattedOptions as $option)
                    @php
                        $optionValue = $option['value'] ?? '';
                        $optionLabel = $option['label'] ?? $optionValue;
                        $isSelected = in_array((string) $optionValue, array_map('strval', $selectedValues));
                    @endphp
                    <option value="{{ $optionValue }}" @selected($isSelected)>
                        {{ $optionLabel }}
                    </option>
                @endforeach
            </select>
        </div>
    @else
        {{-- Searchable Select --}}
        <div class="searchable-select-wrapper relative">
            {{-- Hidden select for form submission --}}
            <select
                name="{{ $multiple ? $name . '[]' : $name }}"
                id="{{ $name }}"
                @if($multiple) multiple @endif
                class="searchable-select-hidden sr-only"
            >
                @if($placeholder && !$multiple)
                    <option value="">{{ $placeholder }}</option>
                @endif

                @foreach($formattedOptions as $option)
                    @php
                        $optionValue = $option['value'] ?? '';
                        $optionLabel = $option['label'] ?? $optionValue;
                        $isSelected = in_array((string) $optionValue, array_map('strval', $selectedValues));
                    @endphp
                    <option value="{{ $optionValue }}" @selected($isSelected)>
                        {{ $optionLabel }}
                    </option>
                @endforeach
            </select>

            {{-- Custom dropdown trigger --}}
            <button
                type="button"
                class="searchable-select-trigger {{ $containerClasses }} flex items-center justify-between w-full text-left px-3 py-2"
                aria-haspopup="listbox"
                aria-expanded="false"
            >
                <span class="searchable-select-display text-sm text-gray-900 dark:text-gray-100 truncate flex-1">
                    @php
                        $selectedLabels = collect($formattedOptions)
                            ->filter(fn($opt) => in_array((string) ($opt['value'] ?? ''), array_map('strval', $selectedValues)))
                            ->pluck('label')
                            ->implode(', ');
                    @endphp
                    @if($selectedLabels)
                        {{ $selectedLabels }}
                    @else
                        <span class="text-gray-400 dark:text-gray-500">{{ $placeholder }}</span>
                    @endif
                </span>

                <span class="searchable-select-icons flex items-center gap-1">
                    @if(count($selectedValues) > 0 && $selectedValues[0] !== null && $selectedValues[0] !== '')
                        <span class="searchable-select-clear text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 cursor-pointer" title="Clear">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </span>
                    @endif

                    <span class="searchable-select-arrow text-gray-400 dark:text-gray-500 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                </span>
            </button>

            {{-- Dropdown panel --}}
            <div class="searchable-select-dropdown hidden absolute left-0 right-0 z-50 mt-1 rounded-lg border border-gray-300 bg-white shadow-lg dark:border-gray-600 dark:bg-gray-800">
                {{-- Search input --}}
                @if($searchable)
                    <div class="searchable-select-search-wrapper p-2 border-b border-gray-200 dark:border-gray-700">
                        <input
                            type="text"
                            class="searchable-select-search w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                            placeholder="Search..."
                            autocomplete="off"
                        />
                    </div>
                @endif

                {{-- Options list --}}
                <ul class="searchable-select-options max-h-60 overflow-auto py-1" role="listbox">
                    @foreach($formattedOptions as $option)
                        @php
                            $optionValue = $option['value'] ?? '';
                            $optionLabel = $option['label'] ?? $optionValue;
                            $isSelected = in_array((string) $optionValue, array_map('strval', $selectedValues));
                        @endphp
                        <li
                            class="searchable-select-option px-3 py-2 text-sm cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-900 dark:text-gray-100 truncate {{ $isSelected ? 'bg-primary-50 dark:bg-primary-900/20' : '' }}"
                            data-value="{{ $optionValue }}"
                            role="option"
                            @if($isSelected) aria-selected="true" @endif
                        >
                            <div class="flex items-center justify-between">
                                <span class="searchable-select-option-label truncate">{{ $optionLabel }}</span>
                                <span class="searchable-select-option-check {{ $isSelected ? '' : 'hidden' }} ms-2 text-primary-600 shrink-0">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                            </div>
                        </li>
                    @endforeach
                </ul>

                {{-- No results message --}}
                <div class="searchable-select-no-results hidden px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                    No results found
                </div>
            </div>
        </div>

        {{-- Multiple selection tags display --}}
        @if($multiple && count($selectedValues) > 0)
            <div class="searchable-select-tags flex flex-wrap gap-1 mt-2">
                @foreach($formattedOptions as $option)
                    @php
                        $optionValue = $option['value'] ?? '';
                        $optionLabel = $option['label'] ?? $optionValue;
                        $isSelected = in_array((string) $optionValue, array_map('strval', $selectedValues));
                    @endphp
                    @if($isSelected)
                        <span class="searchable-select-tag inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full bg-primary-100 text-primary-800 dark:bg-primary-900/50 dark:text-primary-300" data-value="{{ $optionValue }}">
                            {{ $optionLabel }}
                            <button type="button" class="searchable-select-tag-remove hover:text-primary-600 dark:hover:text-primary-200">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                    @endif
                @endforeach
            </div>
        @endif
    @endif
</div>
