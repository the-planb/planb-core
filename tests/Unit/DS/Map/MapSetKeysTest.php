<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\Map;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\DS\Map\Map;
use PlanB\Core\DS\Traits\SetKeyTrait;
use PlanB\Core\Tests\Unit\DS\DataSet\CollectionDataSet;

/**
 * @internal
 */
#[CoversClass(Map::class)]
#[CoversTrait(SetKeyTrait::class)]
final class MapSetKeysTest extends TestCase
{
    #[Test]
    public function test_diff_keys_excludes_entries_whose_keys_exist_in_the_given_array(): void
    {
        $initialData = CollectionDataSet::associativeDictionary(); // ['es' => 'España', 'fr' => 'Francia', 'it' => 'Italia']
        $map = Map::collect($initialData);

        // Excluimos las claves 'fr' y una que no existe ('uk')
        $exclude = ['fr' => true, 'uk' => true];
        $result = $map->diffKeys($exclude);

        // Validamos inmutabilidad
        $this->assertEquals($initialData, $map->toArray());

        // El resultado mantiene 'es' e 'it'
        $this->assertFalse($result->has('fr'));
        $this->assertTrue($result->has('es'));
        $this->assertTrue($result->has('it'));
        $this->assertCount(2, $result);
    }

    #[Test]
    public function test_diff_keys_excludes_entries_whose_keys_exist_in_the_given_iterable(): void
    {
        $initialData = CollectionDataSet::associativeDictionary(); // ['es' => 'España', 'fr' => 'Francia', 'it' => 'Italia']
        $map = Map::collect($initialData);

        // Excluimos las claves 'fr' y una que no existe ('uk')
        $exclude = new \ArrayIterator(['fr' => true, 'uk' => true]);
        $result = $map->diffKeys($exclude);

        // Validamos inmutabilidad
        $this->assertEquals($initialData, $map->toArray());

        // El resultado mantiene 'es' e 'it'
        $this->assertFalse($result->has('fr'));
        $this->assertTrue($result->has('es'));
        $this->assertTrue($result->has('it'));
        $this->assertCount(2, $result);
    }

    #[Test]
    public function test_intersect_keys_keeps_only_entries_whose_keys_exist_in_the_given_array(): void
    {
        $initialData = CollectionDataSet::associativeDictionary();
        $map = Map::collect($initialData);

        // Conservamos solo 'es' y añadimos una clave ajena
        $keep = ['es' => 'any_value', 'de' => 'Germany'];
        $result = $map->intersectKeys($keep);

        // Validamos inmutabilidad
        $this->assertEquals($initialData, $map->toArray());

        // El resultado solo debe contener 'es'
        $this->assertTrue($result->has('es'));
        $this->assertFalse($result->has('fr'));
        $this->assertFalse($result->has('it'));
        $this->assertCount(1, $result);
        $this->assertSame('España', $result->get('es'));
    }

    #[Test]
    public function test_intersect_keys_keeps_only_entries_whose_keys_exist_in_the_given_iterable(): void
    {
        $initialData = CollectionDataSet::associativeDictionary();
        $map = Map::collect($initialData);

        // Conservamos solo 'es' y añadimos una clave ajena
        $keep = new \ArrayIterator(['es' => 'any_value', 'de' => 'Germany']);
        $result = $map->intersectKeys($keep);

        // Validamos inmutabilidad
        $this->assertEquals($initialData, $map->toArray());

        // El resultado solo debe contener 'es'
        $this->assertTrue($result->has('es'));
        $this->assertFalse($result->has('fr'));
        $this->assertFalse($result->has('it'));
        $this->assertCount(1, $result);
        $this->assertSame('España', $result->get('es'));
    }
}
