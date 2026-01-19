<?php

declare(strict_types=1);

namespace Accelade\Filters\Concerns;

use Closure;
use Illuminate\Support\Collection;

/**
 * Trait for filters that have options.
 */
trait HasOptions
{
    protected array|Closure $options = [];

    protected ?string $optionLabel = null;

    protected ?string $optionValue = null;

    protected bool $preload = true;

    /**
     * Set the options.
     *
     * @param  array<mixed>|Closure|Collection<int, mixed>  $options
     */
    public function options(array|Closure|Collection $options): static
    {
        if ($options instanceof Collection) {
            $options = $options->toArray();
        }

        $this->options = $options;

        return $this;
    }

    /**
     * Get the options.
     *
     * @return array<mixed>
     */
    public function getOptions(): array
    {
        if ($this->options instanceof Closure) {
            return ($this->options)();
        }

        return $this->options;
    }

    /**
     * Set options from an enum.
     *
     * @param  class-string  $enum
     */
    public function enum(string $enum): static
    {
        $this->options = collect($enum::cases())
            ->mapWithKeys(fn ($case) => [
                $case->value => method_exists($case, 'label') ? $case->label() : $case->name,
            ])
            ->toArray();

        return $this;
    }

    /**
     * Set the key to use for option labels.
     */
    public function optionLabel(string $key): static
    {
        $this->optionLabel = $key;

        return $this;
    }

    /**
     * Get the option label key.
     */
    public function getOptionLabel(): ?string
    {
        return $this->optionLabel;
    }

    /**
     * Set the key to use for option values.
     */
    public function optionValue(string $key): static
    {
        $this->optionValue = $key;

        return $this;
    }

    /**
     * Get the option value key.
     */
    public function getOptionValue(): ?string
    {
        return $this->optionValue;
    }

    /**
     * Disable preloading options.
     */
    public function preload(bool $preload = true): static
    {
        $this->preload = $preload;

        return $this;
    }

    /**
     * Check if preloading is enabled.
     */
    public function shouldPreload(): bool
    {
        return $this->preload;
    }

    /**
     * Get formatted options for rendering.
     *
     * @return array<int, array{value: mixed, label: string}>
     */
    public function getFormattedOptions(): array
    {
        $options = $this->getOptions();
        $formatted = [];

        foreach ($options as $key => $value) {
            if (is_array($value)) {
                $formatted[] = [
                    'value' => $value[$this->optionValue ?? 'id'] ?? $key,
                    'label' => $value[$this->optionLabel ?? 'name'] ?? $value[$this->optionLabel ?? 'label'] ?? $key,
                ];
            } else {
                $formatted[] = [
                    'value' => $key,
                    'label' => $value,
                ];
            }
        }

        return $formatted;
    }
}
