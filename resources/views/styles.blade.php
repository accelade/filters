{{-- Filters CSS --}}
<style>
.accelade-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: flex-end;
}

.accelade-filter {
    min-width: 150px;
}

.accelade-filter-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.accelade-filter-input {
    display: block;
    width: 100%;
    border-radius: 0.375rem;
    border: 1px solid #d1d5db;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}

.accelade-filter-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.25);
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .accelade-filter-label {
        color: #d1d5db;
    }

    .accelade-filter-input {
        background-color: #374151;
        border-color: #4b5563;
        color: #fff;
    }
}
</style>
