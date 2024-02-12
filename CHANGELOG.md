# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.5.3] - 2024-02-12
### Removed
- Dropped support for PHP 8.0.
### Added
- Added support for PHP 8.3.
- Added support for `nikic/php-parser` v5.

## [0.5.2] - 2023-06-16
### Fixed
- Attribute list works correctly with NULL values now.

## [0.5.1] - 2023-06-15
### Fixed
- Fixed PHP requirements.

## [0.5.0] - 2023-06-15
### Removed
- Dropped support dor PHP 7.
### Added
- Added support for `thecondingmachine/safe` version 2.

## [0.4.2] - 2022-02-10
### Fixed
- Symfony dependencies updated.

## [0.4.1] - 2021-08-16
### Fixed
- PHP 8.1 compatibility issue ([#3](./issues/3)).

## [0.4.0] - 2021-01-14
### Added
- PHP 8.0 support.
### Changed
- Rangesets moved to `remorhaz/int-rangesets`.
- Unicode property ranges generation moved to `remorhaz/ucd`.

## [0.3.1] - 2020-04-12
### Fixed
- Long conditions are moved to private methods in generated token matchers.

## [0.3.0] - 2020-04-12
### Changed
- Token matching redesigned to work with partially minimized DFA.
### Fixed
- Regular expression maps take care of modes.

## [0.2.2] - 2020-04-07
### Added
- DFA minimisation is done before building token matcher.

## [0.2.1] - 2020-04-04
### Fixed
- Performance of AND operation on range sets improved.

## [0.2.0] - 2020-04-03
### Added
- Unicode properties are partially supported (only scripts).
- Internal CLI tool to build Unicode properties.
- Ranges can be added to set without checks (external code must guarantee consistence in this case).
### Changed
- CLI utility switched to `symfony/console` and changed API.
### Removed
- Ability to build token matchers using Phing.
### Fixed
- UTF-8 matcher correctly calculates code of multi-byte symbols.
- Performance on adding ranges to set improved.
- Performance of XOR operation on range sets improved.

## [0.1.0] - 2020-02-17
### Added
- Changelog started
