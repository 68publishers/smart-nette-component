# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.0.0] - 2026-10-28

### Added
- Support for `nette/robot-loader` `^4.0` (dev dependency, suggested for the option `scanDirs`).
- Integration test of the authorization (trait → authorizator → rules provider → reader) without mocks.

### Changed
- Minimal PHP version is 8.3.
- ❗ `AuthorizationTrait` passes the action name (`default`) and the signal name (`delete`) to `ComponentAuthorizatorInterface::checkAction()` and `checkSignal()` instead of the method names (`actionDefault`, `handleDelete`). Custom implementations of `ComponentAuthorizatorInterface` must be updated.
- `nette/utils` is declared as a dependency (it was used without being required).
- Dev tooling: PHPStan 2, Nette Tester 2.6+, Mockery 1.6.12+, PHP-CS-Fixer 3.89+, CI matrix PHP 8.3, 8.4 and 8.5. The tests attach components to a presenter (required by `nette/application` 3.2+).

### Removed
- ❗ Support for PHP < 8.3.

### Fixed
- **Security:** authorization attributes on action methods (`action<Action>()`) and signal methods (`handle<Signal>()`) were never enforced, because `AuthorizationTrait` passed the method name and the rules provider formatted it once more (`actionActionDefault`). Requests that were allowed until now may be forbidden after the upgrade.
- PHP 8.4 deprecation of the implicitly nullable parameter `$previous` in `ForbiddenRequestException::__construct()`.

## [1.0.0] - 2022-12-12

### Added
- Support for PHP `^8.1`.
- New authorization of actions and signals with PHP 8 attributes, custom attributes, rules and handlers.
- Dockerfile, docker-compose.yml, Makefile, tests, PHPStan and GitHub Actions.

### Changed
- Installation and usage changes, see the [README](https://github.com/68publishers/smart-nette-component/blob/v1.0.0/README.md).

### Removed
- Support for PHP 7 and Nette 2.4.
- Doctrine annotations.

## [0.2.1] - 2022-05-15

### Added
- Methods `Control::render()` and `Control::doRenderToString()`.

## [0.2] - 2020-11-19

### Added
- Support for both Nette 2.4 and 3.0.

### Changed
- Coding style (interfaces and traits naming conventions, FQN in `use` sections).

## [0.1.4] - 2019-08-26

### Added
- Missing method `ITranslatorAware::getPrefixedTranslator()`.

## [0.1.3] - 2019-08-22

### Added
- Interface `TranslatorAware`.

## [0.1.2] - 2019-07-30

### Fixed
- Order of class annotations.

## [0.1.1] - 2019-07-14

### Added
- Link authorization.

## [0.1] - 2019-06-09

- First release (Nette `^2.4`).

[Unreleased]: https://github.com/68publishers/smart-nette-component/compare/v2.0.0...HEAD
[2.0.0]: https://github.com/68publishers/smart-nette-component/compare/v1.0.0...v2.0.0
[1.0.0]: https://github.com/68publishers/smart-nette-component/compare/v0.2.1...v1.0.0
[0.2.1]: https://github.com/68publishers/smart-nette-component/compare/v0.2...v0.2.1
[0.2]: https://github.com/68publishers/smart-nette-component/compare/v0.1.4...v0.2
[0.1.4]: https://github.com/68publishers/smart-nette-component/compare/v0.1.3...v0.1.4
[0.1.3]: https://github.com/68publishers/smart-nette-component/compare/v0.1.2...v0.1.3
[0.1.2]: https://github.com/68publishers/smart-nette-component/compare/v0.1.1...v0.1.2
[0.1.1]: https://github.com/68publishers/smart-nette-component/compare/v0.1...v0.1.1
[0.1]: https://github.com/68publishers/smart-nette-component/releases/tag/v0.1
