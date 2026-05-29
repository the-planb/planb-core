<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\Map;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PlanB\Core\DS\Collection;
use PlanB\Core\DS\Contract\GroupInterface;
use PlanB\Core\DS\Map\Map;
use PlanB\Core\DS\Traits\GroupTrait;
use PlanB\Core\Tests\Unit\DS\Traits\GroupTestCase;

/**
 * @internal
 */
#[CoversClass(Map::class)]
#[CoversTrait(GroupTrait::class)]
final class MapGroupTest extends GroupTestCase
{
    protected function createCollection(array $items): Collection&GroupInterface
    {
        return Map::collect($items);
    }
}
