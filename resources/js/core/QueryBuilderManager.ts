/**
 * Query Builder Manager
 *
 * Manages complex nested query building with AND/OR grouping.
 */

import type {
    QueryBuilderValue,
    QueryBuilderRule,
    QueryBuilderConstraint,
    QueryBuilderOperator,
} from '../types';

/**
 * Filter Query Builder Manager class
 */
export class FilterQueryBuilderManager {
    private element: HTMLElement;
    private id: string;
    private name: string;
    private constraints: QueryBuilderConstraint[];
    private value: QueryBuilderValue;
    private input: HTMLInputElement | null;
    private rulesContainer: HTMLElement | null;
    private ruleTemplate: HTMLTemplateElement | null;
    private groupTemplate: HTMLTemplateElement | null;
    private constraintPickerTemplate: HTMLTemplateElement | null;

    constructor(element: HTMLElement) {
        this.element = element;
        this.id = element.getAttribute('data-query-builder-id') || '';
        this.name = element.getAttribute('data-query-builder-name') || '';
        this.constraints = JSON.parse(element.getAttribute('data-query-builder-constraints') || '[]');
        this.value = JSON.parse(element.getAttribute('data-query-builder-value') || '{"rules":[],"combinator":"and"}');
        this.input = element.querySelector<HTMLInputElement>('[data-query-builder-input]');
        this.rulesContainer = element.querySelector<HTMLElement>('[data-query-builder-rules]');
        this.ruleTemplate = element.querySelector<HTMLTemplateElement>('[data-query-builder-rule-template]');
        this.groupTemplate = element.querySelector<HTMLTemplateElement>('[data-query-builder-group-template]');
        this.constraintPickerTemplate = element.querySelector<HTMLTemplateElement>('[data-query-builder-constraint-picker-template]');

        this.init();
    }

    private init(): void {
        this.bindEvents();
        this.render();
    }

    private bindEvents(): void {
        const addRuleBtn = this.element.querySelector<HTMLButtonElement>('[data-query-builder-add-rule]');
        if (addRuleBtn) {
            addRuleBtn.addEventListener('click', () => this.addRule());
        }

        const addGroupBtn = this.element.querySelector<HTMLButtonElement>('[data-query-builder-add-group]');
        if (addGroupBtn) {
            addGroupBtn.addEventListener('click', () => this.addGroup());
        }
    }

    private render(): void {
        if (!this.rulesContainer) return;
        this.rulesContainer.innerHTML = '';
        this.renderRules(this.value.rules, this.rulesContainer);
        this.updateInput();
    }

    private renderRules(rules: QueryBuilderRule[], container: HTMLElement): void {
        rules.forEach((rule, index) => {
            if (rule.rules) {
                this.renderGroup(rule, container, index);
            } else {
                this.renderRule(rule, container, index);
            }
        });
    }

    private renderRule(rule: QueryBuilderRule, container: HTMLElement, index: number): void {
        if (!this.ruleTemplate) return;

        const template = this.ruleTemplate.content.cloneNode(true) as DocumentFragment;
        const ruleEl = template.querySelector<HTMLElement>('[data-rule]');
        if (!ruleEl) return;

        ruleEl.setAttribute('data-rule-index', String(index));

        const constraintLabel = ruleEl.querySelector<HTMLElement>('[data-constraint-label]');
        const constraint = this.constraints.find((c) => c.name === rule.constraint);
        if (constraintLabel) {
            constraintLabel.textContent = constraint ? constraint.label : 'Select field...';
        }

        const operatorSelect = ruleEl.querySelector<HTMLSelectElement>('[data-operator-select]');
        if (operatorSelect && constraint?.operators) {
            operatorSelect.innerHTML = '';
            constraint.operators.forEach((op: QueryBuilderOperator) => {
                const option = document.createElement('option');
                option.value = op.name;
                option.textContent = op.label;
                if (op.name === rule.operator) {
                    option.selected = true;
                }
                operatorSelect.appendChild(option);
            });
        }

        const valueInput = ruleEl.querySelector<HTMLInputElement>('[data-value-input]');
        if (valueInput) {
            valueInput.value = rule.value || '';
        }

        this.bindConstraintPicker(ruleEl, rule);

        operatorSelect?.addEventListener('change', (e) => {
            rule.operator = (e.target as HTMLSelectElement).value;
            this.updateInput();
        });

        valueInput?.addEventListener('input', (e) => {
            rule.value = (e.target as HTMLInputElement).value;
            this.updateInput();
        });

        const removeBtn = ruleEl.querySelector<HTMLButtonElement>('[data-remove-rule]');
        removeBtn?.addEventListener('click', () => {
            const ruleIndex = Array.from(container.children).indexOf(ruleEl);
            this.value.rules.splice(ruleIndex, 1);
            this.render();
        });

        container.appendChild(template);
    }

    private renderGroup(group: QueryBuilderRule, container: HTMLElement, index: number): void {
        if (!this.groupTemplate || !group.rules) return;

        const template = this.groupTemplate.content.cloneNode(true) as DocumentFragment;
        const groupEl = template.querySelector<HTMLElement>('[data-group]');
        if (!groupEl) return;

        groupEl.setAttribute('data-group-index', String(index));

        const combinatorSelect = groupEl.querySelector<HTMLSelectElement>('[data-combinator-select]');
        if (combinatorSelect) {
            combinatorSelect.value = group.combinator || 'and';
            combinatorSelect.addEventListener('change', (e) => {
                group.combinator = (e.target as HTMLSelectElement).value as 'and' | 'or';
                this.updateInput();
            });
        }

        const groupRulesContainer = groupEl.querySelector<HTMLElement>('[data-group-rules]');
        if (groupRulesContainer) {
            this.renderRules(group.rules, groupRulesContainer);
        }

        const addRuleBtn = groupEl.querySelector<HTMLButtonElement>('[data-group-add-rule]');
        addRuleBtn?.addEventListener('click', () => {
            group.rules!.push({ constraint: null, operator: null, value: '' });
            this.render();
        });

        const addGroupBtn = groupEl.querySelector<HTMLButtonElement>('[data-group-add-group]');
        addGroupBtn?.addEventListener('click', () => {
            group.rules!.push({ rules: [], combinator: 'and', constraint: null, operator: null, value: '' });
            this.render();
        });

        const removeBtn = groupEl.querySelector<HTMLButtonElement>('[data-remove-group]');
        removeBtn?.addEventListener('click', () => {
            const groupIndex = Array.from(container.children).indexOf(groupEl);
            this.value.rules.splice(groupIndex, 1);
            this.render();
        });

        container.appendChild(template);
    }

    private bindConstraintPicker(ruleEl: HTMLElement, rule: QueryBuilderRule): void {
        const trigger = ruleEl.querySelector<HTMLElement>('[data-constraint-trigger]');
        const selector = ruleEl.querySelector<HTMLElement>('[data-constraint-selector]');

        trigger?.addEventListener('click', (e) => {
            e.stopPropagation();
            if (selector) {
                this.showConstraintPicker(selector, rule);
            }
        });
    }

    private showConstraintPicker(selector: HTMLElement, rule: QueryBuilderRule): void {
        const existingPicker = document.querySelector<HTMLElement>('.constraint-picker');
        existingPicker?.remove();

        if (!this.constraintPickerTemplate) return;

        const template = this.constraintPickerTemplate.content.cloneNode(true) as DocumentFragment;
        const picker = template.querySelector<HTMLElement>('.constraint-picker');
        if (!picker) return;

        const list = picker.querySelector<HTMLElement>('[data-constraint-list]');
        const search = picker.querySelector<HTMLInputElement>('[data-constraint-search]');

        this.constraints.forEach((constraint) => {
            const item = document.createElement('button');
            item.type = 'button';
            item.className = 'w-full px-3 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-700';
            item.textContent = constraint.label;
            item.addEventListener('click', () => {
                rule.constraint = constraint.name;
                rule.operator = constraint.operators[0]?.name || null;
                picker.remove();
                this.render();
            });
            list?.appendChild(item);
        });

        search?.addEventListener('input', (e) => {
            const query = (e.target as HTMLInputElement).value.toLowerCase();
            list?.querySelectorAll<HTMLButtonElement>('button').forEach((btn) => {
                btn.style.display = btn.textContent?.toLowerCase().includes(query) ? '' : 'none';
            });
        });

        selector.appendChild(picker);

        const closeHandler = (e: MouseEvent): void => {
            if (!picker.contains(e.target as Node) && !selector.contains(e.target as Node)) {
                picker.remove();
                document.removeEventListener('click', closeHandler);
            }
        };

        setTimeout(() => document.addEventListener('click', closeHandler), 0);
    }

    public addRule(): void {
        this.value.rules.push({ constraint: null, operator: null, value: '' });
        this.render();
    }

    public addGroup(): void {
        this.value.rules.push({ rules: [], combinator: 'and', constraint: null, operator: null, value: '' });
        this.render();
    }

    private updateInput(): void {
        if (this.input) {
            this.input.value = JSON.stringify(this.value);
        }
    }
}
