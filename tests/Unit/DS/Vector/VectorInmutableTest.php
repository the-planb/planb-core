<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\Vector;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\DS\Traits\VectorInmutableTrait;
use PlanB\Core\DS\Vector\Vector;

/**
 * @internal
 */
#[CoversClass(Vector::class)]
#[CoversTrait(VectorInmutableTrait::class)]
final class VectorInmutableTest extends TestCase
{
    #[Test]
    public function test_insert_at_start_prepends_multiple_elements_safely(): void
    {
        $vector = Vector::collect(['C', 'D']);

        $result = $vector->insertAtStart('A', 'B');

        $this->assertSame(['C', 'D'], $vector->toArray()); // Inmutabilidad
        $this->assertSame(['A', 'B', 'C', 'D'], $result->toArray());
    }

    #[Test]
    public function test_insert_at_throws_exception_if_no_values_are_provided(): void
    {
        $vector = Vector::collect(['A', 'B']);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('You must provide at least one value to insert.');

        // Pasamos el índice pero ningún argumento variádico subsiguiente
        $vector->insertAt(1);
    }

    #[Test]
    public function test_insert_at_end_appends_multiple_elements_safely(): void
    {
        $vector = Vector::collect(['A', 'B']);

        $result = $vector->insertAtEnd('C', 'D');

        $this->assertSame(['A', 'B'], $vector->toArray());
        $this->assertSame(['A', 'B', 'C', 'D'], $result->toArray());
    }

    #[Test]
    public function test_insert_at_places_elements_at_specific_index_and_shifts_the_rest(): void
    {
        $vector = Vector::collect(['A', 'C']);

        $result = $vector->insertAt(1, 'B');

        $this->assertSame(['A', 'B', 'C'], $result->toArray());
    }

    #[Test]
    public function test_insert_at_places_elements_at_the_first_index(): void
    {
        $vector = Vector::collect(['A', 'C']);

        $result = $vector->insertAt(0, 'B');

        $this->assertSame(['B', 'A', 'C'], $result->toArray());
    }

    #[Test]
    public function test_insert_at_places_elements_at_the_last_index(): void
    {
        $vector = Vector::collect(['A', 'C']);

        $result = $vector->insertAt(1, 'B');

        $this->assertSame(['A', 'B', 'C'], $result->toArray());
    }

    #[Test]
    #[DataProvider('invalidIndex')]
    public function test_insert_at_throws_exception_if_index_is_out_of_bounds(int $index): void
    {
        $vector = Vector::collect(['A', 'B']);

        $this->expectException(\OutOfBoundsException::class);

        $vector->insertAt($index, 'Z');
    }

    public static function invalidIndex()
    {
        return [
            [-10],
            [-1],
            [2],
            [20],
        ];
    }

    #[Test]
    public function test_remove_from_start_omits_first_element(): void
    {
        $vector = Vector::collect(['A', 'B', 'C']);

        $result = $vector->removeFromStart();

        $this->assertSame(['A', 'B', 'C'], $vector->toArray());
        $this->assertSame(['B', 'C'], $result->toArray());
    }

    #[Test]
    public function test_remove_from_start_returns_a_new_empty_instance_if_vector_is_empty(): void
    {
        $vector = Vector::collect([]);

        $result = $vector->removeFromStart();

        // Verificamos que devuelve una instancia vacía
        $this->assertTrue($result->isEmpty());
        $this->assertCount(0, $result);

        // Verificamos la inmutabilidad: no debe retornar el mismo espacio de memoria ($this)
        $this->assertNotSame($vector, $result);
        $this->assertInstanceOf(Vector::class, $result);
    }

    #[Test]
    public function test_remove_from_end_omits_last_element(): void
    {
        $vector = Vector::collect(['A', 'B', 'C']);

        $result = $vector->removeFromEnd();
        $this->assertSame(['A', 'B'], $result->toArray());
    }

    #[Test]
    public function test_remove_from_end_returns_a_new_empty_instance_if_vector_is_empty(): void
    {
        $vector = Vector::collect([]);

        $result = $vector->removeFromEnd();

        $this->assertTrue($result->isEmpty());
        $this->assertCount(0, $result);
        $this->assertNotSame($vector, $result);
        $this->assertInstanceOf(Vector::class, $result);
    }

    #[Test]
    public function test_remove_at_purges_multiple_indexes_simultaneously(): void
    {
        $vector = Vector::collect(['A', 'B', 'C', 'D']);

        // Eliminamos los índices de 'B' (1) y 'D' (3)
        $result = $vector->removeAt(1, 3);

        $this->assertSame(['A', 'C'], $result->toArray());
    }

    #[Test]
    public function test_zip_interleaves_multiple_arrays_until_the_shortest_exhausts(): void
    {
        $vector = Vector::collect([1, 2, 3]);
        $letters = ['A', 'B']; // Más corta, detendrá el proceso en el índice 1

        $result = $vector->zip($letters);

        $this->assertInstanceOf(Vector::class, $result);
        $this->assertCount(2, $result);
        $this->assertSame([1, 'A'], $result->get(0));
        $this->assertSame([2, 'B'], $result->get(1));
    }

    #[Test]
    public function test_zip_interleaves_multiple_iterables(): void
    {
        $vector = Vector::collect([1, 2, 3]);
        $letters = new \ArrayIterator(['A', 'B']);

        $result = $vector->zip($letters);

        $this->assertInstanceOf(Vector::class, $result);
        $this->assertCount(2, $result);
        $this->assertSame([1, 'A'], $result->get(0));
        $this->assertSame([2, 'B'], $result->get(1));
    }

    #[Test]
    public function test_flatten_collapses_multidimensional_structures_into_a_linear_vector(): void
    {
        // Vector que contiene sub-arrays o colecciones anidadas
        $vector = Vector::collect([
            ['A', 'B'],
            ['C'],
            ['D', 'E'],
            'F',
        ]);

        $result = $vector->flatten();

        $this->assertInstanceOf(Vector::class, $result);

        $this->assertSame(['A', 'B', 'C', 'D', 'E', 'F'], $result->toArray());
    }
}
