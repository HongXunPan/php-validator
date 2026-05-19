# Validator

[English README](./README.md)

`hongxunpan/validator` 是一个与框架解耦的验证器内核，当前围绕三条主线实现：

- `Rule` 是公开扩展主体，并由规则类自己执行校验；
- 使用侧通过 `Validator` 子类数组扩展，而不是实现 handler/source/registry 体系；
- kernel 只做编排，把执行细节下沉到更小的内部协作者。

## 当前状态

当前仓库处于 pre-1.0 开发阶段。

变更记录请见：

- [CHANGELOG](./CHANGELOG.md)

贡献方式请见：

- [贡献说明](./CONTRIBUTING.md)

性能与 benchmark 态度请见：

- [性能与基准说明](./docs/性能与基准说明.zh-CN.md)

更完整的 canonical 使用场景请见：

- [高价值 canonical 示例](./docs/高价值 canonical 示例.zh-CN.md)

规则能力、规划状态与不受理原因请见：

- [规则能力矩阵](./docs/规则能力矩阵.zh-CN.md)

如果你正在评估是否从 Laravel Validator、Symfony Validator、Respect、Valitron 或 Rakit 等验证库迁移，请先阅读：

- [与同类验证库对比](./docs/与同类验证库对比.zh-CN.md)
- [从其他验证库迁移](./docs/从其他验证库迁移.zh-CN.md)

已完成：

- Composer 子包骨架；
- PHP `>=5.6` 兼容基线；
- `RuleInterface + AbstractRule + KEY + of(...)` 公开约定；
- `Validator` 子类三组扩展配置：
  - `extraRules`
  - `ruleAliases`
  - `ruleMessages`
- canonical core 主校验链路。

进行中：

- 更完整的 canonical core 规则覆盖；
- backend 适配层接回；
- README 示例与发布收口。

## 选型摘要

这个包不是 Laravel Validator 或 Symfony Validator 的完整替代品，而是一个低依赖、框架无关、面向数组输入的验证与归一化内核。

优先选择它的典型理由：

- 你需要在非 Laravel 项目、共享 Composer 包或旧 PHP 环境中复用验证内核；
- 你希望把自定义规则作为第一优先级，用结构化 `Rule` 类承接业务规则，而不是长期堆回调或数组黑盒；
- 你需要让校验、归一化、默认值、条件规则和跨字段依赖读取处于同一条可测试流水线里；
- 你希望 core 保持低依赖，把 ORM、HTTP 响应、上传文件等项目能力放到 adapter 层。

不建议选择它的典型场景：

- Laravel 项目只需要常规表单验证、FormRequest、`unique / exists` 或文件上传规则；
- Symfony 项目主要围绕 Entity / DTO / Attribute Constraint 建模；
- 你优先需要上百条开箱即用规则，而不是优先建设自定义规则扩展面。

简要对比与迁移成本请见上方两份 docs，README 只保留入口级判断，避免首页继续膨胀。

## 默认扩展方式

默认使用侧只需要定义一个 `Validator` 子类：

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
        'trimTest' => '$paramName 需要先完成 trim',
    );
}
```

当规则、别名、文案配置较多时，推荐按两类场景处理：

- 若只是想把长数组拆文件，优先使用 provider class 常量：
  - `EXTRA_RULES_PROVIDER_CLASS`
  - `RULE_ALIASES_PROVIDER_CLASS`
  - `RULE_MESSAGES_PROVIDER_CLASS`
- 若确实需要继承合并或动态逻辑，再覆写：
  - `defineExtraRules()`
  - `defineRuleAliases()`
  - `defineRuleMessages()`

规则查找顺序固定为：

1. 先把输入规则名当作真实规则查找；
2. 只有真实规则不存在时才尝试 alias；
3. 得到最终规则类；
4. 执行 `RuleClass::validate(RuleContext $context)`。

## 公开 DSL 约定

- 规则字符串采用 `ruleName[:argument]`；
- 每条规则只按第一个 `:` 解析；
- 公开 keyword 类统一提供：
  - `KEY`
  - `key()`
  - `of(...)`

## 目录结构

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

## 安装

```bash
composer require hongxunpan/validator
```

## 30 秒最小示例

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
        'name:姓名' => 'required|trim|minLength:2|maxLength:20',
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

这个示例展示了最小默认用法：

- 不定义任何自定义 rule；
- 直接使用 core canonical 规则；
- `validateAndNormalize(...)` 返回 `ValidationResult`；
- 成功后通过 `validatedData()` 读取归一化后的结果。

## 规则速查

本表故意保持简短，用于快速判断应该选哪一类规则；完整逐条状态、参数格式、是否改值和规划说明，请以
[规则能力矩阵](./docs/规则能力矩阵.zh-CN.md) 为准。

| 范围 | 规则 | 简短说明 |
| --- | --- | --- |
| 存在性 / missing | `required`, `default`, `nullable` | 要求字段存在、为缺失字段补默认值，或 present 且为 `null` 时中断后续 local value 规则 |
| 类型 / 归一化 | `string`, `int`, `array`, `listOf`, `boolean`, `toBool`, `trim`, `blankToNull` | 断言常见输入类型，或归一化常见标量形态 |
| 整数 transform | `positiveInt`, `nonNegativeInt`, `negativeInt`, `nonPositiveInt` | 把类整数值归一化为 `int`，并限制正负边界 |
| 数值 assertion | `numeric`, `number`, `float`, `numericBetween`, `multipleOf`, `decimalPlaces`, `gt`, `gte`, `lt`, `lte` | 校验真实数值；`float` 比 `number` 更严格，会拒绝 `int` 和 numeric string |
| 字符串格式 / 内容 | `nonBlank`, `email`, `url`, `uuid`, `json`, `regex`, `notRegex`, `ascii`, `alpha`, `alphaNum`, `alphaDash`, `lowercase`, `uppercase` | 校验常见字符串格式和 ASCII 内容边界 |
| 字符串参数 | `minLength`, `maxLength`, `lengthBetween`, `startsWith`, `endsWith`, `contains`, `in`, `notIn`, `eq`, `neq` | 校验长度、前后缀/子串、集合命中和严格相等 |
| 时间 / 日期 | `time`, `formatTime`, `timeAfter`, `timeAfterOrEqual`, `timeBefore`, `timeBeforeOrEqual`, `date`, `dateFormat` | 解析、格式化或严格断言时间/日期值，不额外引入日期库 |
| 数组 / 列表 | `distinct`, `sortAsc`, `minItems`, `maxItems`, `itemsBetween`, `requiredKeys`, `prohibitedKeys`, `arrayKeysIn` | 校验或归一化列表值，以及当前数组内部 key |
| 跨字段 / 条件 | `gtField`, `gteField`, `ltField`, `lteField`, `timeAfterField`, `timeBeforeField`, `requiredIf*`, `nullableIf*`, `prohibitedIf*` | 读取其他字段 prepared value，承接比较、条件存在性或 guard |

## 兼容性

- PHP: `>=5.6`

## 性能取舍

当前阶段默认：

- 优先保证公开契约清楚；
- 优先保证执行链可维护、可测试、可扩展；
- 在没有真实热点前，不为了“看起来更快”而回退到数组黑盒或大方法实现。

如需进一步了解当前性能立场与 benchmark 触发条件，请见：

- [性能与基准说明](./docs/性能与基准说明.zh-CN.md)

## 公开契约与稳定性边界

当前仓库仍处于 **pre-1.0** 阶段，但已开始冻结公开契约边界。

当前稳定公开面主要包括：

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
- `AbstractConditionalFieldPresenceRule`
- `AbstractConditionalPresentValueGuardRule`
- `AbstractReferencedFieldCompareRule`
- `RuleContext / RuleValueReaderInterface / ValidationOptions / RuleResult`
- `ValidatedDataWriterInterface / ArrayAccessValidatedDataWriter`

明确不属于稳定承诺范围的是：

- `Internal/*`
- `Rule\\CoreRules`
- 其他未在公开文档中列出的内部装配辅助方法

完整说明请见：

- [公开契约与稳定性承诺](./docs/公开契约与稳定性承诺.zh-CN.md)

## 使用 `ValidationResult`

`validate(...)`、`validateAndNormalize(...)`、`validateListAndNormalize(...)` 当前都返回：

- `HongXunPan\Validator\Result\ValidationResult`

最常用的方法有：

- `isPassed() / isFailed()`
- `count()`
- `errors()`
- `detail()`
- `validatedData()`
- `toArray()`

示例：

```php
$result = DemoValidator::validate(
    array('name' => ''),
    array('name:姓名' => 'required')
);

if ($result->isFailed()) {
    var_dump($result->count());
    var_dump($result->errors());
    var_dump($result->detail());
}
```

如果旧项目仍然依赖数组 envelope，可在适配层里显式：

```php
$legacy = $result->toArray();
```

而不是要求 core 回退成数组优先接口。

## 自定义 Rule 最小示例

```php
<?php

use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\AbstractPresentValueNormalizationRule;
use HongXunPan\Validator\Context\RuleContext;

class TrimNameRule extends AbstractPresentValueNormalizationRule
{
    const KEY = 'trimName';
    const MESSAGE = '$paramName 必须是字符串';

    public static function validate(RuleContext $context)
    {
        if (!is_string($context->value())) {
            return RuleResult::fail($context->value());
        }

        return RuleResult::pass(trim($context->value()));
    }
}
```

然后在使用侧 `Validator` 子类里挂进去：

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

调用：

```php
$result = DemoValidator::validateAndNormalize(
    array('name' => '  Alice  '),
    array('name:姓名' => 'trimName')
);
```

## 引用字段条件规则扩展

如果你的自定义规则属于“**引用其他字段作为触发条件**”这一类，推荐直接继承：

- `HongXunPan\Validator\Rule\Condition\AbstractConditionalFieldPresenceRule`
- `HongXunPan\Validator\Rule\Condition\AbstractConditionalPresentValueGuardRule`

这两个基类公开了一组 `protected` 前置判断 helper，例如：

- `skipUnlessReferencedEq(...)`
- `skipUnlessReferencedIn(...)`
- `skipUnlessReferencedNotEq(...)`
- `skipUnlessReferencedNotIn(...)`
- `skipUnlessReferencedPresent(...)`
- `skipUnlessReferencedMissing(...)`

它们的语义是：

- 条件未命中：直接返回当前字段的 `pass` 结果；
- 条件命中：返回 `null`，由具体规则继续处理后半段语义。

`requiredIfXxx` 风格示例：

```php
<?php

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\Argument\FieldExpectedLiteralArgumentParser;
use HongXunPan\Validator\Rule\Condition\AbstractConditionalFieldPresenceRule;

class RequiredIfFlagRule extends AbstractConditionalFieldPresenceRule
{
    const KEY = 'requiredIfFlag';
    const MESSAGE = '$paramName is required';
    const ARGUMENT_PARSER = FieldExpectedLiteralArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        if ($result = static::skipUnlessReferencedEq($context)) {
            return $result;
        }

        if (!$context->current()->exists()) {
            return RuleResult::failPath($context->current());
        }

        return RuleResult::passPath($context->current());
    }
}
```

`nullableIfXxx` 风格示例：

```php
<?php

use HongXunPan\Validator\Context\RuleContext;
use HongXunPan\Validator\Result\RuleResult;
use HongXunPan\Validator\Rule\Argument\FieldReferenceArgumentParser;
use HongXunPan\Validator\Rule\Condition\AbstractConditionalPresentValueGuardRule;

class NullableIfProfilePresentRule extends AbstractConditionalPresentValueGuardRule
{
    const KEY = 'nullableIfProfilePresent';
    const MESSAGE = '$paramName nullable';
    const ARGUMENT_PARSER = FieldReferenceArgumentParser::class;

    public static function validate(RuleContext $context)
    {
        if ($result = static::skipUnlessReferencedPresent($context)) {
            return $result;
        }

        if ($context->value() === null) {
            return RuleResult::passAndBreakPath($context->current());
        }

        return RuleResult::passPath($context->current());
    }
}
```

## 引用字段比较规则扩展

如果你的规则属于“**当前字段与引用字段做数值 / 时间比较**”，推荐继承：

- `HongXunPan\Validator\Rule\AbstractReferencedFieldCompareRule`

它会统一处理：

- 解析引用字段路径；
- 读取 dependent target value；
- 引用字段缺失时直接 `pass`；
- 当前值或引用值为空时直接 `pass`；
- `displayRuleValue(...)` 的字段显示。

具体子类只需要关心：

- 如何把两个值归一化成可比较值；
- 最终比较关系是否成立。

## `RuleContext / RuleResult` 便捷 API

当前公开的便捷 API 包括：

- `RuleContext::current()`
- `RuleContext::raw()`
- `RuleContext::materialized($fieldPath)`
- `RuleContext::dependent($fieldPath)`
- `RuleResult::passPath(PathValue $value)`
- `RuleResult::failPath(PathValue $value)`
- `RuleResult::passAndBreakPath(PathValue $value)`

这些方法的目的不是替代现有 `value()/fieldExists()` 用法，而是让“需要同时关心 value + exists”的规则写法更短、更直观。

## `extraRules / ruleAliases / ruleMessages` 示例

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
        'trimName' => '$paramName 需要先去掉首尾空格',
        'minLength' => '$paramName 长度太短',
    );
}
```

说明：

- `extraRules`：注册项目自己的真实规则名；
- `ruleAliases`：把 legacy / 简写映射到最终规则名；
- `ruleMessages`：按**最终规则名**覆盖错误文案。
- 当配置规模继续增长时：
  - 若只是想把长数组拆文件，优先使用 provider class 常量：
    - `EXTRA_RULES_PROVIDER_CLASS`
    - `RULE_ALIASES_PROVIDER_CLASS`
    - `RULE_MESSAGES_PROVIDER_CLASS`
  - 若确实需要继承合并或动态逻辑，再覆写 `defineExtraRules() / defineRuleAliases() / defineRuleMessages()`。

规则查找顺序固定为：

1. 先把输入规则名当作真实规则查找；
2. 只有真实规则不存在时才查 alias；
3. alias 命中后执行最终规则类。

## 列表校验示例

### 标量列表

```php
$result = DemoValidator::validateListAndNormalize(
    array('  a  ', '  bb  '),
    'trim|minLength:1'
);
```

### 对象列表

```php
$result = DemoValidator::validateListAndNormalize(
    array(
        array('name' => ' Alice '),
        array('name' => ' Bob '),
    ),
    array(
        'name:姓名' => 'required|trim|minLength:1',
    ),
    array(
        'field_prefix' => 'items',
    )
);
```

对象列表场景下，如果某一项不是数组，`detail` 中会记录类似：

- `items.2`

这样的失败位置。

## 更多 canonical 示例

如果你已经理解最小 README 用法，但还想快速看几类更贴近真实输入的 core 场景，例如：

- 字符串归一化与长度断言组合；
- `default` + 数字归一化；
- 时间格式化与跨字段比较；
- 数字字段比较读取归一化后的依赖值；
- 列表规则组合；

请继续看：

- [高价值 canonical 示例](./docs/高价值 canonical 示例.zh-CN.md)

## 适配层与 legacy 项目

如果你的项目历史上仍依赖：

- 数组 envelope
- `validateOrThrow()` 直接返回 payload 数组
- 中文旧文案
- legacy alias / legacy rule
- ORM 规则如 `unique / exists`

推荐做法不是修改 core，而是在项目内保留 adapter layer。

项目级适配层样板属于共享工作区内部协作材料，不作为包内对外文档公开提供。

## 接入与适配注意事项

本包当前**不是旧 validator helper 的无缝替换物**。

core 默认返回：

- `validate(...)` -> `ValidationResult`
- `validateAndNormalize(...)` -> `ValidationResult`
- `validateListAndNormalize(...)` -> `ValidationResult`

因此如果你的项目历史上依赖以下行为：

- 直接消费数组 envelope：`count / errors / detail / validated_data`
- `validateOrThrow()` 直接返回 payload 数组
- 旧的 alias / legacy rule / 中文错误提示
- 旧的跨字段比较参数格式：`fieldPath,label`

则应在**业务项目自己的适配层**中接回，而不是期待 core 自动兼容。

推荐做法：

- 需要旧 envelope 时，用 `ValidationResult::toArray()` 显式转换
- 需要旧 `*OrThrow` 签名时，在项目内封装 facade / helper
- 需要 legacy 规则或文案时，在项目自己的 `Validator` 子类中补 `extraRules / ruleAliases / ruleMessages`；若只是拆分长配置，优先使用 provider class 常量；只有确实需要继承合并或动态逻辑时再覆写 `define*()` 方法
- 需要 ORM / 业务规则（如 `unique / exists`）时，继续放在项目适配层

不建议：

- 为兼容旧项目而让 `ValidationResult` 实现 `ArrayAccess`
- 让 core 回退为“数组优先”心智
- 把某个项目的历史 helper 契约直接提升成公共包默认契约

如需给业务项目落适配层，应在项目自己的仓库或共享工作区协作层中维护适配样板，而不是把项目兼容说明继续堆回公共包文档。

## 测试

```bash
composer test
```

或直接执行：

```bash
php tests/TestRunner.php
```

当前 GitHub Actions 默认会在以下版本矩阵上执行：

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

并统一运行：

```bash
composer test
```

## 静态分析

当前仓库使用：

- `phpstan`

默认命令：

```bash
composer analyse
```

当前静态分析在 CI 中单独运行，使用较高版本 PHP 执行工具链。

需要注意：

- 当前 `phpstan` 系列已不能直接按 PHP 5.6 语义建模；
- 因此静态分析只负责结构与类型层约束；
- PHP 5.6 运行兼容性继续依赖多版本测试矩阵兜底。

## License

本项目采用 [MIT License](./LICENSE)。
