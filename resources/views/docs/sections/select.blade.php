@props(['framework' => 'vanilla', 'prefix' => 'a', 'documentation' => null, 'hasDemo' => true])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.docs :framework="$framework" section="filters-select" :documentation="$documentation" :hasDemo="$hasDemo">
    @include('filters::demo.partials._select', ['prefix' => $prefix])
</x-accelade::layouts.docs>
