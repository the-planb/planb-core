<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\DataSet\Domain;

final class Role
{
    public function __construct(public private(set) string $name, public private(set) int $level) {}
}
