# Migration From Other Validator Libraries

This document describes syntax changes and migration costs when moving from Laravel Validator, Symfony Validator, Respect Validation, Valitron, Rakit Validation, or similar packages to `hongxunpan/validator`.

This package does not aim for one-to-one compatibility. Before migrating, decide whether you want to replace an entire validation system or only move selected project-specific rules into a shared core.

---

## 1. Before Migration

### 1.1 Good Candidates for Core Migration

The following capabilities are good candidates for this core:

- field presence: `required`, `nullable`, `default`;
- string normalization: `trim`, `blankToNull`;
- basic type and numeric transforms: `string`, `int`, `positiveInt`, `nonNegativeInt`;
- length, item count, and enum assertions;
- conditional required / nullable / prohibited rules;
- numeric or time comparison against referenced fields;
- reusable project-level business rules.

### 1.2 Do Not Move These Directly Into Core

The following capabilities are better kept in project adapters or framework layers:

- database `unique / exists`;
- file upload, image, MIME, and dimension validation;
- HTTP Request / Response handling;
- Laravel FormRequest;
- Symfony Entity / DTO Attribute constraints;
- translation resources and framework localization systems;
- business rules that depend on ORM, cache, or external services.

---

## 2. Migration Cost Levels

| Cost | Type | Notes |
| --- | --- | --- |
| Low | Basic field rules | `required`, `nullable`, `trim`, `minLength`, `maxLength`, `in` usually only need rule-name or argument-format changes |
| Medium | Conditional rules | `required_if`, `prohibited_if`, `nullable_if` need clearer rule names and strict arguments |
| Medium | Normalization rules | Call sites must read `validatedData()` instead of raw input |
| Medium | Custom rules | Callbacks or framework rules should become Rule classes; more work upfront, better long-term maintenance |
| High | Database rules | Keep them in adapters or framework layers instead of core |
| High | File uploads | Keep them in project adapters |
| High | Object / DTO / Entity constraints | Migration is a modeling change if the old system is metadata-driven |

---

## 3. Migrating From Laravel Validator

### 3.1 Basic Rules

Laravel style:

```php
$validated = $request->validate(array(
    'name' => 'required|string|max:20',
));
```

Recommended style in this package:

```php
$result = DemoValidator::validateAndNormalize(
    array('name' => ' Alice '),
    array('name:Name' => 'required|trim|string|maxLength:20')
);

if ($result->isPassed()) {
    $data = $result->validatedData();
}
```

Differences:

- this package does not read a Request object automatically;
- this package does not throw Laravel `ValidationException` automatically;
- this package returns `ValidationResult`;
- legacy array envelopes can be produced by adapters through `toArray()`;
- Laravel's compound `max` semantics should be split by type, such as `maxLength`, `maxItems`, or numeric comparison rules.

### 3.2 Conditional Rules

Common Laravel / Laravel-like style:

```text
required_if:type,guest
```

This package discourages keeping bare comma-split strings. Prefer clearer conditional rules, for example:

```text
requiredIfEq:type,"guest"
```

If the expected value is a number, boolean, or null, use a strict literal:

```text
requiredIfEq:status,1
requiredIfEq:enabled,true
requiredIfEq:deleted_at,null
```

For sets:

```text
requiredIfIn:type,["guest","member"]
```

When building rules in PHP, prefer structured helpers over hand-written commas and `json_encode(...)`:

```php
use HongXunPan\Validator\Rule\Condition\RequiredIfEqRule;
use HongXunPan\Validator\Rule\Condition\RequiredIfInRule;

RequiredIfEqRule::ofFieldValue('type', 'guest');
RequiredIfInRule::ofFieldValues('type', array('guest', 'member'));
```

This avoids mixing `"1"`, `1`, `true`, and `"true"`.

### 3.3 Database Rules

Laravel style:

```text
unique:users,email
exists:users,id
```

Do not move these rules directly into the core.

Recommended approach:

1. let core handle presence, format, and basic normalization;
2. keep ORM queries in project adapters or business services;
3. if many projects need the same behavior, create a separate adapter package instead of making the core depend on a database layer.

### 3.4 FormRequest and Automatic Responses

Laravel FormRequest combines:

- authorization;
- input preparation;
- validation rules;
- error responses;
- controller injection.

This package does not replace those responsibilities. Keep a project-level adapter:

```php
$result = DemoValidator::validateAndNormalize($input, $rules);

if ($result->isFailed()) {
    // Convert to framework-specific exception or response in the adapter.
}
```

---

## 4. Migrating From Rakit Validation

Rakit is closer to a Laravel-like standalone validator, so basic rules usually have lower migration cost. The main differences are:

- this package returns `ValidationResult`;
- this package emphasizes normalized `validatedData()`;
- conditional rule arguments are stricter;
- file uploads and Laravel-like complex rules stay outside the core;
- custom rules should become structured Rule classes.

Example:

```php
$result = DemoValidator::validateAndNormalize(
    $_POST,
    array(
        'email:Email' => 'required|trim',
        'age:Age' => 'nullable|positiveInt',
    )
);
```

If old Rakit rules depend on hooks such as `ModifyValue` or `BeforeValidate`, reclassify them by this package's stages:

- input preprocessing: adapter;
- missing value creation: `AbstractMissingValueCreationRule`;
- present value normalization: `AbstractPresentValueNormalizationRule`;
- present value transform: `AbstractPresentValueTransformRule`;
- assertion: `AbstractPresentValueAssertionRule`.

---

## 5. Migrating From Valitron

Valitron style is simple and direct:

```php
$v = new Valitron\Validator(array('name' => 'Alice'));
$v->rule('required', 'name');
$v->rule('lengthMax', 'name', 20);
```

In this package, declarations are centralized in a field rule map:

```php
$result = DemoValidator::validateAndNormalize(
    array('name' => 'Alice'),
    array('name:Name' => 'required|maxLength:20')
);
```

Migration cost:

- basic rules: low;
- custom callbacks: medium, should become Rule classes;
- multiple validation contexts: medium, use different Validator subclasses or rule maps;
- complex error messages: medium, move to `ruleMessages` or adapters.

---

## 6. Migrating From Respect Validation

Respect is commonly used as fluent single-value validation:

```php
v::numericVal()->positive()->between(1, 255)->isValid($input)
```

In this package, the model becomes field-level payload rules:

```php
$result = DemoValidator::validateAndNormalize(
    array('count' => '12'),
    array('count:Count' => 'positiveInt|gte:1|lte:255')
);
```

Differences:

- Respect is stronger for fluent single-value rules;
- this package is stronger for array input, field paths, and output data;
- do not mechanically port Respect's large rule set;
- migrate only high-value rules that your project actually uses.

If an old Respect chain is large, first convert it into a business-level custom Rule instead of copying every fluent segment one by one.

---

## 7. Migrating From Symfony Validator

Symfony Validator is usually built around:

- Constraint;
- ConstraintValidator;
- Attribute / YAML / XML / PHP metadata;
- Entity / DTO;
- ConstraintViolationList.

This package is built around:

- array input;
- field paths;
- string DSL;
- Rule classes;
- `ValidationResult`;
- `validatedData()`.

Therefore, Symfony migration is not just rule-name conversion; it is a modeling change.

Good candidates:

- API payloads that do not need to become Entity / DTO objects;
- old PHP or non-Symfony environments;
- lightweight validation plus normalization in a reusable core.

Poor candidates:

- systems already stable around Attribute constraints;
- systems deeply using violation groups, cascaded object validation, and Symfony ecosystem behavior;
- validation targets that are primarily object graphs rather than array payloads.

---

## 8. Custom Rule Migration Template

### 8.1 From Callback Rules

Old style may look like:

```php
function isValidCode($value) {
    return preg_match('/^[A-Z0-9]+$/', $value);
}
```

Recommended Rule class:

```php
use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueAssertionRule;

class CodeRule extends AbstractPresentValueAssertionRule
{
    const KEY = 'code';
    const MESSAGE = '$paramName has an invalid format';

    public static function validate(RuleContext $context)
    {
        if (!is_string($context->value())) {
            return RuleResult::fail($context->value());
        }

        if (!preg_match('/^[A-Z0-9]+$/', $context->value())) {
            return RuleResult::fail($context->value());
        }

        return RuleResult::pass($context->value());
    }
}
```

Register it in a Validator subclass:

```php
class DemoValidator extends Validator
{
    protected static $extraRules = array(
        'code' => CodeRule::class,
    );
}
```

### 8.2 Conditional Custom Rules

If a rule needs to read another field, prefer conditional or cross-field base classes instead of reading raw arrays directly:

- `AbstractConditionalFieldPresenceRule`
- `AbstractConditionalPresentValueGuardRule`
- `AbstractReferencedFieldCompareRule`

They reuse field reference parsing, dependent value reads, missing-value skipping, and display-name rendering.

---

## 9. Recommended Migration Order

1. Migrate basic normalization rules first: `trim`, `blankToNull`, `default`.
2. Migrate basic assertions: `required`, `string`, `int`, `minLength`, `maxLength`.
3. Migrate enum and list rules.
4. Migrate conditional rules and standardize on strict literal arguments.
5. Migrate custom business rules.
6. Keep database, file upload, and HTTP response behavior in adapters.

Do not try to replace an old validator completely at the beginning. Start with one high-value, low-coupling payload scenario, then expand gradually.
