<?php

declare(strict_types=1);

namespace PHPAlchemist\Json\Contract;

interface JsonHydratableInterface
{
    /**
     * Populate the implementing object from a JSON string.
     */
    public function hydrateFromJson(string $json) : void;
}
