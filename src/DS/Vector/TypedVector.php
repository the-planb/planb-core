<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Vector;

use PlanB\Core\DS\Collection;
use PlanB\Core\DS\Contract\BasicInterface;
use PlanB\Core\DS\Traits\BasicTrait;

/**
 * @template TValue
 *
 * @extends Collection<int, TValue>
 *
 * @implements BasicInterface<int, TValue>
 */
abstract class TypedVector extends Collection implements BasicInterface
{
    /** @use BasicTrait<int, TValue> */
    use BasicTrait;

    /**
     * @param iterable<array-key, mixed> $items
     */
    protected function __construct(iterable $items = [])
    {
        $items = is_array($items) ? $items : iterator_to_array($items);
        parent::__construct(array_values($items));
    }

    /**
     * Crea una nueva instancia de la colección validando y normalizando los elementos.
     *
     * @param iterable<array-key, mixed>                $input
     * @param null|(callable(mixed, array-key): TValue) $normalizer
     *
     * @return static<TValue>
     */
    public static function collect(iterable $input = [], ?callable $normalizer = null): static
    {
        $instance = new static();

        $normalizer ??= $instance->normalizeValue(...);
        $items = self::ensureItems($input, $normalizer);

        /** @var static<TValue> $instance */
        $instance = new static($items);

        return $instance;
    }

    /**
     * @template TOutput
     *
     * @param callable(TValue, int): TOutput $callback
     *
     * @return Vector<TOutput>
     */
    final public function map(callable $callback): Vector
    {
        $keys = array_keys($this->items);
        $mapped = array_map($callback, $this->items, $keys);

        /** @var Vector<TOutput> */
        return Vector::collect($mapped);
    }

    /**
     * Obtiene el elemento en el índice especificado o devuelve un valor por defecto si no existe.
     *
     * @template TDefault
     *
     * @param TDefault $default
     *
     * @return TDefault|TValue
     */
    final public function get(int $index, mixed $default = null): mixed
    {
        if (!array_key_exists($index, $this->items)) {
            return $default;
        }

        return $this->items[$index];
    }

    final public function has(int $index): bool
    {
        return array_key_exists($index, $this->items);
    }

    /**
     * @param TValue $value
     */
    final public function indexOf(mixed $value): ?int
    {
        $index = array_search($value, $this->items, true);

        return $index !== false ? $index : null;
    }

    /**
     * @param TValue $value
     *
     * @return TValue
     */
    protected function normalizeValue(mixed $value, int|string $key): mixed
    {
        return $value;
    }

    #[\Override]
    protected function updateItems(array $items): static
    {
        $items = self::ensureItems(
            $items,
            $this->normalizeValue(...),
        );

        return parent::updateItems($items);
    }
}
