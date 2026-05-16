# Changelog

All notable changes to `hongxunpan/validator` will be documented in this file.

This project is currently in **pre-1.0**. The first public tag has been published as `0.0.1`, but the package still does not promise a stable `1.x` compatibility contract.

The format loosely follows Keep a Changelog, but is intentionally kept simple for the current stage.

## [Unreleased]

### Added

- initial Composer package skeleton for `hongxunpan/validator`
- PHP `>=5.6` compatibility baseline for the core package
- public `Rule` self-execution model with:
  - `RuleInterface`
  - `AbstractRule`
  - `AbstractPresenceRule`
  - `AbstractValueRule`
- validator subclass extension arrays:
  - `extraRules`
  - `ruleAliases`
  - `ruleMessages`
- validator subclass provider-class constants for large static configuration maps:
  - `EXTRA_RULES_PROVIDER_CLASS`
  - `RULE_ALIASES_PROVIDER_CLASS`
  - `RULE_MESSAGES_PROVIDER_CLASS`
- canonical core validation pipeline
- `ValidationResult` object result boundary
- validated output writer support for existing `ArrayAccess` targets
- runtime data lifecycle context model for target values
- list validation pipeline and output aggregation model
- package-level onboarding README examples
- public contract and stability boundary document
- GitHub Actions multi-version test matrix
- GitHub Actions Node24 runtime readiness for JavaScript-based actions
- PHPStan static analysis configuration and CI job
- a dedicated canonical examples document for copy-friendly core scenarios
- highest-standard pre-release structural refactor plan document in the shared workspace

### Changed

- internal runtime collaborators were regrouped into clearer directories such as:
  - `Internal/Input`
  - `Internal/Context`
  - `Internal/Target`
  - `Internal/Output`
  - `Internal/Runner`
  - `Internal/Rules`
  - `Internal/Execution`
- public-facing helper types `PathValue` and `PathLabelMap` were moved out of `Internal/*` into `Context/*`
- package README was refocused on public usage, while project-specific adapter samples were moved back to the shared workspace collaboration layer
- `RuleContext` public construction no longer leaks `Internal/*` runtime types
- `RuleInterface::validate(...)` and built-in rules now use `RuleContext $context`, so custom rule implementations can get IDE completion and jump-to-definition support
- object validation now compiles target rule plans once and reuses them across execution stages
- `RuleSet` was slimmed into a composition facade over registry, alias, and message components
- list scalar validation no longer reuses object validation through synthetic public rule arrays
- output aggregation was separated from failure reporting and message rendering
- GitHub Actions checkout was upgraded from `actions/checkout@v4` to `@v6`, and the workflow now forces JavaScript actions onto Node24 ahead of the platform default switch
- public docs now distinguish the two recommended extension paths more clearly:
  - provider-class constants for large static maps
  - `define*()` overrides for inherited merging or dynamic logic

### Fixed

- cross-target comparison rules now read prepared dependent values instead of raw peer input
- dependent comparison rules skip gracefully when the referenced target is not dependency-readable
- field-compare message rendering now resolves referenced field display names from declared path labels
- internal output/detail construction no longer relies on scattered anonymous array shapes
