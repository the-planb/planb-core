<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Traits;

use PlanB\Core\DS\Contract\MapMutableInterface;
use PlanB\Core\DS\Map\TypedMap;

/**
 * @phpstan-require-extends TypedMap<TKey, TValue>
 *
 * @phpstan-require-implements MapMutableInterface<TKey, TValue>
 *
 * @template TKey of string|int
 * @template TValue
 */
trait MapMutableTrait
{
    final public function set(int|string $key, mixed $value): self
    {
        [$item] = self::ensureItems([$value]);

        $items = $this->items;
        $items[$key] = $item;

        $this->updateItems($items);

        return $this;
    }

    final public function delete(int|string $key): self
    {
        $items = $this->items;
        unset($items[$key]);

        $this->updateItems($items);

        return $this;
    }

    final public function clear(): self
    {
        $this->updateItems([]);

        return $this;
    }
}
