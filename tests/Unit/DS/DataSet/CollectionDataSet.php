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

    /**
     * Produce un mapa base asociativo para validar operaciones
     * de adición, reemplazo y descarte de claves específicas.
     *
     * @return array<string, string>
     */
    public static function associativeDictionary(): array
    {
        return [
            'es' => 'España',
            'fr' => 'Francia',
            'it' => 'Italia',
        ];
    }
}
