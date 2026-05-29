<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Contract;

/**
 * @template TKey of string|int
 * @template TValue
 */
interface MapMutableInterface
{
    /**
     * Establece o reemplaza un valor modificando la instancia actual.
     *
     * @param TKey   $key
     * @param TValue $value
     *
     * @return $this
     */
    public function set(int|string $key, mixed $value): self;

    /**
     * Elimina una clave modificando la instancia actual.
     *
     * @param TKey $key
     *
     * @return $this
     */
    public function delete(int|string $key): self;

    /**
     * Vacía por completo todos los elementos modificando la instancia actual.
     *
     * @return $this
     */
    public function clear(): self;
}
