<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\System;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\System\Family;

/**
 * @internal
 */
#[CoversClass(Family::class)]
final class FamilyTest extends TestCase
{
    #[Test]
    #[DataProvider('familyProvider')]
    public function it_checks_the_type(Family $os, string $expectedMethod): void
    {
        self::assertEquals($expectedMethod === 'isWindows', $os->isWindows());
        self::assertEquals($expectedMethod === 'isLinux', $os->isLinux());
        self::assertEquals($expectedMethod === 'isDarwin', $os->isDarwin());
        self::assertEquals($expectedMethod === 'isBSD', $os->isBSD());
        self::assertEquals($expectedMethod === 'isSolaris', $os->isSolaris());
        self::assertEquals($expectedMethod === 'isUnknown', $os->isUnknown());
    }

    public static function familyProvider(): array
    {
        return [
            'Caso Windows' => [Family::Windows, 'isWindows'],
            'Caso Linux' => [Family::Linux, 'isLinux'],
            'Caso Darwin' => [Family::Darwin, 'isDarwin'], // Unificado con la aserción isMacOs
            'Caso BSD' => [Family::BSD, 'isBSD'],
            'Caso Solaris' => [Family::Solaris, 'isSolaris'],
            'Caso Unknown' => [Family::Unknown, 'isUnknown'],
        ];
    }

    #[Test]
    #[DataProvider('currentProvider')]
    public function it_checks_the_current_family(?string $default, Family $expected): void
    {
        $this->assertEquals($expected, Family::current($default));
    }

    public static function currentProvider()
    {
        return [
            [null, Family::current(PHP_OS_FAMILY)],
            [PHP_OS_FAMILY, Family::current(PHP_OS_FAMILY)],
            ['Windows', Family::Windows],
            ['Linux', Family::Linux],
            ['Darwin', Family::Darwin],
            ['BSD', Family::BSD],
            ['Solaris', Family::Solaris],
            ['Unknown', Family::Unknown],
            ['other', Family::Unknown],
        ];
    }
}
