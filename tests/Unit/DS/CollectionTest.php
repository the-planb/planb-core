<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\DS\Attribute\ElementType;
use PlanB\Core\DS\Collection;
use PlanB\Core\Type\Exception\InvalidTypeError;

/**
 * @extends Collection<int, string>
 */
#[ElementType('string')]
class CollectionMock extends Collection
{
    public mixed $_inner_items {
        get => $this->items;
    }

    public array $_inner_types {
        get => self::$allowedTypes;
    }

    public static function collect(iterable $items): static
    {
        $items = static::ensureItems($items);

        return new static($items);
    }

    #[\Override]
    public function updateItems(array $items): static
    {
        return parent::updateItems($items);
    }

    public static function trust(iterable $items): static
    {
        return new static($items);
    }
}

/**
 * @internal
 */
#[CoversClass(Collection::class)]
#[CoversClass(ElementType::class)]
final class CollectionTest extends TestCase
{
    #[Test]
    public function test_collection_returns_right_allowed_types(): void
    {
        $this->assertEquals(['string'], CollectionMock::allowedTypes());
    }

    #[Test]
    public function test_collection_can_be_created_using_an_iterable(): void
    {
        $data = new \ArrayIterator(['A', 'B', 'C']);
        $collection = CollectionMock::trust($data);
        $this->assertIsArray($collection->_inner_items);

        $data = new \ArrayIterator(['A', 'B', 'C']);
        $collection = CollectionMock::collect($data);
        $this->assertIsArray($collection->_inner_items);
    }

    #[Test]
    public function test_apply_to_executes_callback_passing_the_collection_and_returns_its_result(): void
    {
        $collection = $this->createCollectionInstance(['A', 'B', 'C']);

        // applyTo debe pasar la propia instancia al callback y retornar lo que este devuelva
        $result = $collection->applyTo(fn (Collection $col): int => count($col->toArray()));

        $this->assertSame(3, $result);
    }

    #[Test]
    public function test_json_serialize_returns_the_raw_array_representation_for_json_encoding(): void
    {
        $data = ['id' => 'uuid', 'name' => 'PlanB'];
        $collection = $this->createCollectionInstance($data);

        // jsonSerialize() es invocado automáticamente por json_encode()
        $serializedData = $collection->jsonSerialize();
        $jsonString = json_encode($collection);

        // Validamos que devuelve la estructura pura del array
        $this->assertSame($data, $serializedData);

        // Validamos la correcta transformación nativa a string JSON
        $this->assertSame('{"id":"uuid","name":"PlanB"}', $jsonString);
    }

    #[Test]
    public function test_collection_throws_an_exception_if_some_element_type_is_wrong(): void
    {
        $this->expectException(InvalidTypeError::class);

        $data = ['id' => 1, 'name' => 'PlanB'];
        $this->createCollectionInstance($data);
    }

    #[Test]
    public function test_collection_can_be_used_without_types(): void
    {
        $collection = new class extends Collection {
            public function __construct(iterable $items = [])
            {
                parent::__construct($items);
            }

            public static function collect(array $items): static
            {
                $items = self::ensureItems($items);

                return new self($items);
            }
        };

        $list = $collection::collect(['id' => 1, 'name' => 'PlanB']);
        $this->assertSame(['id' => 1, 'name' => 'PlanB'], $list->toArray());
    }

    #[Test]
    public function test_update_items_does_not_change_the_instance(): void
    {
        $collection = $this->createCollectionInstance(['A', 'B', 'C']);
        $this->assertSame(['A', 'B', 'C'], $collection->toArray());

        $newCollection = $collection->updateItems(['D', 'E', 'F']);
        $this->assertSame(['D', 'E', 'F'], $newCollection->toArray());
        $this->assertSame(['D', 'E', 'F'], $collection->toArray());

        $this->assertSame($collection, $newCollection);
    }

    #[Test]
    public function test_count_returns_total_items_properly(): void
    {
        $collection = $this->createCollectionInstance(['A', 'B', 'C']);
        $this->assertEquals(3, $collection->count());
        $this->assertEquals(3, count($collection));
    }

    #[Test]
    public function test_collection_is_iterable(): void
    {
        $collection = $this->createCollectionInstance(['A', 'B', 'C']);
        $items = [];
        foreach ($collection as $item) {
            $items[] = $item;
        }
        $this->assertSame(['A', 'B', 'C'], $items);
        $this->assertSame(['A', 'B', 'C'], iterator_to_array($items));
    }

    /**
     * Factoría para instanciar una clase anónima que extienda de la abstracta Collection.
     * Esto permite aislar los métodos de la infraestructura base.
     *
     * @param iterable <mixed> $items
     */
    private function createCollectionInstance(iterable $items): Collection
    {
        return CollectionMock::collect($items);
    }
}
