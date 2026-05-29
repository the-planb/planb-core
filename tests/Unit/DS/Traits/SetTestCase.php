<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\Traits;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\DS\Collection;
use PlanB\Core\DS\Contract\SetInterface;
use PlanB\Core\Tests\Unit\DS\DataSet\CollectionDataSet;
use PlanB\Core\Tests\Unit\DS\DataSet\Domain\User;

abstract class SetTestCase extends TestCase
{
    #[Test]
    public function test_unique_removes_duplicated_values_keeping_the_first_match(): void
    {
        $rawData = CollectionDataSet::simpleDuplicates();
        $collection = $this->createCollection($rawData);

        $result = $collection->unique();

        $this->assertCount(3, $result);

        $values = is_array($result) ? $result : iterator_to_array($result);
        $this->assertEquals(['A', 'B', 'C'], array_values($values));
    }

    #[Test]
    public function test_unique_resolves_identity_using_a_scalar_key_extractor(): void
    {
        $users = CollectionDataSet::usersWithDuplicateIdentities();
        $collection = $this->createCollection($users);

        $result = $collection->unique(fn (User $user) => $user->id);

        $this->assertCount(2, $result);

        $values = array_values(iterator_to_array($result));
        $this->assertSame('Clark Kent', $values[0]->name);
        $this->assertSame('Bruce Wayne', $values[1]->name);
    }

    #[Test]
    public function test_unique_allows_stringable_objects_as_valid_discriminators(): void
    {
        $users = CollectionDataSet::usersWithDuplicateIdentities();
        $collection = $this->createCollection($users);

        $result = $collection->unique(fn (User $user) => $user->email);

        $this->assertCount(2, $result);

        $values = array_values(iterator_to_array($result));
        $this->assertSame('clark.kent@dailyplanet.com', (string) $values[0]->email);
        $this->assertSame('bruce.wayne@waynecorp.com', (string) $values[1]->email);
    }

    #[Test]
    public function test_unique_throws_exception_when_extractor_returns_an_invalid_type(): void
    {
        $users = CollectionDataSet::usersWithDuplicateIdentities();
        $collection = $this->createCollection($users);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/debe devolver un valor escalar.*o Stringable/i');

        $collection->unique(fn (User $user) => $user->role);
    }

    #[Test]
    public function test_diff_excludes_elements_present_in_the_given_array(): void
    {
        $data = CollectionDataSet::userSetsForAlgebra();
        $sourceCollection = $this->createCollection($data['source']);
        $targetIterable = $data['target'];

        $result = $sourceCollection->diff($targetIterable);

        $this->assertCount(1, $result);

        $values = array_values(iterator_to_array($result));
        $this->assertSame('Alice', $values[0]->name);
    }

    #[Test]
    public function test_diff_excludes_elements_present_in_the_given_iterable(): void
    {
        $data = CollectionDataSet::userSetsForAlgebra();
        $sourceCollection = $this->createCollection($data['source']);
        $targetIterable = new \ArrayIterator($data['target']);

        $result = $sourceCollection->diff($targetIterable);

        $this->assertCount(1, $result);

        $values = array_values(iterator_to_array($result));
        $this->assertSame('Alice', $values[0]->name);
    }

    #[Test]
    public function test_diff_evaluates_exclusion_using_a_custom_comparator_callback(): void
    {
        $data = CollectionDataSet::userSetsForAlgebra();
        $sourceCollection = $this->createCollection($data['source']);
        $targetIterable = $data['target'];

        $result = $sourceCollection->diff(
            $targetIterable,
            fn (User $a, User $b) => $a->role->level <=> $b->role->level,
        );

        $this->assertCount(1, $result);
        $values = array_values(iterator_to_array($result));
        $this->assertSame('Alice', $values[0]->name);
    }

    #[Test]
    public function test_intersect_keeps_only_elements_present_in_the_given_array(): void
    {
        $data = CollectionDataSet::userSetsForAlgebra();
        $sourceCollection = $this->createCollection($data['source']);
        $targetIterable = $data['target'];

        $result = $sourceCollection->intersect($targetIterable);

        $this->assertCount(1, $result);

        $values = array_values(iterator_to_array($result));
        $this->assertSame('Bob', $values[0]->name);
    }

    #[Test]
    public function test_intersect_keeps_only_elements_present_in_the_given_iterable(): void
    {
        $data = CollectionDataSet::userSetsForAlgebra();
        $sourceCollection = $this->createCollection($data['source']);
        $targetIterable = new \ArrayIterator($data['target']);

        $result = $sourceCollection->intersect($targetIterable);

        $this->assertCount(1, $result);

        $values = array_values(iterator_to_array($result));
        $this->assertSame('Bob', $values[0]->name);
    }

    #[Test]
    public function test_intersect_evaluates_inclusion_using_a_custom_comparator_callback(): void
    {
        $data = CollectionDataSet::userSetsForAlgebra();
        $sourceCollection = $this->createCollection($data['source']);
        $targetIterable = $data['target'];

        // Intersecamos forzando la comparación por el nivel del rol
        $result = $sourceCollection->intersect(
            $targetIterable,
            fn (User $a, User $b) => $a->role->level <=> $b->role->level,
        );

        $this->assertCount(1, $result);

        $values = array_values(iterator_to_array($result));
        $this->assertSame('Bob', $values[0]->name);
    }

    /**
     * @param array<array-key, mixed> $items
     */
    abstract protected function createCollection(array $items): Collection&SetInterface;
}
