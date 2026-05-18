# 高价值 canonical 示例

本文档用于补充 `README` 之外、更贴近真实使用的 canonical 示例。

定位说明：

- 这里只放 **当前 core 已稳定支持** 的典型场景；
- 示例优先对应当前 `tests/` 已覆盖的行为；
- 不在这里放项目级 legacy helper、ORM 规则或业务适配层逻辑；
- 若某个示例仍需要 adapter layer 才能成立，应继续放在业务项目或共享工作区内部协作材料中，而不是提升为本包示例。

---

## 1. 字符串归一化与基础断言

适用场景：

- 接收表单字符串；
- 先去首尾空格；
- 再做非空与长度限制。

```php
$result = DemoValidator::validateAndNormalize(
    array('name' => '  Alice  '),
    array('name:姓名' => 'trim|nonBlank|maxLength:10')
);

if ($result->isPassed()) {
    var_dump($result->validatedData());
    // array('name' => 'Alice')
}
```

要点：

- `trim` 会先完成归一化；
- `nonBlank` 负责断言“去空格后不能是空字符串”；
- `maxLength` 继续在归一化后的值上执行。

---

## 2. 缺省值与数字归一化

适用场景：

- 查询参数允许缺省；
- 缺省时自动补默认值；
- 最终还要保证是合法整数。

```php
$result = DemoValidator::validateAndNormalize(
    array(),
    array('page:页码' => 'default:1|nonNegativeInt')
);

if ($result->isPassed()) {
    var_dump($result->validatedData());
    // array('page' => 1)
}
```

要点：

- `default` 属于 presence 语义，会先补出默认值；
- `nonNegativeInt` 会继续把值归一化为整数；
- 最终 `validatedData()` 里拿到的是归一化后的 `int`，不是原始字符串。

---

## 3. 时间格式化与跨字段比较

适用场景：

- 一个字段需要先格式化时间；
- 另一个字段要和它做前后关系比较。

```php
$result = DemoValidator::validateAndNormalize(
    array(
        'start_at' => '2026-05-14 10:00:00',
        'end_at' => '2026/05/14 12:00:00',
    ),
    array(
        'start_at:开始时间' => 'time',
        'end_at:结束时间' => 'formatTime:Y-m-d H:i:s|timeAfterOrEqualField:start_at',
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

要点：

- 比较规则当前只需要传 **被依赖字段路径**：
  - `timeAfterOrEqualField:start_at`
- 被依赖字段显示名会自动从字段声明中解析：
  - `start_at:开始时间`
- 比较规则读取的是被依赖字段的 **依赖可读值**，不是脏的原始输入；
- 若被依赖字段本地校验失败，比较规则会跳过，不额外制造级联脏错误。

---

## 4. 固定时间字面量比较

适用场景：

- 字段需要与一个固定发布时间、截止时间或系统配置时间比较；
- 不需要读取另一个字段；
- 希望时间参数是明确的绝对时间，而不是 `next monday` 这类自然语言。

```php
$result = DemoValidator::validateAndNormalize(
    array(
        'publish_at' => '2026-05-14 10:00:01',
        'close_at' => '2026-05-14 09:59:59',
    ),
    array(
        'publish_at:发布时间' => 'timeAfter:2026-05-14 10:00:00',
        'close_at:关闭时间' => 'timeBefore:2026-05-14 10:00:00',
    )
);

if ($result->isPassed()) {
    var_dump($result->validatedData());
    // array(
    //     'publish_at' => '2026-05-14 10:00:01',
    //     'close_at' => '2026-05-14 09:59:59',
    // )
}
```

要点：

- `timeAfter / timeAfterOrEqual / timeBefore / timeBeforeOrEqual` 比较当前字段与固定时间字面量；
- 时间参数要求是明确绝对时间，例如 `2026-05-14 10:00:00`，不接受 `tomorrow / next monday` 这类自然语言；
- 若需要读取另一个字段的 prepared value，请继续使用 `timeAfterField / timeBeforeField` 系列。

---

## 5. 数字字段比较读取归一化后的依赖值

适用场景：

- 当前字段要大于另一个字段；
- 被依赖字段自己也需要先归一化。

```php
$result = DemoValidator::validateAndNormalize(
    array(
        'min_value' => ' 2 ',
        'current_value' => '3',
    ),
    array(
        'min_value:最小值' => 'trim|positiveInt',
        'current_value:当前值' => 'positiveInt|gtField:min_value',
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

要点：

- `gtField:min_value` 读取的是 `min_value` 归一化后的值；
- 因此像 `' 2 '` 这种原始输入不会让比较规则读到脏字符串；
- 这也是当前运行期 data 生命周期上下文化后的一个核心收益。

---

## 6. 列表规则组合

适用场景：

- 输入是一个列表；
- 需要去重、排序，并限制数量。

```php
$result = DemoValidator::validateAndNormalize(
    array('ids' => array(3, 2, 2)),
    array('ids:ID列表' => 'listOf|distinct|sortAsc|minItems:1|maxItems:3')
);

if ($result->isPassed()) {
    var_dump($result->validatedData());
    // array('ids' => array(2, 3))
}
```

要点：

- `listOf` 负责把目标值识别为列表；
- `distinct` 去重；
- `sortAsc` 升序；
- `minItems / maxItems` 再做数量断言；
- 这类规则组合适合 ID 列表、标签列表、简单枚举列表。

---

## 7. 布尔归一化与接受 / 拒绝确认

适用场景：

- HTTP 表单里布尔值可能来自字符串；
- 业务希望最终拿到真实 `bool`；
- 同意条款、拒绝选项只做断言，不一定改变原始输出值。

```php
$result = DemoValidator::validateAndNormalize(
    array(
        'newsletter_enabled' => 'on',
        'terms' => 'yes',
        'marketing_declined' => 'no',
    ),
    array(
        'newsletter_enabled:订阅开关' => 'toBool|boolean',
        'terms:服务条款' => 'accepted',
        'marketing_declined:营销拒绝确认' => 'declined',
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

要点：

- `boolean` 是严格断言，只接受真实 `bool`；
- 字符串布尔值请先用 `toBool` 归一化；
- `accepted / declined` 适合“必须同意 / 必须拒绝”这类确认语义，本身不负责把值转成 `bool`。

---

## 8. 格式校验与范围规则

适用场景：

- 注册或资料表单需要常见格式校验；
- 字符串长度、列表数量、数值范围需要用明确规则表达；
- 希望避免一个多义 `between` 同时承载字符串、数组、数字三种语义。

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
        'tags' => array('alumni', 'event'),
        'score' => 98,
        'ratio' => 0.75,
        'delta' => '-2',
        'offset' => '0',
    ),
    array(
        'email:邮箱' => 'email',
        'homepage:主页链接' => 'url',
        'trace_id:追踪ID' => 'uuid',
        'metadata:扩展信息' => 'json',
        'username:用户名' => 'alphaDash|notIn:["root","admin"]|lengthBetween:[3,20]',
        'slug:短标识' => 'ascii|lowercase|alphaDash|lengthBetween:[3,40]',
        'env_code:环境编码' => 'ascii|uppercase|alphaDash',
        'tags:标签' => 'listOf|itemsBetween:[1,3]',
        'score:分数' => 'int|numericBetween:[0,100]',
        'ratio:比例' => 'number|numericBetween:[0,1]',
        'delta:变化量' => 'negativeInt',
        'offset:偏移量' => 'nonPositiveInt',
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
    //     'tags' => array('alumni', 'event'),
    //     'score' => 98,
    //     'ratio' => 0.75,
    //     'delta' => -2,
    //     'offset' => 0,
    // )
}
```

要点：

- `email / url / uuid / json` 都是低依赖 core 格式断言；
- `json` 只判断字符串是否为合法 JSON，不自动 decode；
- `ascii / alpha / alphaNum / alphaDash / lowercase / uppercase` 覆盖常见 ASCII 字符内容断言，不引入 Unicode 依赖；
- `lengthBetween / itemsBetween / numericBetween` 分别对应字符串长度、列表数量、数值范围，避免 `between` 语义混杂；
- `numeric / number` 是严格数值断言，只接受真实 `int / float`，不接受 numeric string；
- `numericBetween` 要求当前值已经是数字类型；如果输入来自字符串，先用合适的数字归一化规则；
- `negativeInt / nonPositiveInt` 与 `positiveInt / nonNegativeInt` 一样属于整数 transform，成功后输出 `int`；
- 当前竖线 DSL 会用 `|` 切分规则，复杂正则若包含 `|`，应等待后续 DSL 转义或数组规则声明能力，不建议直接硬写进字符串规则。

---

## 9. 字段关系与条件 presence

适用场景：

- 确认字段需要与原字段一致；
- 新旧字段不能相同；
- 某个字段出现 / 缺失会影响另一个字段是否必填或禁止出现。

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
        'password:密码' => 'string|minLength:8|confirmed',
        'password_confirmation:确认密码' => 'string',
        'email:邮箱' => 'email',
        'email_confirmation:确认邮箱' => 'string|sameField:email',
        'old_password:旧密码' => 'string',
        'new_password:新密码' => 'string|differentField:old_password',
        'profile.name:姓名' => 'trim|string',
        'nickname:昵称' => 'requiredIfPresent:profile.name|string',
        'invite_code:邀请码' => 'string',
        'referral_reason:推荐说明' => 'prohibitedIfMissing:invite_code|string',
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

要点：

- `confirmed` 默认比较 `<field>_confirmation`，也可以写成 `confirmed:repeat_password` 指定字段；
- `sameField / differentField / confirmed` 都读取被依赖字段的 prepared value；
- 被依赖字段应在 rule map 中声明，否则比较规则无法稳定读取归一化后的依赖值；
- `requiredIfPresent` 是 core canonical，不把 `requiredWith` 作为主规则名；
- `prohibitedIfMissing` 是 core canonical，不把 `prohibitedWithout` 作为主规则名；
- Laravel-like `requiredWith / requiredWithout / prohibitedWith / prohibitedWithout` 更适合作为 adapter alias 或迁移层写法。

---

## 10. 何时该补到这份文档

建议在以下场景补这份文档：

- 新增一个外部使用者很可能直接照抄的 canonical 场景；
- 某个公开规则的推荐用法发生了变化；
- 某个 README 里的最小示例已经不足以表达真实使用路径；
- 新增的规则组合能够明显提升“第一次接入就能用”的完整感。

不建议：

- 为了堆示例数量，把业务项目里的临时适配写法直接抬到这里；
- 用未经过测试覆盖的代码片段充示例；
- 把 internal 执行细节写成对外承诺。
