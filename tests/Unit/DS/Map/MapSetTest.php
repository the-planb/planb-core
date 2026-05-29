<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\Map;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PlanB\Core\DS\Collection;
use PlanB\Core\DS\Contract\SetInterface;
use PlanB\Core\DS\Map\Map;
use PlanB\Core\DS\Traits\SetTrait;
use PlanB\Core\Tests\Unit\DS\Traits\SetTestCase;

/**
 * @internal
 */
#[CoversClass(Map::class)]
#[CoversTrait(SetTrait::class)]
final class MapSetTest extends SetTestCase
{
    protected function createCollection(array $items): Collection&SetInterface
    {
        return Map::collect($items);
    }
}
