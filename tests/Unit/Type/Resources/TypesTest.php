<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\Type\Resources;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PlanB\Core\Type\Exception\InvalidTypeError;

/**
 * @internal
 */
#[CoversFunction('type_of')]
#[CoversFunction('all_of_type')]
#[CoversFunction('assert_all_of_type')]
#[CoversFunction('any_of_type')]
#[CoversFunction('is_of_type')]
#[CoversClass(InvalidTypeError::class)]
class TypesTest extends TestCase
{
    #[DataProvider('isOfTypeProvider')]
    public function test_is_of_type(mixed $value, array $types, bool $expected): void
    {
        $this->assertSame($expected, is_of_type($value, ...$types));
    }

    public static function isOfTypeProvider(): array
    {
        $resource = fopen('php://memory', 'r');

        return [
            'empty types returns true' => [42, [], true],
            'mixed type always true' => ['hello', ['mixed'], true],
            'int verification' => [10, ['int'], true],
            'integer alias' => [10, ['integer'], true],
            'float verification' => [10.5, ['float'], true],
            'double alias' => [10.5, ['double'], true],
            'string verification' => ['test', ['string'], true],
            'bool verification' => [true, ['bool'], true],
            'boolean alias' => [false, ['boolean'], true],
            'null verification' => [null, ['null'], true],
            'array verification' => [[1, 2], ['array'], true],
            'object verification' => [new \stdClass(), ['object'], true],
            'resource verification' => [$resource, ['resource'], true],

            // Tipos avanzados
            'callable closure' => [fn () => 'hi', ['callable'], true],
            'countable array' => [[1, 2], ['countable'], true],
            'iterable array' => [[1, 2], ['iterable'], true],

            // Multi-tipo (Union types)
            'matches first in union' => ['text', ['string', 'int'], true],
            'matches last in union' => [42, ['string', 'int'], true],
            'fails union' => [42.5, ['string', 'int'], false],

            // Clases e Interfaces (Caso default)
            'instance of class' => [new \stdClass(), [\stdClass::class], true],
            'not instance of class' => [new \stdClass(), [\DateTime::class], false],

            // Case insensitivity
            'uppercase type string' => ['text', ['STRING'], true],
            'uppercase type class' => [new \stdClass(), ['STDCLASS'], true],
        ];
    }

    #[DataProvider('typeOfProvider')]
    public function test_type_of(mixed $value, string $expected): void
    {
        $this->assertSame($expected, type_of($value));
    }

    public static function typeOfProvider(): array
    {
        $resource = fopen('php://memory', 'r');
        fclose($resource);

        return [
            'int detection' => [42, 'int'],
            'float detection' => [42.5, 'float'],
            'string detection' => ['hello', 'string'],
            'bool detection' => [true, 'bool'],
            'array detection' => [[], 'array'],
            'null detection' => [null, 'null'],
            'closed resource' => [$resource, 'resource'],
            'class instance returns FQCN' => [new \DateTime(), \DateTime::class],
            'closure returns FQCN' => [fn () => 1, \Closure::class],
        ];
    }

    public function test_all_of_type_with_empty_array_returns_true(): void
    {
        $this->assertTrue(all_of_type([], 'int'));
    }

    public function test_all_of_type_success(): void
    {
        $this->assertTrue(all_of_type([1, 2, 3], 'int'));
        $this->assertTrue(all_of_type(['a', 'b', 'c'], 'string'));
        $this->assertTrue(all_of_type([new \stdClass(), new \stdClass()], \stdClass::class));
    }

    public function test_all_of_type_fails_if_one_element_differs(): void
    {
        $this->assertFalse(all_of_type([1, 2, '3', 4], 'int'));
    }

    public function test_all_of_type_with_multiple_types(): void
    {
        $this->assertTrue(all_of_type([1, 'two', 3, 'four'], 'int', 'string'));
        $this->assertFalse(all_of_type([1, 'two', 3.5], 'int', 'string'));
    }

    public function test_any_of_type_with_empty_array_returns_false(): void
    {
        $this->assertFalse(any_of_type([], 'int'));
    }

    public function test_any_of_type_success(): void
    {
        $this->assertTrue(any_of_type([1, 2, 'three', 4], 'string'));
    }

    public function test_any_of_type_fails_if_none_match(): void
    {
        $this->assertFalse(any_of_type([1, 2, 3], 'string', 'float'));
    }

    public function test_assert_all_of_type_throws_an_exception_if_some_element_different(): void
    {
        $this->expectException(InvalidTypeError::class);
        assert_all_of_type([1, 2, 3], 'string', 'float');
    }

    public function test_assert_all_of_type_pass_if_elements_are_same_type(): void
    {
        assert_all_of_type([1, 2, 3], 'int');
        $this->assertTrue(true);
    }

    public function test_assert_all_of_type_pass_if_input_is_empty(): void
    {
        assert_all_of_type([], 'int');
        $this->assertTrue(true);
    }
}
