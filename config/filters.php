<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enable/Disable Filters
    |--------------------------------------------------------------------------
    |
    | Enable or disable the filters package.
    |
    */
    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Default Styles
    |--------------------------------------------------------------------------
    |
    | Default Tailwind CSS classes for filter components.
    |
    */
    'styles' => [
        'container' => 'flex flex-wrap gap-4 items-end',
        'filter' => 'min-w-[150px]',
        'label' => 'block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1',
        'input' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white',
        'select' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white',
        'button' => 'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500',
        'clear_button' => 'inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600',
    ],

    /*
    |--------------------------------------------------------------------------
    | Demo Routes
    |--------------------------------------------------------------------------
    |
    | Enable demo routes for testing filters.
    |
    */
    'demo' => [
        'enabled' => env('FILTERS_DEMO_ENABLED', env('APP_ENV') !== 'production'),
        'prefix' => 'filters-demo',
        'middleware' => ['web'],
    ],
];
