<?php

declare(strict_types=1);

namespace PlanB\Core\Path;

use PlanB\Core\Path\Exception\InvalidPathException;
use PlanB\Core\System\Family;
use PlanB\Core\System\Home;
use Symfony\Component\Filesystem\Path as SymfonyPath;

final readonly class CanonicalPath
{
    public const string VALID_SCHEMA_PATTERN = '#^([a-z][a-z0-9+\-.]*):{1,2}//#i';

    private Family $family;
    private Home $home;

    public function __construct(?Family $family = null)
    {
        $this->family = $family ?? Family::current();
        $this->home = new Home($this->family);
    }

    public function resolve(string $path): string
    {
        if (str_starts_with($path, '~/')) {
            $home = $this->home->path();
            $path = $home . substr($path, 1);
        }

        $scheme = '';

        if (preg_match(self::VALID_SCHEMA_PATTERN, $path, $matches)) {
            $scheme = $matches[1]; // Captura solo "vfs"
            // Conservamos las dos barras de entrada más el resto de la ruta
            // Ej: "vfs://var/www/file.php" -> $path pasa a ser "/var/www/file.php"
            $schemeDelimiterLength = strlen($matches[0]);
            $path = '/' . ltrim(substr($path, $schemeDelimiterLength), '/');
        }

        $canonical = SymfonyPath::canonicalize($path);
        $validatedPath = $this->ensureValidCharacters($canonical);

        if ($scheme !== '') {
            return sprintf('%s://%s', $scheme, ltrim($validatedPath, '/'));
        }

        return $validatedPath;
    }

    private function ensureValidCharacters(string $path): string
    {
        if (str_contains($path, "\0")) {
            throw InvalidPathException::containsInvalidCharacters($path);
        }

        if (preg_match('/[*?"<>|]/', $path)) {
            throw InvalidPathException::containsInvalidCharacters($path);
        }

        if ($this->family->isWindows()) {
            $count = substr_count($path, ':');

            return match (true) {
                $count === 0 => $path,
                $count === 1 && strpos($path, ':') === 1 && ctype_alpha($path[0]) => $path,
                default => throw InvalidPathException::containsInvalidCharacters($path),
            };
        }

        if (str_contains($path, ':')) {
            throw InvalidPathException::containsInvalidCharacters($path);
        }

        return $path;
    }
}