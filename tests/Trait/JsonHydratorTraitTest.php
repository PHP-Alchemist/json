<?php

namespace Unit\Trait;

use PHPAlchemist\Trait\JsonHydratorTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

class MockJsonHydratorTraitClass
{
    use JsonHydratorTrait;

    public ?string $foo;
    protected ?string $bar;
    protected ?string $fiz;
    private ?string $buzz;

    public function getFoo() : ?string
    {
        return $this->foo;
    }

    public function getBar() : ?string
    {
        return $this->bar;
    }

    public function setBar($bar) : void
    {
        $this->bar = $bar;
    }

    public function getFiz() : ?string
    {
        return $this->fiz;
    }

    public function getBuzz() : ?string
    {
        return $this->buzz;
    }

    public function setBuzz($buzz) : void
    {
        $this->buzz = $buzz;
    }
}

#[CoversClass(JsonHydratorTrait::class)]
class JsonHydratorTraitTest extends TestCase
{
    public function testHydrateFromJson()
    {
        $json = '{"foo":"alpha","bar":"beta","fiz":"charlie","buzz":"delta"}';

        $obj = new MockJsonHydratorTraitClass();
        $obj->hydrateFromJson($json);

        $this->assertEquals('alpha', $obj->foo);
        $this->assertEquals('beta', $obj->getBar());
        $this->assertEquals('charlie', $obj->getFiz());
        $this->assertEquals('delta', $obj->getBuzz());
    }
}
