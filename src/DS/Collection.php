<?php

declare(strict_types=1);

namespace PlanB\Core\DS;

use PlanB\Core\DS\Attribute\ElementType;

/**
 * @phpstan-consistent-constructor
 *
 * @template TKey of array-key
 * @template TValue
 *
 * @implements \IteratorAggregate<TKey, TValue>
 */
abstract class Collection implements \Countable, \IteratorAggregate, \JsonSerializable
{
    /** @var array<TKey, TValue> */
    protected private(set) array $items = [];

    /** @var array<string, array<string>> */
    protected private(set) static array $allowedTypes = [];

    /**
     * Inicializa la colección con un conjunto de elementos.
     *
     * @param iterable<TKey, TValue> $items
     */
    protected function __construct(iterable $items = [])
    {
        $this->items = is_array($items) ? $items : iterator_to_array($items);
    }

    /**
     * Devuelve el número total de elementos en la colección.
     */
    final public function count(): int
    {
        return count($this->items);
    }

    /**
     * Devuelve un iterador externo para recorrer los elementos de la colección.
     *
     * @return \Traversable<TKey, TValue>
     */
    final public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * Devuelve los datos que deben ser serializados a JSON.
     *
     * @return array<TKey, TValue>
     */
    final public function jsonSerialize(): array
    {
        return $this->items;
    }

    /**
     * Devuelve la representación interna de la colección en forma de array nativo.
     *
     * @return array<TKey, TValue>
     */
    final public function toArray(): array
    {
        return $this->items;
    }

    /**
     * Ejecuta una función de transformación sobre la propia instancia de la colección.
     *
     * @template TReturn
     *
     * @param callable(static): TReturn $transformer
     *
     * @return TReturn
     */
    final public function applyTo(callable $transformer): mixed
    {
        return $transformer($this);
    }

    /**
     * Extrae y cachea mediante reflexión los tipos permitidos definidos en el atributo ElementType.
     *
     * @return string[]
     */
    public static function allowedTypes(): array
    {
        $class = static::class;

        if (isset(self::$allowedTypes[$class])) {
            return self::$allowedTypes[$class];
        }

        $reflection = new \ReflectionClass($class);
        $attributes = $reflection->getAttributes(ElementType::class);

        if ($attributes === []) {
            self::$allowedTypes[$class] = [];

            return [];
        }

        /** @var ElementType $attributeInstance */
        $attributeInstance = $attributes[0]->newInstance();
        self::$allowedTypes[$class] = $attributeInstance->types;

        return self::$allowedTypes[$class];
    }

    /**
     * Permite a los contextos autorizados o traits mutar el estado interno de los elementos de forma controlada.
     *
     * @param array<TKey, TValue> $items
     */
    protected function updateItems(array $items): static
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Valida y normaliza un conjunto de elementos contrastándolos contra las restricciones de tipo.
     *
     * @param iterable<array-key, mixed>                   $input
     * @param null|(callable(mixed, array-key): mixed)     $normalizer
     * @param null|(callable(mixed, array-key): array-key) $keyNormalizer
     *
     * @return array<TKey, TValue>
     */
    protected static function ensureItems(iterable $input, ?callable $normalizer = null, ?callable $keyNormalizer = null): array
    {
        $normalizer ??= fn (mixed $value, int|string $key) => $value;
        $keyNormalizer ??= fn (mixed $value, int|string $key) => $key;

        $processed = [];
        foreach ($input as $key => $value) {
            /** @var array-key $newKey */
            $newKey = $keyNormalizer($value, $key);
            $newValue = $normalizer($value, $newKey);
            $processed[$newKey] = $newValue;
        }

        assert_all_of_type($processed, ...static::allowedTypes());

        /** @var array<TKey, TValue> $processed */
        return $processed;
    }
}
