<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\Path;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PlanB\Core\Path\Exception\InvalidPathException;
use PlanB\Core\Path\Path;

/**
 * @internal
 */
#[CoversClass(Path::class)]
#[CoversClass(InvalidPathException::class)]
final class PathTest extends TestCase
{
    #[Test]
    public function it_fails_with_empty_path(): void
    {
        $this->expectException(InvalidPathException::class);
        $this->expectExceptionMessageIsOrContains('La ruta no puede ser una cadena vacia');
        Path::make('   ');
    }

    #[Test]
    public function it_returns_the_same_instance_if_input_is_already_a_path_object(): void
    {
        $path1 = Path::make('/app/src');
        $path2 = Path::make($path1);

        self::assertSame($path1, $path2);
    }

    #[Test]
    #[DataProvider('pathAnalysisProvider')]
    public function it_analyzes_path_properties(
        string $input,
        string $basename,
        string $filename,
        string $extension,
        string $dirname,
        bool $isAbsolute,
    ): void {
        $path = Path::make($input);

        self::assertSame($basename, $path->basename());
        self::assertSame($filename, $path->filename());
        self::assertSame($extension, $path->extension());
        self::assertSame($dirname, $path->dirname()->path);
        self::assertSame($isAbsolute, $path->isAbsolute());
        self::assertSame(!$isAbsolute, $path->isRelative());
    }

    public static function pathAnalysisProvider(): array
    {
        return [
            'Archivo absoluto' => [
                '/var/www/html/index.php',
                'index.php',
                'index',
                'php',
                '/var/www/html',
                true,
            ],
            'Archivo sin extensión' => [
                '/usr/bin/docker',
                'docker',
                'docker',
                '',
                '/usr/bin',
                true,
            ],
            'Ruta relativa' => [
                'src/Core/Path.php',
                'Path.php',
                'Path',
                'php',
                'src/Core',
                false,
            ],
            'Directorio sin extensión' => [
                '/var/www/html',
                'html',
                'html',
                '',
                '/var/www',
                true,
            ],
            'Oculto con extensión' => [
                '/.github/workflows/ci.yml',
                'ci.yml',
                'ci',
                'yml',
                '/.github/workflows',
                true,
            ],
        ];
    }

    #[Test]
    #[DataProvider('extensionProvider')]
    public function it_checks_and_changes_extensions(
        string $input,
        ?string $check,
        bool $hasExtension,
        string $newExt,
        string $expectedPath,
    ): void {
        $path = Path::make($input);

        self::assertSame($hasExtension, $path->hasExtension($check));
        self::assertSame($expectedPath, $path->changeExtension($newExt)->path);
    }

    public static function extensionProvider(): array
    {
        return [
            'Cualquier extensión existente' => ['image.png', null, true, 'jpg', 'image.jpg'],
            'Extensión específica correcta' => ['image.PNG', 'png', true, 'svg', 'image.svg'],
            'Extensión específica correcta uppercase' => ['image.png', 'PNG', true, 'svg', 'image.svg'],
            'Extensión con punto previo' => ['image.png', '.png', true, 'webp', 'image.webp'],
            'Extensión incorrecta' => ['image.png', 'gif', false, 'png', 'image.png'],
        ];
    }

    #[Test]
    public function it_resolves_ancestors_by_level(): void
    {
        $path = Path::make('/one/two/three/four');

        self::assertSame('/one/two/three', (string) $path->parent());
        self::assertSame('/one/two/three', (string) $path->parent(1));
        self::assertSame('/one/two', (string) $path->parent(2));
        self::assertSame('/one', (string) $path->parent(3));
    }

    #[Test]
    public function it_joins_multiple_segments(): void
    {
        $path = Path::join('/var/www', 'html', Path::make('public'), 'index.php');
        self::assertSame('/var/www/html/public/index.php', $path->path);
    }

    #[Test]
    public function it_appends_segments_fluently(): void
    {
        $base = Path::make('/app');
        $resolved = $base->append('src')->append('Entity');

        self::assertSame('/app/src/Entity', $resolved->path);
    }

    #[Test]
    #[DataProvider('hierarchyProvider')]
    public function it_handles_hierarchical_relationships(
        Path|string $base,
        Path|string $target,
        bool $isBase,
    ): void {
        // Probar primero el método estático pura y duramente con los tipos originales
        self::assertSame($isBase, Path::isBasePath($base, $target));

        // Forzar instancias de objeto para probar los métodos fluidos de instancia
        $basePath = $base instanceof Path ? $base : Path::make($base);
        $targetPath = $target instanceof Path ? $target : Path::make($target);

        self::assertSame($isBase, $basePath->isBaseOf($targetPath));
        self::assertSame($isBase, $targetPath->isChildOf($basePath));
    }

    public static function hierarchyProvider(): array
    {
        return [
            // --- CASO TRUE: HIJO DIRECTO ABSOLUTO (Combinatoria de tipos) ---
            'Hijo directo: Path -> Path' => [Path::make('/app'), Path::make('/app/src'), true],
            'Hijo directo: Path -> string' => [Path::make('/app'), '/app/src', true],
            'Hijo directo: string -> Path' => ['/app', Path::make('/app/src'), true],
            'Hijo directo: string -> string' => ['/app', '/app/src', true],

            // --- CASO FALSE: CARPETA SIMILAR (Combinatoria de tipos) ---
            'Nombre similar: Path -> Path' => [Path::make('/app/src'), Path::make('/app/src-backup'), false],
            'Nombre similar: Path -> string' => [Path::make('/app/src'), '/app/src-backup', false],
            'Nombre similar: string -> Path' => ['/app/src', Path::make('/app/src-backup'), false],
            'Nombre similar: string -> string' => ['/app/src', '/app/src-backup', false],

            // --- OTROS CASOS ESTRUCTURALES ---
            'Nieto absoluto: string -> string' => ['/app', '/app/src/Core', true],
            'Mismo directorio: string -> string' => ['/app', '/app', false],
        ];
    }

    #[Test]
    public function it_finds_longest_common_base_path(): void
    {
        $path = Path::make('/app/src/Domain/Path');

        $common = $path->getLongestCommonBasePath(
            '/app/src/Core/System',
            Path::make('/app/src/Core/Exception'),
        );

        self::assertNotNull($common);
        self::assertSame('/app/src', $common->path);
    }

    #[Test]
    public function it_checks_structural_equality(): void
    {
        $path1 = Path::make('/app/src/../src/Core');
        $path2 = Path::make('/app/src/Core');

        self::assertTrue($path1->equals($path2));
        self::assertTrue($path1->equals('/app/src/Core'));
    }

    #[Test]
    public function it_calcules_the_relative_path(): void
    {
        $base = Path::make('/app/src/Core/System/Go');
        $path = '/app/src/Core';

        $relative = Path::make('System/Go');

        self::assertTrue($base->relativeTo($path)->equals($relative));
        self::assertTrue($base->relativeTo(Path::make($path))->equals($relative));
    }

    #[Test]
    public function it_calcules_the_absolute_path(): void
    {
        $base = Path::make('System/Go');
        $path = '/app/src/Core';

        $absolute = Path::make('/app/src/Core/System/Go');

        self::assertTrue($base->absoluteTo($path)->equals($absolute));
        self::assertTrue($base->absoluteTo(Path::make($path))->equals($absolute));
    }
}
