<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\Traits;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\DS\Collection;
use PlanB\Core\DS\Contract\SliceInterface;
use PlanB\Core\Tests\Unit\DS\DataSet\CollectionDataSet;

abstract class SliceTestCase extends TestCase
{
    #[Test]
    public function test_take_extracts_the_first_n_elements(): void
    {
        $letters = CollectionDataSet::lettersForSlicing(); // ['A', 'B', 'C', 'D', 'E']
        $collection = $this->createCollection($letters);

        $this->assertArraysHaveEqualValues(['A', 'B'], $collection->take(2)->toArray());
        $this->assertArraysHaveEqualValues($letters, $collection->take(10)->toArray()); // Límite excedido
        $this->assertArraysHaveEqualValues([], $collection->take(0)->toArray());
    }

    #[Test]
    public function test_take_last_extracts_the_last_n_elements(): void
    {
        $letters = CollectionDataSet::lettersForSlicing();
        $collection = $this->createCollection($letters);

        $this->assertArraysHaveEqualValues(['D', 'E'], $collection->takeLast(2)->toArray());
        $this->assertArraysHaveEqualValues($letters, $collection->takeLast(10)->toArray());
        $this->assertArraysHaveEqualValues([], $collection->takeLast(0)->toArray());
    }

    #[Test]
    public function test_take_while_extracts_elements_from_start_until_predicate_fails(): void
    {
        $letters = CollectionDataSet::lettersForSlicing();
        $collection = $this->createCollection($letters);

        // Toma mientras la letra sea anterior a 'C'
        $result = $collection->takeWhile(fn (string $char) => $char !== 'C');

        $this->assertArraysHaveEqualValues(['A', 'B'], $result->toArray());
    }

    #[Test]
    public function test_take_last_while_extracts_elements_from_end_until_predicate_fails(): void
    {
        $letters = CollectionDataSet::lettersForSlicing();
        $collection = $this->createCollection($letters);

        // Toma desde el final mientras la letra sea posterior a 'C'
        $result = $collection->takeLastWhile(fn (string $char) => $char !== 'C');

        // Dependiendo de tu implementación interna, puede devolver ['D', 'E'] o ['E', 'D'].
        // Asumiendo que preserva el orden original de la ventana resultante:
        $this->assertArraysHaveEqualValues(['D', 'E'], $result->toArray());
    }

    #[Test]
    public function test_drop_omits_the_first_n_elements_and_returns_the_rest(): void
    {
        $letters = CollectionDataSet::lettersForSlicing();
        $collection = $this->createCollection($letters);

        $this->assertArraysHaveEqualValues(['C', 'D', 'E'], $collection->drop(2)->toArray());
        $this->assertArraysHaveEqualValues([], $collection->drop(10)->toArray());
        $this->assertArraysHaveEqualValues($letters, $collection->drop(0)->toArray());
    }

    #[Test]
    public function test_drop_last_omits_the_last_n_elements_and_returns_the_rest(): void
    {
        $letters = CollectionDataSet::lettersForSlicing();
        $collection = $this->createCollection($letters);

        $this->assertArraysHaveEqualValues(['A', 'B', 'C'], $collection->dropLast(2)->toArray());
        $this->assertArraysHaveEqualValues([], $collection->dropLast(10)->toArray());
        $this->assertArraysHaveEqualValues($letters, $collection->dropLast(0)->toArray());
    }

    #[Test]
    public function test_drop_while_omits_elements_from_start_until_predicate_fails(): void
    {
        $letters = CollectionDataSet::lettersForSlicing();
        $collection = $this->createCollection($letters);

        // Descarta mientras sea menor que 'C', devuelve el resto
        $result = $collection->dropWhile(fn (string $char) => $char !== 'C');

        $this->assertArraysHaveEqualValues(['C', 'D', 'E'], $result->toArray());
    }

    #[Test]
    public function test_drop_last_while_omits_elements_from_end_until_predicate_fails(): void
    {
        $letters = CollectionDataSet::lettersForSlicing();
        $collection = $this->createCollection($letters);

        // Descarta desde el final mientras sea mayor que 'C'
        $result = $collection->dropLastWhile(fn (string $char) => $char !== 'C');

        $this->assertArraysHaveEqualValues(['A', 'B', 'C'], $result->toArray());
    }

    /**
     * @param array<array-key, mixed> $items
     */
    abstract protected function createCollection(array $items): Collection&SliceInterface;
}
