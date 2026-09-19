<?php

declare(strict_types=1);

namespace PlanB\Core\Path\Exception;

final class InvalidPathException extends \InvalidArgumentException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function emptyPath(): self
    {
        return new self('La ruta no puede ser una cadena vacia');
    }

    public static function containsInvalidCharacters(string $path): self
    {
        $message = "La ruta '{$path}' contiene caracteres no permitidos por el sistema de archivos.";

        return new self($message);
    }
}
