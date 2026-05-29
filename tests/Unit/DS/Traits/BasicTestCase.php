<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\Traits;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\DS\Collection;
use PlanB\Core\DS\Contract\BasicInterface;
use PlanB\Core\Tests\Unit\DS\DataSet\CollectionDataSet;

abstract class BasicTestCase extends TestCase
{
    #[Test]
    public function test_is_empty_evaluates_absence_of_elements(): void
    {
        $emptyCollection = $this->createCollection([]);
        $fullCollection = $this->createCollection([1]);

        $this->assertTrue($emptyCollection->isEmpty());
        $this->assertFalse($fullCollection->isEmpty());
    }

    #[Test]
    public function test_filter_removes_elements_without_mutating_original(): void
    {
        $numbers = CollectionDataSet::numbersForBasicOperations(); // [1, 2, 3, 4, 5]
        $collection = $this->createCollection($numbers);

        // Filtramos números pares
        $result = $collection->filter(fn (int $n) => $n % 2 === 0);

        $this->assertArraysHaveIdenticalValues([2, 4], $result->toArray());
        $this->assertEqualsCanonicalizing($numbers, $collection->toArray()); // Inmutabilidad
    }

    #[Test]
    public function test_filter_without_callback_removes_falsy_values(): void
    {
        $collection = $this->createCollection([0, 1, false, 2, '', 3, null]);

        $result = $collection->filter();

        $this->assertArraysHaveIdenticalValues([1, 2, 3], $result->toArray());
    }

    #[Test]
    public function test_sort_orders_elements_with_custom_comparator_without_mutating_original(): void
    {
        $collection = $this->createCollection([3, 1, 4, 2, 5]);

        // Orden descendente
        $result = $collection->sort(fn (int $a, int $b) => $b <=> $a);

        $this->assertSame([5, 4, 3, 2, 1], array_values($result->toArray()));
        $this->assertSame([3, 1, 4, 2, 5], array_values($collection->toArray())); // Inmutabilidad
    }

    #[Test]
    public function test_sort_without_comparator_uses_natural_order(): void
    {
        $collection = $this->createCollection([3, 1, 4, 2, 5]);

        $result = $collection->sort();

        $this->assertSame([1, 2, 3, 4, 5], array_values($result->toArray()));
    }

    #[Test]
    public function test_map_transforms_elements_into_new_types(): void
    {
        $numbers = CollectionDataSet::numbersForBasicOperations();
        $collection = $this->createCollection($numbers);

        // Transformamos enteros a strings formateados
        $result = $collection->map(fn (int $n) => 'val_' . $n);

        $this->assertEqualsCanonicalizing(['val_1', 'val_2', 'val_3', 'val_4', 'val_5'], $result->toArray());
    }

    #[Test]
    public function test_map_transforms_elements_using_keys(): void
    {
        $numbers = CollectionDataSet::numbersForBasicOperations();
        $collection = $this->createCollection($numbers);

        // Transformamos enteros a strings formateados
        $result = $collection->map(fn (int $n, int $key) => "val_{$n}_{$key}");

        $this->assertEqualsCanonicalizing(['val_1_0', 'val_2_1', 'val_3_2', 'val_4_3', 'val_5_4'], $result->toArray());
    }

    #[Test]
    public function test_normalize_transforms_values_maintaining_the_same_instance_type(): void
    {
        $collection = $this->createCollection([1, 2, 3]);

        $result = $collection->normalize(fn (int $n) => $n * 10);

        $this->assertInstanceOf($collection::class, $result);
        $this->assertEqualsCanonicalizing([10, 20, 30], $result->toArray());
    }

    #[Test]
    public function test_normalize_transforms_values_using_keys(): void
    {
        $collection = $this->createCollection([1, 2, 3]);

        $result = $collection->normalize(fn (int $n, int $key) => $n * 10 + $key);

        $this->assertInstanceOf($collection::class, $result);
        $this->assertEqualsCanonicalizing([10, 21, 32], $result->toArray());
    }

    #[Test]
    public function test_reduce_accumulates_values_into_a_single_output(): void
    {
        $numbers = CollectionDataSet::numbersForBasicOperations(); // [1, 2, 3, 4, 5]
        $collection = $this->createCollection($numbers);

        // Sumatorio con valor inicial 10
        $sum = $collection->reduce(fn (?int $carry, int $n) => ($carry ?? 0) + $n, 10);

        $this->assertSame(25, $sum);
    }

    #[Test]
    public function test_slice_extracts_a_sub_section_of_the_collection(): void
    {
        $numbers = CollectionDataSet::numbersForBasicOperations();
        $collection = $this->createCollection($numbers);

        // Offset 1, longitud 3 -> [2, 3, 4]
        $result = $collection->slice(1, 3);

        $this->assertArraysHaveIdenticalValues([2, 3, 4], $result->toArray());
    }

    /**
     * @param array<array-key, mixed> $items
     */
    abstract protected function createCollection(iterable $items): BasicInterface&Collection;
}
