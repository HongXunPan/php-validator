# Validator

[简体中文文档](./README.zh-CN.md)

`hongxunpan/validator` is a framework-agnostic validator core built around three ideas:

- rules are public extension units and execute themselves;
- consumers extend through validator subclass arrays instead of handler/source registries;
- the kernel only orchestrates and pushes execution details into smaller collaborators.

## Current Status

This repository is in pre-1.0 development.

Already in place:

- Composer package skeleton;
- PHP `>=5.6` compatibility baseline;
- `RuleInterface + AbstractRule + KEY + of(...)` public convention;
- validator subclass extension arrays:
  - `extraRules`
  - `ruleAliases`
  - `ruleMessages`
- canonical core validation pipeline.

Still in progress:

- broader core canonical rule coverage;
- backend adapter integration;
- README examples and release hardening.

## Public Extension Model

Default consumers only need a validator subclass:

```php
class DemoValidator extends Validator
{
    protected static $extraRules = array(
        'trimTest' => TrimTestRule::class,
    );

    protected static $ruleAliases = array(
        'trimAlias' => 'trimTest',
    );

    protected static $ruleMessages = array(
        'trimTest' => '$paramName must be trimmed',
    );
}
```

Rule lookup order is fixed:

1. try the input rule key as a real rule;
2. only when it does not exist, try alias mapping;
3. resolve the final rule class;
4. execute `RuleClass::validate($context)`.

## Public DSL Conventions

- rule strings use `ruleName[:argument]`;
- each rule only splits on the first `:`;
- public keyword classes expose:
  - `KEY`
  - `key()`
  - `of(...)`

## Package Layout

```text
src/
  Validator.php
  ValidationKernel.php
  Internal/
  Rule/
  Context/
  Support/
  Result/
  Output/
  Exception/
tests/
```

## Installation

```bash
composer require hongxunpan/validator
```

## Compatibility

- PHP: `>=5.6`

## Integration and Migration Notes

This package is **not a seamless drop-in replacement** for older validator helpers.

Core methods currently return objects:

- `validate(...)` -> `ValidationResult`
- `validateAndNormalize(...)` -> `ValidationResult`
- `validateListAndNormalize(...)` -> `ValidationResult`

So if an existing project historically depends on:

- an array envelope: `count / errors / detail / validated_data`
- `validateOrThrow()` returning validated payload arrays
- legacy aliases, legacy rules, or project-specific messages
- old cross-field compare arguments such as `fieldPath,label`

that compatibility should be handled in the **project-level adapter layer**, not pushed back into the core package.

Recommended approach:

- call `ValidationResult::toArray()` when an old array envelope must be preserved
- keep project-local `*OrThrow` facade/helper methods if old signatures must remain stable
- declare project-specific `extraRules / ruleAliases / ruleMessages` in a project validator subclass
- keep ORM or business-specific rules such as `unique / exists` outside core

Not recommended:

- making `ValidationResult` implement `ArrayAccess` just for backward compatibility
- turning the core package back into an array-first API
- promoting one project's historical helper contract into the default public contract of this package

For a practical project-level adapter example, see:

- [Project Adapter Sample (Chinese)](./docs/项目适配层接入样板.zh-CN.md)

## Testing

```bash
composer test
```

Or run directly:

```bash
php tests/TestRunner.php
```

## License

This project is licensed under the [MIT License](./LICENSE).
