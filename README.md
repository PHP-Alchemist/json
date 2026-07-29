# PHP Alchemist JSON
Package: `php-alchemist/json`
Current Version: v1.x -- (2025/06)


## What?

A basic library for working with json and being able to map json objects to a particular class

[![Unit Tests](https://github.com/PHP-Alchemist/json/actions/workflows/php.yml/badge.svg)](https://github.com/PHP-Alchemist/json/actions/workflows/php.yml)
[![StyleCI](https://github.styleci.io/repos/1007277226/shield?branch=master)](https://github.styleci.io/repos/1007277226?branch=master)  
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=PHP-Alchemist_json&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=PHP-Alchemist_json)

 
## Usage

The PHP Alchemist JSON library consists of two parts that can be used together or separately.

Both camelCase and snake_case JSON keys are supported: a `first_name` key resolves to a
`setFirstName()` setter or a `$firstName` property just as `firstName` would.

### JsonHydratorTrait

The first is a trait `PHPAlchemist\Json\Trait\JsonHydratorTrait` which has a single function called
`hydrateFromJson()` which accepts a string called `$json` which is expected to be a JSON object. It
then will loop through the object and look for a setter function or a matching property for each key
and set the data appropriately on the object. Because the trait runs in the object's own scope, it
can also populate protected/private properties that have no setter.

Classes using the trait should implement `PHPAlchemist\Json\Contract\JsonHydratableInterface`; the
trait satisfies the interface, and implementing it is how the `JsonMapper` detects that the class
hydrates itself.

```php
class Person implements \PHPAlchemist\Json\Contract\JsonHydratableInterface
{
    use \PHPAlchemist\Json\Trait\JsonHydratorTrait;

    public ?string $firstName = null;
    public ?string $lastName  = null;
}

$person = new Person();
$person->hydrateFromJson('{"first_name":"Ada","last_name":"Lovelace"}');
```

### JsonMapper

The second is `PHPAlchemist\Json\Service\JsonMapper` which has a `map()` function which will accept
a JSON string (`$json`) and a classname (`$class`). This will validate the JSON and throw a
`PHPAlchemist\Json\Exception\BadJsonException` if it is invalid. It will then instantiate a new
object of the given type and, if that object implements `JsonHydratableInterface`, delegate to its
`hydrateFromJson()`. Otherwise it sets the JSON keys via setters or public properties.


#### Example

```php 
class Demo {
  public string $test;
  public string $doSomething;
}

$json   = '{"test":"blah","doSomething":"asdf"}';
$mapper = new PHPAlchemist\Json\Service\JsonMapper();
$asdf   = $mapper->map($json, Demo::class);

var_dump($asdf);

```

OUTPUT:
```php
object(Demo)#1 (2) {
  ["test"]=>
  string(4) "blah"
  ["doSomething"]=>
  string(4) "asdf"
}
```