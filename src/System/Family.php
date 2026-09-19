<?php

declare(strict_types=1);

namespace PlanB\Core\System;

enum Family: string
{
    case Windows = 'Windows';
    case Linux = 'Linux';
    case Darwin = 'Darwin';
    case BSD = 'BSD';
    case Solaris = 'Solaris';
    case Unknown = 'Unknown';

    public static function current(?string $default = null): self
    {
        $value = $default ?? PHP_OS_FAMILY;

        return self::tryFrom($value) ?? self::Unknown;
    }

    public function isWindows(): bool
    {
        return $this === self::Windows;
    }

    public function isLinux(): bool
    {
        return $this === self::Linux;
    }

    public function isSolaris(): bool
    {
        return $this === self::Solaris;
    }

    public function isDarwin(): bool
    {
        return $this === self::Darwin;
    }

    public function isBSD(): bool
    {
        return $this === self::BSD;
    }

    public function isUnknown(): bool
    {
        return $this === self::Unknown;
    }
}
