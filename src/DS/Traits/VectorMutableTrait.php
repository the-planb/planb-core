<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Traits;

use PlanB\Core\DS\Contract\VectorMutableInterface;
use PlanB\Core\DS\Vector\TypedVector;

/**
 * @phpstan-require-extends TypedVector<TValue>
 *
 * @phpstan-require-implements VectorMutableInterface<TValue>
 *
 * @template TValue
 */
trait VectorMutableTrait
{
    final public function addAtStart(mixed $value): self
    {
        $items = $this->items;
        array_unshift($items, $value);

        $this->updateItems($items);

        return $this;
    }

    final public function addAtEnd(mixed $value): self
    {
        $items = $this->items;
        $items[] = $value;

        $this->updateItems($items);

        return $this;
    }

    final public function addAt(int $index, mixed $value): self
    {
        $count = $this->count();
        if ($index < 0 || $index >= $count) {
            throw new \OutOfBoundsException("Index {$index} is out of bounds for mutation.");
        }

        $items = $this->items;
        array_splice($items, $index, 0, [$value]);

        $this->updateItems($items);

        return $this;
    }

    final public function deleteFromStart(): self
    {
        if ($this->isEmpty()) {
            return $this;
        }

        $items = $this->items;
        array_shift($items);

        $this->updateItems($items);

        return $this;
    }

    final public function deleteFromEnd(): self
    {
        if ($this->isEmpty()) {
            return $this;
        }

        $items = $this->items;
        array_pop($items);

        $this->updateItems($items);

        return $this;
    }

    final public function deleteAt(int $index): self
    {
        if (!array_key_exists($index, $this->items)) {
            throw new \OutOfBoundsException("Index {$index} does not exist.");
        }

        $items = $this->items;
        unset($items[$index]);

        $this->updateItems(array_values($items));

        return $this;
    }

    final public function clear(): self
    {
        $this->updateItems([]);

        return $this;
    }
}
