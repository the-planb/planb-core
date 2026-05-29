<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Map;

use PlanB\Core\DS\Collection;
use PlanB\Core\DS\Contract\BasicInterface;
use PlanB\Core\DS\Traits\BasicTrait;
use PlanB\Core\DS\Vector\Vector;

/**
 * @template TValue
 *
 * @extends Collection<array-key, TValue>
 *
 * @implements BasicInterface<array-key, TValue>
 */
abstract class TypedMap extends Collection implements BasicInterface
{
    /** @use BasicTrait<array-key, TValue> */
    use BasicTrait;

    /**
     * @param iterable<array-key, mixed> $items
     */
    protected function __construct(iterable $items = [])
    {
        parent::__construct($items);
    }

    /**
     * Crea una nueva instancia de la colección validando y normalizando los elementos.
     *
     * @param iterable<array-key, mixed>                   $input
     * @param null|(callable(mixed, array-key): TValue)    $normalizer
     * @param null|(callable(mixed, array-key): array-key) $keyNormalizer
     *
     * @return static< TValue>
     */
    public static function collect(iterable $input = [], ?callable $normalizer = null, ?callable $keyNormalizer = null): static
    {
        $instance = new static();

        $keyNormalizer ??= $instance->normalizeKey(...);
        $normalizer ??= $instance->normalizeValue(...);
        $items = self::ensureItems($input, $normalizer, $keyNormalizer);

        /** @var static< TValue> $instance */
        $instance = new static($items);

        return $instance;
    }

    /**
     * @template TOutput
     *
     * @param callable(TValue, array-key): TOutput $callback
     *
     * @return Map<TOutput>
     */
    final public function map(callable $callback): Map
    {
        $keys = array_keys($this->items);
        $mapped = array_map($callback, $this->items, $keys);

        /** @var Map<TOutput> */
        return new Map($mapped);
    }

    /**
     * @template TOutput
     *
     * @param callable(TValue, array-key): iterable<array-key, TOutput> $callback
     *
     * @return Map<TOutput>
     */
    final public function flatMap(callable $callback): Map
    {
        $result = [];
        foreach ($this->items as $key => $value) {
            foreach ($callback($value, $key) as $mapKey => $mapValue) {
                $result[$mapKey] = $mapValue;
            }
        }

        /** @var Map<TOutput> */
        return new Map($result);
    }

    /**
     * Obtiene el elemento asociado a la clave especificada o devuelve un valor por defecto si no existe.
     *
     * @template TDefault
     *
     * @param array-key $key
     * @param TDefault  $default
     *
     * @return TDefault|TValue
     */
    final public function get(int|string $key, mixed $default = null): mixed
    {
        if (!array_key_exists($key, $this->items)) {
            return $default;
        }

        return $this->items[$key];
    }

    /**
     * Comprueba si la clave especificada existe en el mapa.
     */
    final public function has(int|string $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * Busca el valor dado y devuelve su clave correspondiente, o null si no lo encuentra.
     *
     * @param TValue $value
     *
     * @return null|array-key
     */
    final public function findKey(mixed $value): int|string|null
    {
        $key = array_search($value, $this->items, true);

        return $key !== false ? $key : null;
    }

    /**
     * Devuelve un Vector con las claves.
     *
     * @return Vector<array-key>
     */
    public function keys(): Vector
    {
        return Vector::collect(array_keys($this->items));
    }

    /**
     * Devuelve un Vector con los valores.
     *
     * @return Vector<TValue>
     */
    public function values(): Vector
    {
        return Vector::collect($this->items);
    }

    /**
     * @param TValue    $value
     * @param array-key $key
     *
     * @return array-key
     */
    protected function normalizeKey(mixed $value, int|string $key): int|string
    {
        return $key;
    }

    /**
     * @param TValue    $value
     * @param array-key $key
     *
     * @return TValue
     */
    protected function normalizeValue(mixed $value, int|string $key): mixed
    {
        return $value;
    }

    /**
     * Permite a los contextos autorizados o traits mutar el estado interno de los elementos de forma controlada.
     *
     * @param array<array-key, TValue> $items
     */
    #[\Override]
    protected function updateItems(array $items): static
    {
        $items = self::ensureItems(
            $items,
            $this->normalizeValue(...),
            $this->normalizeKey(...),
        );

        return parent::updateItems($items);
    }
}
