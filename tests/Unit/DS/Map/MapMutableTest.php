<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\Map;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\DS\Attribute\ElementType;
use PlanB\Core\DS\Contract\MapMutableInterface;
use PlanB\Core\DS\Map\Map;
use PlanB\Core\DS\Map\TypedMap;
use PlanB\Core\DS\Traits\MapMutableTrait;
use PlanB\Core\Tests\Unit\DS\DataSet\CollectionDataSet;
use PlanB\Core\Type\Exception\InvalidTypeError;

/**
 * @internal
 */
#[CoversClass(Map::class)]
#[CoversTrait(MapMutableTrait::class)]
final class MapMutableTest extends TestCase
{
    #[Test]
    public function test_set_modifies_the_current_instance_state_in_place(): void
    {
        $data = CollectionDataSet::associativeDictionary();
        $map = $this->createMap($data);

        $result = $map->set('de', 'Alemania');

        // Verificamos que es exactamente la misma instancia (mutación fluida por referencia)
        $this->assertSame($map, $result);

        // Verificamos que el estado interno ha cambiado en la instancia original
        $this->assertTrue($map->has('de'));
        $this->assertSame('Alemania', $map->get('de'));
        $this->assertCount(4, $map);
    }

    #[Test]
    public function test_set_throws_an_exception_when_input_has_invalid_type(): void
    {
        $data = CollectionDataSet::associativeDictionary();
        $map = $this->createMap($data);

        self::expectException(InvalidTypeError::class);
        $map->set('de', 5);
    }

    #[Test]
    public function test_delete_removes_the_key_directly_from_the_same_instance(): void
    {
        $data = CollectionDataSet::associativeDictionary();
        $map = $this->createMap($data);

        $result = $map->delete('fr');

        // Verificamos identidad de la instancia
        $this->assertSame($map, $result);

        // Verificamos que la clave ha desaparecido del objeto original
        $this->assertFalse($map->has('fr'));
        $this->assertCount(2, $map);
    }

    #[Test]
    public function test_clear_purges_all_elements_leaving_the_instance_empty(): void
    {
        $data = CollectionDataSet::associativeDictionary();
        $map = $this->createMap($data);

        $result = $map->clear();

        // Verificamos identidad de la instancia
        $this->assertSame($map, $result);

        // Verificamos el vaciado absoluto
        $this->assertTrue($map->isEmpty());
        $this->assertCount(0, $map);
        $this->assertEquals([], $map->toArray());
    }

    /**
     * Factoría interna para instanciar un Map que incorpore el comportamiento mutable.
     *
     * @param array<int|string, mixed> $items
     */
    private function createMap(array $items): MapMutableInterface&TypedMap
    {
        return new #[ElementType('string')]
        class($items) extends TypedMap implements MapMutableInterface {
            use MapMutableTrait;

            /**
             * @param array<int|string, mixed> $items
             */
            public function __construct(array $items)
            {
                parent::__construct($items);
            }
        };
    }
}
