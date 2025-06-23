<?php

namespace PHPAlchemist\Json\Trait;

trait JsonHydratorTrait
{
    public function hydrateFromJson(string $json)
    {
        $jsonDecodedData = json_decode($json, true);
        foreach ($jsonDecodedData as $key => $value) {
            if (!property_exists($this, $key)) {
                continue;
            }

            if (is_callable([$this, 'set'.ucfirst($key)])) {
                $this->{'set'.ucfirst($key)}($value);
            } elseif (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }
}
