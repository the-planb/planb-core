<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\Traits;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\DS\Collection;
use PlanB\Core\DS\Contract\GroupInterface;
use PlanB\Core\DS\Map\Map;
use PlanB\Core\DS\Vector\Vector;
use PlanB\Core\Tests\Unit\DS\DataSet\CollectionDataSet;
use PlanB\Core\Tests\Unit\DS\DataSet\Domain\User;

abstract class GroupTestCase extends TestCase
{
    #[Test]
    public function test_partition_splits_collection_into_two_vectors_based_on_boolean_condition(): void
    {
        $users = CollectionDataSet::usersForGrouping();
        $collection = $this->createCollection($users);

        // Separamos: los que son Administradores/Editores (nivel > 1) de los que no
        $result = $collection->partition(fn (User $user) => $user->role->level > 1);

        $this->assertInstanceOf(Vector::class, $result);

        // El resultado debe contener exactamente dos elementos (Vector de Vectores)
        $this->assertCount(2, $result);

        $matchVector = $result->get(0);
        $mismatchVector = $result->get(1);

        $this->assertInstanceOf(Vector::class, $matchVector);
        $this->assertInstanceOf(Vector::class, $mismatchVector);

        // Validamos quién cumple (Alice, Bob, David = 3) y quién no (Charlie = 1)
        $this->assertCount(3, $matchVector);
        $this->assertCount(1, $mismatchVector);

        $this->assertSame('Alice', $matchVector->get(0)->name);
        $this->assertSame('Charlie', $mismatchVector->get(0)->name);
    }

    #[Test]
    public function test_chunk_divides_collection_into_fixed_size_vectors(): void
    {
        $users = CollectionDataSet::usersForGrouping(); // 4 usuarios en total
        $collection = $this->createCollection($users);

        // Dividimos en trozos de tamaño 2
        $result = $collection->chunk(2);

        $this->assertInstanceOf(Vector::class, $result);
        $this->assertCount(2, $result);

        $chunk1 = $result->get(0);
        $chunk2 = $result->get(1);

        $this->assertInstanceOf(Vector::class, $chunk1);
        $this->assertCount(2, $chunk1);
        $this->assertSame('Alice', $chunk1->get(0)->name);
        $this->assertSame('Bob', $chunk1->get(1)->name);

        $this->assertInstanceOf(Vector::class, $chunk2);
        $this->assertCount(2, $chunk2);
        $this->assertSame('Charlie', $chunk2->get(0)->name);
        $this->assertSame('David', $chunk2->get(1)->name);
    }

    #[Test]
    public function test_chunk_throws_exception_if_size_is_zero(): void
    {
        $users = CollectionDataSet::usersForGrouping();
        $collection = $this->createCollection($users);

        $this->expectException(\InvalidArgumentException::class);
        // Si tu librería lanza un mensaje específico en español, adáptalo aquí:
        $this->expectExceptionMessageMatches('/must be greater than zero./i');

        $collection->chunk(0);
    }

    #[Test]
    public function test_chunk_throws_exception_if_size_is_negative(): void
    {
        $users = CollectionDataSet::usersForGrouping();
        $collection = $this->createCollection($users);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/must be greater than zero/i');

        $collection->chunk(-5);
    }

    #[Test]
    public function test_group_by_clusters_elements_into_a_map_of_vectors_using_string_or_integer_keys(): void
    {
        $users = CollectionDataSet::usersForGrouping();
        $collection = $this->createCollection($users);

        // Agrupamos por el nombre del rol ('Admin', 'Editor', 'User')
        $result = $collection->groupBy(fn (User $user) => $user->role->name);

        $this->assertInstanceOf(Map::class, $result);
        $this->assertCount(3, $result); // 3 grupos únicos creados

        // Comprobamos el grupo de Editores (debe contener a Bob y David)
        $editors = $result->get('Editor');
        $this->assertInstanceOf(Vector::class, $editors);
        $this->assertCount(2, $editors);
        $this->assertSame('Bob', $editors->get(0)->name);
        $this->assertSame('David', $editors->get(1)->name);

        // Comprobamos el grupo de Administradores (debe contener a Alice)
        $admins = $result->get('Admin');
        $this->assertCount(1, $admins);
        $this->assertSame('Alice', $admins->get(0)->name);
    }

    /**
     * @param array<array-key, mixed> $items
     */
    abstract protected function createCollection(array $items): Collection&GroupInterface;
}
