<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Contract;

use PlanB\Core\DS\Vector\Vector;

/**
 * @template TValue
 */
interface VectorInmutableInterface
{
    /**
     * Añade uno o más elementos al inicio de la colección de forma inmutable.
     *
     * @param TValue ...$values
     *
     * @return static<TValue>
     */
    public function insertAtStart(mixed ...$values): static;

    /**
     * Añade uno o más elementos al final de la colección de forma inmutable.
     *
     * @param TValue ...$values
     *
     * @return static<TValue>
     */
    public function insertAtEnd(mixed ...$values): static;

    /**
     * Inserta elementos en una posición específica desplazando los existentes de forma inmutable.
     *
     * @param TValue ...$values
     *
     * @return static<TValue>
     *
     * @throws \InvalidArgumentException|\OutOfBoundsException
     */
    public function insertAt(int $index, mixed ...$values): static;

    /**
     * Elimina el primer elemento de la colección de forma inmutable.
     *
     * @return static<TValue>
     */
    public function removeFromStart(): static;

    /**
     * Elimina el último elemento de la colección de forma inmutable.
     *
     * @return static<TValue>
     */
    public function removeFromEnd(): static;

    /**
     * Elimina los elementos de los índices indicados de forma inmutable.
     *
     * @return static<TValue>
     */
    public function removeAt(int ...$indexes): static;

    /**
     * Combina los elementos de este vector con los de otros iterables en los mismos índices.
     * El proceso se detiene en cuanto la colección más corta se agota.
     *
     * @param iterable<array-key, mixed> ...$collections
     *
     * @return Vector<array<int, mixed>>
     */
    public function zip(iterable ...$collections): Vector;

    /**
     * Aplana un vector bidimensional o multidimensional de elementos en un único vector plano.
     *
     * @return Vector<mixed>
     */
    public function flatten(): Vector;
}
