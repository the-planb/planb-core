<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\Map;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\DS\Map\Map;
use PlanB\Core\DS\Traits\MapInmutableTrait;
use PlanB\Core\Tests\Unit\DS\DataSet\CollectionDataSet;

/**
 * @internal
 */
#[CoversClass(Map::class)]
#[CoversTrait(MapInmutableTrait::class)]
final class MapInmutableTest extends TestCase
{
    #[Test]
    public function test_put_adds_a_new_key_value_pair_without_mutating_the_original(): void
    {
        $data = CollectionDataSet::associativeDictionary(); // ['es' => 'España', ...]
        $map = Map::collect($data);

        $result = $map->put('pt', 'Portugal');

        // Validamos la inmutabilidad de la instancia original
        $this->assertFalse($map->has('pt'));
        $this->assertEquals($data, $map->toArray());

        // Validamos que el nuevo mapa contiene el elemento añadido
        $this->assertTrue($result->has('pt'));
        $this->assertSame('Portugal', $result->get('pt'));
        $this->assertCount(4, $result);
    }

    #[Test]
    public function test_put_overwrites_an_existing_key_safely(): void
    {
        $data = CollectionDataSet::associativeDictionary();
        $map = Map::collect($data);

        $result = $map->put('fr', 'Francia Renovada');

        // El original sigue intacto
        $this->assertSame('Francia', $map->get('fr'));

        // El nuevo mapa tiene el valor actualizado bajo la misma clave
        $this->assertSame('Francia Renovada', $result->get('fr'));
        $this->assertCount(3, $result); // El tamaño no cambia al sobreescribir
    }

    #[Test]
    public function test_forget_removes_a_single_key_without_mutating_the_original(): void
    {
        $data = CollectionDataSet::associativeDictionary();
        $map = Map::collect($data);

        $result = $map->forget('fr');

        // El original conserva la clave
        $this->assertTrue($map->has('fr'));

        // El nuevo mapa ya no la incluye
        $this->assertFalse($result->has('fr'));
        $this->assertCount(2, $result);
    }

    #[Test]
    public function test_forget_removes_multiple_keys_simultaneously(): void
    {
        $data = CollectionDataSet::associativeDictionary();
        $map = Map::collect($data);

        // Pasamos múltiples argumentos usando el operador variádico
        $result = $map->forget('es', 'it');

        $this->assertFalse($result->has('es'));
        $this->assertFalse($result->has('it'));
        $this->assertTrue($result->has('fr'));
        $this->assertCount(1, $result);
    }
}
