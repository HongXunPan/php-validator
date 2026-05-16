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

## 4. 数字字段比较读取归一化后的依赖值

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

## 5. 列表规则组合

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

## 6. 何时该补到这份文档

建议在以下场景补这份文档：

- 新增一个外部使用者很可能直接照抄的 canonical 场景；
- 某个公开规则的推荐用法发生了变化；
- 某个 README 里的最小示例已经不足以表达真实使用路径；
- 新增的规则组合能够明显提升“第一次接入就能用”的完整感。

不建议：

- 为了堆示例数量，把业务项目里的临时适配写法直接抬到这里；
- 用未经过测试覆盖的代码片段充示例；
- 把 internal 执行细节写成对外承诺。
