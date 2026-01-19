<?php

declare(strict_types=1);

use Accelade\Filters\Components\SelectFilter;
use Accelade\Filters\Components\TextFilter;
use Accelade\Filters\FilterFactory;
use Illuminate\Support\Facades\View;

it('registers the config', function () {
    expect(config('filters.enabled'))->toBeTrue();
});

it('registers the facade', function () {
    expect(app('accelade.filter'))->toBeInstanceOf(FilterFactory::class);
});

it('can create a text filter', function () {
    $filter = TextFilter::make('search');

    expect($filter)->toBeInstanceOf(TextFilter::class);
    expect($filter->getName())->toBe('search');
});

it('can create a select filter', function () {
    $filter = SelectFilter::make('status')
        ->options(['active' => 'Active', 'inactive' => 'Inactive']);

    expect($filter)->toBeInstanceOf(SelectFilter::class);
    expect($filter->getOptions())->toBe(['active' => 'Active', 'inactive' => 'Inactive']);
});

it('loads filter views', function () {
    expect(View::exists('filters::filters.text'))->toBeTrue();
    expect(View::exists('filters::filters.select'))->toBeTrue();
    expect(View::exists('filters::filters.boolean'))->toBeTrue();
    expect(View::exists('filters::filters.date'))->toBeTrue();
});
