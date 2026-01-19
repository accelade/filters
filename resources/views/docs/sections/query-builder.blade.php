@props(['framework' => 'vanilla', 'prefix' => 'a', 'documentation' => null, 'hasDemo' => true])

@php
    app('accelade')->setFramework($framework);
@endphp

<x-accelade::layouts.docs :framework="$framework" section="filters-query-builder" :documentation="$documentation" :hasDemo="$hasDemo">
    @include('filters::demo.partials._query-builder', ['prefix' => $prefix])
</x-accelade::layouts.docs>
