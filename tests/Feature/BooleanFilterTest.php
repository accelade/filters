<?php

declare(strict_types=1);

use Accelade\Filters\Components\BooleanFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('test_products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->boolean('active')->default(true);
        $table->boolean('featured')->default(false);
        $table->timestamps();
    });

    TestProductModel::create(['name' => 'Product A', 'active' => true, 'featured' => true]);
    TestProductModel::create(['name' => 'Product B', 'active' => true, 'featured' => false]);
    TestProductModel::create(['name' => 'Product C', 'active' => false, 'featured' => false]);
});

afterEach(function () {
    Schema::dropIfExists('test_products');
});

it('can create a boolean filter', function () {
    $filter = BooleanFilter::make('active');

    expect($filter)->toBeInstanceOf(BooleanFilter::class);
    expect($filter->getName())->toBe('active');
});

it('can set a label', function () {
    $filter = BooleanFilter::make('active')
        ->label('Is Active');

    expect($filter->getLabel())->toBe('Is Active');
});

it('can set true and false labels', function () {
    $filter = BooleanFilter::make('active')
        ->trueLabel('Yes')
        ->falseLabel('No');

    expect($filter->getTrueLabel())->toBe('Yes');
    expect($filter->getFalseLabel())->toBe('No');
});

it('has default true and false labels', function () {
    $filter = BooleanFilter::make('active');

    expect($filter->getTrueLabel())->toBe('Yes');
    expect($filter->getFalseLabel())->toBe('No');
});

it('is not active when value is null', function () {
    $filter = BooleanFilter::make('active');

    expect($filter->isActive())->toBeFalse();
});

it('is active when value is true', function () {
    $filter = BooleanFilter::make('active')
        ->setValue(true);

    expect($filter->isActive())->toBeTrue();
});

it('is active when value is false', function () {
    $filter = BooleanFilter::make('active')
        ->setValue(false);

    expect($filter->isActive())->toBeTrue();
});

it('applies filter for true value', function () {
    $filter = BooleanFilter::make('active');

    $query = TestProductModel::query();
    $filter->apply($query, true);

    expect($query->count())->toBe(2);
});

it('applies filter for false value', function () {
    $filter = BooleanFilter::make('active');

    $query = TestProductModel::query();
    $filter->apply($query, false);

    expect($query->count())->toBe(1);
    expect($query->first()->name)->toBe('Product C');
});

it('can filter on different column', function () {
    $filter = BooleanFilter::make('is_featured')
        ->column('featured');

    $query = TestProductModel::query();
    $filter->apply($query, true);

    expect($query->count())->toBe(1);
    expect($query->first()->name)->toBe('Product A');
});

it('converts to array', function () {
    $filter = BooleanFilter::make('active')
        ->label('Is Active')
        ->trueLabel('Enabled')
        ->falseLabel('Disabled')
        ->setValue(true);

    $array = $filter->toArray();

    expect($array['type'])->toBe('BooleanFilter');
    expect($array['name'])->toBe('active');
    expect($array['label'])->toBe('Is Active');
    expect($array['value'])->toBeTrue();
    expect($array['active'])->toBeTrue();
});

it('returns correct view name', function () {
    $filter = BooleanFilter::make('active');

    expect($filter->getView())->toBe('accelade::filters.boolean');
});

it('can be nullable', function () {
    $filter = BooleanFilter::make('active')
        ->nullable();

    expect($filter->isNullable())->toBeTrue();
});

/**
 * Test model for boolean filter tests.
 */
class TestProductModel extends Model
{
    protected $table = 'test_products';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'featured' => 'boolean',
        ];
    }
}
