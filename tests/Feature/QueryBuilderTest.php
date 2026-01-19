<?php

declare(strict_types=1);

use Accelade\Filters\QueryBuilder\Constraints\BooleanConstraint;
use Accelade\Filters\QueryBuilder\Constraints\DateConstraint;
use Accelade\Filters\QueryBuilder\Constraints\NumberConstraint;
use Accelade\Filters\QueryBuilder\Constraints\SelectConstraint;
use Accelade\Filters\QueryBuilder\Constraints\TextConstraint;
use Accelade\Filters\QueryBuilder\QueryBuilderFilter;

it('can create a query builder filter', function () {
    $filter = QueryBuilderFilter::make('query');

    expect($filter)->toBeInstanceOf(QueryBuilderFilter::class);
    expect($filter->getName())->toBe('query');
});

it('can set constraints', function () {
    $filter = QueryBuilderFilter::make('query')
        ->constraints([
            TextConstraint::make('name'),
            NumberConstraint::make('age'),
        ]);

    expect($filter->getConstraints())->toHaveCount(2);
});

it('can add individual constraint', function () {
    $filter = QueryBuilderFilter::make('query')
        ->addConstraint(TextConstraint::make('name'))
        ->addConstraint(NumberConstraint::make('age'));

    expect($filter->getConstraints())->toHaveCount(2);
});

it('has default value structure', function () {
    $filter = QueryBuilderFilter::make('query');

    expect($filter->getValue())->toBe(['rules' => [], 'combinator' => 'and']);
});

it('is not active when empty', function () {
    $filter = QueryBuilderFilter::make('query');

    expect($filter->isActive())->toBeFalse();
});

it('is active when has rules', function () {
    $filter = QueryBuilderFilter::make('query')
        ->setValue([
            'rules' => [
                ['constraint' => 'name', 'operator' => 'contains', 'value' => 'test'],
            ],
            'combinator' => 'and',
        ]);

    expect($filter->isActive())->toBeTrue();
});

it('converts to array with constraints', function () {
    $filter = QueryBuilderFilter::make('query')
        ->constraints([
            TextConstraint::make('name')->label('Name'),
        ]);

    $array = $filter->toArray();

    expect($array)->toHaveKey('constraints');
    expect($array['constraints'])->toHaveCount(1);
    expect($array['constraints'][0]['name'])->toBe('name');
    expect($array['constraints'][0]['label'])->toBe('Name');
});

// Text Constraint Tests
it('creates text constraint with operators', function () {
    $constraint = TextConstraint::make('name')
        ->label('Name');

    $array = $constraint->toArray();

    expect($array['type'])->toBe('text');
    expect($array['operators'])->not->toBeEmpty();
    expect(collect($array['operators'])->pluck('name'))->toContain('contains', 'equals', 'starts_with');
});

// Number Constraint Tests
it('creates number constraint with operators', function () {
    $constraint = NumberConstraint::make('age')
        ->label('Age');

    $array = $constraint->toArray();

    expect($array['type'])->toBe('number');
    expect(collect($array['operators'])->pluck('name'))->toContain('equals', 'greater_than', 'less_than', 'between');
});

// Boolean Constraint Tests
it('creates boolean constraint with operators', function () {
    $constraint = BooleanConstraint::make('active')
        ->label('Active');

    $array = $constraint->toArray();

    expect($array['type'])->toBe('boolean');
    expect(collect($array['operators'])->pluck('name'))->toContain('is_true', 'is_false');
});

// Date Constraint Tests
it('creates date constraint with operators', function () {
    $constraint = DateConstraint::make('created_at')
        ->label('Created Date');

    $array = $constraint->toArray();

    expect($array['type'])->toBe('date');
    expect(collect($array['operators'])->pluck('name'))->toContain('is', 'is_after', 'is_before', 'is_between');
});

// Select Constraint Tests
it('creates select constraint with options', function () {
    $constraint = SelectConstraint::make('status')
        ->label('Status')
        ->options([
            'active' => 'Active',
            'inactive' => 'Inactive',
        ]);

    $array = $constraint->toArray();

    expect($array['type'])->toBe('select');
    expect($array['settings']['options'])->not->toBeEmpty();
    expect(collect($array['operators'])->pluck('name'))->toContain('is', 'is_not');
});

it('can set constraint picker columns', function () {
    $filter = QueryBuilderFilter::make('query')
        ->constraintPickerColumns(3);

    $array = $filter->toArray();

    expect($array['constraintPickerColumns'])->toBe(3);
});
