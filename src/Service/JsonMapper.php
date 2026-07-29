<?php

declare(strict_types=1);

namespace PHPAlchemist\Json\Service;

use PHPAlchemist\Json\Contract\JsonHydratableInterface;
use PHPAlchemist\Json\Exception\BadJsonException;
use PHPAlchemist\Json\Support\ObjectHydrator;

/**
 * Class is *not* final so it could be extended by others if
 * needs be.
 *
 * @psalm-suppress ClassMustBeFinal
 */
class JsonMapper
{
    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @throws BadJsonException
     *
     * @return T
     */
    public function map(string $json, string $class) : object
    {
        if (!$this->validateJson($json)) {
            throw new BadJsonException();
        }

        /** @psalm-suppress InvalidStringClass */
        $newObject = new $class();

        if ($newObject instanceof JsonHydratableInterface) {
            $newObject->hydrateFromJson($json);

            return $newObject;
        }

        /** @var array<string, mixed> $jsonDecodedData */
        $jsonDecodedData = json_decode($json, true);
        ObjectHydrator::hydrate($newObject, $jsonDecodedData);

        return $newObject;
    }

    protected function validateJson(string $json) : bool
    {
        if (!json_validate($json)) {
            return false;
        }

        // Only a JSON object (or array) can be mapped onto an object's fields;
        // reject scalars such as `1`, `"foo"`, `true` and `null`.
        return is_array(json_decode($json, true));
    }
}
