<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Traits;

use PlanB\Core\DS\Contract\VectorInmutableInterface;
use PlanB\Core\DS\Vector\TypedVector;
use PlanB\Core\DS\Vector\Vector;

/**
 * @phpstan-require-extends TypedVector<TValue>
 *
 * @phpstan-require-implements VectorInmutableInterface<TValue>
 *
 * @template TValue
 */
trait VectorInmutableTrait
{
    final public function insertAtStart(mixed ...$values): static
    {
        $items = $this->items;
        array_unshift($items, ...$values);

        return new static($items);
    }

    final public function insertAtEnd(mixed ...$values): static
    {
        $items = $this->items;
        foreach ($values as $value) {
            $items[] = $value;
        }

        return new static($items);
    }

    final public function insertAt(int $index, mixed ...$values): static
    {
        if ($values === []) {
            throw new \InvalidArgumentException('You must provide at least one value to insert.');
        }

        $count = $this->count();
        if ($index < 0 || $index >= $count) {
            throw new \OutOfBoundsException("Index {$index} is out of bounds for insertion.");
        }
        $items = $this->items;

        array_splice($items, $index, 0, $values);

        return new static($items);
    }

    final public function removeFromStart(): static
    {
        if ($this->count() === 0) {
            return new static();
        }

        $items = $this->items;
        array_shift($items);

        return new static($items);
    }

    final public function removeFromEnd(): static
    {
        if ($this->count() === 0) {
            return new static();
        }

        $items = $this->items;
        array_pop($items);

        return new static($items);
    }

    final public function removeAt(int ...$indexes): static
    {
        $items = $this->items;
        foreach ($indexes as $index) {
            unset($items[$index]);
        }

        return new static($items);
    }

    final public function zip(iterable ...$collections): Vector
    {
        $arrays = [$this->items];
        $minLength = count($this->items);

        foreach ($collections as $collection) {
            $array = is_array($collection) ? $collection : iterator_to_array($collection);
            $arrays[] = $array;
            $minLength = min($minLength, count($array));
        }

        // [[v1, v2], [v1, v2]]
        $zipped = array_map(null, ...$arrays);

        /** @var array<int, mixed> $zipped */
        $zipped = array_slice($zipped, 0, $minLength);

        /** @phpstan-ignore return.type */
        return Vector::collect($zipped);
    }

    final public function flatten(): Vector
    {
        $flattened = [];

        foreach ($this->items as $item) {
            if (is_iterable($item)) {
                foreach ($item as $subItem) {
                    $flattened[] = $subItem;
                }
            } else {
                $flattened[] = $item;
            }
        }

        /** @var Vector<mixed> */
        return Vector::collect($flattened);
    }
}
