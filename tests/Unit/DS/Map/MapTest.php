<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\Map;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\DS\Attribute\ElementType;
use PlanB\Core\DS\Map\Map;
use PlanB\Core\DS\Map\TypedMap;
use PlanB\Core\DS\Vector\Vector;
use PlanB\Core\Type\Exception\InvalidTypeError;

/**
 * @internal
 */
#[CoversClass(Map::class)]
final class MapTest extends TestCase
{
    #[Test]
    public function test_collect_instantiates_typed_map_with_correct_elements(): void
    {
        $map = $this->collectTyped(['es' => 'España', 'fr' => 'Francia']);

        $this->assertInstanceOf(TypedMap::class, $map);
        $this->assertCount(2, $map);
        $this->assertSame('España', $map->get('es'));
    }

    #[Test]
    public function test_collect_applies_both_normalizers_before_validation(): void
    {
        // Forzamos claves a mayúsculas y valores formateados
        $map = $this->collectTyped(
            items: ['es' => 'españa'],
            normalizer: fn (string $val): string => ucfirst($val),
            keyNormalizer: fn (string $_, string $key): string => strtoupper($key),
        );

        $this->assertFalse($map->has('es'));
        $this->assertTrue($map->has('ES'));
        $this->assertSame('España', $map->get('ES'));
    }

    #[Test]
    public function test_collect_throws_exception_if_value_fails_type_validation(): void
    {
        $this->expectException(InvalidTypeError::class);

        // 123 no es un string válido para el #[ElementType('string')]
        $this->collectTyped(['es' => 123]);
    }

    #[Test]
    public function test_flat_map_transforms_and_collapses_structures_into_a_new_instance(): void
    {
        $map = $this->collectTyped(['a' => 'apple', 'b' => 'banana']);

        // Simulamos que flatMap mapea cada elemento y devuelve colecciones/arrays
        // que la librería aplana o combina de forma asociativa o lineal.
        $result = $map->flatMap(fn (string $value) => [
            $value => strlen($value),
        ]);

        // Dependiendo de si tu flatMap en Map retorna un Map o cambia la firma:
        $this->assertSame(5, $result->get('apple'));
        $this->assertSame(6, $result->get('banana'));
    }

    #[Test]
    public function test_find_key_returns_the_first_key_that_matches_the_predicate_or_null(): void
    {
        $map = $this->collectTyped(['es' => 'España', 'fr' => 'Francia', 'it' => 'Italia']);

        // Buscamos la clave cuyo valor empiece por 'F'
        $key = $map->findKey('Francia');

        $this->assertSame('fr', $key);

        // Caso de no coincidencia
        $notFound = $map->findKey(fn (string $value) => $value === 'Alemania');
        $this->assertNull($notFound);
    }

    #[Test]
    public function test_get_returns_element_or_provided_default_value_when_key_does_not_exist(): void
    {
        $map = $this->collectTyped(['es' => 'España']);

        // Clave existente
        $this->assertSame('España', $map->get('es'));

        // Clave inexistente con valores por defecto
        $this->assertNull($map->get('uk'));
        $this->assertSame('Reino Unido', $map->get('uk', 'Reino Unido'));
    }

    #[Test]
    public function test_keys_returns_a_vector_containing_all_the_map_keys(): void
    {
        // Instanciamos un mapa asociativo con claves string
        $map = $this->collectTyped(['es' => 'España', 'fr' => 'Francia']);

        $keysVector = $map->keys();

        // Debe devolver una instancia pura de Vector
        $this->assertInstanceOf(Vector::class, $keysVector);
        $this->assertCount(2, $keysVector);

        // Verificamos que contiene exactamente las claves del mapa original
        $this->assertSame(['es', 'fr'], $keysVector->toArray());
    }

    #[Test]
    public function test_values_returns_a_vector_containing_all_the_map_values(): void
    {
        $map = $this->collectTyped(['es' => 'España', 'fr' => 'Francia']);

        $valuesVector = $map->values();

        $this->assertInstanceOf(Vector::class, $valuesVector);
        $this->assertCount(2, $valuesVector);

        // Verificamos que contiene los valores indexados de forma posicional/secuencial
        $this->assertSame(['España', 'Francia'], $valuesVector->toArray());
    }

    /**
     * Factoría para instanciar la clase anónima tipada emulando el método estático collect.
     * Admite tanto un normalizador de valores como un normalizador de claves.
     *
     * @param array<int|string, mixed>                $items
     * @param null|callable(mixed): mixed             $normalizer
     * @param null|callable(int|string): (int|string) $keyNormalizer
     */
    private function collectTyped(array $items, ?callable $normalizer = null, ?callable $keyNormalizer = null): TypedMap
    {
        $anonymousClass = new #[ElementType('string')]
        class extends TypedMap {
            public function __construct(iterable $items = [])
            {
                parent::__construct($items);
            }
        };

        return $anonymousClass::collect($items, $normalizer, $keyNormalizer);
    }
}
