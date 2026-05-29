<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\Vector;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PlanB\Core\DS\Collection;
use PlanB\Core\DS\Contract\SliceInterface;
use PlanB\Core\DS\Traits\SliceTrait;
use PlanB\Core\DS\Vector\Vector;
use PlanB\Core\Tests\Unit\DS\Traits\SliceTestCase;

/**
 * @internal
 */
#[CoversClass(Vector::class)]
#[CoversTrait(SliceTrait::class)]
final class VectorSliceTest extends SliceTestCase
{
    protected function createCollection(array $items): Collection&SliceInterface
    {
        return Vector::collect($items);
    }
}
