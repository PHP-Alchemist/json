<?php

namespace PHPAlchemist\Json\Trait;

use PHPAlchemist\Json\Support\ObjectHydrator;

trait JsonHydratorTrait
{
    public function hydrateFromJson(string $json) : void
    {
        $jsonDecodedData = json_decode($json, true);
        if (!is_array($jsonDecodedData)) {
            return;
        }

        // The closure is bound to this object's scope, so the trait retains its
        // ability to populate protected/private fields that have no setter.
        ObjectHydrator::hydrate($this, $jsonDecodedData, function (string $property, $value) : void {
            $this->{$property} = $value;
        });
    }
}
