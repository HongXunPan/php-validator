# High-value Canonical Examples

This document supplements `README` with practical canonical examples that are closer to real usage.

Positioning:

- Only include scenarios that are stably supported by the current core.
- Prefer examples backed by current `tests/` behavior.
- Do not include project-level legacy helpers, ORM rules, or business adapter logic here.
- If an example still requires an adapter layer, keep it in the business project or internal workspace notes instead of promoting it to package-level examples.

---

## 1. String normalization and basic assertions

Use when:

- A form field arrives as a string.
- Edge spaces should be removed first.
- The normalized value must then be non-blank and length-limited.

```php
$result = DemoValidator::validateAndNormalize(
    array('name' => '  Alice  '),
    array('name:Name' => 'trim|nonBlank|maxLength:10')
);

if ($result->isPassed()) {
    var_dump($result->validatedData());
    // array('name' => 'Alice')
}
```

Notes:

- `trim` normalizes the string first.
- `nonBlank` asserts that the trimmed string is not blank.
- `maxLength` runs against the normalized value.

---

## 2. Default value and integer normalization

Use when:

- A query parameter may be missing.
- A default should be filled when it is missing.
- The final output should be a valid integer.

```php
$result = DemoValidator::validateAndNormalize(
    array(),
    array('page:Page' => 'default:1|nonNegativeInt')
);

if ($result->isPassed()) {
    var_dump($result->validatedData());
    // array('page' => 1)
}
```

Notes:

- `default` is a missing-value rule and creates the key only when the field is missing.
- `nonNegativeInt` then normalizes and validates the value as an integer.
- `validatedData()` returns the normalized `int`, not the original string.

---

## 3. Time formatting and cross-field comparison

Use when:

- One field needs time formatting.
- Another field must be compared with it.

```php
$result = DemoValidator::validateAndNormalize(
    array(
        'start_at' => '2026-05-14 10:00:00',
        'end_at' => '2026/05/14 12:00:00',
    ),
    array(
        'start_at:Start time' => 'time',
        'end_at:End time' => 'formatTime:Y-m-d H:i:s|timeAfterOrEqualField:start_at',
    )
);

if ($result->isPassed()) {
    var_dump($result->validatedData());
    // array(
    //     'start_at' => '2026-05-14 10:00:00',
    //     'end_at' => '2026-05-14 12:00:00',
    // )
}
```

Notes:

- Field comparison rules only need the referenced field path, for example `timeAfterOrEqualField:start_at`.
- The referenced field label is read from its field declaration.
- Comparison reads the referenced field's prepared value, not dirty raw input.
- If the referenced field fails local validation, comparison rules skip to avoid cascading noisy errors.

---

## 4. Fixed time literal comparison

Use when:

- A field must be compared with a fixed publish time, deadline, or configured time.
- No referenced field is needed.
- The time argument should be an explicit absolute time, not natural language such as `next monday`.

```php
$result = DemoValidator::validateAndNormalize(
    array(
        'publish_at' => '2026-05-14 10:00:01',
        'close_at' => '2026-05-14 09:59:59',
        'event_date' => '2026-05-14',
        'display_date' => '2026/05/14',
    ),
    array(
        'publish_at:Publish time' => 'timeAfter:2026-05-14 10:00:00',
        'close_at:Close time' => 'timeBefore:2026-05-14 10:00:00',
        'event_date:Event date' => 'date',
        'display_date:Display date' => 'dateFormat:Y/m/d',
    )
);

if ($result->isPassed()) {
    var_dump($result->validatedData());
    // array(
    //     'publish_at' => '2026-05-14 10:00:01',
    //     'close_at' => '2026-05-14 09:59:59',
    //     'event_date' => '2026-05-14',
    //     'display_date' => '2026/05/14',
    // )
}
```

Notes:

- `timeAfter / timeAfterOrEqual / timeBefore / timeBeforeOrEqual` compare the current field with a fixed time literal.
- The argument must be an explicit absolute time, for example `2026-05-14 10:00:00`; natural language such as `tomorrow / next monday` is rejected.
- Use `timeAfterField / timeBeforeField` variants when you need to read another field's prepared value.
- `date` is a strict `Y-m-d` calendar-date assertion; `dateFormat` checks an exact format and does not normalize output.

---

## 5. Numeric field comparison reads normalized dependency values

Use when:

- The current field must be greater than another field.
- The referenced field also needs normalization first.

```php
$result = DemoValidator::validateAndNormalize(
    array(
        'min_value' => ' 2 ',
        'current_value' => '3',
    ),
    array(
        'min_value:Minimum value' => 'trim|positiveInt',
        'current_value:Current value' => 'positiveInt|gtField:min_value',
    )
);

if ($result->isPassed()) {
    var_dump($result->validatedData());
    // array(
    //     'min_value' => 2,
    //     'current_value' => 3,
    // )
}
```

Notes:

- `gtField:min_value` reads the normalized value of `min_value`.
- Raw input such as `' 2 '` is not leaked into the comparison rule.
- This is one of the core benefits of the current runtime data lifecycle.

---

## 6. List rule composition

Use when:

- Input is a list.
- It should be deduplicated, sorted, and size-limited.

```php
$result = DemoValidator::validateAndNormalize(
    array('ids' => array(3, 2, 2)),
    array('ids:ID list' => 'listOf|distinct|sortAsc|minItems:1|maxItems:3')
);

if ($result->isPassed()) {
    var_dump($result->validatedData());
    // array('ids' => array(2, 3))
}
```

Notes:

- `listOf` asserts that the value is a list.
- `distinct` deduplicates the list.
- `sortAsc` sorts it ascending.
- `minItems / maxItems` then assert item counts.
- This pattern works well for ID lists, tag lists, and simple enum lists.

---

## 7. Boolean normalization and accepted / declined confirmations

Use when:

- HTTP form booleans may arrive as strings.
- Business logic wants a real `bool` in validated output.
- Accepted / declined confirmations are assertions and do not necessarily mutate the output value.

```php
$result = DemoValidator::validateAndNormalize(
    array(
        'newsletter_enabled' => 'on',
        'terms' => 'yes',
        'marketing_declined' => 'no',
    ),
    array(
        'newsletter_enabled:Newsletter enabled' => 'toBool|boolean',
        'terms:Terms' => 'accepted',
        'marketing_declined:Marketing declined' => 'declined',
    )
);

if ($result->isPassed()) {
    var_dump($result->validatedData());
    // array(
    //     'newsletter_enabled' => true,
    //     'terms' => 'yes',
    //     'marketing_declined' => 'no',
    // )
}
```

Notes:

- `boolean` is strict and only accepts real `bool` values.
- Use `toBool` first when string booleans should be normalized.
- `accepted / declined` are best for must-accept / must-decline confirmation semantics and do not convert values to `bool`.

---

## 8. Format assertions and range rules

Use when:

- Registration or profile forms need common format checks.
- String length, list count, and numeric ranges should be expressed explicitly.
- You want to avoid one ambiguous `between` rule for strings, arrays, and numbers.

```php
$result = DemoValidator::validateAndNormalize(
    array(
        'email' => 'alice@example.com',
        'homepage' => 'https://example.com/profile/alice',
        'trace_id' => '550e8400-e29b-41d4-a716-446655440000',
        'metadata' => '{"source":"form"}',
        'username' => 'alice_2026',
        'slug' => 'alumni-2026',
        'env_code' => 'PROD_2026',
        'redirect_url' => 'https://example.com/callback',
        'tracking_code' => 'promo-2026',
        'description' => 'Alumni Event 2026',
        'tags' => array('alumni', 'event'),
        'profile' => array('id' => 1, 'name' => 'Alice'),
        'payload' => array('title' => 'Event', 'visible' => true),
        'score' => 98,
        'ratio' => 0.75,
        'quantity' => 12,
        'amount' => 19.95,
        'delta' => '-2',
        'offset' => '0',
    ),
    array(
        'email:Email' => 'email',
        'homepage:Homepage' => 'url',
        'trace_id:Trace ID' => 'uuid',
        'metadata:Metadata' => 'json',
        'username:Username' => 'alphaDash|notIn:["root","admin"]|lengthBetween:[3,20]',
        'slug:Slug' => 'ascii|lowercase|alphaDash|lengthBetween:[3,40]',
        'env_code:Environment code' => 'ascii|uppercase|alphaDash',
        'redirect_url:Redirect URL' => 'startsWith:["http://","https://"]',
        'tracking_code:Tracking code' => 'endsWith:"2026"',
        'description:Description' => 'contains:["Event","Meetup"]',
        'tags:Tags' => 'listOf|itemsBetween:[1,3]',
        'profile:Profile' => 'requiredKeys:["id","name"]|arrayKeysIn:["id","name","status"]',
        'payload:Payload' => 'prohibitedKeys:["password","token"]',
        'score:Score' => 'int|numericBetween:[0,100]',
        'ratio:Ratio' => 'float|numericBetween:[0,1]',
        'quantity:Quantity' => 'number|multipleOf:3',
        'amount:Amount' => 'number|decimalPlaces:2',
        'delta:Delta' => 'negativeInt',
        'offset:Offset' => 'nonPositiveInt',
    )
);

if ($result->isPassed()) {
    var_dump($result->validatedData());
    // array(
    //     'email' => 'alice@example.com',
    //     'homepage' => 'https://example.com/profile/alice',
    //     'trace_id' => '550e8400-e29b-41d4-a716-446655440000',
    //     'metadata' => '{"source":"form"}',
    //     'username' => 'alice_2026',
    //     'slug' => 'alumni-2026',
    //     'env_code' => 'PROD_2026',
    //     'redirect_url' => 'https://example.com/callback',
    //     'tracking_code' => 'promo-2026',
    //     'description' => 'Alumni Event 2026',
    //     'tags' => array('alumni', 'event'),
    //     'profile' => array('id' => 1, 'name' => 'Alice'),
    //     'payload' => array('title' => 'Event', 'visible' => true),
    //     'score' => 98,
    //     'ratio' => 0.75,
    //     'quantity' => 12,
    //     'amount' => 19.95,
    //     'delta' => -2,
    //     'offset' => 0,
    // )
}
```

Notes:

- `email / url / uuid / json` are low-dependency core format assertions.
- `json` only validates that the value is a valid JSON string; it does not decode the value.
- `ascii / alpha / alphaNum / alphaDash / lowercase / uppercase` cover common ASCII string content assertions without adding Unicode dependencies.
- `startsWith / endsWith / contains` accept strict JSON string literals or JSON string array literals, for example `startsWith:"api-"` or `startsWith:["http://","https://"]`; bare strings are rejected.
- `requiredKeys / prohibitedKeys / arrayKeysIn` validate keys inside the current array. They are not a replacement for global unknown-field rejection.
- `lengthBetween / itemsBetween / numericBetween` separately cover string length, list count, and numeric range.
- `numeric / number` are strict numeric assertions; they only accept real `int / float` values, not numeric strings.
- `float` is stricter than `number`; it only accepts real `float` values and does not normalize numeric strings.
- `numericBetween` expects the current value to already be a numeric type. If the input is a string, normalize it first with an appropriate numeric rule.
- `multipleOf` expects a positive JSON number argument, and `decimalPlaces` expects a non-negative JSON integer argument; both validate real `int / float` values only.
- `negativeInt / nonPositiveInt`, like `positiveInt / nonNegativeInt`, are integer transforms and output `int` values on success.
- The current pipe-based DSL splits rules by `|`. Complex regex patterns containing `|` should wait for a future DSL escaping or array rule declaration capability instead of being hard-coded into a string rule.

---

## 9. Field relationships and conditional presence

Use when:

- A confirmation field must match the original field.
- New and old values must differ.
- One field being present or missing controls whether another field is required or prohibited.

```php
$result = DemoValidator::validateAndNormalize(
    array(
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
        'email' => 'alice@example.com',
        'email_confirmation' => 'alice@example.com',
        'old_password' => 'old-secret',
        'new_password' => 'new-secret',
        'profile' => array('name' => '  Alice  '),
        'nickname' => 'Alice',
    ),
    array(
        'password:Password' => 'string|minLength:8|confirmed',
        'password_confirmation:Password confirmation' => 'string',
        'email:Email' => 'email',
        'email_confirmation:Email confirmation' => 'string|sameField:email',
        'old_password:Old password' => 'string',
        'new_password:New password' => 'string|differentField:old_password',
        'profile.name:Name' => 'trim|string',
        'nickname:Nickname' => 'requiredIfPresent:profile.name|string',
        'invite_code:Invite code' => 'string',
        'referral_reason:Referral reason' => 'prohibitedIfMissing:invite_code|string',
    )
);

if ($result->isPassed()) {
    var_dump($result->validatedData());
    // array(
    //     'password' => 'secret123',
    //     'password_confirmation' => 'secret123',
    //     'email' => 'alice@example.com',
    //     'email_confirmation' => 'alice@example.com',
    //     'old_password' => 'old-secret',
    //     'new_password' => 'new-secret',
    //     'profile' => array('name' => 'Alice'),
    //     'nickname' => 'Alice',
    // )
}
```

Notes:

- `confirmed` compares with `<field>_confirmation` by default, and also supports explicit paths such as `confirmed:repeat_password`.
- `sameField / differentField / confirmed` read the referenced field's prepared value.
- Referenced fields should be declared in the rule map; otherwise relationship rules cannot reliably read normalized dependency values.
- `requiredIfPresent` is the core canonical name; `requiredWith` is not the primary rule name.
- `prohibitedIfMissing` is the core canonical name; `prohibitedWithout` is not the primary rule name.
- Laravel-like `requiredWith / requiredWithout / prohibitedWith / prohibitedWithout` are better kept as adapter aliases or migration-layer names.
- When building conditional rules in PHP, prefer `RequiredIfEqRule::ofFieldValue(...)`, `RequiredIfInRule::ofFieldValues(...)`, `SameFieldRule::ofField(...)`, and `RuleChain::join(...)` over hand-written `json_encode(...)` and repeated string concatenation.

```php
use HongXunPan\Validator\Rule\Condition\NullableIfNotEqRule;
use HongXunPan\Validator\Rule\Condition\RequiredIfEqRule;
use HongXunPan\Validator\Rule\RuleChain;

'source_id:Source ID' => RuleChain::join(array(
    RequiredIfEqRule::ofFieldValue('target_mode', 'activity'),
    NullableIfNotEqRule::ofFieldValue('target_mode', 'activity'),
    'nonNegativeInt',
));
```

---

## 10. When to add examples here

Add to this document when:

- A new scenario is likely to be copied directly by external users.
- The recommended usage of a public rule changes.
- A minimal README example is no longer enough to describe a real usage path.
- A new rule composition clearly improves first-time adoption.

Do not add examples here just to increase quantity, copy temporary business adapter code, or publish snippets that are not covered by tests.
