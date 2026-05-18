# Rule Capability Matrix

This document is the rule capability source of truth for `hongxunpan/validator`. It explains whether each rule is currently supported, planned, adapter-owned, rejected, or still under evaluation.

README should only link to this document. Rule capabilities, statuses, and planning batches should be read from this document and the corresponding version tag.

---

## 1. Status Marks

| Mark | Status | Meaning |
| --- | --- | --- |
| ✅ | Supported | Implemented in the current core and expected to have tests |
| 🟡 | Planned | Included in the rule roadmap and expected to enter core |
| 🔵 | Adapter | Valid need, but should be implemented by framework / project adapters, not the low-dependency core |
| ⛔ | Rejected | Conflicts with package boundaries and is not expected to be accepted |
| ⚪ | To evaluate | No commitment yet; waiting for real use cases or issues |

---

## 2. Columns

| Column | Meaning |
| --- | --- |
| Status | Emoji plus text for quick scanning and text search |
| Rule | Canonical rule key; migration aliases are not primary keys in this matrix |
| Category | presence / missing-value / normalization / guard / transform / assertion / cross-field / adapter |
| Argument | DSL argument format; `none` means no argument |
| Mutates value | Whether a successful rule changes output in `validatedData()` |
| Reads dependency | Whether it reads another field's prepared value |
| Missing behavior | Default behavior when the current field is missing |
| Batch | Supported / P0 / P1 / P2 / Adapter / Rejected / To evaluate |
| Reason / notes | Planning reason, boundary, or migration note |

---

## 3. Supported Rules

| Status | Rule | Category | Argument | Mutates value | Reads dependency | Missing behavior | Batch | Reason / notes |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| ✅ Supported | `required` | presence | none | no | no | fails when missing | Supported | Basic presence rule |
| ✅ Supported | `default` | missing-value | JSON literal / raw argument | yes | no | creates key when missing | Supported | Missing-only; does not override present values |
| ✅ Supported | `nullable` | guard | none | no | no | skipped by default | Supported | Breaks following local value rules when present value is null |
| ✅ Supported | `boolean` | assertion | none | no | no | skipped by default | Supported | Strict boolean assertion; only accepts real bool values; use `toBool` for string booleans |
| ✅ Supported | `toBool` | transform | none | yes | no | skipped by default | Supported | Normalizes common `1/0/true/false/yes/no/on/off`-like values to bool |
| ✅ Supported | `accepted` | assertion | none | no | no | skipped by default | Supported | Accepts confirmation values such as `true/1/yes/on` |
| ✅ Supported | `declined` | assertion | none | no | no | skipped by default | Supported | Accepts decline values such as `false/0/no/off` |
| ✅ Supported | `trim` | normalization | none | yes | no | skipped by default | Supported | Trims string edges |
| ✅ Supported | `blankToNull` | normalization | none | yes | no | skipped by default | Supported | Converts blank string to null |
| ✅ Supported | `positiveInt` | transform | none | yes | no | skipped by default | Supported | Positive integer transform / validation |
| ✅ Supported | `nonNegativeInt` | transform | none | yes | no | skipped by default | Supported | Non-negative integer transform / validation |
| ✅ Supported | `negativeInt` | transform | none | yes | no | skipped by default | Supported | Negative integer transform / validation |
| ✅ Supported | `nonPositiveInt` | transform | none | yes | no | skipped by default | Supported | Non-positive integer transform / validation |
| ✅ Supported | `formatTime` | transform | time format | yes | no | skipped by default | Supported | Formats time output |
| ✅ Supported | `string` | assertion | none | no | no | skipped by default | Supported | String type assertion |
| ✅ Supported | `int` | assertion | none | no | no | skipped by default | Supported | Integer type assertion |
| ✅ Supported | `array` | assertion | none | no | no | skipped by default | Supported | Array type assertion |
| ✅ Supported | `listOf` | assertion | none | no | no | skipped by default | Supported | List type assertion |
| ✅ Supported | `time` | assertion | none | no | no | skipped by default | Supported | Time parseability assertion |
| ✅ Supported | `timeAfter` | assertion | absolute time literal | no | no | skipped by default | Supported | Current time after fixed absolute time literal |
| ✅ Supported | `timeAfterOrEqual` | assertion | absolute time literal | no | no | skipped by default | Supported | Current time after or equal to fixed absolute time literal |
| ✅ Supported | `timeBefore` | assertion | absolute time literal | no | no | skipped by default | Supported | Current time before fixed absolute time literal |
| ✅ Supported | `timeBeforeOrEqual` | assertion | absolute time literal | no | no | skipped by default | Supported | Current time before or equal to fixed absolute time literal |
| ✅ Supported | `nonBlank` | assertion | none | no | no | skipped by default | Supported | Non-blank string assertion |
| ✅ Supported | `ascii` | assertion | none | no | no | skipped by default | Supported | ASCII-only string content assertion |
| ✅ Supported | `alpha` | assertion | none | no | no | skipped by default | Supported | ASCII letters only |
| ✅ Supported | `alphaNum` | assertion | none | no | no | skipped by default | Supported | ASCII letters and numbers only |
| ✅ Supported | `alphaDash` | assertion | none | no | no | skipped by default | Supported | ASCII letters, numbers, dashes, and underscores only |
| ✅ Supported | `lowercase` | assertion | none | no | no | skipped by default | Supported | ASCII lowercase assertion; does not transform |
| ✅ Supported | `uppercase` | assertion | none | no | no | skipped by default | Supported | ASCII uppercase assertion; does not transform |
| ✅ Supported | `startsWith` | assertion | JSON string / JSON string array | no | no | skipped by default | Supported | Prefix assertion; parameters must be strict JSON string literal or string array literal |
| ✅ Supported | `endsWith` | assertion | JSON string / JSON string array | no | no | skipped by default | Supported | Suffix assertion; parameters must be strict JSON string literal or string array literal |
| ✅ Supported | `contains` | assertion | JSON string / JSON string array | no | no | skipped by default | Supported | Substring assertion; parameters must be strict JSON string literal or string array literal |
| ✅ Supported | `regex` | assertion | pattern | no | no | skipped by default | Supported | Pure PHP format rule; complex patterns containing `|` need a future escaping strategy in the DSL |
| ✅ Supported | `notRegex` | assertion | pattern | no | no | skipped by default | Supported | Negative regex rule; complex patterns containing `|` need a future escaping strategy in the DSL |
| ✅ Supported | `email` | assertion | none | no | no | skipped by default | Supported | Based on PHP built-in filter without new dependency |
| ✅ Supported | `url` | assertion | none | no | no | skipped by default | Supported | Based on PHP built-in filter without new dependency |
| ✅ Supported | `uuid` | assertion | none | no | no | skipped by default | Supported | Common UUID string format |
| ✅ Supported | `json` | assertion | none | no | no | skipped by default | Supported | Only validates JSON string; does not decode |
| ✅ Supported | `minLength` | assertion | integer | no | no | skipped by default | Supported | Minimum string length |
| ✅ Supported | `maxLength` | assertion | integer | no | no | skipped by default | Supported | Maximum string length |
| ✅ Supported | `eq` | assertion | JSON literal | no | no | skipped by default | Supported | Current value strictly equals expected value |
| ✅ Supported | `neq` | assertion | JSON literal | no | no | skipped by default | Supported | Current value strictly differs from expected value |
| ✅ Supported | `in` | assertion | JSON literal array | no | no | skipped by default | Supported | Current value is in set |
| ✅ Supported | `notIn` | assertion | JSON literal array | no | no | skipped by default | Supported | Symmetric with `in`, uses strict literal set argument |
| ✅ Supported | `lengthBetween` | assertion | `[min,max]` | no | no | skipped by default | Supported | String length range; avoids ambiguous `between` |
| ✅ Supported | `itemsBetween` | assertion | `[min,max]` | no | no | skipped by default | Supported | List item count range |
| ✅ Supported | `numericBetween` | assertion | `[min,max]` | no | no | skipped by default | Supported | Numeric range with JSON number literal arguments |
| ✅ Supported | `gt` | assertion | number | no | no | skipped by default | Supported | Current value is greater than number |
| ✅ Supported | `gte` | assertion | number | no | no | skipped by default | Supported | Current value is greater than or equal to number |
| ✅ Supported | `lt` | assertion | number | no | no | skipped by default | Supported | Current value is less than number |
| ✅ Supported | `lte` | assertion | number | no | no | skipped by default | Supported | Current value is less than or equal to number |
| ✅ Supported | `numeric` | assertion | none | no | no | skipped by default | Supported | Strict numeric type assertion; accepts real int / float only, not numeric strings |
| ✅ Supported | `number` | assertion | none | no | no | skipped by default | Supported | Same strict numeric type boundary as `numeric`; kept as clearer alias-like canonical name |
| ✅ Supported | `multipleOf` | assertion | positive JSON number | no | no | skipped by default | Supported | Step / quantity assertion; accepts real int / float values only |
| ✅ Supported | `decimalPlaces` | assertion | non-negative JSON integer | no | no | skipped by default | Supported | At-most decimal-place assertion without BCMath dependency |
| ✅ Supported | `gtField` | cross-field | field path | no | yes | skipped by default | Supported | Reads referenced prepared value |
| ✅ Supported | `gteField` | cross-field | field path | no | yes | skipped by default | Supported | Reads referenced prepared value |
| ✅ Supported | `ltField` | cross-field | field path | no | yes | skipped by default | Supported | Reads referenced prepared value |
| ✅ Supported | `lteField` | cross-field | field path | no | yes | skipped by default | Supported | Reads referenced prepared value |
| ✅ Supported | `timeAfterField` | cross-field | field path | no | yes | skipped by default | Supported | Current time after referenced prepared value |
| ✅ Supported | `timeAfterOrEqualField` | cross-field | field path | no | yes | skipped by default | Supported | Current time after or equal to referenced prepared value |
| ✅ Supported | `timeBeforeField` | cross-field | field path | no | yes | skipped by default | Supported | Current time before referenced prepared value |
| ✅ Supported | `timeBeforeOrEqualField` | cross-field | field path | no | yes | skipped by default | Supported | Current time before or equal to referenced prepared value |
| ✅ Supported | `distinct` | transform | none | yes | no | skipped by default | Supported | Deduplicates list |
| ✅ Supported | `sortAsc` | transform | none | yes | no | skipped by default | Supported | Sorts list ascending |
| ✅ Supported | `minItems` | assertion | integer | no | no | skipped by default | Supported | Minimum item count |
| ✅ Supported | `maxItems` | assertion | integer | no | no | skipped by default | Supported | Maximum item count |
| ✅ Supported | `requiredKeys` | assertion | JSON string / JSON string array | no | no | skipped by default | Supported | Array key presence; parameters must be strict JSON string literal or string array literal |
| ✅ Supported | `prohibitedKeys` | assertion | JSON string / JSON string array | no | no | skipped by default | Supported | Array key prohibition; parameters must be strict JSON string literal or string array literal |
| ✅ Supported | `arrayKeysIn` | assertion | JSON string / JSON string array | no | no | skipped by default | Supported | Array key allowlist; differs from global unknown-field rejection |
| ✅ Supported | `requiredIfEq` | presence | field path + JSON literal | no | yes | fails when condition hits and current field is missing | Supported | Conditional required with strict literal |
| ✅ Supported | `requiredIfIn` | presence | field path + JSON literal array | no | yes | fails when condition hits and current field is missing | Supported | Conditional required with set match |
| ✅ Supported | `requiredIfNotEq` | presence | field path + JSON literal | no | yes | fails when condition hits and current field is missing | Supported | Conditional required with strict not-equal |
| ✅ Supported | `requiredIfNotIn` | presence | field path + JSON literal array | no | yes | fails when condition hits and current field is missing | Supported | Conditional required with outside-set match |
| ✅ Supported | `requiredIfMissing` | presence | field path | no | yes | fails when referenced field and current field are missing | Supported | Similar to required_without, but with clearer semantics |
| ✅ Supported | `nullableIfEq` | guard | field path + JSON literal | no | yes | skipped by default | Supported | Allows null when condition hits |
| ✅ Supported | `nullableIfIn` | guard | field path + JSON literal array | no | yes | skipped by default | Supported | Allows null when set condition hits |
| ✅ Supported | `nullableIfNotEq` | guard | field path + JSON literal | no | yes | skipped by default | Supported | Allows null when not equal |
| ✅ Supported | `nullableIfNotIn` | guard | field path + JSON literal array | no | yes | skipped by default | Supported | Allows null when outside set |
| ✅ Supported | `prohibitedIfEq` | presence | field path + JSON literal | no | yes | missing passes | Supported | Prohibits current field when condition hits |
| ✅ Supported | `prohibitedIfIn` | presence | field path + JSON literal array | no | yes | missing passes | Supported | Prohibits current field when set condition hits |
| ✅ Supported | `prohibitedIfNotEq` | presence | field path + JSON literal | no | yes | missing passes | Supported | Prohibits current field when not equal |
| ✅ Supported | `prohibitedIfNotIn` | presence | field path + JSON literal array | no | yes | missing passes | Supported | Prohibits current field when outside set |
| ✅ Supported | `prohibitedIfPresent` | presence | field path | no | yes | missing passes | Supported | Prohibits current field when referenced field is present |
| ✅ Supported | `sameField` | cross-field | field path | no | yes | skipped by default | Supported | Generic field equality, reads prepared value |
| ✅ Supported | `differentField` | cross-field | field path | no | yes | skipped by default | Supported | Generic field inequality, reads prepared value |
| ✅ Supported | `confirmed` | cross-field | optional field path | no | yes | skipped by default | Supported | Defaults to `<field>_confirmation`, and also supports explicit field path |
| ✅ Supported | `requiredIfPresent` | presence | field path | no | yes | fails when referenced field is present and current field is missing | Supported | Symmetric with `prohibitedIfPresent`; Laravel-like `requiredWith` is only an alias / migration concept |
| ✅ Supported | `prohibitedIfMissing` | presence | field path | no | yes | fails when referenced field is missing and current field is present | Supported | Symmetric with `requiredIfMissing`; Laravel-like `prohibitedWithout` is only an alias / migration concept |

---

## 4. P0 Planned Rules

| Status | Rule | Category | Argument | Mutates value | Reads dependency | Missing behavior | Batch | Reason / notes |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| 🔵 Adapter | `requiredWith` | alias | field path / field set | no | yes | alias-defined | Adapter | Not a core canonical rule; single-field case can map to `requiredIfPresent`, multi-field semantics belong to adapters |
| 🔵 Adapter | `requiredWithout` | alias | field path / field set | no | yes | alias-defined | Adapter | Not a core canonical rule; single-field case can map to `requiredIfMissing`, multi-field semantics belong to adapters |
| 🔵 Adapter | `prohibitedWith` | alias | field path / field set | no | yes | alias-defined | Adapter | Not a core canonical rule; single-field case can map to `prohibitedIfPresent`, multi-field semantics belong to adapters |
| 🔵 Adapter | `prohibitedWithout` | alias | field path / field set | no | yes | alias-defined | Adapter | Not a core canonical rule; single-field case can map to `prohibitedIfMissing`, multi-field semantics belong to adapters |

---

## 5. P1 Planned Rules

| Status | Rule | Category | Argument | Mutates value | Reads dependency | Missing behavior | Batch | Reason / notes |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| 🟡 Planned | `float` | assertion / transform | none | TBD | no | skipped by default | P1 | Still needs a separate boundary decision; `numeric` / `number` already cover strict int / float assertion |
| 🟡 Planned | `date` | assertion | none | no | no | skipped by default | P1 | Must clarify boundary with `time` |
| 🟡 Planned | `dateFormat` | assertion | format | no | no | skipped by default | P1 | Format assertion only; does not mutate output |

---

## 6. Adapter / Rejected / To Evaluate

| Status | Rule / capability | Category | Argument | Mutates value | Reads dependency | Missing behavior | Batch | Reason / notes |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| 🔵 Adapter | `unique` | adapter | table / field / conditions | no | maybe | adapter-defined | Adapter | Depends on ORM / DB / soft-delete policy; not low-dependency core |
| 🔵 Adapter | `exists` | adapter | table / field / conditions | no | maybe | adapter-defined | Adapter | Depends on ORM / DB |
| 🔵 Adapter | `file` | adapter | none | no | no | adapter-defined | Adapter | Depends on HTTP upload model and temp files |
| 🔵 Adapter | `image` | adapter | none | no | no | adapter-defined | Adapter | Depends on filesystem / image extensions |
| 🔵 Adapter | `mimes` / `mimetypes` | adapter | MIME set | no | no | adapter-defined | Adapter | Depends on uploaded files and MIME detection |
| 🔵 Adapter | `dimensions` | adapter | width / height constraints | no | no | adapter-defined | Adapter | Depends on image processing capability |
| 🔵 Adapter | framework auto-response | adapter | none | no | no | adapter-defined | Adapter | FormRequest / HTTP response is not core responsibility |
| 🔵 Adapter | framework translation resources | adapter | none | no | no | adapter-defined | Adapter | Core only provides message templates and overrides |
| ⛔ Rejected | `activeUrl` | network | URL | no | no | skipped by default | Rejected | Depends on DNS / network request; unstable tests |
| ⛔ Rejected | DNS MX lookup | network | domain | no | no | skipped by default | Rejected | Depends on network environment; conflicts with predictable core |
| ⚪ To evaluate | `ulid` | assertion | none | no | no | skipped by default | To evaluate | Pure PHP possible, but lower priority than `uuid` |
| ⚪ To evaluate | `listUniqueBy` | assertion / transform | field path | TBD | yes | skipped by default | To evaluate | Requires object-list path reads; wait for real use case |
| ⚪ To evaluate | password composition rules | assertion | policy argument | no | no | skipped by default | To evaluate | Pure rules may fit; leak checks / user-object rules do not belong in core |

---

## 7. Maintenance Rules

1. When adding a core rule, update both Chinese and English matrix documents.
2. When a rule moves from 🟡 Planned to ✅ Supported, add tests and canonical examples; do not only change status.
3. When a rule is marked 🔵 Adapter or ⛔ Rejected, keep the reason to avoid repeated debates.
4. README should only link to the matrix, not copy the full table.
5. If the matrix conflicts with code, the current version's code and tests win; fix the documentation immediately.
