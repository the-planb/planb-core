<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Traits;

use PlanB\Core\DS\Collection;
use PlanB\Core\DS\Contract\MutationInterface;

/**
 * @phpstan-require-extends Collection
 *
 * @phpstan-require-implements  MutationInterface
 *
 * @template TKey of array-key
 * @template TValue
 */
trait MutationTrait
{
    /**
     * @return static<TValue>
     */
    final public function reversed(): static
    {
        return new static(array_reverse($this->items, true));
    }

    /**
     * @return static<TValue>
     */
    final public function shuffle(): static
    {
        $items = $this->items;
        shuffle($items);

        return new static($items);
    }
}
