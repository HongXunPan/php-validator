# Comparison With Other Validator Libraries

This document helps users decide whether `hongxunpan/validator` fits their project.

Note: versions, dependencies, PHP requirements, and rule coverage of third-party packages change over time. This document focuses on selection dimensions and capability differences. For formal dependency decisions, always check each package's official README, documentation, and `composer.json`.

---

## 1. Package Positioning

`hongxunpan/validator` is positioned as:

- framework-agnostic;
- low-dependency;
- focused on array input and API payloads;
- responsible for validation, normalization, default value creation, conditional rules, and cross-field dependency reads;
- designed with custom rules as the first priority, rather than maximizing built-in rule count.

It is closer to a long-term validation core than to a drop-in replacement for a framework validator.

---

## 2. Quick Comparison

| Project | Best fit | Advantage over this package | Difference in this package |
| --- | --- | --- | --- |
| Laravel Validator | Requests, forms, FormRequest, database uniqueness, file upload in Laravel apps | Official integration, rich rule set, mature HTTP error response flow, strongest Laravel ecosystem | No Laravel dependency; better fit for shared cores, old PHP, non-Laravel projects, and custom-rule governance |
| Symfony Validator | Entity / DTO / Attribute / Constraint-driven object validation | Mature object constraint model, complete violation model, strong Symfony ecosystem | More focused on array input, field paths, normalized output, and API payload lifecycle |
| Respect Validation | Fluent single-value validation and many composable rules | Large rule set, natural fluent syntax, mature community | More focused on multi-field payloads, output data, conditional rules, and cross-field prepared values |
| Valitron | Simple, lightweight, low-dependency form validation | Simple API, mature usage, low learning cost | Stronger structure around Rule / Context / Result for long-term custom-rule maintenance |
| Rakit Validation | Laravel-like standalone validation | Laravel-like syntax, array and file validation closer to form use cases | Does not aim to be Laravel-like; prioritizes a low-dependency core, rule lifecycle, and adapter layering |
| Particle Validator | Readable fluent array validator | Clear chain-based declarations and useful context features | Keeps a string DSL while making execution stages and normalized output core concepts |

---

## 3. Capability Dimensions

### 3.1 Custom Rule Extension

This package treats custom rules as the first-class priority.

Consumers are encouraged to build rules with:

- `RuleInterface`;
- `AbstractRule`;
- rule archetype base classes such as:
  - `AbstractPresentValueNormalizationRule`
  - `AbstractPresentValueGuardRule`
  - `AbstractPresentValueTransformRule`
  - `AbstractPresentValueAssertionRule`
  - `AbstractCrossFieldAssertionRule`
  - `AbstractConditionalFieldPresenceRule`
  - `AbstractConditionalPresentValueGuardRule`
  - `AbstractReferencedFieldCompareRule`
- `RuleContext`;
- `RuleResult`;
- `RuleArgumentParserInterface`.

A custom rule is not just a callback. It can explicitly declare:

- rule key;
- default message;
- argument parser;
- current field value;
- raw field value;
- dependent field value;
- whether later rules should stop;
- whether the output value changes;
- whether the current value exists.

Compared with other packages:

- Laravel / Rakit are stronger when you want to reuse existing ecosystem rules directly;
- Respect is stronger for fluent single-value rule composition;
- Symfony is stronger for Constraint / Validator object modeling;
- this package is stronger when project-specific business rules should become stable, testable, and portable Rule classes.

### 3.2 Validation and Normalization

This package is not assertion-only.

The core has an explicit data lifecycle:

1. missing value creation, such as `default`;
2. present value normalization, such as `trim` and `blankToNull`;
3. present value guard, such as `nullable` and `nullableIf*`;
4. present value transform, such as `positiveInt`, `nonNegativeInt`, and `formatTime`;
5. present value assertion, such as `minLength`, `maxLength`, and `in`;
6. cross-field assertion, such as `gtField` and `timeAfterOrEqualField`.

As a result, `validatedData()` can directly return normalized data, not just say whether the raw input passed.

### 3.3 Cross-field Dependency Reads

A key difference is that cross-field comparison rules prefer prepared values from dependent fields.

For example:

- `min_value` is first normalized by `trim|positiveInt` into an integer;
- `current_value` then runs `gtField:min_value`;
- the comparison rule reads the normalized `min_value`, not the raw string.

This reduces cascading dirty errors and better matches the final usable API input.

### 3.4 Conditional Rule Arguments

This package prefers structured arguments and strict literals.

For example, conditional rules should not keep relying on ambiguous `field,value` splitting. Instead, argument parsers should turn field references, expected values, and expected value sets into explicit objects.

Benefits:

- clearer type semantics;
- strings, numbers, booleans, and null can be distinguished;
- fewer historical bugs from comma splitting, bare strings, and weak comparison.

Costs:

- migration from Laravel / Rakit conditional rules requires more visible syntax changes;
- users need to understand the rule argument parser boundary.

### 3.5 Dependency Complexity

The runtime core currently only requires PHP and aims to stay low-dependency.

This does not mean every validation feature belongs in the core. The following capabilities are better kept in adapter layers:

- ORM `unique / exists`;
- file uploads, MIME checks, image dimensions;
- HTTP request objects;
- framework error responses;
- business database queries;
- translation resource packs.

This reduces core dependency complexity, but it also means the package does not cover complete application-level scenarios out of the box like Laravel or Symfony.

---

## 4. Strengths

1. **Custom-rule first**: Rule classes, context, result objects, and argument parsers are all designed around extension.
2. **Low dependency**: Suitable for shared packages, old projects, and non-framework projects.
3. **Validation plus normalization**: Final output can come directly from `validatedData()`.
4. **Cross-field prepared values**: Dependent fields finish their own processing before being read by other fields.
5. **Clear public boundary**: README and stability docs distinguish public surface from internals.
6. **Adapter-friendly**: The core does not take over HTTP, ORM, file upload, or other project-layer responsibilities.

---

## 5. Weaknesses and Non-goals

1. **Built-in rule count is still small**: It cannot match Laravel, Symfony, Respect, or other mature ecosystems yet.
2. **Pre-1.0 stage**: The public boundary is being clarified, but it should not be treated as a 1.x-stable library.
3. **Migration is not seamless**: Especially for conditional rules, database rules, file rules, and error responses.
4. **Modern PHP syntax is constrained**: To support older PHP, the core baseline cannot rely on attributes, enums, typed properties, and similar features.
5. **Object validation is not the main battlefield**: If your primary target is Entity / DTO constraints, Symfony Validator is usually a better fit.
6. **Ecosystem trust takes time**: Downloads, community feedback, and long-term issue history cannot yet match mature projects.

---

## 6. Selection Advice

### 6.1 Choose This Package When

You need to:

- share validation rules across multiple projects;
- support older PHP runtimes;
- avoid pulling Laravel / Symfony dependencies into a core package;
- turn business validation rules into structured Rule classes;
- run input normalization, conditional checks, cross-field reads, and output data generation in one pipeline.

### 6.2 Do Not Choose This Package When

You mainly need:

- Laravel FormRequest;
- automatic 422 JSON responses;
- `unique / exists` database rules;
- file and image validation;
- Entity / DTO Attribute constraints;
- the largest possible set of built-in rules.

Those scenarios are better served by the corresponding framework or mature validation library. This package can still be used as a local core or behind an adapter, but should not be forced to replace an entire ecosystem.
