<?php

declare(strict_types=1);

use PlanB\Core\DS\Map\Map;
use PlanB\Core\DS\Vector\Vector;

/**
 * @template TInputValue
 *
 * @param iterable<array-key, TInputValue>               $input
 * @param null|(callable(mixed, array-key): TInputValue) $normalizer
 *
 * @return Vector<TInputValue>
 */
function vector(iterable $input, ?callable $normalizer = null): Vector
{
    return Vector::collect($input, $normalizer);
}

/**
 * @template TInputKey of array-key
 * @template TInputValue
 *
 * @param iterable<TInputKey, TInputValue>               $input
 * @param null|(callable(mixed, array-key): TInputValue) $normalizer
 * @param null|(callable(mixed, array-key): array-key)   $keyNormalizer
 *
 * @return Map<TInputValue>
 */
function map(iterable $input, ?callable $normalizer = null, ?callable $keyNormalizer = null): Map
{
    return Map::collect($input, $normalizer, $keyNormalizer);
}
