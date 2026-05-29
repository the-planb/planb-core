<?php

declare(strict_types=1);

namespace PlanB\Core\DS\PHPStan\Rule;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Generic\TemplateType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;
use PHPStan\Type\VerbosityLevel;
use PlanB\Core\DS\Attribute\ElementType;

/**
 * @implements Rule<Class_>
 */
class ElementTypeRule implements Rule
{
    public function __construct(private readonly ReflectionProvider $reflectionProvider) {}

    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        /** @var Class_ $node */
        if ($node->isAbstract() || $node->name === null) {
            return [];
        }

        if (!isset($node->namespacedName)) {
            return [];
        }

        $className = $node->namespacedName->toString();

        if (!$this->reflectionProvider->hasClass($className)) {
            return [];
        }

        $classReflection = $this->reflectionProvider->getClass($className);
        $ancestors = $classReflection->getAncestors();
        $parentReflection = array_find($ancestors, fn ($ancestorReflection, $ancestorFqcn) => ($ancestorFqcn === 'PlanB\DS\Collection' || $ancestorReflection->isSubclassOf('PlanB\DS\Collection')) && $ancestorReflection->getActiveTemplateTypeMap()->hasType('TValue'));

        if ($parentReflection === null) {
            return [];
        }

        $templateTypeMap = $parentReflection->getActiveTemplateTypeMap();
        $resolvedPhpStanType = $templateTypeMap->getType('TValue');

        if ($resolvedPhpStanType === null) {
            return [];
        }

        if ($this->hasTemplateType($resolvedPhpStanType)) {
            return [];
        }

        // Corrección 1: Extraer atributos usando la API estática de PHPStan en lugar de la reflexión de PHP
        $nativeReflection = $classReflection->getNativeReflection();
        $attributes = $nativeReflection->getAttributes(ElementType::class);

        if (empty($attributes)) {
            return [
                RuleErrorBuilder::message('Falta el atributo obligatorio: Toda clase que herede de la familia Collection debe declarar el atributo #[ElementType].')
                    ->line($node->getLine())
                    ->identifier('collection.missingAttribute') // Corrección 2: Identificador obligatorio
                    ->build(),
            ];
        }

        $attributeArgs = $attributes[0]->getArguments();

        // Corrección 3: Asegurar que tratamos los argumentos como strings puros para complacer a array_map
        /** @var string[] $stringArgs */
        $stringArgs = array_map(static fn (mixed $arg): string => is_string($arg) ? $arg : '', $attributeArgs);

        $attributeTypes = array_map(function (string $typeName): string {
            if (str_contains($typeName, '\\')) {
                $parts = explode('\\', $typeName);

                return strtolower(end($parts));
            }

            return strtolower($typeName);
        }, $stringArgs);
        sort($attributeTypes);

        $phpStanTypes = [];
        if ($resolvedPhpStanType instanceof UnionType) {
            foreach ($resolvedPhpStanType->getTypes() as $subType) {
                $phpStanTypes[] = strtolower($this->normalizeTypeName($subType));
            }
        } else {
            $phpStanTypes[] = strtolower($this->normalizeTypeName($resolvedPhpStanType));
        }
        sort($phpStanTypes);

        if ($attributeTypes !== $phpStanTypes) {
            return [
                RuleErrorBuilder::message(sprintf(
                    'Asimetría de tipos en la colección: El atributo #[ElementType(%s)] no coincide con el bloque @extends (%s). Ambos deben tener exactamente los mismos tipos.',
                    implode(', ', $stringArgs), // Corrección 4: implode recibe ahora un array tipado estrictamente como string[]
                    $resolvedPhpStanType->describe(VerbosityLevel::typeOnly()),
                ))
                    ->line($node->getLine())
                    ->identifier('collection.typeAsymmetry') // Corrección 2: Identificador obligatorio
                    ->build(),
            ];
        }

        return [];
    }

    private function hasTemplateType(Type $type): bool
    {
        if ($type instanceof TemplateType) {
            return true;
        }

        if ($type instanceof UnionType) {
            foreach ($type->getTypes() as $subType) {
                if ($this->hasTemplateType($subType)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function normalizeTypeName(Type $type): string
    {
        // Usamos isObject() de la API oficial de PHPStan
        if ($type->isObject()->yes()) {
            $classNames = $type->getObjectClassNames();
            $fqcn = $classNames[0] ?? '';

            // Separamos el FQCN por barras invertidas para quedarnos con el nombre corto de la clase
            $parts = explode('\\', $fqcn);

            return end($parts);
        }

        return $type->describe(VerbosityLevel::typeOnly());
    }
}
