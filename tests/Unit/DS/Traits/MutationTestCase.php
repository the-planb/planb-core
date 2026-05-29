<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\Traits;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\DS\Collection;
use PlanB\Core\DS\Contract\MutationInterface;

abstract class MutationTestCase extends TestCase
{
    #[Test]
    public function test_reversed_inverts_the_order_of_elements_without_modifying_the_original(): void
    {
        $rawData = ['A', 'B', 'C'];
        $collection = $this->createCollection($rawData);

        $result = $collection->reversed();

        // Validamos la inmutabilidad: el objeto original no cambia
        $this->assertEquals($rawData, $collection->toArray());

        // Validamos que el resultado está invertido
        $this->assertEquals(['C', 'B', 'A'], array_values($result->toArray()));
    }

    #[Test]
    public function test_shuffle_randomizes_element_order_safely(): void
    {
        $rawData = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $collection = $this->createCollection($rawData);

        $result = $collection->shuffle();

        // Validamos la inmutabilidad: el objeto original sigue intacto
        $this->assertEquals($rawData, $collection->toArray());

        // Validamos que contiene exactamente los mismos elementos
        $this->assertEqualsCanonicalizing($rawData, $result->toArray());

        // Validamos que el orden ha cambiado
        $this->assertNotEquals($rawData, $result->toArray());
    }

    /**
     * @param array<array-key, mixed> $items
     */
    abstract protected function createCollection(array $items): Collection&MutationInterface;
}
