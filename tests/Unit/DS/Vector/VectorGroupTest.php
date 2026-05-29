<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\Vector;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PlanB\Core\DS\Collection;
use PlanB\Core\DS\Contract\GroupInterface;
use PlanB\Core\DS\Traits\GroupTrait;
use PlanB\Core\DS\Vector\Vector;
use PlanB\Core\Tests\Unit\DS\Traits\GroupTestCase;

/**
 * @internal
 */
#[CoversClass(Vector::class)]
#[CoversTrait(GroupTrait::class)]
final class VectorGroupTest extends GroupTestCase
{
    protected function createCollection(array $items): Collection&GroupInterface
    {
        return Vector::collect($items);
    }
}
