<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Contract;

/**
 * @template TKey of array-key
 * @template TValue
 */
interface QueryInterface
{
    /**
     * Comprueba si la colección contiene exactamente el número de elementos especificado.
     */
    public function hasCount(int $total): bool;

    /**
     * Comprueba si la colección contiene al menos un elemento.
     */
    public function isNotEmpty(): bool;

    /**
     * Verifica si el valor dado existe en la colección.
     *
     * @param TValue $value
     */
    public function contains(mixed $value): bool;

    /**
     * Verifica si al menos un elemento cumple con la condición dada.
     *
     * @param callable(TValue, TKey): bool $condition
     */
    public function some(callable $condition): bool;

    /**
     * Verifica si todos los elementos cumplen con la condición dada.
     *
     * @param callable(TValue, TKey): bool $condition
     */
    public function every(callable $condition): bool;
}
