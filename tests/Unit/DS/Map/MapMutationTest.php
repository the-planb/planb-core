<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\Map;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PlanB\Core\DS\Collection;
use PlanB\Core\DS\Contract\MutationInterface;
use PlanB\Core\DS\Map\Map;
use PlanB\Core\DS\Traits\MapMutableTrait;
use PlanB\Core\Tests\Unit\DS\Traits\MutationTestCase;

/**
 * @internal
 */
#[CoversClass(Map::class)]
#[CoversTrait(MapMutableTrait::class)]
final class MapMutationTest extends MutationTestCase
{
    protected function createCollection(array $items): Collection&MutationInterface
    {
        return Map::collect($items);
    }
}
