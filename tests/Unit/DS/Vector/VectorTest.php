<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\Vector;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\DS\Attribute\ElementType;
use PlanB\Core\DS\Vector\TypedVector;
use PlanB\Core\DS\Vector\Vector;
use PlanB\Core\Type\Exception\InvalidTypeError;

/**
 * @internal
 */
#[CoversClass(Vector::class)]
final class VectorTest extends TestCase
{
    #[Test]
    public function test_collect_instantiates_typed_vector_with_correct_elements(): void
    {
        // Enviamos elementos válidos según el atributo #[ElementType('string')]
        $vector = $this->collectTyped(['Alpha', 'Beta']);

        $this->assertInstanceOf(TypedVector::class, $vector);
        $this->assertCount(2, $vector);
        $this->assertSame(['Alpha', 'Beta'], $vector->toArray());
    }

    #[Test]
    public function test_collect_applies_normalizer_callback_to_elements_before_type_validation(): void
    {
        // Enviamos enteros pero el normalizer los transformará a strings válidos antes del chequeo
        $vector = $this->collectTyped([10, 20], fn (int $item): string => 'item_' . $item);

        $this->assertSame(['item_10', 'item_20'], $vector->toArray());
    }

    #[Test]
    public function test_collect_throws_exception_if_an_element_fails_type_validation(): void
    {
        // Si tu librería lanza una excepción específica (ej. InvalidArgumentException / TypeError), cámbiala aquí:
        $this->expectException(InvalidTypeError::class);

        // Intentamos colar un entero sin normalizador en una colección estrictamente de strings
        $this->collectTyped(['Alpha', 123, 'Gamma']);
    }

    #[Test]
    public function test_collect_throws_exception_if_normalized_element_fails_type_validation(): void
    {
        $this->expectException(InvalidTypeError::class);

        // El normalizador devuelve un tipo erróneo (bool) que no hace match con #[ElementType('string')]
        $this->collectTyped(['Alpha'], fn (string $item): bool => true);
    }

    #[Test]
    public function test_has_evaluates_presence_of_elements_by_strict_comparison(): void
    {
        $vector = Vector::collect(['1', 2, 3]);

        $this->assertTrue($vector->has(0));
        $this->assertTrue($vector->has(1));
        $this->assertTrue($vector->has(2));
        $this->assertFalse($vector->has(3));
    }

    #[Test]
    public function test_index_of_returns_the_first_matching_index_or_null_if_not_found(): void
    {
        $vector = Vector::collect(['A', 'B', 'C', 'B']);

        // Encuentra la primera ocurrencia
        $this->assertSame(1, $vector->indexOf('B'));
        $this->assertSame(0, $vector->indexOf('A'));

        // Elemento inexistente
        $this->assertNull($vector->indexOf('Z'));
    }

    #[Test]
    public function test_get_returns_element_at_index_or_provided_default_value_when_out_of_bounds(): void
    {
        $vector = Vector::collect(['A', 'B']);

        // Índices válidos
        $this->assertSame('A', $vector->get(0));
        $this->assertSame('B', $vector->get(1));

        // Índice fuera de límites con fallback por defecto (asumiendo firma: get(int $index, mixed $default = null))
        $this->assertNull($vector->get(5));
        $this->assertSame('Fallback', $vector->get(5, 'Fallback'));
    }

    /**
     * Factoría para instanciar la clase anónima tipada emulando el método estático collect.
     * Como 'collect' es estático y devuelve 'new static()', invocarlo desde una clase anónima
     * preservará el tipo configurado por el atributo de la clase hija.
     *
     * @param array<int, mixed>           $items
     * @param null|callable(mixed): mixed $normalizer
     */
    private function collectTyped(array $items, ?callable $normalizer = null): TypedVector
    {
        $anonymousClass = new #[ElementType('string')] class extends TypedVector {
            public function __construct(iterable $items = [])
            {
                parent::__construct($items);
            }
        };

        // Invocamos el método estático a través del nombre de la clase anónima generada
        return $anonymousClass::collect($items, $normalizer);
    }
}
