<?php

declare(strict_types=1);

namespace PlanB\Core\DS\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ElementType
{
    /** @var string[] */
    public private(set) array $types;

    public function __construct(string ...$types)
    {
        $this->types = $types;
    }
}
