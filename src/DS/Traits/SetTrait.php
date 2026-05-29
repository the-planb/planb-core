<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Traits;

use PlanB\Core\DS\Contract\SetInterface;

/**
 * @phpstan-require-implements SetInterface
 *
 * @template TKey of string|int
 * @template TValue
 */
trait SetTrait
{
    final public function diff(iterable $values, ?callable $comparator = null): static
    {
        $array = is_array($values) ? $values : iterator_to_array($values);

        if ($comparator !== null) {
            $result = array_udiff($this->items, $array, $comparator);
        } else {
            $result = array_filter($this->items, fn (mixed $item): bool => !in_array($item, $array, true));
        }

        return static::collect($result);
    }

    final public function intersect(iterable $values, ?callable $comparator = null): static
    {
        $array = is_array($values) ? $values : iterator_to_array($values);

        if ($comparator !== null) {
            $result = array_uintersect($this->items, $array, $comparator);
        } else {
            $result = array_filter($this->items, fn (mixed $item): bool => in_array($item, $array, true));
        }

        return static::collect($result);
    }

    final public function unique(?callable $keyExtractor = null): static
    {
        if ($keyExtractor === null) {
            return static::collect(array_unique($this->items, SORT_REGULAR));
        }

        $result = [];
        $seenKeys = [];

        foreach ($this->items as $key => $value) {
            $hashKey = $keyExtractor($value);

            if (!is_scalar($hashKey) && !($hashKey instanceof \Stringable)) {
                throw new \InvalidArgumentException(sprintf(
                    'El callback $keyExtractor debe devolver un valor escalar (string, int, float, bool) o Stringable, se obtuvo: "%s".',
                    get_debug_type($hashKey),
                ));
            }

            if (!in_array($hashKey, $seenKeys)) {
                $seenKeys[] = $hashKey;
                $result[$key] = $value;
            }
        }

        return static::collect($result);
    }
}
