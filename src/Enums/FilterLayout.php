<?php

declare(strict_types=1);

namespace Accelade\Filters\Enums;

/**
 * Filter layout options.
 *
 * Determines how filters are displayed in the UI.
 */
enum FilterLayout: string
{
    /**
     * Filters displayed in a dropdown menu (default).
     */
    case Dropdown = 'dropdown';

    /**
     * Filters displayed in a modal dialog.
     */
    case Modal = 'modal';

    /**
     * Filters displayed above the table content.
     */
    case AboveContent = 'above-content';

    /**
     * Filters displayed above the table content, collapsible.
     */
    case AboveContentCollapsible = 'above-content-collapsible';

    /**
     * Filters displayed below the table content.
     */
    case BelowContent = 'below-content';

    /**
     * Filters displayed to the left of content.
     */
    case Sidebar = 'sidebar';

    /**
     * Filters displayed to the left, collapsible.
     */
    case SidebarCollapsible = 'sidebar-collapsible';

    /**
     * Filters displayed inline with other controls.
     */
    case Inline = 'inline';

    /**
     * Get CSS classes for the layout container.
     */
    public function getContainerClass(): string
    {
        return match ($this) {
            self::Dropdown => 'filter-layout-dropdown',
            self::Modal => 'filter-layout-modal',
            self::AboveContent => 'filter-layout-above',
            self::AboveContentCollapsible => 'filter-layout-above filter-layout-collapsible',
            self::BelowContent => 'filter-layout-below',
            self::Sidebar => 'filter-layout-sidebar',
            self::SidebarCollapsible => 'filter-layout-sidebar filter-layout-collapsible',
            self::Inline => 'filter-layout-inline',
        };
    }

    /**
     * Check if the layout is collapsible.
     */
    public function isCollapsible(): bool
    {
        return match ($this) {
            self::AboveContentCollapsible, self::SidebarCollapsible => true,
            default => false,
        };
    }

    /**
     * Check if the layout uses a trigger button.
     */
    public function usesTrigger(): bool
    {
        return match ($this) {
            self::Dropdown, self::Modal => true,
            default => false,
        };
    }

    /**
     * Get the label for UI display.
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::Dropdown => 'Dropdown',
            self::Modal => 'Modal',
            self::AboveContent => 'Above Content',
            self::AboveContentCollapsible => 'Above Content (Collapsible)',
            self::BelowContent => 'Below Content',
            self::Sidebar => 'Sidebar',
            self::SidebarCollapsible => 'Sidebar (Collapsible)',
            self::Inline => 'Inline',
        };
    }
}
