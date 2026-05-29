<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Contract;

/**
 * @template TValue
 */
interface VectorMutableInterface
{
    /**
     * Añade un elemento al inicio modificando la instancia actual.
     *
     * @param TValue $value
     *
     * @return $this
     */
    public function addAtStart(mixed $value): self;

    /**
     * Añade un elemento al final modificando la instancia actual.
     *
     * @param TValue $value
     *
     * @return $this
     */
    public function addAtEnd(mixed $value): self;

    /**
     * Añade un elemento en un índice específico modificando la instancia actual.
     *
     * @param TValue $value
     *
     * @return $this
     *
     * @throws \OutOfBoundsException
     */
    public function addAt(int $index, mixed $value): self;

    /**
     * Elimina el primer elemento modificando la instancia actual.
     *
     * @return $this
     */
    public function deleteFromStart(): self;

    /**
     * Elimina el último elemento modificando la instancia actual.
     *
     * @return $this
     */
    public function deleteFromEnd(): self;

    /**
     * Elimina un elemento por su índice modificando la instancia actual.
     *
     * @return $this
     *
     * @throws \OutOfBoundsException
     */
    public function deleteAt(int $index): self;

    /**
     * Vacía por completo todos los elementos modificando la instancia actual.
     *
     * @return $this
     */
    public function clear(): self;
}
