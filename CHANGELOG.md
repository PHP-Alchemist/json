# Changelog

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
 * `PHPAlchemist\Json\Contract\JsonHydratableInterface` — the contract the JsonMapper
   now uses (via `instanceof`) to detect self-hydrating classes.
 * `PHPAlchemist\Json\Support\ObjectHydrator` — shared key/setter resolution used by
   both the JsonMapper and the JsonHydratorTrait.
 * snake_case JSON keys now map onto camelCase setters/properties
   (`first_name` -> `setFirstName()` / `$firstName`).

### Changed
 * `JsonMapper::validateJson()` now uses `json_validate()` instead of a re-encode
   round-trip, so valid JSON containing whitespace or unicode escapes is no longer
   rejected.
 * `JsonMapper::map()` detects self-hydrating classes via `JsonHydratableInterface`
   instead of duck-typing on a `hydrateFromJson()` method.
 * `JsonHydratorTrait::hydrateFromJson()` now declares a `: void` return type.

## [1.0.0] - 2025-06-25 (EDT) 

### Added
Initial Commit.  

Exception: 
 * BadJsonExeption

Services:
 * JsonMapper

Trait:
 * JsonHydratorTrait

Unit Tests:
 * JsonHydratorTraitTests
 * JsonMapperTest

### Changed
N/A

### Removed
N/A