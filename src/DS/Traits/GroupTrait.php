<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Traits;

use PlanB\Core\DS\Collection;
use PlanB\Core\DS\Contract\GroupInterface;
use PlanB\Core\DS\Map\Map;
use PlanB\Core\DS\Vector\Vector;

/**
 * @phpstan-require-extends Collection
 *
 * @phpstan-require-implements  GroupInterface
 *
 * @template TKey of array-key
 * @template TValue
 */
trait GroupTrait
{
    final public function partition(callable $condition): Vector
    {
        $matches = [];
        $noMatches = [];

        foreach ($this->items as $key => $value) {
            if ($condition($value, $key)) {
                $matches[] = $value;
            } else {
                $noMatches[] = $value;
            }
        }

        /** @var Vector<Vector<TValue>> */
        return Vector::collect([
            Vector::collect($matches),
            Vector::collect($noMatches),
        ]);
    }

    final public function chunk(int $size): Vector
    {
        if ($size <= 0) {
            throw new \InvalidArgumentException('Chunk size must be greater than zero.');
        }

        $chunks = array_chunk($this->items, $size);

        $vectorChunks = [];
        foreach ($chunks as $chunk) {
            $vectorChunks[] = Vector::collect($chunk);
        }

        /** @var Vector<Vector<TValue>> */
        return Vector::collect($vectorChunks);
    }

    final public function groupBy(callable $grouper): Map
    {
        $groups = [];

        foreach ($this->items as $key => $value) {
            $groupKey = $grouper($value, $key);
            $groups[$groupKey][] = $value;
        }

        $mapGroups = [];
        foreach ($groups as $groupKey => $items) {
            $mapGroups[$groupKey] = Vector::collect($items);
        }

        /** @var Map<Vector<TValue>> */
        return Map::collect($mapGroups);
    }
}
