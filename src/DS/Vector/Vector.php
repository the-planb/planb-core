<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Vector;

use PlanB\Core\DS\Contract\GroupInterface;
use PlanB\Core\DS\Contract\VectorInmutableInterface;
use PlanB\Core\DS\Traits\GroupTrait;
use PlanB\Core\DS\Traits\VectorInmutableTrait;

/**
 * @template TValue
 *
 * @extends TypedVector<TValue>
 *
 * @implements VectorInmutableInterface<TValue>
 * @implements GroupInterface<int, TValue>
 */
final class Vector extends TypedVector implements VectorInmutableInterface, GroupInterface
{
    /** @use VectorInmutableTrait<TValue> */
    use VectorInmutableTrait;

    /** @use GroupTrait<int, TValue> */
    use GroupTrait;

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
