<?php

declare(strict_types=1);

use Accelade\Filters\Components\BooleanFilter;
use Accelade\Filters\Components\SelectFilter;
use Accelade\Filters\Components\TextFilter;
use Accelade\Filters\Enums\FilterLayout;
use Accelade\Filters\Enums\FilterWidth;
use Accelade\Filters\FilterPanel;

it('can create a filter panel', function () {
    $panel = FilterPanel::make();

    expect($panel)->toBeInstanceOf(FilterPanel::class);
});

it('can set filters on panel', function () {
    $panel = FilterPanel::make()
        ->filters([
            TextFilter::make('search'),
            SelectFilter::make('status'),
        ]);

    expect($panel->getFilters())->toHaveCount(2);
});

it('can add individual filter', function () {
    $panel = FilterPanel::make()
        ->addFilter(TextFilter::make('search'))
        ->addFilter(SelectFilter::make('status'));

    expect($panel->getFilters())->toHaveCount(2);
});

it('can set layout', function () {
    $panel = FilterPanel::make()
        ->layout(FilterLayout::Dropdown);

    expect($panel->getLayout())->toBe(FilterLayout::Dropdown);
});

it('can set width', function () {
    $panel = FilterPanel::make()
        ->width(FilterWidth::Large);

    expect($panel->getWidth())->toBe(FilterWidth::Large);
});

it('can set columns', function () {
    $panel = FilterPanel::make()
        ->columns(3);

    expect($panel->getColumns())->toBe(3);
});

it('enforces minimum of 1 column', function () {
    $panel = FilterPanel::make()
        ->columns(0);

    expect($panel->getColumns())->toBe(1);
});

it('can set max height', function () {
    $panel = FilterPanel::make()
        ->maxHeight('400px');

    expect($panel->getMaxHeight())->toBe('400px');
});

it('can enable session persistence', function () {
    $panel = FilterPanel::make()
        ->persistInSession(true, 'my-filters');

    expect($panel->isPersistingInSession())->toBeTrue();
});

it('can defer filters', function () {
    $panel = FilterPanel::make()
        ->deferFilters(true);

    expect($panel->isDeferred())->toBeTrue();
});

it('can show indicators', function () {
    $panel = FilterPanel::make()
        ->showIndicators(true);

    expect($panel->shouldShowIndicators())->toBeTrue();
});

it('can set collapsed state', function () {
    $panel = FilterPanel::make()
        ->collapsed(true);

    expect($panel->isCollapsed())->toBeTrue();
});

it('can set trigger label', function () {
    $panel = FilterPanel::make()
        ->triggerLabel('My Filters');

    expect($panel->getTriggerLabel())->toBe('My Filters');
});

it('can set apply label', function () {
    $panel = FilterPanel::make()
        ->applyLabel('Submit');

    expect($panel->getApplyLabel())->toBe('Submit');
});

it('can set reset label', function () {
    $panel = FilterPanel::make()
        ->resetLabel('Clear All');

    expect($panel->getResetLabel())->toBe('Clear All');
});

it('can get visible filters', function () {
    $panel = FilterPanel::make()
        ->filters([
            TextFilter::make('search'),
            SelectFilter::make('status')->hidden(),
        ]);

    expect($panel->getVisibleFilters())->toHaveCount(1);
});

it('can get active filters', function () {
    $panel = FilterPanel::make()
        ->filters([
            TextFilter::make('search')->setValue('test'),
            SelectFilter::make('status'),
        ]);

    expect($panel->getActiveFilters())->toHaveCount(1);
});

it('can set filter values', function () {
    $panel = FilterPanel::make()
        ->filters([
            TextFilter::make('search'),
            SelectFilter::make('status'),
        ])
        ->setFilterValues([
            'search' => 'test',
            'status' => 'active',
        ]);

    $values = $panel->getFilterValues();

    expect($values['search'])->toBe('test');
    expect($values['status'])->toBe('active');
});

it('can check for active filters', function () {
    $panel = FilterPanel::make()
        ->filters([
            TextFilter::make('search'),
            SelectFilter::make('status'),
        ]);

    expect($panel->hasActiveFilters())->toBeFalse();

    $panel->setFilterValues(['search' => 'test']);

    expect($panel->hasActiveFilters())->toBeTrue();
});

it('can reset filters', function () {
    $panel = FilterPanel::make()
        ->filters([
            TextFilter::make('search')->default('default'),
            SelectFilter::make('status'),
        ])
        ->setFilterValues(['search' => 'test', 'status' => 'active']);

    $panel->reset();

    $values = $panel->getFilterValues();
    expect($values['search'])->toBe('default');
    expect($values['status'])->toBeNull();
});

it('can get indicators', function () {
    $panel = FilterPanel::make()
        ->filters([
            TextFilter::make('search')->label('Search')->setValue('test'),
            BooleanFilter::make('active')->label('Active')->setValue(true),
        ]);

    $indicators = $panel->getIndicators();

    expect($indicators)->toHaveCount(2);
    expect($indicators[0]['name'])->toBe('search');
    expect($indicators[0]['formatted'])->toBe('test');
    expect($indicators[1]['formatted'])->toBe('Yes');
});

it('converts to array', function () {
    $panel = FilterPanel::make()
        ->layout(FilterLayout::Dropdown)
        ->columns(2)
        ->filters([
            TextFilter::make('search'),
        ]);

    $array = $panel->toArray();

    expect($array)->toHaveKey('layout');
    expect($array)->toHaveKey('columns');
    expect($array)->toHaveKey('filters');
    expect($array['layout'])->toBe('dropdown');
    expect($array['columns'])->toBe(2);
});

it('converts to json', function () {
    $panel = FilterPanel::make()
        ->layout(FilterLayout::Dropdown);

    $json = $panel->toJson();

    expect($json)->toBeString();
    expect(json_decode($json, true))->toBeArray();
});
