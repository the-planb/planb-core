<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Traits;

use PlanB\Core\DS\Collection;
use PlanB\Core\DS\Contract\BasicInterface;

/**
 * @phpstan-require-extends Collection<TKey, TValue>
 *
 * @phpstan-require-implements BasicInterface<TKey, TValue>
 *
 * @template TKey of array-key
 * @template TValue
 */
trait BasicTrait
{
    /**
     * @return static<TValue>
     */
    final public function filter(?callable $condition = null): static
    {
        if ($condition === null) {
            $condition = fn ($value, $key) => $value;
        }
        $items = [];
        foreach ($this->items as $key => $value) {
            if ($condition($value, $key)) {
                $items[$key] = $value;
            }
        }

        /** @var static<TValue> */
        return new static($items);
    }

    /**
     * @return static<TValue>
     */
    final public function normalize(callable $callback): static
    {
        $keys = array_keys($this->items);
        $mapped = array_map($callback, $this->items, $keys);

        /** @var static<TValue> */
        return new static($mapped);
    }

    /**
     * @return static<TValue>
     */
    final public function sort(?callable $comparator = null): static
    {
        $items = $this->items;

        if ($comparator !== null) {
            uasort($items, $comparator);
        } else {
            asort($items);
        }

        /** @var static<TValue> */
        return new static($items);
    }

    final public function reduce(callable $callback, mixed $initial = null): mixed
    {
        $accumulator = $initial;

        foreach ($this->items as $key => $value) {
            $accumulator = $callback($accumulator, $value, $key);
        }

        return $accumulator;
    }

    /**
     * @return static<TValue>
     */
    final public function slice(int $offset, ?int $length = null): static
    {
        /** @var static<TValue> */
        return new static(array_slice($this->items, $offset, $length, true));
    }

    final public function isEmpty(): bool
    {
        return $this->count() === 0;
    }
}
