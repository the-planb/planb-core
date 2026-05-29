<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\Vector;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PlanB\Core\DS\Collection;
use PlanB\Core\DS\Contract\QueryInterface;
use PlanB\Core\DS\Traits\QueryTrait;
use PlanB\Core\DS\Vector\Vector;
use PlanB\Core\Tests\Unit\DS\Traits\QueryTestCase;

/**
 * @internal
 */
#[CoversClass(Vector::class)]
#[CoversTrait(QueryTrait::class)]
final class VectorQueryTest extends QueryTestCase
{
    protected function createCollection(array $items): Collection&QueryInterface
    {
        return Vector::collect($items);
    }
}
