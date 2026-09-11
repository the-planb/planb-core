<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\System;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\System\Exception\EnvironmentHomeNotFoundException;
use PlanB\Core\System\Family;
use PlanB\Core\System\Home;

/**
 * @internal
 */
#[CoversClass(Home::class)]
#[CoversClass(EnvironmentHomeNotFoundException::class)]
final class HomeTest extends TestCase
{
    private array $originalEnv = [];

    /**
     * Guardamos el estado real de la máquina antes de cada test para no romper
     * la configuración del sistema de desarrollo o del entorno de CI/CD.
     */
    protected function setUp(): void
    {
        $this->originalEnv['HOME'] = getenv('HOME') ?: null;
        $this->originalEnv['USERPROFILE'] = getenv('USERPROFILE') ?: null;
    }

    /**
     * Restauramos las variables de entorno originales después de cada test.
     */
    protected function tearDown(): void
    {
        foreach ($this->originalEnv as $variable => $value) {
            if ($value === null) {
                putenv("{$variable}"); // Elimina la variable de entorno
            } else {
                putenv("{$variable}={$value}");
            }
        }
    }

    #[Test]
    public function it_resolves_home_directory_on_attending_to_so_family(): void
    {
        putenv('HOME=/home/linuxuser');
        putenv('USERPROFILE=C:\Users\winuser');

        $home = new Home(Family::Linux);
        self::assertSame('/home/linuxuser', $home->path());

        $home = new Home(Family::Windows);
        self::assertSame('C:\Users\winuser', $home->path());
    }

    #[Test]
    public function it_resolves_home_directory_on_linux_using_home_variable(): void
    {
        putenv('HOME=/home/linuxuser');
        putenv('USERPROFILE'); // Nos aseguramos de que esté vacía

        $home = new Home(Family::Linux);
        self::assertSame('/home/linuxuser', $home->path());
    }

    #[Test]
    public function it_resolves_home_directory_on_windows_using_userprofile_variable(): void
    {
        putenv('HOME'); // Nos aseguramos de que esté vacía
        putenv('USERPROFILE=C:\Users\winuser');

        $home = new Home(Family::Windows);

        self::assertSame('C:\Users\winuser', $home->path());
    }

    #[Test]
    public function it_resolves_home_directory_on_windows_falling_back_to_home_variable(): void
    {
        putenv('HOME=/fallback/home');
        putenv('USERPROFILE');

        $home = new Home(Family::Windows);

        self::assertSame('/fallback/home', $home->path());
    }

    #[Test]
    public function it_throws_an_exception_if_no_environment_variables_are_found(): void
    {
        putenv('HOME');
        putenv('USERPROFILE');

        $home = new Home(Family::Linux);

        $this->expectException(EnvironmentHomeNotFoundException::class);
        $this->expectExceptionMessageIsOrContains('No se pudo determinar el directorio HOME del sistema operativo');

        $home->path();
    }
}
