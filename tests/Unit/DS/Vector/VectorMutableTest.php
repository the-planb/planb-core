<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\Vector;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\DS\Contract\VectorMutableInterface;
use PlanB\Core\DS\Traits\VectorMutableTrait;
use PlanB\Core\DS\Vector\TypedVector;
use PlanB\Core\DS\Vector\Vector;

/**
 * @internal
 */
#[CoversClass(Vector::class)]
#[CoversTrait(VectorMutableTrait::class)]
final class VectorMutableTest extends TestCase
{
    #[Test]
    public function test_add_at_start_mutates_instance_by_prepending_an_element(): void
    {
        $vector = $this->createMutableVector(['B', 'C']);

        $result = $vector->addAtStart('A');

        $this->assertSame($vector, $result); // Mutación fluida (misma instancia)
        $this->assertSame(['A', 'B', 'C'], $vector->toArray());
    }

    #[Test]
    public function test_add_at_end_mutates_instance_by_appending_an_element(): void
    {
        $vector = $this->createMutableVector(['A', 'B']);

        $result = $vector->addAtEnd('C');

        $this->assertSame($vector, $result);
        $this->assertSame(['A', 'B', 'C'], $vector->toArray());
    }

    #[Test]
    public function test_add_at_mutates_instance_at_specific_index(): void
    {
        $vector = $this->createMutableVector(['A', 'C']);

        $result = $vector->addAt(1, 'B');

        $this->assertSame($vector, $result);
        $this->assertSame(['A', 'B', 'C'], $vector->toArray());
    }

    #[Test]
    public function test_add_at_mutates_instance_at_index_zero(): void
    {
        $vector = $this->createMutableVector(['A', 'C']);

        $result = $vector->addAt(0, 'B');

        $this->assertSame($vector, $result);
        $this->assertSame(['B', 'A', 'C'], $vector->toArray());
    }

    #[Test]
    #[DataProvider('invalidIndexProvider')]
    public function test_add_at_throws_exception_if_index_is_invalid(int $index): void
    {
        $vector = $this->createMutableVector(['A', 'B']);

        $this->expectException(\OutOfBoundsException::class);

        $vector->addAt($index, 'Z');
    }

    public static function invalidIndexProvider()
    {
        return [
            [-20],
            [-1],
            [2],
            [20],
        ];
    }

    #[Test]
    public function test_delete_from_start_mutates_instance_by_removing_first_item(): void
    {
        $vector = $this->createMutableVector(['A', 'B', 'C']);

        $result = $vector->deleteFromStart();

        $this->assertSame($vector, $result);
        $this->assertSame(['B', 'C'], $vector->toArray());
    }

    #[Test]
    public function test_delete_from_start_returns_the_same_instance_if_vector_is_empty(): void
    {
        $vector = $this->createMutableVector([]);

        $result = $vector->deleteFromStart();

        // Verificamos que al estar vacío no clona ni altera la referencia original ($this)
        $this->assertSame($vector, $result);
        $this->assertTrue($vector->isEmpty());
    }

    #[Test]
    public function test_delete_from_end_returns_the_same_instance_if_vector_is_empty(): void
    {
        $vector = $this->createMutableVector([]);

        $result = $vector->deleteFromEnd();

        $this->assertSame($vector, $result);
        $this->assertTrue($vector->isEmpty());
    }

    #[Test]
    public function test_delete_from_end_mutates_instance_by_removing_last_item(): void
    {
        $vector = $this->createMutableVector(['A', 'B', 'C']);

        $result = $vector->deleteFromEnd();

        $this->assertSame($vector, $result);
        $this->assertSame(['A', 'B'], $vector->toArray());
    }

    #[Test]
    public function test_delete_at_removes_element_by_index_in_place(): void
    {
        $vector = $this->createMutableVector(['A', 'B', 'C']);

        $result = $vector->deleteAt(1); // Eliminamos 'B'

        $this->assertSame($vector, $result);
        $this->assertSame(['A', 'C'], $vector->toArray());
    }

    #[Test]
    public function test_delete_at_throws_exception_if_the_index_does_not_exist(): void
    {
        $vector = $this->createMutableVector(['A', 'B']); // Índices válidos: 0 y 1

        $this->expectException(\OutOfBoundsException::class);

        // Intentamos borrar un índice inexistente
        $vector->deleteAt(5);
    }

    #[Test]
    public function test_clear_purges_everything_in_place(): void
    {
        $vector = $this->createMutableVector(['A', 'B']);

        $result = $vector->clear();

        $this->assertSame($vector, $result);
        $this->assertSame([], $vector->toArray());
    }

    /**
     * Factoría para instanciar un Vector que implemente el comportamiento mutable.
     * Cambia \PlanB\DS\Vector\TypedVector por \PlanB\DS\Vector\Vector si es tu clase base.
     *
     * @param array<int, mixed> $items
     */
    private function createMutableVector(array $items): VectorMutableInterface
    {
        return new class($items) extends TypedVector implements VectorMutableInterface {
            use VectorMutableTrait;

            /**
             * @param array<int, mixed> $items
             */
            public function __construct(array $items)
            {
                parent::__construct($items);
            }
        };
    }
}
