/**
 * Accelade Filters
 *
 * Filter components for Laravel tables and grids.
 * Provides text, select, date, boolean and custom filters with
 * SPA navigation and partial rehydration support.
 */

import { AcceladeFilters } from './core/AcceladeFilters';
import { FilterQueryBuilderManager } from './core/QueryBuilderManager';

// Export types
export type * from './types';

// Export core modules
export { AcceladeFilters } from './core/AcceladeFilters';
export { FilterQueryBuilderManager } from './core/QueryBuilderManager';

// Prevent duplicate initialization during SPA navigation
if (typeof window.AcceladeFiltersInitialized === 'undefined') {
    window.AcceladeFiltersInitialized = true;

    // Assign to window for global access
    window.AcceladeFilters = AcceladeFilters;
    window.FilterQueryBuilderManager = FilterQueryBuilderManager;
}

// Initialize on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
    window.AcceladeFilters.init();
});

// Re-initialize after SPA navigation
document.addEventListener('accelade:navigated', () => {
    window.AcceladeFilters.init();
});

// Re-initialize after component rehydration (partial update)
document.addEventListener('accelade:rehydrate', () => {
    window.AcceladeFilters.init();
});

// Default export
export default AcceladeFilters;
