<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Traits;

/**
 * @template TKey of array-key
 * @template TValue
 */
trait SetKeyTrait
{
    final public function diffKeys(iterable $values): static
    {
        $array = is_array($values) ? $values : iterator_to_array($values);

        return static::collect(array_diff_key($this->items, $array));
    }

    final public function intersectKeys(iterable $values): static
    {
        $array = is_array($values) ? $values : iterator_to_array($values);

        return static::collect(array_intersect_key($this->items, $array));
    }
}
