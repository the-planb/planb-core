<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Contract;

use PlanB\Core\DS\Map\Map;
use PlanB\Core\DS\Vector\Vector;

/**
 * @template TKey of array-key
 * @template TValue
 */
interface GroupInterface
{
    /**
     * Divide la colección en dos nuevas colecciones basándose en una condición booleana.
     * Retorna un array con dos instancias: [0 => los que cumplen, 1 => los que no cumplen].
     *
     * @param callable(TValue, TKey): bool $condition
     *
     * @return Vector<Vector<TValue>>
     */
    public function partition(callable $condition): Vector;

    /**
     * Divide la colección en fragmentos de un tamaño específico.
     *
     * @return Vector<Vector<TValue>>
     */
    public function chunk(int $size): Vector;

    /**
     * Agrupa los elementos de la colección basándose en un selector de clave.
     *
     * @param callable(TValue, TKey): (int|string) $grouper
     *
     * @return Map<Vector<TValue>>
     */
    public function groupBy(callable $grouper): Map;
}
