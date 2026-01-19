<?php

declare(strict_types=1);

namespace Accelade\Filters;

use Accelade\Docs\DocsRegistry;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class FiltersServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/filters.php',
            'filters'
        );

        $this->app->singleton('accelade.filter', function () {
            return new FilterFactory;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load views under 'accelade' namespace to extend the main Accelade package
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'accelade');

        // Also load under 'filters' namespace for scripts/styles views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'filters');

        // Register anonymous Blade components
        $this->registerBladeComponents();

        // Register Blade directives
        $this->registerBladeDirectives();

        // Inject scripts/styles into Accelade
        $this->injectAcceladeAssets();

        // Register documentation sections
        $this->registerDocumentation();

        if ($this->app->runningInConsole()) {
            // Publish config
            $this->publishes([
                __DIR__.'/../config/filters.php' => config_path('filters.php'),
            ], 'filters-config');

            // Publish views
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/accelade'),
            ], 'filters-views');
        }
    }

    /**
     * Register anonymous Blade components for filters.
     */
    protected function registerBladeComponents(): void
    {
        // Register anonymous Blade components under filters namespace
        Blade::anonymousComponentPath(__DIR__.'/../resources/views/components', 'filters');
    }

    /**
     * Register Blade directives for filters.
     */
    protected function registerBladeDirectives(): void
    {
        Blade::directive('filtersScripts', function () {
            return "<?php echo view('filters::scripts')->render(); ?>";
        });

        Blade::directive('filtersStyles', function () {
            return "<?php echo view('filters::styles')->render(); ?>";
        });
    }

    /**
     * Inject Filters scripts and styles into Accelade.
     */
    protected function injectAcceladeAssets(): void
    {
        if (! $this->app->bound('accelade')) {
            return;
        }

        /** @var \Accelade\Accelade $accelade */
        $accelade = $this->app->make('accelade');

        $accelade->registerScript('filters', function () {
            return view('filters::scripts')->render();
        });

        $accelade->registerStyle('filters', function () {
            return view('filters::styles')->render();
        });
    }

    /**
     * Register documentation sections with Accelade's DocsRegistry.
     */
    protected function registerDocumentation(): void
    {
        if (! $this->app->bound('accelade.docs')) {
            return;
        }

        /** @var DocsRegistry $registry */
        $registry = $this->app->make('accelade.docs');

        $registry->registerPackage('filters', __DIR__.'/../docs');
        $registry->registerGroup('filters', 'Filters', '🎯', 50);

        // Register sub-groups within Filters
        $registry->registerSubgroup('filters', 'text-selection', '📝 Text & Selection', '', 10);
        $registry->registerSubgroup('filters', 'datetime', '📅 Date & Time', '', 20);
        $registry->registerSubgroup('filters', 'layout', '📐 Layout & Panel', '', 30);
        $registry->registerSubgroup('filters', 'advanced', '🔧 Advanced', '', 40);

        foreach ($this->getDocumentationSections() as $section) {
            $this->registerDocSection($registry, $section);
        }
    }

    /**
     * Register a single documentation section.
     *
     * @param  array<string, mixed>  $section
     */
    protected function registerDocSection(DocsRegistry $registry, array $section): void
    {
        $builder = $registry->section($section['id'])
            ->label($section['label'])
            ->icon($section['icon'])
            ->markdown($section['markdown'])
            ->description($section['description'])
            ->keywords($section['keywords'])
            ->package('filters')
            ->inGroup('filters');

        if (isset($section['subgroup'])) {
            $builder->inSubgroup($section['subgroup']);
        }

        if ($section['demo'] ?? true) {
            $builder->demo()->view($section['view']);
        }

        $builder->register();
    }

    /**
     * Get all documentation section definitions.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getDocumentationSections(): array
    {
        return [
            ...$this->getOverviewDocSections(),
            ...$this->getTextSelectionDocSections(),
            ...$this->getDateTimeDocSections(),
            ...$this->getLayoutDocSections(),
            ...$this->getAdvancedDocSections(),
        ];
    }

    /**
     * Get overview documentation sections.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getOverviewDocSections(): array
    {
        return [
            // Main entry - no subgroup
            ['id' => 'filters-overview', 'label' => 'Overview', 'icon' => '🎯', 'markdown' => 'overview.md', 'description' => 'Filter components for tables and grids', 'keywords' => ['filter', 'search', 'query'], 'view' => 'filters::docs.sections.overview'],
        ];
    }

    /**
     * Get text & selection documentation sections.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getTextSelectionDocSections(): array
    {
        return [
            ['id' => 'filters-text', 'label' => 'Text Filter', 'icon' => '📝', 'markdown' => 'text.md', 'description' => 'Text/search filter component', 'keywords' => ['text', 'search', 'input'], 'view' => 'filters::docs.sections.text', 'subgroup' => 'text-selection'],
            ['id' => 'filters-select', 'label' => 'Select Filter', 'icon' => '📋', 'markdown' => 'select.md', 'description' => 'Dropdown/select filter component', 'keywords' => ['select', 'dropdown', 'options'], 'view' => 'filters::docs.sections.select', 'subgroup' => 'text-selection'],
            ['id' => 'filters-boolean', 'label' => 'Boolean Filter', 'icon' => '✓', 'markdown' => 'boolean.md', 'description' => 'Boolean/toggle filter component', 'keywords' => ['boolean', 'toggle', 'yes', 'no'], 'view' => 'filters::docs.sections.boolean', 'subgroup' => 'text-selection'],
            ['id' => 'filters-number', 'label' => 'Number Filter', 'icon' => '🔢', 'markdown' => 'number.md', 'description' => 'Number filter component', 'keywords' => ['number', 'numeric', 'range'], 'view' => 'filters::docs.sections.number', 'subgroup' => 'text-selection'],
        ];
    }

    /**
     * Get date & time documentation sections.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getDateTimeDocSections(): array
    {
        return [
            ['id' => 'filters-date', 'label' => 'Date Filter', 'icon' => '📅', 'markdown' => 'date.md', 'description' => 'Date filter component', 'keywords' => ['date', 'calendar', 'picker'], 'view' => 'filters::docs.sections.date', 'subgroup' => 'datetime'],
            ['id' => 'filters-date-range', 'label' => 'Date Range Filter', 'icon' => '📆', 'markdown' => 'date-range.md', 'description' => 'Date range filter component', 'keywords' => ['date', 'range', 'from', 'to'], 'view' => 'filters::docs.sections.date-range', 'subgroup' => 'datetime'],
        ];
    }

    /**
     * Get layout & panel documentation sections.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getLayoutDocSections(): array
    {
        return [
            ['id' => 'filters-layout', 'label' => 'Filter Layouts', 'icon' => '📐', 'markdown' => 'layout.md', 'description' => 'Different layout options for filter panels', 'keywords' => ['layout', 'dropdown', 'modal', 'sidebar', 'inline', 'collapsible'], 'view' => 'filters::docs.sections.layout', 'subgroup' => 'layout'],
            ['id' => 'filters-panel', 'label' => 'Filter Panel', 'icon' => '🎛️', 'markdown' => 'panel.md', 'description' => 'Container for managing filter collections', 'keywords' => ['panel', 'container', 'group', 'indicators'], 'view' => 'filters::docs.sections.panel', 'subgroup' => 'layout'],
        ];
    }

    /**
     * Get advanced documentation sections.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getAdvancedDocSections(): array
    {
        return [
            ['id' => 'filters-query-builder', 'label' => 'Query Builder', 'icon' => '🔧', 'markdown' => 'query-builder.md', 'description' => 'Complex nested filtering with AND/OR grouping', 'keywords' => ['query', 'builder', 'advanced', 'nested', 'and', 'or', 'constraint'], 'view' => 'filters::docs.sections.query-builder', 'subgroup' => 'advanced'],
        ];
    }
}
