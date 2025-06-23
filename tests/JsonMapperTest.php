<?php

namespace Unit;

use PHPAlchemist\Json\Exception\BadJsonException;
use PHPAlchemist\Json\Service\JsonMapper;
use PHPAlchemist\Json\Trait\JsonHydratorTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

trait FizSetter
{
    public function setFiz($fiz) : void
    {
        $this->fiz = $fiz;
    }
}

abstract class AbstractHydratorClass
{
    public ?string $foo    = null;
    protected ?string $bar = null;
    protected ?string $fiz = null;

    // foo
    public function getFoo() : ?string
    {
        return $this->foo;
    }

    // bar

    public function getBar() : ?string
    {
        return $this->bar;
    }

    public function setBar($bar) : void
    {
        $this->bar = $bar;
    }

    // fiz

    public function getFiz() : ?string
    {
        return $this->fiz;
    }
}

class MockJsonBadHydrationClass extends AbstractHydratorClass
{
    private ?string $buzz = null;

    // buzz
    public function setBuzz(string $buzz) : void
    {
        $this->buzz = $buzz;
    }

    public function getBuzz() : ?string
    {
        return $this->buzz;
    }
}

class MockJsonHydratorClass extends AbstractHydratorClass
{
    use JsonHydratorTrait;
    use FizSetter;

    private ?string $buzz = null;

    // buzz
    public function getBuzz() : ?string
    {
        return $this->buzz;
    }

    public function setBuzz(string $buzz) : void
    {
        $this->buzz = $buzz;
    }
}

class MockBoringClass extends AbstractHydratorClass
{
    use FizSetter;

    private ?string $buzz = null;

    // buzz
    public function getBuzz() : ?string
    {
        return $this->buzz;
    }

    public function setBuzz(string $buzz) : void
    {
        $this->buzz = $buzz;
    }
}

#[CoversClass(JsonMapper::class)]
#[CoversClass(JsonHydratorTrait::class)]
class JsonMapperTest extends TestCase
{
    protected string $json = '{"foo":"alpha","bar":"beta","fiz":"charlie","buzz":"delta"}';

    public function testJsonMapperWithHydrator()
    {
        $jsonMapper = new JsonMapper();
        $obj        = $jsonMapper->map($this->json, MockJsonHydratorClass::class);
        $this->assertEquals('alpha', $obj->foo);
        $this->assertEquals('beta', $obj->getBar());
        $this->assertEquals('charlie', $obj->getFiz());
        $this->assertEquals('delta', $obj->getBuzz());
    }

    public function testJsonMapperWithBadHydration()
    {
        $this->expectException(\Error::class);
        $jsonMapper = new JsonMapper();
        $jsonMapper->map($this->json, MockJsonBadHydrationClass::class);
    }

    public function testJsonMapperWithBoringClass()
    {
        $jsonMapper = new JsonMapper();
        $obj        = $jsonMapper->map($this->json, MockBoringClass::class);
        $this->assertEquals('alpha', $obj->getFoo());
        $this->assertEquals('beta', $obj->getBar());
        $this->assertEquals('charlie', $obj->getFiz());
        $this->assertEquals('delta', $obj->getBuzz());
    }

    public function testJsonMapperSkippingUnknownKey()
    {
        $oldJson    = json_decode($this->json, true);
        $newJson    = json_encode(array_merge($oldJson, ['stuff' => 'thangs']));
        $jsonMapper = new JsonMapper();
        $obj        = $jsonMapper->map($newJson, MockBoringClass::class);
        $this->assertEquals('alpha', $obj->foo);
        $this->assertEquals('beta', $obj->getBar());
        $this->assertEquals('charlie', $obj->getFiz());
        $this->assertEquals('delta', $obj->getBuzz());
        $this->assertFalse(property_exists($obj, 'stuff'));
    }

    public function testBadJsonMapperWithBoringClass()
    {
        $this->expectException(BadJsonException::class);
        $badJson    = '"foo":"alpha","bar":"beta","fiz":"charlie","buzz"=>"delta"';
        $jsonMapper = new JsonMapper();

        $jsonMapper->map($badJson, MockBoringClass::class);
    }

    public function testBadJsonMapperWithHydrator()
    {
        $this->expectException(BadJsonException::class);
        $badJson    = '{foo:alpha,bar:beta,fiz:charlie,buzz:delta}';
        $jsonMapper = new JsonMapper();

        $jsonMapper->map($badJson, MockJsonHydratorClass::class);
    }
}
