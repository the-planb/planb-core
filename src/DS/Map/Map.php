<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Map;

use PlanB\Core\DS\Contract\MapInmutableInterface;
use PlanB\Core\DS\Traits\MapInmutableTrait;

/**
 * @template TValue
 *
 * @extends TypedMap<TValue>
 *
 * @implements MapInmutableInterface<array-key,TValue>
 */
final class Map extends TypedMap implements MapInmutableInterface
{
    /** @use MapInmutableTrait<array-key,TValue> */
    use MapInmutableTrait;

    /**
     * @template TInputKey of array-key
     * @template TInputValue
     *
     * @param iterable<TInputKey, TInputValue>               $input
     * @param null|(callable(mixed, array-key): TInputValue) $normalizer
     * @param null|(callable(mixed, array-key): array-key)   $keyNormalizer
     *
     * @return static<TInputValue>
     */
    #[\Override]
    public static function collect(iterable $input = [], ?callable $normalizer = null, ?callable $keyNormalizer = null): static
    {
        $items = self::ensureItems($input, $normalizer, $keyNormalizer);

        /** @var static<TInputValue> $instance */
        $instance = new self($items);

        return $instance;
    }
}
