<?php

declare(strict_types=1);

namespace PlanB\Core\Path;

use PlanB\Core\Path\Exception\InvalidPathException;
use PlanB\Core\System\Family;
use PlanB\Core\System\Home;
use Symfony\Component\Filesystem\Path as SymfonyPath;

final readonly class CanonicalPath
{
    public const string VALID_SCHEMA_PATTERN = '#^(?P<schema>[a-z][a-z0-9+\-.]*)://(?P<path>.*)$#i';

    private Family $family;
    private Home $home;

    public function __construct(?Family $family = null)
    {
        $this->family = $family ?? Family::current();
        $this->home = new Home($this->family);
    }

    public function resolve(string $path): string
    {
        if ($path === '~') {
            $path = $this->home->path();
        }

        if (str_starts_with($path, '~/')) {
            $home = $this->home->path();
            $path = $home . substr($path, 1);
        }

        $schema = '';

        if (preg_match(self::VALID_SCHEMA_PATTERN, $path, $matches)) {
            $schema = $matches['schema'];
            $path = $matches['path'];
        }

        $canonical = SymfonyPath::canonicalize($path);
        $validatedPath = $this->ensureValidCharacters($canonical);

        if ($schema !== '') {
            return "{$schema}://{$validatedPath}";
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