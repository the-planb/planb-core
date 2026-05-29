<?php

declare(strict_types=1);

namespace PlanB\Core\Type\Exception;

class InvalidTypeError extends \TypeError
{
    public static function make(int|string $key, mixed $value, string ...$types): self
    {
        $expected = implode('|', $types);
        $actual = get_debug_type($value);

        return new self(
            sprintf(
                'Invalid element type detected. Error at index [%s]: expected [%s], got [%s].',
                $key,
                $expected,
                $actual,
            ),
        );
    }
}
