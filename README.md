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

### JsonHydratorTrait

The first is a trait `PHPAlchemist\Json\Trait\JsonHydratorTrait` which has a single function called
`hydrateFromJson()` which accepts a string called `$json` which is expected to be a JSON object. It
then will loop through the object and looks for setter functions or if the property exists for each 
key and then sets the data appropriately on the object.

### JsonMapper

The second is  `PHPAlchemist\Json\Service\JsonMapper` which has a `map()` functino which will accept 
a JSON string (`$json`) and a classname (`$class`). This will validate the JSON and throw a 
`PHPAlchemist\Json\Exception\BadJsonException` if it is invalid. It will then instantiate a new class 
of  the given type, check to see if the `hydrateFromJson()` exists. If it does, then it will use that
otherwise it will then go through the same process to set the JSON keys via setter or public access.


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