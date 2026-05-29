<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Vector;

/**
 * @template TValue
 *
 * @extends TypedVector<TValue>
 */
final class Vector extends TypedVector
{
    /**
     * @template TInputKey of array-key
     * @template TInputValue
     *
     * @param iterable<TInputKey, TInputValue>               $input
     * @param null|(callable(mixed, array-key): TInputValue) $normalizer
     *
     * @return static<TInputValue>
     */
    #[\Override]
    public static function collect(iterable $input = [], ?callable $normalizer = null): static
    {
        $items = self::ensureItems($input, $normalizer);

        /** @var static<TInputValue> $instance */
        $instance = new self($items);

        return $instance;
    }
}
