<?php

declare(strict_types=1);

use Accelade\Filters\Components\SelectFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('test_tasks', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('status');
        $table->string('priority');
        $table->timestamps();
    });

    TestTaskModel::create(['title' => 'Task 1', 'status' => 'pending', 'priority' => 'high']);
    TestTaskModel::create(['title' => 'Task 2', 'status' => 'in_progress', 'priority' => 'medium']);
    TestTaskModel::create(['title' => 'Task 3', 'status' => 'completed', 'priority' => 'low']);
    TestTaskModel::create(['title' => 'Task 4', 'status' => 'pending', 'priority' => 'high']);
});

afterEach(function () {
    Schema::dropIfExists('test_tasks');
});

it('can create a select filter', function () {
    $filter = SelectFilter::make('status');

    expect($filter)->toBeInstanceOf(SelectFilter::class);
    expect($filter->getName())->toBe('status');
});

it('can set options array', function () {
    $filter = SelectFilter::make('status')
        ->options([
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
        ]);

    expect($filter->getOptions())->toBe([
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
    ]);
});

it('can set options from closure', function () {
    $filter = SelectFilter::make('status')
        ->options(fn () => [
            'pending' => 'Pending',
            'completed' => 'Completed',
        ]);

    expect($filter->getOptions())->toBe([
        'pending' => 'Pending',
        'completed' => 'Completed',
    ]);
});

it('can enable multiple selection', function () {
    $filter = SelectFilter::make('status')
        ->multiple();

    expect($filter->isMultiple())->toBeTrue();
});

it('can enable search', function () {
    $filter = SelectFilter::make('status')
        ->searchable();

    expect($filter->isSearchable())->toBeTrue();
});

it('applies single value filter', function () {
    $filter = SelectFilter::make('status')
        ->options([
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
        ]);

    $query = TestTaskModel::query();
    $filter->apply($query, 'pending');

    expect($query->count())->toBe(2);
});

it('applies multiple values filter', function () {
    $filter = SelectFilter::make('status')
        ->multiple()
        ->options([
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
        ]);

    $query = TestTaskModel::query();
    $filter->apply($query, ['pending', 'completed']);

    expect($query->count())->toBe(3);
});

it('is not active when value is empty', function () {
    $filter = SelectFilter::make('status');

    expect($filter->isActive())->toBeFalse();
});

it('is not active when value is empty array', function () {
    $filter = SelectFilter::make('status')
        ->multiple()
        ->setValue([]);

    expect($filter->isActive())->toBeFalse();
});

it('is active when has single value', function () {
    $filter = SelectFilter::make('status')
        ->setValue('pending');

    expect($filter->isActive())->toBeTrue();
});

it('is active when has multiple values', function () {
    $filter = SelectFilter::make('status')
        ->multiple()
        ->setValue(['pending', 'completed']);

    expect($filter->isActive())->toBeTrue();
});

it('can set native select mode', function () {
    $filter = SelectFilter::make('status')
        ->native();

    expect($filter->isNative())->toBeTrue();
});

it('defaults to native mode', function () {
    $filter = SelectFilter::make('status');

    expect($filter->isNative())->toBeTrue();
});

it('can disable native mode', function () {
    $filter = SelectFilter::make('status')
        ->native(false);

    expect($filter->isNative())->toBeFalse();
});

it('converts to array', function () {
    $filter = SelectFilter::make('status')
        ->label('Status')
        ->options([
            'pending' => 'Pending',
            'completed' => 'Completed',
        ])
        ->multiple()
        ->searchable()
        ->setValue(['pending']);

    $array = $filter->toArray();

    expect($array['type'])->toBe('SelectFilter');
    expect($array['name'])->toBe('status');
    expect($array['value'])->toBe(['pending']);
    expect($array['active'])->toBeTrue();
});

it('returns correct view name', function () {
    $filter = SelectFilter::make('status');

    expect($filter->getView())->toBe('accelade::filters.select');
});

it('can filter on custom column', function () {
    $filter = SelectFilter::make('task_priority')
        ->column('priority')
        ->options([
            'high' => 'High',
            'medium' => 'Medium',
            'low' => 'Low',
        ]);

    $query = TestTaskModel::query();
    $filter->apply($query, 'high');

    expect($query->count())->toBe(2);
});

it('can set placeholder', function () {
    $filter = SelectFilter::make('status')
        ->placeholder('Select a status...');

    expect($filter->getPlaceholder())->toBe('Select a status...');
});

/**
 * Test model for select filter tests.
 */
class TestTaskModel extends Model
{
    protected $table = 'test_tasks';

    protected $guarded = [];
}
