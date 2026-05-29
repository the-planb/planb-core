<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\Traits;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\DS\Collection;
use PlanB\Core\DS\Contract\QueryInterface;
use PlanB\Core\Tests\Unit\DS\DataSet\CollectionDataSet;
use PlanB\Core\Tests\Unit\DS\DataSet\Domain\Email;
use PlanB\Core\Tests\Unit\DS\DataSet\Domain\Role;
use PlanB\Core\Tests\Unit\DS\DataSet\Domain\User;

abstract class QueryTestCase extends TestCase
{
    #[Test]
    public function test_has_count_validates_the_exact_size_of_the_collection(): void
    {
        $users = CollectionDataSet::usersForInspection();
        $collection = $this->createCollection($users);

        $this->assertTrue($collection->hasCount(3));
        $this->assertFalse($collection->hasCount(0));
        $this->assertFalse($collection->hasCount(5));
    }

    #[Test]
    public function test_is_not_empty_evaluates_presence_of_elements(): void
    {
        $users = CollectionDataSet::usersForInspection();
        $fullCollection = $this->createCollection($users);
        $emptyCollection = $this->createCollection([]);

        $this->assertTrue($fullCollection->isNotEmpty());
        $this->assertFalse($emptyCollection->isNotEmpty());
    }

    #[Test]
    public function test_contains_finds_existing_elements_by_strict_or_regular_comparison(): void
    {
        $users = CollectionDataSet::usersForInspection();
        $collection = $this->createCollection($users);

        $targetUser = $users[1]; // Bruce Wayne
        $strangerUser = new User('usr_99', 'Peter Parker', new Email('peter@dailybugle.com'), new Role('User', 1));

        $this->assertTrue($collection->contains($targetUser));
        $this->assertFalse($collection->contains(clone $targetUser));
        $this->assertFalse($collection->contains($strangerUser));
    }

    #[Test]
    public function test_some_returns_true_if_at_least_one_element_matches_the_condition(): void
    {
        $users = CollectionDataSet::usersForInspection();
        $collection = $this->createCollection($users);

        // Comprobamos si hay algún usuario con nivel bajo (Diana Prince cumple)
        $hasLowLevelUsers = $collection->some(fn (User $user) => $user->role->level === 1);
        // Comprobamos una condición que nadie cumple
        $hasEditors = $collection->some(fn (User $user) => $user->role->name === 'Editor');

        $this->assertTrue($hasLowLevelUsers);
        $this->assertFalse($hasEditors);
    }

    #[Test]
    public function test_every_returns_true_only_if_all_elements_match_the_condition(): void
    {
        $users = CollectionDataSet::usersForInspection();
        $collection = $this->createCollection($users);

        // Comprobamos si todos tienen email (Todos cumplen)
        $allHaveEmail = $collection->every(fn (User $user) => $user->email->value !== '' && $user->email->value !== '0');
        // Comprobamos si todos son administradores (Diana no cumple)
        $allAreAdmins = $collection->every(fn (User $user) => $user->role->name === 'Admin');

        $this->assertTrue($allHaveEmail);
        $this->assertFalse($allAreAdmins);
    }

    /**
     * @param array<array-key, mixed> $items
     */
    abstract protected function createCollection(array $items): Collection&QueryInterface;
}
