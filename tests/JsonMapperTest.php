<?php

namespace Unit;

use PHPAlchemist\Json\Contract\JsonHydratableInterface;
use PHPAlchemist\Json\Exception\BadJsonException;
use PHPAlchemist\Json\Service\JsonMapper;
use PHPAlchemist\Json\Trait\JsonHydratorTrait;
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
    public ?string    $foo = null;
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

    public function getBuzz() : ?string
    {
        return $this->buzz;
    }

    public function setBuzz(string $buzz) : void
    {
        $this->buzz = $buzz;
    }
}

class MockJsonHydratorClass extends AbstractHydratorClass implements JsonHydratableInterface
{
    use JsonHydratorTrait;
    use FizSetter;

    private ?string $buzz = null;

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

class MockInterfaceOnlyClass implements JsonHydratableInterface
{
    public bool $hydratorCalled = false;
    public ?string $foo         = null;

    public function hydrateFromJson(string $json) : void
    {
        $this->hydratorCalled = true;
        $data                 = json_decode($json, true);
        $this->foo            = $data['foo'] ?? null;
    }
}

class MockSnakeCaseClass
{
    public ?string $firstName = null;
    private ?string $lastName = null;

    public function getLastName() : ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName) : void
    {
        $this->lastName = $lastName;
    }
}

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

    public function testJsonMapperUsesInterfaceHydrator()
    {
        $jsonMapper = new JsonMapper();
        $obj        = $jsonMapper->map($this->json, MockInterfaceOnlyClass::class);

        $this->assertTrue($obj->hydratorCalled);
        $this->assertEquals('alpha', $obj->foo);
    }

    public function testJsonMapperMapsSnakeCaseKeys()
    {
        $json       = '{"first_name":"Ada","last_name":"Lovelace"}';
        $jsonMapper = new JsonMapper();
        $obj        = $jsonMapper->map($json, MockSnakeCaseClass::class);

        $this->assertEquals('Ada', $obj->firstName);
        $this->assertEquals('Lovelace', $obj->getLastName());
    }

    public function testJsonMapperAcceptsValidJsonWithWhitespace()
    {
        $json       = '{ "foo": "alpha", "bar": "beta" }';
        $jsonMapper = new JsonMapper();
        $obj        = $jsonMapper->map($json, MockBoringClass::class);

        $this->assertEquals('alpha', $obj->getFoo());
        $this->assertEquals('beta', $obj->getBar());
    }

    public function testJsonMapperRejectsNonObjectJson()
    {
        $this->expectException(BadJsonException::class);
        $jsonMapper = new JsonMapper();

        $jsonMapper->map('"just a string"', MockBoringClass::class);
    }
}
