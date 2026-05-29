<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Traits;

use PlanB\Core\DS\Contract\SliceInterface;

/**
 * @phpstan-require-implements SliceInterface<TKey, TValue>
 *
 * @template TKey of string|int
 * @template TValue
 */
trait SliceTrait
{
    final public function take(int $limit): static
    {
        $items = array_slice($this->items, 0, $limit, true);

        return new static($items);
    }

    final public function takeLast(int $limit): static
    {
        if ($limit <= 0) {
            return new static();
        }

        $items = array_slice($this->items, -$limit, null, true);

        return new static($items);
    }

    final public function takeWhile(callable $condition): static
    {
        $result = [];
        foreach ($this->items as $key => $value) {
            if (!$condition($value, $key)) {
                break;
            }
            $result[$key] = $value;
        }

        return new static($result);
    }

    final public function takeLastWhile(callable $condition): static
    {
        $result = [];
        // Invertimos el array para evaluar desde el final sin destruir las claves
        $reversed = array_reverse($this->items, true);

        foreach ($reversed as $key => $value) {
            if (!$condition($value, $key)) {
                break;
            }
            $result[$key] = $value;
        }

        // Devolvemos los elementos filtrados a su orden cronológico original
        /** @var static<TValue> */
        return new static(array_reverse($result, true));
    }

    final public function drop(int $limit): static
    {
        $items = array_slice($this->items, $limit, null, true);

        return new static($items);
    }

    final public function dropLast(int $limit): static
    {
        if ($limit <= 0) {
            return new static($this->items);
        }

        $items = array_slice($this->items, 0, -$limit, true);

        return new static($items);
    }

    final public function dropWhile(callable $condition): static
    {
        $items = $this->items;
        foreach ($items as $key => $value) {
            if (!$condition($value, $key)) {
                break;
            }
            unset($items[$key]);
        }

        return new static($items);
    }

    final public function dropLastWhile(callable $condition): static
    {
        $items = $this->items;
        $reversed = array_reverse($items, true);

        foreach ($reversed as $key => $value) {
            if (!$condition($value, $key)) {
                break;
            }
            unset($items[$key]);
        }

        return new static($items);
    }
}
