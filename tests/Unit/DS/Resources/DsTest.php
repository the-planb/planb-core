<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\Resources;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\DS\Map\Map;
use PlanB\Core\DS\Vector\Vector;

/**
 * @internal
 */
#[CoversFunction('vector')]
#[CoversFunction('map')]
final class DsTest extends TestCase
{
    #[Test]
    public function test_vector_helper_instantiates_a_vector_correctly_without_normalizer(): void
    {
        $input = ['A', 'B', 'C'];

        // Invocación a la función helper global
        $result = vector($input);

        $this->assertInstanceOf(Vector::class, $result);
        $this->assertSame($input, $result->toArray());
    }

    #[Test]
    public function test_vector_helper_applies_normalizer_callback_to_elements(): void
    {
        $input = [1, 2];
        $normalizer = fn (int $value): string => 'num_' . $value;

        $result = vector($input, $normalizer);

        $this->assertInstanceOf(Vector::class, $result);
        $this->assertSame(['num_1', 'num_2'], $result->toArray());
    }

    #[Test]
    public function test_map_helper_instantiates_a_map_correctly_without_normalizer(): void
    {
        $input = ['es' => 'España', 'fr' => 'Francia'];

        // Invocación a la función helper global
        $result = map($input);

        $this->assertInstanceOf(Map::class, $result);
        $this->assertSame($input, $result->toArray());
    }

    #[Test]
    public function test_map_helper_applies_normalizer_callback_to_values(): void
    {
        $input = ['es' => 'españa', 'fr' => 'francia'];
        $normalizer = fn (string $value): string => ucfirst($value);

        $result = map($input, $normalizer);

        $this->assertInstanceOf(Map::class, $result);
        $this->assertSame('España', $result->get('es'));
        $this->assertSame('Francia', $result->get('fr'));
    }
}
