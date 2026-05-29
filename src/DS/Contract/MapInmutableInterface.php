<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Contract;

/**
 * @template TKey of string|int
 * @template TValue
 */
interface MapInmutableInterface
{
    /**
     * Añade o reemplaza un elemento bajo una clave específica de forma inmutable.
     *
     * @param TKey   $key
     * @param TValue $value
     */
    public function put(int|string $key, mixed $value): static;

    /**
     * Elimina una o varias claves de la colección de forma inmutable.
     *
     * @param TKey ...$keys
     */
    public function forget(int|string ...$keys): static;
}
