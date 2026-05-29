<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\DataSet;

class CollectionDataSet
{
    /**
     * Produce un set secuencial de números y datos simples
     * ideal para validar mapeos, reducciones y ordenaciones complejas.
     *
     * @return array<int, int>
     */
    public static function numbersForBasicOperations(): array
    {
        return [1, 2, 3, 4, 5];
    }
}
