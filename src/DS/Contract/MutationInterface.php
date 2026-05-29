<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Contract;

use PlanB\Core\DS\Collection;

/**
 * @phpstan-require-extends Collection
 *
 * @template TKey of array-key
 * @template TValue
 */
interface MutationInterface
{
    /**
     * Devuelve una nueva colección con los elementos en orden inverso.
     *
     * @return static<TKey, TValue>
     */
    public function reversed(): static;

    /**
     * Devuelve una nueva colección con los elementos aleatorios.
     *
     * @return static<TKey, TValue>
     */
    public function shuffle(): static;
}
