<?php

namespace PHPAlchemist\Json\Support;

/**
 * Shared property-setting logic used by both the JsonMapper service and the
 * JsonHydratorTrait so the two cannot drift apart.
 *
 * For each key it will, in order of preference:
 *   1. call a matching setter (setFoo / setFirstName), or
 *   2. assign to a matching public property.
 *
 * Both camelCase and snake_case keys are supported: a `first_name` key resolves
 * to `setFirstName()` / `$firstName` just as `firstName` does.
 */
final class ObjectHydrator
{
    /**
     * @param array<string, mixed>               $data
     * @param null|callable(string, mixed): void $assign How to assign a matched
     *                                                   property. Defaults to an external assignment (public properties
     *                                                   only). The JsonHydratorTrait passes a scope-bound closure so it can
     *                                                   also populate its own protected/private fields.
     */
    public static function hydrate(object $object, array $data, ?callable $assign = null) : void
    {
        foreach ($data as $key => $value) {
            $studly = self::studly((string) $key);
            $setter = 'set'.$studly;

            if (is_callable([$object, $setter])) {
                $object->{$setter}($value);

                continue;
            }

            // Fall back to a property, trying the raw key first and then the
            // camelCased form so snake_case keys map onto camelCase fields.
            foreach ([(string) $key, lcfirst($studly)] as $property) {
                if (!property_exists($object, $property)) {
                    continue;
                }

                if ($assign !== null) {
                    $assign($property, $value);
                } else {
                    $object->{$property} = $value;
                }

                break;
            }
        }
    }

    /**
     * Convert a JSON key to StudlyCase: `first_name` -> `FirstName`,
     * `firstName` -> `FirstName`, `foo` -> `Foo`.
     */
    private static function studly(string $key) : string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $key)));
    }
}
