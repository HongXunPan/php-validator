---
name: Bug report
about: Report a validator bug in public behavior, docs, tests, or release-facing engineering setup
title: "[Bug] "
labels: bug
assignees: ""
---

## Summary

What is the bug?

## Scope

- [ ] public API behavior
- [ ] built-in canonical rule behavior
- [ ] docs / README / public contract mismatch
- [ ] test / CI / static analysis infrastructure
- [ ] not sure yet

## Current behavior

What happened?

## Expected behavior

What did you expect to happen?

## Minimal reproduction

Please provide the smallest reproducible example:

```php
<?php
// paste code here
```

## Environment

- package version / commit:
- PHP version:
- execution path:
  - [ ] local
  - [ ] CI
  - [ ] project adapter layer

## Validation already tried

- [ ] `composer test`
- [ ] `composer analyse`
- [ ] checked README / public contract notes

## Extra notes

If this only reproduces in a project-specific adapter layer, please say so explicitly.  
Project-level legacy helper compatibility is not automatically treated as a core bug.
