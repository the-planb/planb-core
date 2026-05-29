<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Traits;

use PlanB\Core\DS\Contract\MapInmutableInterface;
use PlanB\Core\DS\Map\TypedMap;

/**
 * @phpstan-require-extends TypedMap<TKey, TValue>
 *
 * @phpstan-require-implements MapInmutableInterface<TKey, TValue>
 *
 * @template TKey of string|int
 * @template TValue
 */
trait MapInmutableTrait
{
    final public function put(int|string $key, mixed $value): static
    {
        $items = $this->items;
        $items[$key] = $value;

        return new static($items);
    }

    final public function forget(int|string ...$keys): static
    {
        $items = $this->items;
        foreach ($keys as $key) {
            unset($items[$key]);
        }

        return new static($items);
    }
}
