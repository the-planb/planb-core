<?php

declare(strict_types=1);

namespace PlanB\Core\Tests\Unit\DS\DataSet;

use PlanB\Core\Tests\Unit\DS\DataSet\Domain\Email;
use PlanB\Core\Tests\Unit\DS\DataSet\Domain\Role;
use PlanB\Core\Tests\Unit\DS\DataSet\Domain\User;

class CollectionDataSet
{
    /**
     * Produce un set de datos lineal simple con duplicados nativos.
     * Esto servirá para testear la versión de unique() sin argumentos.
     *
     * @return array<int, string>
     */
    public static function simpleDuplicates(): array
    {
        return [0 => 'A', 1 => 'B', 2 => 'A', 3 => 'C', 4 => 'B'];
    }

    /**
     * Produce un set de usuarios donde existen emails idénticos (mismo Value Object Stringable)
     * y mismos IDs de negocio escalares, ideal para probar la unicidad por extractor.
     *
     * @return array<string, User>
     */
    public static function usersWithDuplicateIdentities(): array
    {
        $adminRole = new Role('Admin', 10);
        $userRole = new Role('User', 1);

        $email1 = new Email('clark.kent@dailyplanet.com');
        $email2 = new Email('bruce.wayne@waynecorp.com');

        $user1 = new User('usr_01', 'Clark Kent', $email1, $adminRole);
        $user2 = new User('usr_02', 'Bruce Wayne', $email2, $adminRole);
        $user3 = new User('usr_01', 'Kal El', $email1, $userRole);

        return [
            $user1,
            $user2,
            $user3,
        ];
    }

    /**
     * Produce dos datasets disjuntos para operaciones binarias de conjuntos (diff e intersect)
     * basados en el nivel de jerarquía del Rol de los usuarios.
     *
     * @return array{source: array<string, User>, target: array<string, User>}
     */
    public static function userSetsForAlgebra(): array
    {
        $adminRole = new Role('Admin', 10);
        $editorRole = new Role('Editor', 5);
        $userRole = new Role('User', 1);

        $u1 = new User('usr_01', 'Alice', new Email('alice@test.com'), $adminRole);
        $u2 = new User('usr_02', 'Bob', new Email('bob@test.com'), $editorRole);
        $u3 = new User('usr_03', 'Charlie', new Email('charlie@test.com'), $userRole);

        return [
            'source' => [
                $u1->id => $u1,
                $u2->id => $u2,
            ],
            'target' => [
                $u2->id => $u2, // Coincide exactamente en objeto y rol (Editor, level 5)
                $u3->id => $u3,
            ],
        ];
    }

    /**
     * Produce un set mixto de usuarios con diferentes estados de negocio
     * ideal para validar inspecciones lógicas de presencia y cumplimiento.
     *
     * @return array<int, User>
     */
    public static function usersForInspection(): array
    {
        return [
            new User('usr_01', 'Clark Kent', new Email('clark.kent@dailyplanet.com'), new Role('Admin', 10)),
            new User('usr_02', 'Bruce Wayne', new Email('bruce.wayne@waynecorp.com'), new Role('Admin', 10)),
            new User('usr_03', 'Diana Prince', new Email('diana@themyscira.gov'), new Role('User', 1)),
        ];
    }

    /**
     * Produce un set balanceado de usuarios idóneo para validar
     * particiones, fragmentaciones (chunks) y agrupaciones semánticas.
     *
     * @return array<int, User>
     */
    public static function usersForGrouping(): array
    {
        return [
            new User('usr_01', 'Alice', new Email('alice@test.com'), new Role('Admin', 10)),
            new User('usr_02', 'Bob', new Email('bob@test.com'), new Role('Editor', 5)),
            new User('usr_03', 'Charlie', new Email('charlie@test.com'), new Role('User', 1)),
            new User('usr_04', 'David', new Email('david@test.com'), new Role('Editor', 5)),
        ];
    }

    /**
     * Produce una secuencia ordenada de strings idónea para validar
     * el comportamiento de ventanas de extracción (take/drop).
     *
     * @return array<int, string>
     */
    public static function lettersForSlicing(): array
    {
        return ['A', 'B', 'C', 'D', 'E'];
    }

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
