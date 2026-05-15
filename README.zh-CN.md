# Validator

[English README](./README.md)

`hongxunpan/validator` 是一个与框架解耦的验证器内核，当前围绕三条主线实现：

- `Rule` 是公开扩展主体，并由规则类自己执行校验；
- 使用侧通过 `Validator` 子类数组扩展，而不是实现 handler/source/registry 体系；
- kernel 只做编排，把执行细节下沉到更小的内部协作者。

## 当前状态

当前仓库处于 pre-1.0 开发阶段。

已完成：

- Composer 子包骨架；
- PHP `>=5.6` 兼容基线；
- `RuleInterface + AbstractRule + KEY + of(...)` 公开约定；
- `Validator` 子类三组扩展数组：
  - `extraRules`
  - `ruleAliases`
  - `ruleMessages`
- canonical core 主校验链路。

进行中：

- 更完整的 canonical core 规则覆盖；
- backend 适配层接回；
- README 示例与发布收口。

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

规则查找顺序固定为：

1. 先把输入规则名当作真实规则查找；
2. 只有真实规则不存在时才尝试 alias；
3. 得到最终规则类；
4. 执行 `RuleClass::validate($context)`。

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

## 兼容性

- PHP: `>=5.6`

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
- 需要 legacy 规则或文案时，在项目自己的 `Validator` 子类中补 `extraRules / ruleAliases / ruleMessages`
- 需要 ORM / 业务规则（如 `unique / exists`）时，继续放在项目适配层

不建议：

- 为兼容旧项目而让 `ValidationResult` 实现 `ArrayAccess`
- 让 core 回退为“数组优先”心智
- 把某个项目的历史 helper 契约直接提升成公共包默认契约

如需给业务项目落适配层，可参考：

- [项目适配层接入样板](./docs/项目适配层接入样板.zh-CN.md)

## 测试

```bash
composer test
```

或直接执行：

```bash
php tests/TestRunner.php
```

## License

本项目采用 [MIT License](./LICENSE)。
