<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Contract;

/**
 * @template TKey of array-key
 * @template TValue
 */
interface SetKeyInterface
{
    /**
     * Devuelve una nueva colección con las entradas cuyas claves no estén presentes en la colección dada.
     *
     * @param iterable<array-key, mixed> $values
     */
    public function diffKeys(iterable $values): static;

    /**
     * Devuelve una nueva colección con las entradas cuyas claves también estén presentes en la colección dada.
     *
     * @param iterable<array-key, mixed> $values
     */
    public function intersectKeys(iterable $values): static;
}
