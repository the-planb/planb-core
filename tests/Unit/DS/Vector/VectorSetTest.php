<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\Vector;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PlanB\Core\DS\Collection;
use PlanB\Core\DS\Contract\SetInterface;
use PlanB\Core\DS\Traits\SetTrait;
use PlanB\Core\DS\Vector\Vector;
use PlanB\Core\Tests\Unit\DS\Traits\SetTestCase;

/**
 * @internal
 */
#[CoversClass(Vector::class)]
#[CoversTrait(SetTrait::class)]
final class VectorSetTest extends SetTestCase
{
    protected function createCollection(array $items): Collection&SetInterface
    {
        return Vector::collect($items);
    }
}
