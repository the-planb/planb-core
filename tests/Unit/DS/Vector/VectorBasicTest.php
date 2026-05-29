<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\Vector;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\Test;
use PlanB\Core\DS\Collection;
use PlanB\Core\DS\Contract\BasicInterface;
use PlanB\Core\DS\Traits\BasicTrait;
use PlanB\Core\DS\Vector\TypedVector;
use PlanB\Core\DS\Vector\Vector;
use PlanB\Core\Tests\Unit\DS\Traits\BasicTestCase;

class VectorMock extends TypedVector
{
    public static function trust(iterable $items = []): static
    {
        return new static($items);
    }

    #[\Override]
    protected function normalizeValue(mixed $value, int|string $key): mixed
    {
        return parent::normalizeValue($key, $key);
    }
}

/**
 * @internal
 */
#[CoversClass(TypedVector::class)]
#[CoversTrait(BasicTrait::class)]
final class VectorBasicTest extends BasicTestCase
{
    #[Test]
    public function test_vector_can_be_created_using_an_iterable(): void
    {
        $data = new \ArrayIterator(['A', 'B', 'C']);
        $vector = VectorMock::trust($data);

        $this->assertInstanceOf(TypedVector::class, $vector);
    }

    #[Test]
    public function test_vector_can_normalize_its_values(): void
    {
        $data = new \ArrayIterator(['A', 'B', 'C']);
        $vector = VectorMock::collect($data);

        $this->assertEquals([0, 1, 2], $vector->toArray());
    }

    protected function createCollection(iterable $items): BasicInterface&Collection
    {
        return Vector::collect($items);
    }
}
