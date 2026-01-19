<?php

declare(strict_types=1);

use Accelade\Filters\Components\NumberFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('test_items', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->decimal('price', 10, 2);
        $table->integer('quantity');
        $table->timestamps();
    });

    TestItemModel::create(['name' => 'Item A', 'price' => 10.00, 'quantity' => 5]);
    TestItemModel::create(['name' => 'Item B', 'price' => 25.50, 'quantity' => 10]);
    TestItemModel::create(['name' => 'Item C', 'price' => 50.00, 'quantity' => 3]);
    TestItemModel::create(['name' => 'Item D', 'price' => 100.00, 'quantity' => 1]);
});

afterEach(function () {
    Schema::dropIfExists('test_items');
});

it('can create a number filter', function () {
    $filter = NumberFilter::make('price');

    expect($filter)->toBeInstanceOf(NumberFilter::class);
    expect($filter->getName())->toBe('price');
});

it('can set min and max values', function () {
    $filter = NumberFilter::make('price')
        ->min(0)
        ->max(1000);

    expect($filter->getMin())->toBe(0.0);
    expect($filter->getMax())->toBe(1000.0);
});

it('can set step value', function () {
    $filter = NumberFilter::make('price')
        ->step(0.01);

    expect($filter->getStep())->toBe(0.01);
});

it('can set operator', function () {
    $filter = NumberFilter::make('price')
        ->operator('>=');

    $query = TestItemModel::query();
    $filter->apply($query, 50);

    expect($query->count())->toBe(2);
});

it('applies exact value filter by default', function () {
    $filter = NumberFilter::make('price');

    $query = TestItemModel::query();
    $filter->apply($query, 25.50);

    expect($query->count())->toBe(1);
    expect($query->first()->name)->toBe('Item B');
});

it('can filter greater than', function () {
    $filter = NumberFilter::make('price')
        ->greaterThan();

    $query = TestItemModel::query();
    $filter->apply($query, 25);

    expect($query->count())->toBe(3);
});

it('can filter less than', function () {
    $filter = NumberFilter::make('price')
        ->lessThan();

    $query = TestItemModel::query();
    $filter->apply($query, 50);

    expect($query->count())->toBe(2);
});

it('can filter greater than or equal', function () {
    $filter = NumberFilter::make('price')
        ->greaterThanOrEqual();

    $query = TestItemModel::query();
    $filter->apply($query, 50);

    expect($query->count())->toBe(2);
});

it('can filter less than or equal', function () {
    $filter = NumberFilter::make('price')
        ->lessThanOrEqual();

    $query = TestItemModel::query();
    $filter->apply($query, 25.50);

    expect($query->count())->toBe(2);
});

it('is not active when value is null', function () {
    $filter = NumberFilter::make('price');

    expect($filter->isActive())->toBeFalse();
});

it('is active when has value', function () {
    $filter = NumberFilter::make('price')
        ->setValue(50);

    expect($filter->isActive())->toBeTrue();
});

it('converts to array', function () {
    $filter = NumberFilter::make('price')
        ->label('Price')
        ->min(0)
        ->max(1000)
        ->step(0.01)
        ->setValue(50);

    $array = $filter->toArray();

    expect($array['type'])->toBe('NumberFilter');
    expect($array['name'])->toBe('price');
    expect($array['value'])->toBe(50);
});

it('returns correct view name', function () {
    $filter = NumberFilter::make('price');

    expect($filter->getView())->toBe('accelade::filters.number');
});

it('can use integer column', function () {
    $filter = NumberFilter::make('stock')
        ->column('quantity');

    $query = TestItemModel::query();
    $filter->apply($query, 10);

    expect($query->count())->toBe(1);
    expect($query->first()->name)->toBe('Item B');
});

/**
 * Test model for number filter tests.
 */
class TestItemModel extends Model
{
    protected $table = 'test_items';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'quantity' => 'integer',
        ];
    }
}
