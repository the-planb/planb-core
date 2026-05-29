<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Contract;

/**
 * @template TKey of array-key
 * @template TValue
 */
interface SetInterface
{
    /**
     * Devuelve los elementos de esta colección que no estén presentes en la colección dada.
     *
     * @param iterable<array-key, TValue>          $values
     * @param null|(callable(TValue, TValue): int) $comparator
     */
    public function diff(iterable $values, ?callable $comparator = null): static;

    /**
     * Devuelve los elementos de esta colección que también estén presentes en la colección dada.
     *
     * @param iterable<array-key, TValue>          $values
     * @param null|(callable(TValue, TValue): int) $comparator
     */
    public function intersect(iterable $values, ?callable $comparator = null): static;

    /**
     * Filtra la colección eliminando los elementos duplicados.
     *
     * @param null|(callable(TValue): mixed) $keyExtractor
     */
    public function unique(?callable $keyExtractor = null): static;
}
