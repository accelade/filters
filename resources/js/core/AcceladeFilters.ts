/**
 * Accelade Filters - Core Filter Manager
 *
 * Handles filter panel interactions, form submissions, searchable selects,
 * and integration with Accelade's SPA navigation and rehydration.
 */

import type { SubmitOptions, SearchableSelectConfig } from '../types';

/**
 * Show loading state on a button
 */
function setButtonLoading(button: HTMLButtonElement | null, isLoading: boolean): void {
    if (!button) return;

    if (isLoading) {
        button.setAttribute('data-loading', 'true');
        button.disabled = true;

        // Store original content
        if (!button.hasAttribute('data-original-content')) {
            button.setAttribute('data-original-content', button.innerHTML);
        }

        // Add spinner
        const originalText = button.textContent?.trim() ?? '';
        button.innerHTML = `<span class="filter-spinner inline-flex items-center gap-2"><svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>${originalText}</span></span>`;
    } else {
        button.removeAttribute('data-loading');
        button.disabled = false;

        // Restore original content
        const originalContent = button.getAttribute('data-original-content');
        if (originalContent) {
            button.innerHTML = originalContent;
            button.removeAttribute('data-original-content');
        }
    }
}

/**
 * Submit form via rehydration (partial update) or SPA navigation.
 * Preserves scroll position and only updates the target component.
 */
function submitForm(form: HTMLFormElement | null, options: SubmitOptions = {}): void {
    if (!form) return;

    const preserveScroll = options.preserveScroll !== false;

    // Find and set loading state on submit button
    const submitBtn = form.querySelector<HTMLButtonElement>('[data-filter-apply], button[type="submit"]');
    setButtonLoading(submitBtn, true);

    // Collect form data
    const formData = new FormData(form);
    const params = new URLSearchParams();

    for (const [key, value] of formData.entries()) {
        if (value !== '' && value !== null) {
            params.append(key, value as string);
        }
    }

    // Build URL with query params
    const baseUrl = form.action || window.location.pathname;
    const url = params.toString() ? `${baseUrl}?${params.toString()}` : baseUrl;

    // Check if form has a specific target component to update
    const targetId = form.getAttribute('data-filter-target');

    if (targetId && window.Accelade?.rehydrate) {
        // Find the rehydrate element and update its URL before triggering
        const rehydrateEl = document.querySelector(`[data-rehydrate-id="${targetId}"]`);
        if (rehydrateEl) {
            rehydrateEl.setAttribute('data-rehydrate-url', url);
        }

        // Update the URL in the browser without navigation
        window.history.pushState({}, '', url);

        // Trigger rehydration of just the target component
        const instance = window.Accelade.rehydrate.get(targetId);
        if (instance) {
            if (instance.config) {
                instance.config.url = url;
            }
            instance.rehydrate()
                .then(() => setButtonLoading(submitBtn, false))
                .catch(() => setButtonLoading(submitBtn, false));
        } else {
            window.Accelade.rehydrate.reload(targetId);
            setTimeout(() => setButtonLoading(submitBtn, false), 1000);
        }
        return;
    }

    // Navigation options
    const navOptions = {
        preserveScroll: preserveScroll,
        scrollToTop: false,
    };

    // Use Accelade SPA navigation if available
    if (window.Accelade?.navigate) {
        window.Accelade.navigate(url, navOptions)
            .then(() => setButtonLoading(submitBtn, false))
            .catch(() => setButtonLoading(submitBtn, false));
    } else if (window.Accelade?.router?.navigate) {
        window.Accelade.router.navigate(url, navOptions)
            .then(() => setButtonLoading(submitBtn, false))
            .catch(() => setButtonLoading(submitBtn, false));
    } else {
        form.submit();
    }
}

/**
 * Initialize filter panel interactions.
 */
function initFilterPanels(): void {
    // Intercept form submissions for SPA navigation
    document.querySelectorAll<HTMLFormElement>('form').forEach((form) => {
        if (form.hasAttribute('data-filter-form-init')) return;

        if (form.querySelector('[data-accelade-filter-panel]') || form.querySelector('[data-filter-form]')) {
            form.setAttribute('data-filter-form-init', 'true');
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                submitForm(form);
            });
        }
    });

    // Intercept pagination links for SPA navigation or rehydration
    document.querySelectorAll<HTMLAnchorElement>('nav[role="navigation"] a, .pagination a').forEach((link) => {
        if (link.hasAttribute('data-accelade-link') ||
            link.hasAttribute('a-link') ||
            link.hasAttribute('data-spa-link') ||
            link.hasAttribute('data-spa-init')) {
            return;
        }

        if (link.getAttribute('aria-disabled') === 'true' || link.classList.contains('disabled')) {
            return;
        }

        link.setAttribute('data-spa-init', 'true');
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const href = link.getAttribute('href');
            if (!href) return;

            const rehydrateEl = link.closest<HTMLElement>('[data-accelade-rehydrate]');
            if (rehydrateEl && window.Accelade?.rehydrate) {
                const rehydrateId = rehydrateEl.getAttribute('data-rehydrate-id');
                if (rehydrateId) {
                    rehydrateEl.setAttribute('data-rehydrate-url', href);
                    window.history.pushState({}, '', href);

                    const instance = window.Accelade.rehydrate.get(rehydrateId);
                    if (instance?.config) {
                        instance.config.url = href;
                    }
                    window.Accelade.rehydrate.reload(rehydrateId);
                    return;
                }
            }

            const navOptions = { preserveScroll: true, scrollToTop: false };
            if (window.Accelade?.navigate) {
                window.Accelade.navigate(href, navOptions);
            } else if (window.Accelade?.router?.navigate) {
                window.Accelade.router.navigate(href, navOptions);
            } else {
                window.location.href = href;
            }
        });
    });

    // Dropdown triggers
    document.querySelectorAll<HTMLElement>('[data-filter-dropdown]').forEach((dropdown) => {
        const trigger = dropdown.querySelector<HTMLElement>('[data-filter-trigger]');
        const content = dropdown.querySelector<HTMLElement>('[data-filter-content]');
        const chevron = dropdown.querySelector<HTMLElement>('[data-filter-chevron]');

        if (trigger && content) {
            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                const isOpen = !content.classList.contains('hidden');
                if (isOpen) {
                    content.classList.add('hidden');
                    trigger.setAttribute('aria-expanded', 'false');
                    chevron?.classList.remove('rotate-180');
                } else {
                    content.classList.remove('hidden');
                    trigger.setAttribute('aria-expanded', 'true');
                    chevron?.classList.add('rotate-180');
                }
            });

            document.addEventListener('click', (e) => {
                if (!dropdown.contains(e.target as Node)) {
                    content.classList.add('hidden');
                    trigger.setAttribute('aria-expanded', 'false');
                    chevron?.classList.remove('rotate-180');
                }
            });
        }
    });

    // Collapsible triggers
    document.querySelectorAll<HTMLElement>('[data-filter-collapse-toggle]').forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const container = toggle.closest<HTMLElement>('[data-filter-collapsible]');
            const formContainer = container?.querySelector<HTMLElement>('[data-filter-form-container]');
            const chevron = toggle.querySelector<HTMLElement>('[data-filter-chevron]');
            const isExpanded = toggle.getAttribute('aria-expanded') === 'true';

            if (formContainer) {
                if (isExpanded) {
                    formContainer.classList.add('hidden');
                    toggle.setAttribute('aria-expanded', 'false');
                    chevron?.classList.remove('rotate-180');
                } else {
                    formContainer.classList.remove('hidden');
                    toggle.setAttribute('aria-expanded', 'true');
                    chevron?.classList.add('rotate-180');
                }
            }
        });
    });

    // Reset buttons
    document.querySelectorAll<HTMLButtonElement>('[data-filter-reset]').forEach((btn) => {
        if (btn.hasAttribute('data-filter-reset-init')) return;
        btn.setAttribute('data-filter-reset-init', 'true');

        btn.addEventListener('click', () => {
            const filterForm = btn.closest<HTMLElement>('[data-filter-form]');
            const form = btn.closest<HTMLFormElement>('form');
            if (filterForm) {
                resetFilterForm(filterForm);
            }
            if (form) {
                submitForm(form);
            }
        });
    });

    // Reset all indicators
    document.querySelectorAll<HTMLButtonElement>('[data-filter-reset-all]').forEach((btn) => {
        if (btn.hasAttribute('data-filter-reset-all-init')) return;
        btn.setAttribute('data-filter-reset-all-init', 'true');

        btn.addEventListener('click', () => {
            const panel = btn.closest<HTMLElement>('[data-accelade-filter-panel]');
            const filterForm = panel?.querySelector<HTMLElement>('[data-filter-form]');
            const form = panel?.closest<HTMLFormElement>('form');
            if (filterForm) {
                resetFilterForm(filterForm);
            }
            if (form) {
                submitForm(form);
            }
        });
    });

    // Individual indicator remove buttons
    document.querySelectorAll<HTMLButtonElement>('[data-filter-remove]').forEach((btn) => {
        if (btn.hasAttribute('data-filter-remove-init')) return;
        btn.setAttribute('data-filter-remove-init', 'true');

        btn.addEventListener('click', () => {
            const filterName = btn.getAttribute('data-filter-remove');
            const panel = btn.closest<HTMLElement>('[data-accelade-filter-panel]');
            const filterForm = panel?.querySelector<HTMLElement>('[data-filter-form]');
            const form = panel?.closest<HTMLFormElement>('form');
            const field = filterForm?.querySelector<HTMLElement>(`[data-filter-field="${filterName}"]`);

            if (field) {
                resetFieldInputs(field);
                resetSearchableSelect(field);
            }
            if (form) {
                submitForm(form);
            }
        });
    });

    // Modal triggers
    document.querySelectorAll<HTMLButtonElement>('[data-filter-modal-trigger]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const panel = btn.closest<HTMLElement>('[data-accelade-filter-panel]');
            const panelId = panel?.getAttribute('data-filter-panel-id');
            const modalName = `filter-modal-${panelId}`;
            window.Accelade?.modal?.open(modalName);
        });
    });
}

/**
 * Reset all inputs in a filter form element.
 */
function resetFilterForm(filterForm: HTMLElement): void {
    filterForm.querySelectorAll<HTMLInputElement | HTMLSelectElement>('input, select').forEach((input) => {
        if (input instanceof HTMLInputElement && (input.type === 'checkbox' || input.type === 'radio')) {
            input.checked = false;
        } else {
            input.value = '';
        }
    });

    // Update searchable selects display
    filterForm.querySelectorAll<HTMLElement>('.searchable-select-display').forEach((display) => {
        const wrapper = display.closest<HTMLElement>('[data-searchable-select]');
        const config: SearchableSelectConfig = wrapper
            ? JSON.parse(wrapper.getAttribute('data-searchable-select') || '{}')
            : {};
        display.innerHTML = `<span class="text-gray-400 dark:text-gray-500">${config.placeholder || 'Select...'}</span>`;
    });

    filterForm.querySelectorAll<HTMLElement>('.searchable-select-option').forEach((opt) => {
        opt.classList.remove('bg-primary-50', 'dark:bg-primary-900/20');
        opt.setAttribute('aria-selected', 'false');
        const check = opt.querySelector<HTMLElement>('.searchable-select-option-check');
        check?.classList.add('hidden');
    });
}

/**
 * Reset inputs in a specific field element.
 */
function resetFieldInputs(field: HTMLElement): void {
    field.querySelectorAll<HTMLInputElement | HTMLSelectElement>('input, select').forEach((input) => {
        if (input instanceof HTMLInputElement && (input.type === 'checkbox' || input.type === 'radio')) {
            input.checked = false;
        } else {
            input.value = '';
        }
    });
}

/**
 * Reset searchable select in a field element.
 */
function resetSearchableSelect(field: HTMLElement): void {
    const wrapper = field.querySelector<HTMLElement>('[data-searchable-select]');
    if (!wrapper) return;

    const config: SearchableSelectConfig = JSON.parse(wrapper.getAttribute('data-searchable-select') || '{}');
    const display = wrapper.querySelector<HTMLElement>('.searchable-select-display');

    if (display) {
        display.innerHTML = `<span class="text-gray-400 dark:text-gray-500">${config.placeholder || 'Select...'}</span>`;
    }

    wrapper.querySelectorAll<HTMLElement>('.searchable-select-option').forEach((opt) => {
        opt.classList.remove('bg-primary-50', 'dark:bg-primary-900/20');
        opt.setAttribute('aria-selected', 'false');
        const check = opt.querySelector<HTMLElement>('.searchable-select-option-check');
        check?.classList.add('hidden');
    });
}

/**
 * Initialize searchable select dropdowns.
 */
function initSearchableSelects(): void {
    document.querySelectorAll<HTMLElement>('[data-searchable-select]').forEach((wrapper) => {
        if (wrapper.hasAttribute('data-searchable-init')) return;
        wrapper.setAttribute('data-searchable-init', 'true');

        const config: SearchableSelectConfig = JSON.parse(wrapper.getAttribute('data-searchable-select') || '{}');
        const hiddenSelect = wrapper.querySelector<HTMLSelectElement>('.searchable-select-hidden');
        const trigger = wrapper.querySelector<HTMLElement>('.searchable-select-trigger');
        const dropdown = wrapper.querySelector<HTMLElement>('.searchable-select-dropdown');
        const search = wrapper.querySelector<HTMLInputElement>('.searchable-select-search');
        const options = wrapper.querySelectorAll<HTMLElement>('.searchable-select-option');
        const display = wrapper.querySelector<HTMLElement>('.searchable-select-display');
        const clearBtn = wrapper.querySelector<HTMLButtonElement>('.searchable-select-clear');
        const arrow = wrapper.querySelector<HTMLElement>('.searchable-select-arrow');
        const noResults = wrapper.querySelector<HTMLElement>('.searchable-select-no-results');

        if (!trigger || !dropdown || !hiddenSelect) return;

        let isOpen = false;

        function openDropdown(): void {
            dropdown!.classList.remove('hidden');
            trigger!.setAttribute('aria-expanded', 'true');
            arrow?.classList.add('rotate-180');
            isOpen = true;
            if (search) {
                search.value = '';
                search.focus();
                filterOptions('');
            }
        }

        function closeDropdown(): void {
            dropdown!.classList.add('hidden');
            trigger!.setAttribute('aria-expanded', 'false');
            arrow?.classList.remove('rotate-180');
            isOpen = false;
        }

        function filterOptions(query: string): void {
            const lowerQuery = query.toLowerCase();
            let hasVisible = false;

            options.forEach((option) => {
                const label = option.querySelector<HTMLElement>('.searchable-select-option-label');
                const text = (label?.textContent || option.textContent || '').toLowerCase();
                if (text.includes(lowerQuery)) {
                    option.style.display = '';
                    hasVisible = true;
                } else {
                    option.style.display = 'none';
                }
            });

            noResults?.classList.toggle('hidden', hasVisible);
        }

        function updateDisplay(): void {
            const selected = Array.from(hiddenSelect!.selectedOptions);
            if (display) {
                if (selected.length === 0 || (selected.length === 1 && selected[0].value === '')) {
                    display.innerHTML = `<span class="text-gray-400 dark:text-gray-500">${config.placeholder || 'Select...'}</span>`;
                } else {
                    display.textContent = selected.map((o) => o.textContent).join(', ');
                }
            }
            hiddenSelect!.dispatchEvent(new Event('change', { bubbles: true }));
        }

        // Trigger click
        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (isOpen) {
                closeDropdown();
            } else {
                openDropdown();
            }
        });

        // Close on outside click
        document.addEventListener('click', (e) => {
            if (!wrapper.contains(e.target as Node)) {
                closeDropdown();
            }
        });

        // Search filtering
        if (search) {
            search.addEventListener('input', (e) => {
                filterOptions((e.target as HTMLInputElement).value);
            });
            search.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        }

        // Option selection
        options.forEach((option) => {
            option.addEventListener('click', (e) => {
                e.stopPropagation();
                const value = option.getAttribute('data-value') || '';
                const label = option.querySelector<HTMLElement>('.searchable-select-option-label');
                const labelText = label?.textContent || option.textContent || '';

                if (config.multiple) {
                    const optionEl = hiddenSelect!.querySelector<HTMLOptionElement>(`option[value="${value}"]`);
                    if (optionEl) {
                        optionEl.selected = !optionEl.selected;
                        option.classList.toggle('bg-primary-50');
                        option.classList.toggle('dark:bg-primary-900/20');
                        const check = option.querySelector<HTMLElement>('.searchable-select-option-check');
                        check?.classList.toggle('hidden');
                    }
                    updateDisplay();
                } else {
                    hiddenSelect!.value = value;
                    if (display) {
                        display.innerHTML = labelText;
                    }

                    options.forEach((opt) => {
                        opt.classList.remove('bg-primary-50', 'dark:bg-primary-900/20');
                        opt.setAttribute('aria-selected', 'false');
                        const check = opt.querySelector<HTMLElement>('.searchable-select-option-check');
                        check?.classList.add('hidden');
                    });

                    option.classList.add('bg-primary-50', 'dark:bg-primary-900/20');
                    option.setAttribute('aria-selected', 'true');
                    const check = option.querySelector<HTMLElement>('.searchable-select-option-check');
                    check?.classList.remove('hidden');

                    if (clearBtn?.parentElement) {
                        clearBtn.parentElement.style.display = value ? '' : 'none';
                    }
                    closeDropdown();
                    hiddenSelect!.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        });

        // Clear button
        if (clearBtn) {
            clearBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                hiddenSelect!.value = '';
                if (display) {
                    display.innerHTML = `<span class="text-gray-400 dark:text-gray-500">${config.placeholder || 'Select...'}</span>`;
                }

                options.forEach((opt) => {
                    opt.classList.remove('bg-primary-50', 'dark:bg-primary-900/20');
                    opt.setAttribute('aria-selected', 'false');
                    const check = opt.querySelector<HTMLElement>('.searchable-select-option-check');
                    check?.classList.add('hidden');
                });

                if (clearBtn.parentElement) {
                    clearBtn.parentElement.style.display = 'none';
                }
                hiddenSelect!.dispatchEvent(new Event('change', { bubbles: true }));
            });
        }

        // Keyboard navigation
        trigger.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                if (isOpen) {
                    closeDropdown();
                } else {
                    openDropdown();
                }
            } else if (e.key === 'Escape') {
                closeDropdown();
            }
        });
    });
}

/**
 * Initialize query builder interactions.
 */
function initQueryBuilders(): void {
    document.querySelectorAll<HTMLElement>('[data-accelade-query-builder]').forEach((builder) => {
        if (builder.hasAttribute('data-query-builder-init')) return;
        builder.setAttribute('data-query-builder-init', 'true');

        if (typeof window.FilterQueryBuilderManager !== 'undefined') {
            new window.FilterQueryBuilderManager(builder);
        }
    });
}

/**
 * Initialize auto-submit filters.
 */
function initAutoSubmit(): void {
    document.querySelectorAll<HTMLElement>('[data-filter-auto-submit]').forEach((filterForm) => {
        if (filterForm.hasAttribute('data-auto-submit-init')) return;
        filterForm.setAttribute('data-auto-submit-init', 'true');

        const form = filterForm.closest<HTMLFormElement>('form');
        filterForm.querySelectorAll<HTMLInputElement | HTMLSelectElement>('input, select').forEach((input) => {
            input.addEventListener('change', () => {
                if (form) {
                    submitForm(form);
                }
            });
        });
    });
}

/**
 * Clear all filters in a form.
 */
function clear(form: HTMLFormElement): void {
    form.querySelectorAll<HTMLInputElement | HTMLSelectElement>('input, select').forEach((input) => {
        if (input instanceof HTMLInputElement && (input.type === 'checkbox' || input.type === 'radio')) {
            input.checked = false;
        } else {
            input.value = '';
        }
    });
    submitForm(form);
}

/**
 * Main AcceladeFilters object
 */
export const AcceladeFilters = {
    init(): void {
        initFilterPanels();
        initSearchableSelects();
        initQueryBuilders();
        initAutoSubmit();
    },
    setButtonLoading,
    submitForm,
    clear,
    initFilterPanels,
    initSearchableSelects,
    initQueryBuilders,
    initAutoSubmit,
};
