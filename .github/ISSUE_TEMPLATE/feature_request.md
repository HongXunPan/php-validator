---
name: Feature request
about: Propose a new public capability, core rule, documentation improvement, or engineering enhancement
title: "[Feature] "
labels: enhancement
assignees: ""
---

## Summary

What do you want to add or improve?

## Motivation

Why is this useful?

## Category

- [ ] public API
- [ ] canonical core rule
- [ ] documentation / onboarding
- [ ] CI / static analysis / engineering workflow
- [ ] internal refactor
- [ ] not sure yet

## Proposed behavior

Describe the target behavior or workflow.

## Public contract impact

- [ ] no public API change
- [ ] extends public API
- [ ] may change existing public behavior
- [ ] not sure yet

If public behavior changes, please explain which contract is affected:

- `Validator`
- `ValidationKernel`
- `ValidationResult`
- `RuleInterface` / rule extension model
- other documented public contract

## Adapter-layer or project-specific?

Please explain whether this belongs in:

- [ ] core package
- [ ] project adapter layer
- [ ] not sure yet

Reminder:

- project-local legacy helper compatibility
- ORM-specific rules
- one-project historical aliases/messages

should usually stay outside core unless there is a stronger public reason.

## Extra notes

You can include pseudo-code, expected DSL examples, or migration notes here.
