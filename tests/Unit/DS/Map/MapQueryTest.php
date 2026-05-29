<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\Map;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PlanB\Core\DS\Collection;
use PlanB\Core\DS\Contract\QueryInterface;
use PlanB\Core\DS\Map\Map;
use PlanB\Core\DS\Traits\QueryTrait;
use PlanB\Core\Tests\Unit\DS\Traits\QueryTestCase;

/**
 * @internal
 */
#[CoversClass(Map::class)]
#[CoversTrait(QueryTrait::class)]
final class MapQueryTest extends QueryTestCase
{
    protected function createCollection(array $items): Collection&QueryInterface
    {
        return Map::collect($items);
    }
}
