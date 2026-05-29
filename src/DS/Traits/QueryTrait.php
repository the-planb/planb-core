<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Traits;

use PlanB\Core\DS\Collection;
use PlanB\Core\DS\Contract\QueryInterface;

/**
 * @phpstan-require-extends Collection
 *
 * @phpstan-require-implements  QueryInterface
 *
 * @template TKey of array-key
 * @template TValue
 */
trait QueryTrait
{
    final public function hasCount(int $total): bool
    {
        return $this->count() === $total;
    }

    final public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * @param TValue $value
     */
    final public function contains(mixed $value, bool $strict = true): bool
    {
        return in_array($value, $this->items, $strict);
    }

    final public function some(callable $condition): bool
    {
        foreach ($this->items as $key => $value) {
            if ($condition($value, $key)) {
                return true;
            }
        }

        return false;
    }

    final public function every(callable $condition): bool
    {
        foreach ($this->items as $key => $value) {
            if (!$condition($value, $key)) {
                return false;
            }
        }

        return true;
    }
}
