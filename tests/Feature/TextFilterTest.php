<?php

declare(strict_types=1);

use Accelade\Filters\Components\TextFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('test_users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email');
        $table->timestamps();
    });

    TestUserModel::create(['name' => 'John Doe', 'email' => 'john@example.com']);
    TestUserModel::create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);
    TestUserModel::create(['name' => 'Bob Wilson', 'email' => 'bob@example.com']);
});

afterEach(function () {
    Schema::dropIfExists('test_users');
});

it('can create a text filter', function () {
    $filter = TextFilter::make('search');

    expect($filter)->toBeInstanceOf(TextFilter::class);
    expect($filter->getName())->toBe('search');
});

it('can set a label', function () {
    $filter = TextFilter::make('search')
        ->label('Search Users');

    expect($filter->getLabel())->toBe('Search Users');
});

it('generates label from name if not set', function () {
    $filter = TextFilter::make('search_term');

    expect($filter->getLabel())->toBe('Search term');
});

it('can set a placeholder', function () {
    $filter = TextFilter::make('search')
        ->placeholder('Type to search...');

    expect($filter->getPlaceholder())->toBe('Type to search...');
});

it('can set a default value', function () {
    $filter = TextFilter::make('search')
        ->default('default search');

    expect($filter->getDefault())->toBe('default search');
    expect($filter->getValue())->toBe('default search');
});

it('can set and get value', function () {
    $filter = TextFilter::make('search')
        ->setValue('test value');

    expect($filter->getValue())->toBe('test value');
});

it('is not active when value is empty', function () {
    $filter = TextFilter::make('search');

    expect($filter->isActive())->toBeFalse();
});

it('is active when has value', function () {
    $filter = TextFilter::make('search')
        ->setValue('test');

    expect($filter->isActive())->toBeTrue();
});

it('can be hidden', function () {
    $filter = TextFilter::make('search')
        ->hidden();

    expect($filter->isHidden())->toBeTrue();
});

it('can set custom column', function () {
    $filter = TextFilter::make('search')
        ->column('full_name');

    expect($filter->getColumn())->toBe('full_name');
});

it('uses name as column if not set', function () {
    $filter = TextFilter::make('search');

    expect($filter->getColumn())->toBe('search');
});

it('can add extra attributes', function () {
    $filter = TextFilter::make('search')
        ->extraAttributes(['class' => 'custom-class', 'data-test' => 'value']);

    expect($filter->getExtraAttributes())->toBe([
        'class' => 'custom-class',
        'data-test' => 'value',
    ]);
});

it('applies filter to query using contains', function () {
    $filter = TextFilter::make('name')
        ->setValue('john');

    $query = TestUserModel::query();
    $filter->apply($query, 'john');

    expect($query->count())->toBe(1);
    expect($query->first()->name)->toBe('John Doe');
});

it('can use exact match', function () {
    $filter = TextFilter::make('name')
        ->exact();

    expect($filter->getOperator())->toBe('=');
});

it('can use starts with match', function () {
    $filter = TextFilter::make('name')
        ->startsWith();

    expect($filter->getOperator())->toBe('starts_with');
});

it('can use ends with match', function () {
    $filter = TextFilter::make('name')
        ->endsWith();

    expect($filter->getOperator())->toBe('ends_with');
});

it('converts to array', function () {
    $filter = TextFilter::make('search')
        ->label('Search')
        ->placeholder('Type here...')
        ->setValue('test');

    $array = $filter->toArray();

    expect($array)->toHaveKey('type');
    expect($array)->toHaveKey('name');
    expect($array)->toHaveKey('label');
    expect($array)->toHaveKey('value');
    expect($array)->toHaveKey('active');
    expect($array['name'])->toBe('search');
    expect($array['label'])->toBe('Search');
    expect($array['value'])->toBe('test');
    expect($array['active'])->toBeTrue();
});

it('returns correct view name', function () {
    $filter = TextFilter::make('search');

    expect($filter->getView())->toBe('accelade::filters.text');
});

it('supports conditionable when', function () {
    $filter = TextFilter::make('search')
        ->when(true, fn (TextFilter $f) => $f->label('Conditional Label'));

    expect($filter->getLabel())->toBe('Conditional Label');
});

it('supports conditionable unless', function () {
    $filter = TextFilter::make('search')
        ->unless(false, fn (TextFilter $f) => $f->placeholder('Conditional Placeholder'));

    expect($filter->getPlaceholder())->toBe('Conditional Placeholder');
});

it('supports closure for label', function () {
    $filter = TextFilter::make('search')
        ->label(fn () => 'Dynamic Label');

    expect($filter->getLabel())->toBe('Dynamic Label');
});

it('supports closure for placeholder', function () {
    $filter = TextFilter::make('search')
        ->placeholder(fn () => 'Dynamic Placeholder');

    expect($filter->getPlaceholder())->toBe('Dynamic Placeholder');
});

/**
 * Test model for filter tests.
 */
class TestUserModel extends Model
{
    protected $table = 'test_users';

    protected $guarded = [];
}
