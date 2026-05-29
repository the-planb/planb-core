<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\DataSet\Domain;

final class Email implements \Stringable
{
    public function __construct(public private(set) string $value) {}

    public function __toString(): string
    {
        return $this->value;
    }
}
