# Validator

[简体中文文档](./README.zh-CN.md)

`hongxunpan/validator` is a framework-agnostic validator core built around three ideas:

- rules are public extension units and execute themselves;
- consumers extend through validator subclass arrays instead of handler/source registries;
- the kernel only orchestrates and pushes execution details into smaller collaborators.

## Current Status

This repository is in pre-1.0 development.

For change history, see:

- [CHANGELOG](./CHANGELOG.md)

For contribution workflow, see:

- [Contribution Guide](./CONTRIBUTING.md)

For current performance notes, see:

- [Performance and Benchmark Notes (Chinese)](./docs/性能与基准说明.zh-CN.md)

For richer canonical usage scenarios, see:

- [High-value Canonical Examples (Chinese)](./docs/高价值 canonical 示例.zh-CN.md)

Already in place:

- Composer package skeleton;
- PHP `>=5.6` compatibility baseline;
- `RuleInterface + AbstractRule + KEY + of(...)` public convention;
- validator subclass extension configuration:
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

When these maps become large, prefer two paths depending on the use case:

- if you only want to split long static maps out of the validator class, prefer the provider-class constants:
  - `EXTRA_RULES_PROVIDER_CLASS`
  - `RULE_ALIASES_PROVIDER_CLASS`
  - `RULE_MESSAGES_PROVIDER_CLASS`
- only override:
  - `defineExtraRules()`
  - `defineRuleAliases()`
  - `defineRuleMessages()`
  when you really need inherited merging or dynamic logic.

Rule lookup order is fixed:

1. try the input rule key as a real rule;
2. only when it does not exist, try alias mapping;
3. resolve the final rule class;
4. execute `RuleClass::validate(RuleContext $context)`.

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

## 30-second Quick Start

```php
<?php

use HongXunPan\Validator\Validator;

class DemoValidator extends Validator
{
}

$result = DemoValidator::validateAndNormalize(
    array(
        'name' => '  Alice  ',
    ),
    array(
        'name:Name' => 'required|trim|minLength:2|maxLength:20',
    )
);

if ($result->isFailed()) {
    var_dump($result->errors());
    var_dump($result->detail());
    return;
}

var_dump($result->validatedData());
// array('name' => 'Alice')
```

This shows the default smallest path:

- no custom rule yet;
- only built-in canonical rules;
- `validateAndNormalize(...)` returns `ValidationResult`;
- successful output is read from `validatedData()`.

## Compatibility

- PHP: `>=5.6`

## Performance Trade-off

At the current stage, the package intentionally prioritizes:

- clear public contract boundaries;
- maintainable and testable execution flow;
- measured optimization only after a real hotspot appears.

For the current performance stance and benchmark trigger conditions, see:

- [Performance and Benchmark Notes (Chinese)](./docs/性能与基准说明.zh-CN.md)

## Public Contract and Stability Boundary

This repository is still in **pre-1.0**, but the public boundary is now being frozen.

The current stable public surface mainly includes:

- `Validator`
- `ValidationKernel`
- `ValidationResult`
- `RuleInterface / AbstractRule`
- `AbstractFieldPresenceAssertionRule`
- `AbstractMissingValueCreationRule`
- `AbstractPresentValueNormalizationRule`
- `AbstractPresentValueGuardRule`
- `AbstractPresentValueTransformRule`
- `AbstractPresentValueAssertionRule`
- `AbstractCrossFieldAssertionRule`
- `RuleContext / RuleValueReaderInterface / ValidationOptions / RuleResult`
- `ValidatedDataWriterInterface / ArrayAccessValidatedDataWriter`

The following are explicitly **not** part of the stable contract:

- `Internal/*`
- `Rule\\CoreRules`
- undocumented internal assembly helpers

For the full contract notes, see:

- [Public Contract and Stability Notes (Chinese)](./docs/公开契约与稳定性承诺.zh-CN.md)

## Working with `ValidationResult`

`validate(...)`, `validateAndNormalize(...)`, and `validateListAndNormalize(...)` currently return:

- `HongXunPan\Validator\Result\ValidationResult`

The most common methods are:

- `isPassed() / isFailed()`
- `count()`
- `errors()`
- `detail()`
- `validatedData()`
- `toArray()`

Example:

```php
$result = DemoValidator::validate(
    array('name' => ''),
    array('name:Name' => 'required')
);

if ($result->isFailed()) {
    var_dump($result->count());
    var_dump($result->errors());
    var_dump($result->detail());
}
```

If an old project still needs the legacy array envelope, convert explicitly in the adapter layer:

```php
$legacy = $result->toArray();
```

## Smallest Custom Rule Example

```php
<?php

use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueNormalizationRule;
use HongXunPan\Validator\Context\RuleContext;

class TrimNameRule extends AbstractPresentValueNormalizationRule
{
    const KEY = 'trimName';
    const MESSAGE = '$paramName must be string';

    public static function validate(RuleContext $context)
    {
        if (!is_string($context->value())) {
            return RuleResult::fail($context->value());
        }

        return RuleResult::pass(trim($context->value()));
    }
}
```

Then attach it in your validator subclass:

```php
<?php

use HongXunPan\Validator\Validator;

class DemoValidator extends Validator
{
    protected static $extraRules = array(
        'trimName' => TrimNameRule::class,
    );
}
```

Call it:

```php
$result = DemoValidator::validateAndNormalize(
    array('name' => '  Alice  '),
    array('name:Name' => 'trimName')
);
```

## `extraRules / ruleAliases / ruleMessages`

```php
class DemoValidator extends Validator
{
    protected static $extraRules = array(
        'trimName' => TrimNameRule::class,
    );

    protected static $ruleAliases = array(
        'trimAlias' => 'trimName',
        'lenMin' => 'minLength',
    );

    protected static $ruleMessages = array(
        'trimName' => '$paramName must be trimmed first',
        'minLength' => '$paramName is too short',
    );
}
```

Meaning:

- `extraRules`: project-defined real rule keys;
- `ruleAliases`: legacy or shorthand names mapped to final rule keys;
- `ruleMessages`: message overrides by the **final rule key**.
- When these maps grow large:
  - if you only want to split long arrays out of the validator class, prefer the provider-class constants:
    - `EXTRA_RULES_PROVIDER_CLASS`
    - `RULE_ALIASES_PROVIDER_CLASS`
    - `RULE_MESSAGES_PROVIDER_CLASS`
  - only override `defineExtraRules() / defineRuleAliases() / defineRuleMessages()` when you really need inherited merging or dynamic logic.

Rule lookup order is stable:

1. try the input key as a real rule;
2. only when the real rule does not exist, try alias lookup;
3. execute the final resolved rule class.

## List Validation Examples

### Scalar list

```php
$result = DemoValidator::validateListAndNormalize(
    array('  a  ', '  bb  '),
    'trim|minLength:1'
);
```

### Object list

```php
$result = DemoValidator::validateListAndNormalize(
    array(
        array('name' => ' Alice '),
        array('name' => ' Bob '),
    ),
    array(
        'name:Name' => 'required|trim|minLength:1',
    ),
    array(
        'field_prefix' => 'items',
    )
);
```

When one item is not an array in an object-list scenario, the failure detail path will look like:

- `items.2`

## More Canonical Examples

If the minimal README examples are already clear but you want a few more
copy-friendly core scenarios, such as:

- string normalization with length assertions;
- `default` plus numeric normalization;
- time formatting with cross-field comparison;
- numeric comparison reading normalized dependent values;
- list-rule composition;

see:

- [High-value Canonical Examples (Chinese)](./docs/高价值 canonical 示例.zh-CN.md)

## Adapter Layer for Legacy Projects

If an existing project still depends on:

- array envelopes
- `validateOrThrow()` returning payload arrays
- project-local messages
- legacy aliases or legacy rules
- ORM rules such as `unique / exists`

prefer keeping those concerns in a project adapter layer instead of pushing them back into core.

Project-level adapter samples belong to the shared workspace collaboration layer and are not published as part of the package-facing docs.

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
- declare project-specific `extraRules / ruleAliases / ruleMessages` in a project validator subclass; if you only need to split large static maps, prefer the provider-class constants, and reserve the matching `define*()` overrides for inherited merging or dynamic logic
- keep ORM or business-specific rules such as `unique / exists` outside core

Not recommended:

- making `ValidationResult` implement `ArrayAccess` just for backward compatibility
- turning the core package back into an array-first API
- promoting one project's historical helper contract into the default public contract of this package

If a real project still needs adapter samples, keep them in that project's own repository or in the shared collaboration workspace instead of pushing project compatibility notes back into the public package docs.

## Testing

```bash
composer test
```

Or run directly:

```bash
php tests/TestRunner.php
```

GitHub Actions currently runs the package test matrix on:

- PHP 5.6
- PHP 7.0
- PHP 7.1
- PHP 7.2
- PHP 7.3
- PHP 7.4
- PHP 8.0
- PHP 8.1
- PHP 8.2
- PHP 8.3
- PHP 8.4
- PHP 8.5

using:

```bash
composer test
```

## Static Analysis

The repository currently uses:

- `phpstan`

Default command:

```bash
composer analyse
```

Static analysis runs in CI as a dedicated job on a modern PHP runtime.

Notes:

- the current `phpstan` line no longer models PHP 5.6 directly;
- static analysis therefore focuses on structure and type safety;
- runtime compatibility down to PHP 5.6 remains covered by the multi-version test matrix.

## License

This project is licensed under the [MIT License](./LICENSE).
