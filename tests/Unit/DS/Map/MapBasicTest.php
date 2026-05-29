<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\Map;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\Test;
use PlanB\Core\DS\Collection;
use PlanB\Core\DS\Contract\BasicInterface;
use PlanB\Core\DS\Map\Map;
use PlanB\Core\DS\Map\TypedMap;
use PlanB\Core\DS\Traits\BasicTrait;
use PlanB\Core\Tests\Unit\DS\Traits\BasicTestCase;

class MapMock extends TypedMap
{
    public static function trust(iterable $items = []): static
    {
        return new static($items);
    }

    #[\Override]
    protected function normalizeValue(mixed $value, int|string $key): mixed
    {
        return parent::normalizeValue(ord($value), $key);
    }

    #[\Override]
    protected function normalizeKey(mixed $value, int|string $key): int|string
    {
        return parent::normalizeKey($value, strtolower((string) $value));
    }
}

/**
 * @internal
 */
#[CoversClass(Map::class)]
#[CoversTrait(BasicTrait::class)]
final class MapBasicTest extends BasicTestCase
{
    #[Test]
    public function test_map_can_be_created_using_an_iterable(): void
    {
        $data = new \ArrayIterator(['A', 'B', 'C']);
        $vector = MapMock::trust($data);

        $this->assertInstanceOf(TypedMap::class, $vector);
    }

    #[Test]
    public function test_vector_can_normalize_its_values(): void
    {
        $data = new \ArrayIterator(['A', 'B', 'C']);
        $vector = MapMock::collect($data);

        $this->assertEquals([65, 66, 67], array_values($vector->toArray()));
    }

    #[Test]
    public function test_vector_can_normalize_its_keys(): void
    {
        $data = new \ArrayIterator(['A', 'B', 'C']);
        $vector = MapMock::collect($data);

        $this->assertEquals(['a', 'b', 'c'], array_keys($vector->toArray()));
    }

    protected function createCollection(iterable $items): BasicInterface&Collection
    {
        return Map::collect($items);
    }
}
