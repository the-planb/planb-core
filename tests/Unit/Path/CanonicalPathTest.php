<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\Path;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\Path\CanonicalPath;
use PlanB\Core\Path\Exception\InvalidPathException;
use PlanB\Core\System\Family;

/**
 * @internal
 */
#[CoversClass(CanonicalPath::class)]
#[CoversClass(InvalidPathException::class)]
final class CanonicalPathTest extends TestCase
{
    #[Test]
    #[DataProvider('validPathsProvider')]
    public function it_resolves_canonical_paths_successfully(string $input, string $expected): void
    {
        $canonicalPath = new CanonicalPath();

        self::assertSame($expected, $canonicalPath->resolve($input));
    }

    public static function validPathsProvider(): array
    {
        return [
            'Normaliza barras duplicadas y puntos' => ['/var/www//html/../html/index.php', '/var/www/html/index.php'],
            'Mantiene rutas relativas limpias' => ['src/Core/./Path.php', 'src/Core/Path.php'],
            'Quita barras finales en directorios' => ['/app/config/', '/app/config'],
        ];
    }

    #[Test]
    public function it_resolves_home_directory_shortcut(): void
    {
        $canonicalPath = new CanonicalPath();
        $resolved = $canonicalPath->resolve('~/documents/invoice.pdf');

        $expected = getenv('HOME') . '/documents/invoice.pdf';

        self::assertSame($expected, $resolved);
    }

    #[Test]
    #[DataProvider('invalidCharactersProvider')]
    public function it_fails_when_path_contains_invalid_characters(string $invalidPath): void
    {
        $canonicalPath = new CanonicalPath();

        $this->expectException(InvalidPathException::class);

        $canonicalPath->resolve($invalidPath);
    }

    public static function invalidCharactersProvider(): array
    {
        return [
            'Contiene Null Byte' => ["/app/src/file\0.php"],
            'Contiene asterisco' => ['/app/src/*.php'],
            'Contiene signo de interrogación' => ['/app/src/file?.php'],
            'Contiene comillas dobles' => ['/app/src/"file".php'],
            'Contiene menor que' => ['/app/src/<file>.php'],
            'Contiene mayor que' => ['/app/src/file>.php'],
            'Contiene tubería (pipe)' => ['/app/src/file|backup.php'],
        ];
    }

    #[Test]
    #[DataProvider('familyProvider')]
    public function it_fails_with_colons_only_on_non_windows_systems(Family $family, string $input, ?string $exception): void
    {
        $path = new CanonicalPath($family);
        if (is_string($exception)) {
            $this->expectException($exception);
            $path->resolve($input);

            return;
        }

        $resolved = $path->resolve($input);
        self::assertIsString($resolved);
    }

    public static function familyProvider(): array
    {
        return [
            'Linux: Letra de unidad simulada debe fallar' => [Family::Linux, 'C:\app\src\file.php', InvalidPathException::class],
            'Linux: Dos puntos en cualquier otra posición debe fallar' => [Family::Linux, '/var/www/html/drive:file.php', InvalidPathException::class],
            'Solaris: Dos puntos en cualquier posición debe fallar' => [Family::Solaris, '/etc/init.d/service:daemon', InvalidPathException::class],
            'Windows: Letra de unidad estándar válida' => [Family::Windows, 'C:\app\src\file.php', null],
            'Windows: Letra de unidad en minúscula válida' => [Family::Windows, 'd:\project\index.php', null],
            'Windows: Ruta UNC sin dos puntos válida' => [Family::Windows, '\\\server\share\file.txt', null],
            'Windows: Ruta relativa sin dos puntos válida' => [Family::Windows, 'src\Core\Path.php', null],
            'Windows: Múltiples dos puntos debe fallar' => [Family::Windows, 'C:\path:\to\file.php', InvalidPathException::class],
            'Windows: Dos puntos mal ubicados al final debe fallar' => [Family::Windows, 'C:\path\to\:file.php', InvalidPathException::class],
            'Windows: Dos puntos en posición incorrecta sin unidad válida' => [Family::Windows, '\path\to:file.php', InvalidPathException::class],
            'Windows: Carácter numérico como unidad de disco debe fallar' => [Family::Windows, '1:\path\to\file.php', InvalidPathException::class],
            'Windows: Carácter especial como unidad de disco debe fallar' => [Family::Windows, '&:\path\to\file.php', InvalidPathException::class],
        ];
    }
}
