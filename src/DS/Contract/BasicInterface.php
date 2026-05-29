<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Contract;

/**
 * @template TKey of array-key
 * @template TValue
 */
interface BasicInterface
{
    /**
     * Filtra los elementos de la colección mediante un callback y devuelve una nueva instancia.
     *
     * @param null|callable(TValue, TKey): bool $condition
     *
     * @return static<TKey, TValue>
     */
    public function filter(?callable $condition = null): static;

    /**
     * Ordena los elementos de la colección mediante un comparador y devuelve una nueva instancia.
     *
     * @param null|(callable(TValue, TValue): int) $comparator
     *
     * @return static<TKey, TValue>
     */
    public function sort(?callable $comparator = null): static;

    /**
     * @template TOutput
     *
     * @param callable(TValue, array-key): TOutput $callback
     *
     * @return static<TKey, TOutput>
     */
    public function map(callable $callback): self;

    /**
     * Transforma los elementos manteniendo la instancia de la clase concreta actual.
     *
     * @param callable(TValue, int): TValue $callback
     *
     * @return static<TKey, TValue>
     */
    public function normalize(callable $callback): static;

    /**
     * Reduce la colección a un único valor utilizando una función acumuladora.
     *
     * @template TInitial
     *
     * @param callable(null|TInitial, TValue, TKey): TInitial $callback
     * @param null|TInitial                                   $initial
     *
     * @return null|TInitial
     */
    public function reduce(callable $callback, mixed $initial = null): mixed;

    /**
     * Extrae una porción de la colección y devuelve una nueva instancia.
     *
     * @return static<TKey, TValue>
     */
    public function slice(int $offset, ?int $length = null): static;

    /**
     * Comprueba si la colección está vacía.
     */
    public function isEmpty(): bool;
}
