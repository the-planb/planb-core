<?php

declare(strict_types=1);

namespace PlanB\Core\System;

use PlanB\Core\System\Exception\EnvironmentHomeNotFoundException;

final readonly class Home
{
    private Family $family;

    public function __construct(?Family $family = null)
    {
        $this->family = $family ?? Family::current();
    }

    public function path(): string
    {
        $home = $this->family->isWindows()
            ? (getenv('USERPROFILE') ?: getenv('HOME'))
            : (getenv('HOME') ?: getenv('USERPROFILE'));

        if (!$home) {
            throw EnvironmentHomeNotFoundException::missingVariable();
        }

        return $home;
    }
}
