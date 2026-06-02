<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Integration\DS;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * This test suite acts as living documentation for the library.
 * It demonstrates real-world business scenarios combining Maps and Vectors.
 *
 * @internal
 */
#[CoversNothing]
final class ExampleTest extends TestCase
{
    #[Test]
    public function it_processes_a_shopping_cart_and_calculates_totals_with_tax(): void
    {
        // 1. Given raw input data from an API payload or a form submission
        $rawCartItems = [
            ['id' => 'prod_1', 'name' => 'Mechanical Keyboard', 'price' => 89.99, 'quantity' => 1],
            ['id' => 'prod_2', 'name' => 'Ergonomic Mouse', 'price' => 45.50, 'quantity' => 2],
            ['id' => 'prod_3', 'name' => 'Desk Mat XL', 'price' => 15.00, 'quantity' => 1],
        ];

        // 2. When hydrating an immutable Vector using the global helper function
        $cart = vector($rawCartItems);

        // 3. Then filtering expensive items (> 20.00) and reducing to a total amount
        $subtotal = $cart
            ->filter(fn (array $item): bool => $item['price'] > 20.00)
            ->reduce(fn (float $accumulator, array $item): float => $accumulator + ($item['price'] * $item['quantity']), 0.0)
        ;

        $totalWithTax = $subtotal * 1.21;

        // Asserting the fluid business logic transformation works flawlessly
        $this->assertSame(180.99, $subtotal); // (89.99 * 1) + (45.50 * 2)
        $this->assertEqualsWithDelta(218.997, $totalWithTax, 0.001);
    }

    #[Test]
    public function it_sanitizes_and_filters_an_external_configuration_dictionary(): void
    {
        // 1. Given raw data containing formatting inconsistencies (whitespace and duplicates)
        $dirtyConfig = [
            '  database_host ' => 'localhost',
            'DATABASE_PORT' => '3306',
            'enabled_features' => ['auth', 'api', 'auth'], // duplicate values
        ];

        // 2. When hydrating a Map and applying normalizers to standardize keys and values
        $cleanConfig = map(
            input: $dirtyConfig,
            normalizer: function (mixed $value, int|string $key): mixed {
                if (is_string($value)) {
                    return trim($value);
                }
                if (is_array($value)) {
                    return vector($value)->unique()->toArray(); // Clean array duplicates via Vector
                }

                return $value;
            },
            keyNormalizer: fn ($_, string $key): string => trim(strtolower($key)),
        );

        // 3. Then isolating database entries using set key operations
        $requiredKeys = ['database_host' => true, 'database_port' => true];
        $databaseConfig = $cleanConfig->intersectKeys($requiredKeys);

        // Asserting the final state displays the expected structural purity
        $this->assertCount(2, $databaseConfig);
        $this->assertSame(['auth', 'api'], $cleanConfig->get('enabled_features'));
    }
}
