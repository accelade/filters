<?php

declare(strict_types=1);

namespace Accelade\Filters\Tests;

use Accelade\AcceladeServiceProvider;
use Accelade\Filters\FiltersServiceProvider;
use Accelade\QueryBuilder\QueryBuilderServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            AcceladeServiceProvider::class,
            QueryBuilderServiceProvider::class,
            FiltersServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('filters.enabled', true);
        $app['config']->set('filters.demo.enabled', true);
    }
}
