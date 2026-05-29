<?php

declare(strict_types=1);

use PlanB\Core\Type\Exception\InvalidTypeError;

function is_of_type(mixed $value, string ...$types): bool
{
    if ($types === []) {
        return true;
    }

    foreach ($types as $type) {
        $lowerType = strtolower($type);

        $matched = match ($lowerType) {
            'mixed' => true,
            'array' => is_array($value),
            'string' => is_string($value),
            'int', 'integer' => is_int($value),
            'float', 'double' => is_float($value),
            'bool', 'boolean' => is_bool($value),
            'object' => is_object($value),
            'null' => is_null($value),
            'callable' => is_callable($value),
            'countable' => is_countable($value),
            'iterable' => is_iterable($value),
            'resource' => is_resource($value),
            default => $value instanceof $type
        };

        if ($matched) {
            return true;
        }
    }

    return false;
}

function type_of(mixed $value): string
{
    $type = get_debug_type($value);

    return match ($type) {
        'resource (closed)' => 'resource',
        default => $type
    };
}

/**
 * @param iterable<array-key, mixed> $input
 */
function all_of_type(iterable $input, string ...$types): bool
{
    if (empty($input)) {
        return true;
    }

    foreach ($input as $item) {
        if (!is_of_type($item, ...$types)) {
            return false;
        }
    }

    return true;
}

/**
 * @param iterable<array-key, mixed> $input
 *
 * @throws InvalidTypeError si algún elemento no coincide con los tipos requeridos
 */
function assert_all_of_type(iterable $input, string ...$types): void
{
    if (empty($input)) {
        return;
    }

    foreach ($input as $index => $item) {
        if (!is_of_type($item, ...$types)) {
            throw InvalidTypeError::make($index, $input, ...$types);
        }
    }
}

/**
 * @param iterable<array-key, mixed> $input
 */
function any_of_type(iterable $input, string ...$types): bool
{
    if (empty($input)) {
        return false;
    }

    foreach ($input as $item) {
        if (is_of_type($item, ...$types)) {
            return true;
        }
    }

    return false;
}
