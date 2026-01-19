{{-- Filters JavaScript --}}
<script data-accelade-filters-script>
// Prevent duplicate execution during SPA navigation
if (typeof window.AcceladeFiltersInitialized === 'undefined') {
    window.AcceladeFiltersInitialized = true;

window.AcceladeFilters = {
    /**
     * Initialize all filter components.
     */
    init: function() {
        this.initFilterPanels();
        this.initSearchableSelects();
        this.initQueryBuilders();
        this.initAutoSubmit();
    },

    /**
     * Show loading state on a button
     */
    setButtonLoading: function(button, isLoading) {
        if (!button) return;

        if (isLoading) {
            button.setAttribute('data-loading', 'true');
            button.disabled = true;

            // Store original content
            if (!button.hasAttribute('data-original-content')) {
                button.setAttribute('data-original-content', button.innerHTML);
            }

            // Add spinner
            var originalText = button.textContent.trim();
            button.innerHTML = '<span class="filter-spinner inline-flex items-center gap-2"><svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>' + originalText + '</span></span>';
        } else {
            button.removeAttribute('data-loading');
            button.disabled = false;

            // Restore original content
            var originalContent = button.getAttribute('data-original-content');
            if (originalContent) {
                button.innerHTML = originalContent;
                button.removeAttribute('data-original-content');
            }
        }
    },

    /**
     * Submit form via rehydration (partial update) or SPA navigation.
     * Preserves scroll position and only updates the target component.
     */
    submitForm: function(form, options) {
        if (!form) return;

        options = options || {};
        var preserveScroll = options.preserveScroll !== false; // Default to true
        var self = this;

        // Find and set loading state on submit button
        var submitBtn = form.querySelector('[data-filter-apply], button[type="submit"]');
        this.setButtonLoading(submitBtn, true);

        // Collect form data
        var formData = new FormData(form);
        var params = new URLSearchParams();

        for (var pair of formData.entries()) {
            if (pair[1] !== '' && pair[1] !== null) {
                params.append(pair[0], pair[1]);
            }
        }

        // Build URL with query params
        var baseUrl = form.action || window.location.pathname;
        var url = params.toString() ? baseUrl + '?' + params.toString() : baseUrl;

        // Check if form has a specific target component to update
        var targetId = form.getAttribute('data-filter-target');

        if (targetId && window.Accelade && window.Accelade.rehydrate) {
            // Find the rehydrate element and update its URL before triggering
            var rehydrateEl = document.querySelector('[data-rehydrate-id="' + targetId + '"]');
            if (rehydrateEl) {
                // Set the URL on the element so rehydrate fetches the correct filtered data
                rehydrateEl.setAttribute('data-rehydrate-url', url);
            }

            // Update the URL in the browser without navigation
            window.history.pushState({}, '', url);

            // Trigger rehydration of just the target component
            var instance = window.Accelade.rehydrate.get(targetId);
            if (instance) {
                // Update the instance's config URL directly so it fetches the new filtered URL
                if (instance.config) {
                    instance.config.url = url;
                }
                instance.rehydrate().then(function() {
                    self.setButtonLoading(submitBtn, false);
                }).catch(function() {
                    self.setButtonLoading(submitBtn, false);
                });
            } else {
                // Fallback: try the reload method
                window.Accelade.rehydrate.reload(targetId);
                // Reset button after a delay since we can't await
                setTimeout(function() {
                    self.setButtonLoading(submitBtn, false);
                }, 1000);
            }
            return;
        }

        // Navigation options - preserve scroll by default for filter submissions
        var navOptions = {
            preserveScroll: preserveScroll,
            scrollToTop: false
        };

        // Use Accelade SPA navigation if available
        if (window.Accelade && window.Accelade.navigate) {
            window.Accelade.navigate(url, navOptions).then(function() {
                self.setButtonLoading(submitBtn, false);
            }).catch(function() {
                self.setButtonLoading(submitBtn, false);
            });
        } else if (window.Accelade && window.Accelade.router && window.Accelade.router.navigate) {
            window.Accelade.router.navigate(url, navOptions).then(function() {
                self.setButtonLoading(submitBtn, false);
            }).catch(function() {
                self.setButtonLoading(submitBtn, false);
            });
        } else {
            // Fallback to regular form submit
            form.submit();
        }
    },

    /**
     * Initialize filter panel interactions.
     */
    initFilterPanels: function() {
        var self = this;

        // Intercept form submissions for SPA navigation
        document.querySelectorAll('form').forEach(function(form) {
            // Skip if already initialized
            if (form.hasAttribute('data-filter-form-init')) return;

            if (form.querySelector('[data-accelade-filter-panel]') || form.querySelector('[data-filter-form]')) {
                form.setAttribute('data-filter-form-init', 'true');
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    self.submitForm(form);
                });
            }
        });

        // Intercept pagination links for SPA navigation or rehydration
        document.querySelectorAll('nav[role="navigation"] a, .pagination a').forEach(function(link) {
            // Skip if already has SPA attributes or event listener
            if (link.hasAttribute('data-accelade-link') || link.hasAttribute('a-link') || link.hasAttribute('data-spa-link') || link.hasAttribute('data-spa-init')) {
                return;
            }
            // Skip disabled/inactive links
            if (link.getAttribute('aria-disabled') === 'true' || link.classList.contains('disabled')) {
                return;
            }
            // Mark as initialized
            link.setAttribute('data-spa-init', 'true');
            link.addEventListener('click', function(e) {
                e.preventDefault();
                var href = link.getAttribute('href');
                if (!href) return;

                // Check if the link is inside a rehydrate component
                var rehydrateEl = link.closest('[data-accelade-rehydrate]');
                if (rehydrateEl && window.Accelade && window.Accelade.rehydrate) {
                    var rehydrateId = rehydrateEl.getAttribute('data-rehydrate-id');
                    if (rehydrateId) {
                        // Set the URL on the element so rehydrate fetches the correct page
                        rehydrateEl.setAttribute('data-rehydrate-url', href);

                        // Update the URL in the browser
                        window.history.pushState({}, '', href);

                        // Update instance config URL and trigger rehydration
                        var instance = window.Accelade.rehydrate.get(rehydrateId);
                        if (instance && instance.config) {
                            instance.config.url = href;
                        }
                        window.Accelade.rehydrate.reload(rehydrateId);
                        return;
                    }
                }

                // Fallback to full SPA navigation
                var navOptions = { preserveScroll: true, scrollToTop: false };
                if (window.Accelade && window.Accelade.navigate) {
                    window.Accelade.navigate(href, navOptions);
                } else if (window.Accelade && window.Accelade.router && window.Accelade.router.navigate) {
                    window.Accelade.router.navigate(href, navOptions);
                } else {
                    window.location.href = href;
                }
            });
        });

        // Dropdown triggers
        document.querySelectorAll('[data-filter-dropdown]').forEach(function(dropdown) {
            var trigger = dropdown.querySelector('[data-filter-trigger]');
            var content = dropdown.querySelector('[data-filter-content]');
            var chevron = dropdown.querySelector('[data-filter-chevron]');

            if (trigger && content) {
                trigger.addEventListener('click', function(e) {
                    e.stopPropagation();
                    var isOpen = !content.classList.contains('hidden');
                    if (isOpen) {
                        content.classList.add('hidden');
                        trigger.setAttribute('aria-expanded', 'false');
                        if (chevron) chevron.classList.remove('rotate-180');
                    } else {
                        content.classList.remove('hidden');
                        trigger.setAttribute('aria-expanded', 'true');
                        if (chevron) chevron.classList.add('rotate-180');
                    }
                });

                // Close on outside click
                document.addEventListener('click', function(e) {
                    if (!dropdown.contains(e.target)) {
                        content.classList.add('hidden');
                        trigger.setAttribute('aria-expanded', 'false');
                        if (chevron) chevron.classList.remove('rotate-180');
                    }
                });
            }
        });

        // Collapsible triggers
        document.querySelectorAll('[data-filter-collapse-toggle]').forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                var container = toggle.closest('[data-filter-collapsible]');
                var formContainer = container.querySelector('[data-filter-form-container]');
                var chevron = toggle.querySelector('[data-filter-chevron]');
                var isExpanded = toggle.getAttribute('aria-expanded') === 'true';

                if (isExpanded) {
                    formContainer.classList.add('hidden');
                    toggle.setAttribute('aria-expanded', 'false');
                    if (chevron) chevron.classList.remove('rotate-180');
                } else {
                    formContainer.classList.remove('hidden');
                    toggle.setAttribute('aria-expanded', 'true');
                    if (chevron) chevron.classList.add('rotate-180');
                }
            });
        });

        // Reset buttons
        document.querySelectorAll('[data-filter-reset]').forEach(function(btn) {
            // Skip if already initialized
            if (btn.hasAttribute('data-filter-reset-init')) return;
            btn.setAttribute('data-filter-reset-init', 'true');

            btn.addEventListener('click', function() {
                var filterForm = btn.closest('[data-filter-form]');
                var form = btn.closest('form');
                if (filterForm) {
                    filterForm.querySelectorAll('input, select').forEach(function(input) {
                        if (input.type === 'checkbox' || input.type === 'radio') {
                            input.checked = false;
                        } else {
                            input.value = '';
                        }
                    });
                    // Also update searchable selects display
                    filterForm.querySelectorAll('.searchable-select-display').forEach(function(display) {
                        var wrapper = display.closest('[data-searchable-select]');
                        var config = wrapper ? JSON.parse(wrapper.getAttribute('data-searchable-select') || '{}') : {};
                        display.innerHTML = '<span class="text-gray-400 dark:text-gray-500">' + (config.placeholder || 'Select...') + '</span>';
                    });
                    filterForm.querySelectorAll('.searchable-select-option').forEach(function(opt) {
                        opt.classList.remove('bg-primary-50', 'dark:bg-primary-900/20');
                        opt.setAttribute('aria-selected', 'false');
                        var check = opt.querySelector('.searchable-select-option-check');
                        if (check) check.classList.add('hidden');
                    });
                }
                if (form) {
                    window.AcceladeFilters.submitForm(form);
                }
            });
        });

        // Reset all indicators
        document.querySelectorAll('[data-filter-reset-all]').forEach(function(btn) {
            // Skip if already initialized
            if (btn.hasAttribute('data-filter-reset-all-init')) return;
            btn.setAttribute('data-filter-reset-all-init', 'true');

            btn.addEventListener('click', function() {
                var panel = btn.closest('[data-accelade-filter-panel]');
                var filterForm = panel.querySelector('[data-filter-form]');
                var form = panel.closest('form');
                if (filterForm) {
                    filterForm.querySelectorAll('input, select').forEach(function(input) {
                        if (input.type === 'checkbox' || input.type === 'radio') {
                            input.checked = false;
                        } else {
                            input.value = '';
                        }
                    });
                    // Also update searchable selects display
                    filterForm.querySelectorAll('.searchable-select-display').forEach(function(display) {
                        var wrapper = display.closest('[data-searchable-select]');
                        var config = wrapper ? JSON.parse(wrapper.getAttribute('data-searchable-select') || '{}') : {};
                        display.innerHTML = '<span class="text-gray-400 dark:text-gray-500">' + (config.placeholder || 'Select...') + '</span>';
                    });
                    filterForm.querySelectorAll('.searchable-select-option').forEach(function(opt) {
                        opt.classList.remove('bg-primary-50', 'dark:bg-primary-900/20');
                        opt.setAttribute('aria-selected', 'false');
                        var check = opt.querySelector('.searchable-select-option-check');
                        if (check) check.classList.add('hidden');
                    });
                }
                if (form) {
                    window.AcceladeFilters.submitForm(form);
                }
            });
        });

        // Individual indicator remove buttons
        document.querySelectorAll('[data-filter-remove]').forEach(function(btn) {
            // Skip if already initialized
            if (btn.hasAttribute('data-filter-remove-init')) return;
            btn.setAttribute('data-filter-remove-init', 'true');

            btn.addEventListener('click', function() {
                var filterName = btn.getAttribute('data-filter-remove');
                var panel = btn.closest('[data-accelade-filter-panel]');
                var filterForm = panel.querySelector('[data-filter-form]');
                var form = panel.closest('form');
                var field = filterForm ? filterForm.querySelector('[data-filter-field="' + filterName + '"]') : null;
                if (field) {
                    field.querySelectorAll('input, select').forEach(function(input) {
                        if (input.type === 'checkbox' || input.type === 'radio') {
                            input.checked = false;
                        } else {
                            input.value = '';
                        }
                    });
                    // Also update searchable selects display in this field
                    var wrapper = field.querySelector('[data-searchable-select]');
                    if (wrapper) {
                        var config = JSON.parse(wrapper.getAttribute('data-searchable-select') || '{}');
                        var display = wrapper.querySelector('.searchable-select-display');
                        if (display) {
                            display.innerHTML = '<span class="text-gray-400 dark:text-gray-500">' + (config.placeholder || 'Select...') + '</span>';
                        }
                        wrapper.querySelectorAll('.searchable-select-option').forEach(function(opt) {
                            opt.classList.remove('bg-primary-50', 'dark:bg-primary-900/20');
                            opt.setAttribute('aria-selected', 'false');
                            var check = opt.querySelector('.searchable-select-option-check');
                            if (check) check.classList.add('hidden');
                        });
                    }
                }
                if (form) {
                    window.AcceladeFilters.submitForm(form);
                }
            });
        });

        // Modal triggers
        document.querySelectorAll('[data-filter-modal-trigger]').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var panel = btn.closest('[data-accelade-filter-panel]');
                var panelId = panel.getAttribute('data-filter-panel-id');
                var modalName = 'filter-modal-' + panelId;
                // Use Accelade modal system if available
                if (window.Accelade && window.Accelade.modal) {
                    window.Accelade.modal.open(modalName);
                }
            });
        });
    },

    /**
     * Initialize searchable select dropdowns.
     */
    initSearchableSelects: function() {
        document.querySelectorAll('[data-searchable-select]').forEach(function(wrapper) {
            // Skip if already initialized
            if (wrapper.hasAttribute('data-searchable-init')) return;
            wrapper.setAttribute('data-searchable-init', 'true');

            var config = JSON.parse(wrapper.getAttribute('data-searchable-select') || '{}');
            var hiddenSelect = wrapper.querySelector('.searchable-select-hidden');
            var trigger = wrapper.querySelector('.searchable-select-trigger');
            var dropdown = wrapper.querySelector('.searchable-select-dropdown');
            var search = wrapper.querySelector('.searchable-select-search');
            var options = wrapper.querySelectorAll('.searchable-select-option');
            var display = wrapper.querySelector('.searchable-select-display');
            var clearBtn = wrapper.querySelector('.searchable-select-clear');
            var arrow = wrapper.querySelector('.searchable-select-arrow');
            var noResults = wrapper.querySelector('.searchable-select-no-results');

            if (!trigger || !dropdown || !hiddenSelect) return;

            var isOpen = false;

            // Toggle dropdown
            function openDropdown() {
                dropdown.classList.remove('hidden');
                trigger.setAttribute('aria-expanded', 'true');
                if (arrow) arrow.classList.add('rotate-180');
                isOpen = true;
                if (search) {
                    search.value = '';
                    search.focus();
                    filterOptions('');
                }
            }

            function closeDropdown() {
                dropdown.classList.add('hidden');
                trigger.setAttribute('aria-expanded', 'false');
                if (arrow) arrow.classList.remove('rotate-180');
                isOpen = false;
            }

            // Trigger click
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (isOpen) {
                    closeDropdown();
                } else {
                    openDropdown();
                }
            });

            // Close on outside click
            document.addEventListener('click', function(e) {
                if (!wrapper.contains(e.target)) {
                    closeDropdown();
                }
            });

            // Search filtering
            function filterOptions(query) {
                query = query.toLowerCase();
                var hasVisible = false;
                options.forEach(function(option) {
                    var label = option.querySelector('.searchable-select-option-label');
                    var text = (label ? label.textContent : option.textContent).toLowerCase();
                    if (text.includes(query)) {
                        option.style.display = '';
                        hasVisible = true;
                    } else {
                        option.style.display = 'none';
                    }
                });
                if (noResults) {
                    noResults.classList.toggle('hidden', hasVisible);
                }
            }

            if (search) {
                search.addEventListener('input', function(e) {
                    filterOptions(e.target.value);
                });
                search.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }

            // Option selection
            options.forEach(function(option) {
                option.addEventListener('click', function(e) {
                    e.stopPropagation();
                    var value = option.getAttribute('data-value');
                    var label = option.querySelector('.searchable-select-option-label');
                    var labelText = label ? label.textContent : option.textContent;

                    if (config.multiple) {
                        // Toggle selection for multiple
                        var optionEl = hiddenSelect.querySelector('option[value="' + value + '"]');
                        if (optionEl) {
                            optionEl.selected = !optionEl.selected;
                            option.classList.toggle('bg-primary-50');
                            option.classList.toggle('dark:bg-primary-900/20');
                            var check = option.querySelector('.searchable-select-option-check');
                            if (check) check.classList.toggle('hidden');
                        }
                        updateDisplay();
                    } else {
                        // Single selection
                        hiddenSelect.value = value;
                        // Update display
                        if (display) {
                            display.innerHTML = labelText;
                        }
                        // Update visual selection
                        options.forEach(function(opt) {
                            opt.classList.remove('bg-primary-50', 'dark:bg-primary-900/20');
                            opt.setAttribute('aria-selected', 'false');
                            var check = opt.querySelector('.searchable-select-option-check');
                            if (check) check.classList.add('hidden');
                        });
                        option.classList.add('bg-primary-50', 'dark:bg-primary-900/20');
                        option.setAttribute('aria-selected', 'true');
                        var check = option.querySelector('.searchable-select-option-check');
                        if (check) check.classList.remove('hidden');
                        // Show clear button
                        if (clearBtn) clearBtn.parentElement.style.display = value ? '' : 'none';
                        closeDropdown();
                        // Trigger change event
                        hiddenSelect.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
            });

            // Update display for multiple selection
            function updateDisplay() {
                var selected = Array.from(hiddenSelect.selectedOptions);
                if (display) {
                    if (selected.length === 0 || (selected.length === 1 && selected[0].value === '')) {
                        display.innerHTML = '<span class="text-gray-400 dark:text-gray-500">' + (config.placeholder || 'Select...') + '</span>';
                    } else {
                        display.textContent = selected.map(function(o) { return o.textContent; }).join(', ');
                    }
                }
                // Trigger change event
                hiddenSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }

            // Clear button
            if (clearBtn) {
                clearBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    hiddenSelect.value = '';
                    if (display) {
                        display.innerHTML = '<span class="text-gray-400 dark:text-gray-500">' + (config.placeholder || 'Select...') + '</span>';
                    }
                    // Reset visual selection
                    options.forEach(function(opt) {
                        opt.classList.remove('bg-primary-50', 'dark:bg-primary-900/20');
                        opt.setAttribute('aria-selected', 'false');
                        var check = opt.querySelector('.searchable-select-option-check');
                        if (check) check.classList.add('hidden');
                    });
                    clearBtn.parentElement.style.display = 'none';
                    // Trigger change event
                    hiddenSelect.dispatchEvent(new Event('change', { bubbles: true }));
                });
            }

            // Keyboard navigation
            trigger.addEventListener('keydown', function(e) {
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
    },

    /**
     * Initialize query builder interactions.
     */
    initQueryBuilders: function() {
        document.querySelectorAll('[data-accelade-query-builder]').forEach(function(builder) {
            // Skip if already initialized
            if (builder.hasAttribute('data-query-builder-init')) return;
            builder.setAttribute('data-query-builder-init', 'true');

            if (typeof window.FilterQueryBuilderManager !== 'undefined') {
                new window.FilterQueryBuilderManager(builder);
            }
        });
    },

    /**
     * Initialize auto-submit filters.
     */
    initAutoSubmit: function() {
        var self = this;
        document.querySelectorAll('[data-filter-auto-submit]').forEach(function(filterForm) {
            // Skip if already initialized
            if (filterForm.hasAttribute('data-auto-submit-init')) return;
            filterForm.setAttribute('data-auto-submit-init', 'true');

            var form = filterForm.closest('form');
            filterForm.querySelectorAll('input, select').forEach(function(input) {
                input.addEventListener('change', function() {
                    if (form) {
                        self.submitForm(form);
                    }
                });
            });
        });
    },

    /**
     * Clear all filters in a form.
     */
    clear: function(form) {
        form.querySelectorAll('input, select').forEach(function(input) {
            if (input.type === 'checkbox' || input.type === 'radio') {
                input.checked = false;
            } else {
                input.value = '';
            }
        });
        this.submitForm(form);
    }
};

/**
 * Query Builder Manager - wrapped to prevent redeclaration
 */
window.FilterQueryBuilderManager = class {
    constructor(element) {
        this.element = element;
        this.id = element.getAttribute('data-query-builder-id');
        this.name = element.getAttribute('data-query-builder-name');
        this.constraints = JSON.parse(element.getAttribute('data-query-builder-constraints') || '[]');
        this.value = JSON.parse(element.getAttribute('data-query-builder-value') || '{"rules":[],"combinator":"and"}');
        this.input = element.querySelector('[data-query-builder-input]');
        this.rulesContainer = element.querySelector('[data-query-builder-rules]');
        this.ruleTemplate = element.querySelector('[data-query-builder-rule-template]');
        this.groupTemplate = element.querySelector('[data-query-builder-group-template]');
        this.constraintPickerTemplate = element.querySelector('[data-query-builder-constraint-picker-template]');

        this.init();
    }

    init() {
        this.bindEvents();
        this.render();
    }

    bindEvents() {
        // Add rule button
        var addRuleBtn = this.element.querySelector('[data-query-builder-add-rule]');
        if (addRuleBtn) {
            addRuleBtn.addEventListener('click', () => this.addRule());
        }

        // Add group button
        var addGroupBtn = this.element.querySelector('[data-query-builder-add-group]');
        if (addGroupBtn) {
            addGroupBtn.addEventListener('click', () => this.addGroup());
        }
    }

    render() {
        this.rulesContainer.innerHTML = '';
        this.renderRules(this.value.rules, this.rulesContainer);
        this.updateInput();
    }

    renderRules(rules, container) {
        var self = this;
        rules.forEach(function(rule, index) {
            if (rule.rules) {
                self.renderGroup(rule, container, index);
            } else {
                self.renderRule(rule, container, index);
            }
        });
    }

    renderRule(rule, container, index) {
        var self = this;
        var template = this.ruleTemplate.content.cloneNode(true);
        var ruleEl = template.querySelector('[data-rule]');
        ruleEl.setAttribute('data-rule-index', index);

        var constraintLabel = ruleEl.querySelector('[data-constraint-label]');
        var constraint = this.constraints.find(function(c) { return c.name === rule.constraint; });
        constraintLabel.textContent = constraint ? constraint.label : 'Select field...';

        var operatorSelect = ruleEl.querySelector('[data-operator-select]');
        if (constraint && constraint.operators) {
            operatorSelect.innerHTML = '';
            constraint.operators.forEach(function(op) {
                var option = document.createElement('option');
                option.value = op.name;
                option.textContent = op.label;
                if (op.name === rule.operator) option.selected = true;
                operatorSelect.appendChild(option);
            });
        }

        var valueInput = ruleEl.querySelector('[data-value-input]');
        valueInput.value = rule.value || '';

        this.bindConstraintPicker(ruleEl, rule);

        operatorSelect.addEventListener('change', function(e) {
            rule.operator = e.target.value;
            self.updateInput();
        });

        valueInput.addEventListener('input', function(e) {
            rule.value = e.target.value;
            self.updateInput();
        });

        var removeBtn = ruleEl.querySelector('[data-remove-rule]');
        removeBtn.addEventListener('click', function() {
            var ruleIndex = Array.from(container.children).indexOf(ruleEl);
            self.value.rules.splice(ruleIndex, 1);
            self.render();
        });

        container.appendChild(template);
    }

    renderGroup(group, container, index) {
        var self = this;
        var template = this.groupTemplate.content.cloneNode(true);
        var groupEl = template.querySelector('[data-group]');
        groupEl.setAttribute('data-group-index', index);

        var combinatorSelect = groupEl.querySelector('[data-combinator-select]');
        combinatorSelect.value = group.combinator || 'and';
        combinatorSelect.addEventListener('change', function(e) {
            group.combinator = e.target.value;
            self.updateInput();
        });

        var groupRulesContainer = groupEl.querySelector('[data-group-rules]');
        this.renderRules(group.rules, groupRulesContainer);

        var addRuleBtn = groupEl.querySelector('[data-group-add-rule]');
        addRuleBtn.addEventListener('click', function() {
            group.rules.push({ constraint: null, operator: null, value: '' });
            self.render();
        });

        var addGroupBtn = groupEl.querySelector('[data-group-add-group]');
        addGroupBtn.addEventListener('click', function() {
            group.rules.push({ rules: [], combinator: 'and' });
            self.render();
        });

        var removeBtn = groupEl.querySelector('[data-remove-group]');
        removeBtn.addEventListener('click', function() {
            var groupIndex = Array.from(container.children).indexOf(groupEl);
            self.value.rules.splice(groupIndex, 1);
            self.render();
        });

        container.appendChild(template);
    }

    bindConstraintPicker(ruleEl, rule) {
        var self = this;
        var trigger = ruleEl.querySelector('[data-constraint-trigger]');
        var selector = ruleEl.querySelector('[data-constraint-selector]');

        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            self.showConstraintPicker(selector, rule);
        });
    }

    showConstraintPicker(selector, rule) {
        var self = this;
        var existingPicker = document.querySelector('.constraint-picker');
        if (existingPicker) existingPicker.remove();

        var template = this.constraintPickerTemplate.content.cloneNode(true);
        var picker = template.querySelector('.constraint-picker');
        var list = picker.querySelector('[data-constraint-list]');
        var search = picker.querySelector('[data-constraint-search]');

        this.constraints.forEach(function(constraint) {
            var item = document.createElement('button');
            item.type = 'button';
            item.className = 'w-full px-3 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-700';
            item.textContent = constraint.label;
            item.addEventListener('click', function() {
                rule.constraint = constraint.name;
                rule.operator = constraint.operators[0]?.name || null;
                picker.remove();
                self.render();
            });
            list.appendChild(item);
        });

        search.addEventListener('input', function(e) {
            var query = e.target.value.toLowerCase();
            list.querySelectorAll('button').forEach(function(btn) {
                btn.style.display = btn.textContent.toLowerCase().includes(query) ? '' : 'none';
            });
        });

        selector.appendChild(picker);

        var closeHandler = function(e) {
            if (!picker.contains(e.target) && !selector.contains(e.target)) {
                picker.remove();
                document.removeEventListener('click', closeHandler);
            }
        };
        setTimeout(function() { document.addEventListener('click', closeHandler); }, 0);
    }

    addRule() {
        this.value.rules.push({ constraint: null, operator: null, value: '' });
        this.render();
    }

    addGroup() {
        this.value.rules.push({ rules: [], combinator: 'and' });
        this.render();
    }

    updateInput() {
        this.input.value = JSON.stringify(this.value);
    }
};

// End of initialization guard
}

// Initialize on DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    window.AcceladeFilters.init();
});

// Re-initialize after SPA navigation
document.addEventListener('accelade:navigated', function() {
    window.AcceladeFilters.init();
});

// Re-initialize after component rehydration (partial update)
document.addEventListener('accelade:rehydrate', function(e) {
    // Re-init filters after a rehydrate component is refreshed
    window.AcceladeFilters.init();
});
</script>
