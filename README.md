# Validator

[简体中文文档](./README.zh-CN.md)

`hongxunpan/validator` is a **framework-agnostic validator core** intended for shared Composer package workflows. It is designed to provide:

- an extensible DSL keyword system;
- stable public contracts for package consumers;
- an adapter-friendly validation core for business repositories.

## Current Status

This repository is currently in **pre-1.0 development**.

Already in place:

- the Composer package skeleton;
- public DSL keyword conventions;
- the `AbstractRule + RuleInterface + KEY + of(...)` convention;
- marker interfaces and the package directory strategy;
- the PHP `>=5.6` compatibility target.

Still in progress:

- the validation execution kernel;
- rule definition resolution and lazy loading;
- migration of built-in rules;
- `validated_data` output writing;
- backend adapter integration.

At this stage, the repository should be treated as an **open-source package skeleton under active implementation**, rather than a feature-complete stable release.

## Design Goals

This package is primarily intended to solve the following problems:

1. prevent rule logic, path handling, result output, and exception concerns from collapsing back into a single giant `Validator.php`;
2. provide clearer naming, structure, and reuse patterns for public DSL keywords;
3. let business repositories keep a thin adapter layer while moving the reusable validation core into a shared package;
4. preserve a conservative PHP syntax baseline without giving up structural extensibility.

## Public DSL Conventions

The current public conventions are:

- rule strings use `ruleName[:argument]`;
- each rule is split on the **first** `:` only;
- public keyword classes consistently expose:
  - `KEY`
  - `key()`
  - `of(...)`

Example:

```php
TrimRule::KEY;
NonBlankRule::KEY;
FormatTimeRule::of('Y-m-d H:i:s');
MinLengthRule::of(2);
```

## Package Layout

```text
src/
  Validator.php
  Rule/
  Handler/
  Definition/
  Context/
  Message/
  Config/
  Support/
  Result/
  Output/
  Exception/
tests/
```

Where:

- `Rule/*` contains public DSL keywords, marker interfaces, and type keywords;
- `Handler/*` contains execution logic;
- `Definition/*` contains rule definitions and resolution logic;
- `Support/*` contains reusable helpers such as path access and parsing utilities.

## Installation

```bash
composer require hongxunpan/validator
```

> Until the first stable release is published, local path repositories or explicit ref-based integration are recommended for real project adoption.

## Compatibility

Current target compatibility:

- PHP: `>=5.6`

The implementation therefore avoids language features that require PHP 7+ or PHP 8+.

## Testing

This repository currently uses a lightweight custom test runner so that the package can keep a conservative PHP compatibility baseline while the core is still evolving.

```bash
composer test
```

Or run it directly:

```bash
php tests/TestRunner.php
```

## Roadmap

The next milestones are:

- rule definition resolution;
- core presence / transform / assert / collection rules;
- `ValidationResult`;
- `ArrayAccess` output writing support;
- backend adapter examples and minimum verification scripts.

## License

This project is licensed under the [MIT License](./LICENSE).
