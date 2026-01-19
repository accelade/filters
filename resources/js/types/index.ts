/**
 * Accelade Filters Type Definitions
 */

export interface AcceladeInstance {
    navigate?: (url: string, options?: NavigationOptions) => Promise<void>;
    router?: {
        navigate: (url: string, options?: NavigationOptions) => Promise<void>;
    };
    rehydrate?: RehydrateManager;
    modal?: {
        open: (name: string) => void;
        close: (name: string) => void;
    };
}

export interface RehydrateManager {
    get: (id: string) => RehydrateInstance | undefined;
    reload: (id: string) => void;
}

export interface RehydrateInstance {
    config?: {
        url?: string;
    };
    rehydrate: () => Promise<void>;
}

export interface NavigationOptions {
    preserveScroll?: boolean;
    scrollToTop?: boolean;
}

export interface SearchableSelectConfig {
    placeholder?: string;
    multiple?: boolean;
    searchable?: boolean;
}

export interface QueryBuilderValue {
    rules: QueryBuilderRule[];
    combinator: 'and' | 'or';
}

export interface QueryBuilderRule {
    constraint: string | null;
    operator: string | null;
    value: string;
    rules?: QueryBuilderRule[];
    combinator?: 'and' | 'or';
}

export interface QueryBuilderConstraint {
    name: string;
    label: string;
    operators: QueryBuilderOperator[];
}

export interface QueryBuilderOperator {
    name: string;
    label: string;
}

export interface SubmitOptions {
    preserveScroll?: boolean;
}

declare global {
    interface Window {
        AcceladeFilters: typeof import('../core/AcceladeFilters').AcceladeFilters;
        AcceladeFiltersInitialized?: boolean;
        FilterQueryBuilderManager: typeof import('../core/QueryBuilderManager').FilterQueryBuilderManager;
        Accelade?: AcceladeInstance;
    }
}
