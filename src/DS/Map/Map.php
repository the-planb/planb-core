<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Map;

use PlanB\Core\DS\Contract\GroupInterface;
use PlanB\Core\DS\Contract\MapInmutableInterface;
use PlanB\Core\DS\Contract\MutationInterface;
use PlanB\Core\DS\Contract\QueryInterface;
use PlanB\Core\DS\Contract\SetInterface;
use PlanB\Core\DS\Contract\SetKeyInterface;
use PlanB\Core\DS\Contract\SliceInterface;
use PlanB\Core\DS\Traits\GroupTrait;
use PlanB\Core\DS\Traits\MapInmutableTrait;
use PlanB\Core\DS\Traits\MutationTrait;
use PlanB\Core\DS\Traits\QueryTrait;
use PlanB\Core\DS\Traits\SetKeyTrait;
use PlanB\Core\DS\Traits\SetTrait;
use PlanB\Core\DS\Traits\SliceTrait;

/**
 * @template TValue
 *
 * @extends TypedMap<TValue>
 *
 * @implements MapInmutableInterface<array-key,TValue>
 * @implements GroupInterface<array-key,TValue>
 * @implements MutationInterface<array-key,TValue>
 * @implements QueryInterface<array-key,TValue>
 * @implements SetInterface<array-key,TValue>
 * @implements SetKeyInterface<array-key,TValue>
 * @implements SliceInterface<array-key,TValue>
 */
final class Map extends TypedMap implements MapInmutableInterface, GroupInterface, MutationInterface, QueryInterface, SetInterface, SetKeyInterface, SliceInterface
{
    /** @use MapInmutableTrait<array-key,TValue> */
    use MapInmutableTrait;

    /** @use GroupTrait<array-key,TValue> */
    use GroupTrait;

    /** @use MutationTrait<array-key,TValue> */
    use MutationTrait;

    /** @use QueryTrait<array-key,TValue> */
    use QueryTrait;

    /** @use SetTrait<array-key,TValue> */
    use SetTrait;

    /** @use SetKeyTrait<array-key,TValue> */
    use SetKeyTrait;

    /** @use SliceTrait<array-key,TValue> */
    use SliceTrait;

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
