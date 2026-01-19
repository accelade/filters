<?php

declare(strict_types=1);

use Accelade\Filters\Components\DateFilter;
use Accelade\Filters\Components\DateRangeFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('test_orders', function (Blueprint $table) {
        $table->id();
        $table->string('order_number');
        $table->date('order_date');
        $table->timestamps();
    });

    TestOrderModel::create(['order_number' => 'ORD-001', 'order_date' => '2024-01-15']);
    TestOrderModel::create(['order_number' => 'ORD-002', 'order_date' => '2024-02-20']);
    TestOrderModel::create(['order_number' => 'ORD-003', 'order_date' => '2024-03-10']);
});

afterEach(function () {
    Schema::dropIfExists('test_orders');
});

// Date Filter Tests
it('can create a date filter', function () {
    $filter = DateFilter::make('order_date');

    expect($filter)->toBeInstanceOf(DateFilter::class);
    expect($filter->getName())->toBe('order_date');
});

it('can set date format', function () {
    $filter = DateFilter::make('order_date')
        ->format('d/m/Y');

    expect($filter->getFormat())->toBe('d/m/Y');
});

it('has default format', function () {
    $filter = DateFilter::make('order_date');

    expect($filter->getFormat())->toBe('Y-m-d');
});

it('can set min and max date', function () {
    $filter = DateFilter::make('order_date')
        ->minDate('2024-01-01')
        ->maxDate('2024-12-31');

    expect($filter->getMinDate())->toBe('2024-01-01');
    expect($filter->getMaxDate())->toBe('2024-12-31');
});

it('applies date filter to query', function () {
    $filter = DateFilter::make('order_date');

    $query = TestOrderModel::query();
    $filter->apply($query, '2024-02-20');

    expect($query->count())->toBe(1);
    expect($query->first()->order_number)->toBe('ORD-002');
});

it('converts to array', function () {
    $filter = DateFilter::make('order_date')
        ->label('Order Date')
        ->format('Y-m-d')
        ->setValue('2024-01-15');

    $array = $filter->toArray();

    expect($array['type'])->toBe('DateFilter');
    expect($array['name'])->toBe('order_date');
    expect($array['value'])->toBe('2024-01-15');
});

it('returns correct view name', function () {
    $filter = DateFilter::make('order_date');

    expect($filter->getView())->toBe('accelade::filters.date');
});

it('can filter from date', function () {
    $filter = DateFilter::make('order_date')
        ->from();

    $query = TestOrderModel::query();
    $filter->apply($query, '2024-02-01');

    expect($query->count())->toBe(2);
});

it('can filter until date', function () {
    $filter = DateFilter::make('order_date')
        ->until();

    $query = TestOrderModel::query();
    $filter->apply($query, '2024-02-28');

    expect($query->count())->toBe(2);
});

// Date Range Filter Tests
it('can create a date range filter', function () {
    $filter = DateRangeFilter::make('created_at');

    expect($filter)->toBeInstanceOf(DateRangeFilter::class);
});

it('can set from and to keys', function () {
    $filter = DateRangeFilter::make('created_at')
        ->keys('start_date', 'end_date');

    expect($filter->getFromKey())->toBe('start_date');
    expect($filter->getToKey())->toBe('end_date');
});

it('has default from and to keys', function () {
    $filter = DateRangeFilter::make('created_at');

    expect($filter->getFromKey())->toBe('from');
    expect($filter->getToKey())->toBe('to');
});

it('applies date range filter to query', function () {
    $filter = DateRangeFilter::make('order_date');

    $query = TestOrderModel::query();
    $filter->apply($query, [
        'from' => '2024-01-01',
        'to' => '2024-02-28',
    ]);

    expect($query->count())->toBe(2);
});

it('applies date range filter with only from date', function () {
    $filter = DateRangeFilter::make('order_date');

    $query = TestOrderModel::query();
    $filter->apply($query, [
        'from' => '2024-02-01',
    ]);

    expect($query->count())->toBe(2);
});

it('applies date range filter with only to date', function () {
    $filter = DateRangeFilter::make('order_date');

    $query = TestOrderModel::query();
    $filter->apply($query, [
        'to' => '2024-02-28',
    ]);

    expect($query->count())->toBe(2);
});

it('date range is active when has from value', function () {
    $filter = DateRangeFilter::make('order_date')
        ->setValue(['from' => '2024-01-01']);

    expect($filter->isActive())->toBeTrue();
});

it('date range is active when has to value', function () {
    $filter = DateRangeFilter::make('order_date')
        ->setValue(['to' => '2024-12-31']);

    expect($filter->isActive())->toBeTrue();
});

it('date range is not active when empty array', function () {
    $filter = DateRangeFilter::make('order_date')
        ->setValue([]);

    expect($filter->isActive())->toBeFalse();
});

it('returns correct view name for date range', function () {
    $filter = DateRangeFilter::make('order_date');

    expect($filter->getView())->toBe('accelade::filters.date-range');
});

/**
 * Test model for date filter tests.
 */
class TestOrderModel extends Model
{
    protected $table = 'test_orders';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
        ];
    }
}
