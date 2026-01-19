<?php

declare(strict_types=1);

use Accelade\Filters\Enums\FilterLayout;
use Accelade\Filters\Enums\FilterWidth;
use Tests\TestCase;

uses(TestCase::class);

// FilterLayout Tests
it('has all layout cases', function () {
    $cases = FilterLayout::cases();

    expect($cases)->toHaveCount(8);
    expect(collect($cases)->pluck('value'))->toContain(
        'dropdown',
        'modal',
        'above-content',
        'above-content-collapsible',
        'below-content',
        'sidebar',
        'sidebar-collapsible',
        'inline'
    );
});

it('identifies collapsible layouts', function () {
    expect(FilterLayout::AboveContentCollapsible->isCollapsible())->toBeTrue();
    expect(FilterLayout::SidebarCollapsible->isCollapsible())->toBeTrue();
    expect(FilterLayout::Dropdown->isCollapsible())->toBeFalse();
    expect(FilterLayout::Modal->isCollapsible())->toBeFalse();
});

it('identifies layouts that use triggers', function () {
    expect(FilterLayout::Dropdown->usesTrigger())->toBeTrue();
    expect(FilterLayout::Modal->usesTrigger())->toBeTrue();
    expect(FilterLayout::AboveContent->usesTrigger())->toBeFalse();
    expect(FilterLayout::Inline->usesTrigger())->toBeFalse();
});

it('returns container classes', function () {
    expect(FilterLayout::Dropdown->getContainerClass())->toBeString();
    expect(FilterLayout::Sidebar->getContainerClass())->toBeString();
});

// FilterWidth Tests
it('has all width cases', function () {
    $cases = FilterWidth::cases();

    expect($cases)->toHaveCount(11);
    expect(collect($cases)->pluck('value'))->toContain(
        'xs',
        'sm',
        'md',
        'lg',
        'xl',
        '2xl',
        '3xl',
        '4xl',
        '5xl',
        '6xl',
        'full'
    );
});

it('returns max width values', function () {
    expect(FilterWidth::ExtraSmall->getMaxWidth())->toBe('20rem');
    expect(FilterWidth::Small->getMaxWidth())->toBe('24rem');
    expect(FilterWidth::Medium->getMaxWidth())->toBe('28rem');
    expect(FilterWidth::Large->getMaxWidth())->toBe('32rem');
    expect(FilterWidth::ExtraLarge->getMaxWidth())->toBe('36rem');
    expect(FilterWidth::TwoExtraLarge->getMaxWidth())->toBe('42rem');
    expect(FilterWidth::ThreeExtraLarge->getMaxWidth())->toBe('48rem');
});
