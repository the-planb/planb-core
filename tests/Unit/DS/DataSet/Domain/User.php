<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\DataSet\Domain;

final class User
{
    public function __construct(public private(set) string $id, public private(set) string $name, public private(set) Email $email, public private(set) Role $role) {}
}
