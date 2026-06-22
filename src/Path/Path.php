<?php

declare(strict_types=1);

namespace PlanB\Core\Path;

use PlanB\Core\Path\Exception\InvalidPathException;
use Symfony\Component\Filesystem\Path as SymfonyPath;

final class Path implements \Stringable
{
    public false|string $realPath {
        get => realpath($this->path);
    }

    private function __construct(
        public private(set) string $path,
    ) {}

    public function __toString(): string
    {
        return $this->path;
    }

    public static function make(self|string $path): self
    {
        if ($path instanceof self) {
            return $path;
        }

        if (trim($path) === '') {
            throw InvalidPathException::emptyPath();
        }

        $canonical = new CanonicalPath();

        return new self($canonical->resolve($path));
    }

    /**
     * Combina múltiples fragmentos de rutas en una sola ruta canónica.
     */
    public static function join(self|string ...$paths): self
    {
        $stringPaths = array_map(static fn ($p) => (string) $p, $paths);

        return self::make(SymfonyPath::join(...$stringPaths));
    }

    /**
     * Comprueba si una ruta es la base (directorio padre/ancestro) de otra.
     */
    public static function isBasePath(self|string $basePath, self|string $ofPath): bool
    {
        $baseStr = $basePath instanceof self ? $basePath->path : self::make($basePath)->path;
        $ofStr = $ofPath instanceof self ? $ofPath->path : self::make($ofPath)->path;

        // Si son la misma ruta, una no puede ser base/ancestro de la otra
        if ($baseStr === $ofStr) {
            return false;
        }

        return SymfonyPath::isBasePath($baseStr, $ofStr);
    }

    /**
     * Devuelve el nombre completo con su extensión (ej: "invoice.pdf").
     */
    public function basename(): string
    {
        $extension = $this->extension();
        $filename = $this->filename();

        return $extension !== '' ? $filename . '.' . $extension : $filename;
    }

    /**
     * Devuelve el nombre del archivo sin la extensión (ej: "invoice").
     */
    public function filename(): string
    {
        return SymfonyPath::getFilenameWithoutExtension($this->path);
    }

    /**
     * Devuelve la extensión del archivo (ej: "pdf").
     */
    public function extension(): string
    {
        return SymfonyPath::getExtension($this->path);
    }

    /**
     * Comprueba si la ruta actual tiene una extensión (u opcionalmente una extensión específica).
     */
    public function hasExtension(?string $extension = null): bool
    {
        if ($extension === null) {
            return $this->extension() !== '';
        }

        return strtolower($this->extension()) === strtolower(ltrim($extension, '.'));
    }

    /**
     * Devuelve un nuevo objeto Path con la extensión cambiada o añadida.
     */
    public function changeExtension(string $extension): self
    {
        return self::make(SymfonyPath::changeExtension($this->path, $extension));
    }

    /**
     * Devuelve el objeto Path del directorio contenedor.
     */
    public function dirname(): self
    {
        return self::make(SymfonyPath::getDirectory($this->path));
    }

    /**
     * Devuelve un ancestro según el nivel (1 = padre, 2 = abuelo...).
     */
    public function parent(int $level = 1): self
    {
        $current = $this;
        for ($i = 0; $i < $level; ++$i) {
            $current = $current->dirname();
        }

        return $current;
    }

    /**
     * Encuentra la ruta base común más larga entre la ruta actual y otras rutas dadas.
     */
    public function getLongestCommonBasePath(self|string ...$paths): ?self
    {
        $stringPaths = array_map(static fn ($p) => (string) $p, $paths);

        // Añadimos la ruta actual a la comparación
        array_unshift($stringPaths, $this->path);

        $common = SymfonyPath::getLongestCommonBasePath(...$stringPaths);

        return $common !== null ? self::make($common) : null;
    }

    public function isAbsolute(): bool
    {
        return SymfonyPath::isAbsolute($this->path);
    }

    public function isRelative(): bool
    {
        return SymfonyPath::isRelative($this->path);
    }

    /**
     * Une fragmentos a la ruta actual de forma fluida.
     */
    public function append(self|string $segment): self
    {
        return self::join($this, $segment);
    }

    /**
     * Calcula la ruta relativa con respecto a una base.
     */
    public function relativeTo(self|string $base): self
    {
        $basePath = $base instanceof self ? $base : self::make($base);

        return self::make(SymfonyPath::makeRelative($this->path, $basePath->path));
    }

    /**
     * Calcula la ruta absoluta utilizando una base.
     */
    public function absoluteTo(self|string $base): self
    {
        $basePath = $base instanceof self ? $base : self::make($base);

        return self::make(SymfonyPath::makeAbsolute($this->path, $basePath->path));
    }

    /**
     * Comprueba si la ruta actual es descendiente de la ruta dada.
     */
    public function isChildOf(self|string $parent): bool
    {
        return self::isBasePath($parent, $this);
    }

    /**
     * Comprueba si la instancia actual es la base de la ruta dada (Fluido).
     */
    public function isBaseOf(self|string $path): bool
    {
        return self::isBasePath($this, $path);
    }

    /**
     * Comprueba si dos rutas son estructuralmente idénticas.
     */
    public function equals(self|string $other): bool
    {
        $otherPath = $other instanceof self ? $other : self::make($other);

        return $this->path === $otherPath->path;
    }
}
