<?php

declare(strict_types=1);

namespace PlanB\Core\System\Exception;

final class EnvironmentHomeNotFoundException extends \RuntimeException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function missingVariable(): self
    {
        return new self('No se pudo determinar el directorio HOME del sistema operativo (variables HOME o USERPROFILE ausentes).');
    }
}
