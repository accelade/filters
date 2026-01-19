@props([
    'filter' => null,
    'name' => '',
    'constraints' => [],
    'value' => null,
])

@php
    $filterInstance = $filter;
    $filterName = $filterInstance?->getName() ?? $name;
    $filterConstraints = $filterInstance?->getConstraints() ?? $constraints;
    $filterValue = $filterInstance?->getValue() ?? $value ?? ['rules' => [], 'combinator' => 'and'];
    $filterLabel = $filterInstance?->getLabel() ?? 'Query Builder';

    $qbId = 'query-builder-' . uniqid();
    $constraintsJson = json_encode(array_map(fn($c) => $c->toArray(), $filterConstraints));
    $valueJson = json_encode($filterValue);
@endphp

<div
    data-accelade
    data-accelade-query-builder
    data-query-builder-id="{{ $qbId }}"
    data-query-builder-name="{{ $filterName }}"
    data-query-builder-constraints="{{ $constraintsJson }}"
    data-query-builder-value="{{ $valueJson }}"
    class="query-builder space-y-4"
    {{ $attributes }}
>
    {{-- Hidden input to store the value --}}
    <input type="hidden" name="{{ $filterName }}" data-query-builder-input>

    {{-- Rules Container --}}
    <div class="query-builder-rules space-y-3" data-query-builder-rules>
        {{-- Rules will be rendered by JavaScript --}}
    </div>

    {{-- Add Rule/Group Buttons --}}
    <div class="query-builder-actions flex items-center gap-2">
        <button
            type="button"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-md border border-dashed border-gray-300 text-gray-600 hover:border-indigo-400 hover:text-indigo-600 dark:border-gray-600 dark:text-gray-400 dark:hover:border-indigo-500 dark:hover:text-indigo-400 transition-colors"
            data-query-builder-add-rule
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Rule
        </button>

        <button
            type="button"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium rounded-md border border-dashed border-gray-300 text-gray-600 hover:border-purple-400 hover:text-purple-600 dark:border-gray-600 dark:text-gray-400 dark:hover:border-purple-500 dark:hover:text-purple-400 transition-colors"
            data-query-builder-add-group
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            Add Group
        </button>
    </div>

    {{-- Constraint Picker Template (hidden, used by JS) --}}
    <template data-query-builder-constraint-picker-template>
        <div class="constraint-picker absolute z-50 mt-1 w-64 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800">
            <div class="p-2">
                <input
                    type="text"
                    class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    placeholder="Search constraints..."
                    data-constraint-search
                >
            </div>
            <div class="max-h-64 overflow-y-auto" data-constraint-list>
                {{-- Constraints will be rendered by JS --}}
            </div>
        </div>
    </template>

    {{-- Rule Template (hidden, used by JS) --}}
    <template data-query-builder-rule-template>
        <div class="query-builder-rule flex items-start gap-2 p-3 rounded-lg border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/50" data-rule>
            {{-- Constraint Selector --}}
            <div class="relative" data-constraint-selector>
                <button
                    type="button"
                    class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
                    data-constraint-trigger
                >
                    <span data-constraint-label>Select field...</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>

            {{-- Operator Selector --}}
            <div class="relative" data-operator-selector>
                <select
                    class="px-3 py-2 text-sm rounded-md border border-gray-300 bg-white text-gray-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
                    data-operator-select
                >
                    {{-- Operators will be populated by JS based on selected constraint --}}
                </select>
            </div>

            {{-- Value Input --}}
            <div class="flex-1" data-value-container>
                <input
                    type="text"
                    class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    placeholder="Enter value..."
                    data-value-input
                >
            </div>

            {{-- Remove Button --}}
            <button
                type="button"
                class="p-2 text-gray-400 hover:text-red-500 dark:hover:text-red-400"
                data-remove-rule
                aria-label="Remove rule"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </template>

    {{-- Group Template (hidden, used by JS) --}}
    <template data-query-builder-group-template>
        <div class="query-builder-group p-4 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 space-y-3" data-group>
            {{-- Group Header --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Match</span>
                    <select
                        class="px-2 py-1 text-sm rounded border border-gray-300 bg-white text-gray-700 focus:border-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
                        data-combinator-select
                    >
                        <option value="and">All (AND)</option>
                        <option value="or">Any (OR)</option>
                    </select>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">of the following:</span>
                </div>

                <button
                    type="button"
                    class="p-1.5 text-gray-400 hover:text-red-500 dark:hover:text-red-400"
                    data-remove-group
                    aria-label="Remove group"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Group Rules --}}
            <div class="space-y-2" data-group-rules>
                {{-- Nested rules will go here --}}
            </div>

            {{-- Group Actions --}}
            <div class="flex items-center gap-2 pt-2">
                <button
                    type="button"
                    class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded border border-dashed border-gray-300 text-gray-500 hover:border-indigo-400 hover:text-indigo-600 dark:border-gray-600 dark:text-gray-400"
                    data-group-add-rule
                >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Rule
                </button>

                <button
                    type="button"
                    class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded border border-dashed border-gray-300 text-gray-500 hover:border-purple-400 hover:text-purple-600 dark:border-gray-600 dark:text-gray-400"
                    data-group-add-group
                >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Add Group
                </button>
            </div>
        </div>
    </template>
</div>
