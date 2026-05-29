<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Contract;

/**
 * @template TKey of string|int
 * @template TValue
 */
interface SliceInterface
{
    /**
     * Toma los primeros N elementos de la colección.
     *
     * @return static<TKey, TValue>
     */
    public function take(int $limit): static;

    /**
     * Toma los últimos N elementos de la colección.
     *
     * @return static<TKey, TValue>
     */
    public function takeLast(int $limit): static;

    /**
     * Toma elementos desde el principio mientras se cumpla la condición.
     *
     * @param callable(TValue, TKey): bool $condition
     *
     * @return static<TKey, TValue>
     */
    public function takeWhile(callable $condition): static;

    /**
     * Toma elementos desde el final mientras se cumpla la condición.
     *
     * @param callable(TValue, TKey): bool $condition
     *
     * @return static<TKey, TValue>
     */
    public function takeLastWhile(callable $condition): static;

    /**
     * Descarta los primeros N elementos de la colección y devuelve el resto.
     *
     * @return static<TKey, TValue>
     */
    public function drop(int $limit): static;

    /**
     * Descarta los últimos N elementos de la colección y devuelve el resto.
     *
     * @return static<TKey, TValue>
     */
    public function dropLast(int $limit): static;

    /**
     * Descarta elementos desde el principio mientras se cumpla la condición.
     *
     * @param callable(TValue, TKey): bool $condition
     *
     * @return static<TKey, TValue>
     */
    public function dropWhile(callable $condition): static;

    /**
     * Descarta elementos desde el final mientras se cumpla la condición.
     *
     * @param callable(TValue, TKey): bool $condition
     *
     * @return static<TKey, TValue>
     */
    public function dropLastWhile(callable $condition): static;
}
