<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\Map;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\Test;
use PlanB\Core\DS\Collection;
use PlanB\Core\DS\Contract\SliceInterface;
use PlanB\Core\DS\Map\Map;
use PlanB\Core\DS\Traits\SliceTrait;
use PlanB\Core\Tests\Unit\DS\Traits\SliceTestCase;

/**
 * @internal
 */
#[CoversClass(Map::class)]
#[CoversTrait(SliceTrait::class)]
final class MapSliceTest extends SliceTestCase
{
    #[Test]
    public function test_map_preserves_associative_keys_fidelty_after_slice(): void
    {
        $map = Map::collect(['u1' => 'Alice', 'u2' => 'Bob', 'u3' => 'Charlie']);

        $result = $map->take(2);

        // Verificamos que las claves string originales se mantienen intactas en el Map
        $this->assertSame(['u1', 'u2'], array_keys($result->toArray()));
    }

    protected function createCollection(array $items): Collection&SliceInterface
    {
        return Map::collect($items);
    }
}
