<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\Vector;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PlanB\Core\DS\Collection;
use PlanB\Core\DS\Contract\MutationInterface;
use PlanB\Core\DS\Traits\MutationTrait;
use PlanB\Core\DS\Vector\Vector;
use PlanB\Core\Tests\Unit\DS\Traits\MutationTestCase;

/**
 * @internal
 */
#[CoversClass(Vector::class)]
#[CoversTrait(MutationTrait::class)]
final class VectorMutationTest extends MutationTestCase
{
    protected function createCollection(array $items): Collection&MutationInterface
    {
        return Vector::collect($items);
    }
}
