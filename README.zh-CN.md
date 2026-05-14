# Validator

[English README](./README.md)

`hongxunpan/validator` 是一个**与框架解耦**的验证器内核，目标是提供：

- 可扩展的 DSL keyword 体系；
- 面向共享 Composer 包的稳定公开契约；
- 便于业务仓通过适配层接入的验证核心能力。

## 当前状态

当前仓库处于 **pre-1.0 开发阶段**。

已完成：

- Composer 子包骨架；
- 公开 DSL keyword 约定；
- `AbstractRule + RuleInterface + KEY + of(...)` 约定；
- marker interface 与目录分层约定；
- PHP `>=5.6` 兼容目标收口。

开发中：

- 校验执行内核；
- 规则定义解析与懒加载；
- 内建规则迁移；
- `validated_data` 输出写入能力；
- backend 业务适配层接回。

因此，当前版本更适合作为**设计与实现中的开源仓骨架**，尚不是完整可用的稳定发布版。

## 设计目标

本项目重点解决以下问题：

1. 避免把所有规则、路径处理、结果输出和错误边界继续堆进单一巨型 `Validator.php`；
2. 为公开 DSL keyword 提供更清晰的命名、分层与复用方式；
3. 让业务仓可以保留适配层，而把通用验证内核沉淀到共享包；
4. 在尽量保守的 PHP 语法前提下，保持结构可扩展。

## 公开 DSL 约定

当前已确定的公开约定包括：

- 规则字符串采用 `ruleName[:argument]`；
- 每条规则只按**第一个** `:` 解析；
- 公开 keyword 类统一采用：
  - `KEY`
  - `key()`
  - `of(...)`
- 示例：

```php
TrimRule::KEY;
NonBlankRule::KEY;
FormatTimeRule::of('Y-m-d H:i:s');
MinLengthRule::of(2);
```

## 目录结构

```text
src/
  Validator.php
  Rule/
  Handler/
  Definition/
  Context/
  Message/
  Config/
  Support/
  Result/
  Output/
  Exception/
tests/
```

其中：

- `Rule/*` 负责公开 DSL keyword、marker interface 与类型断言 keyword；
- `Handler/*` 负责执行逻辑；
- `Definition/*` 负责规则定义与解析；
- `Support/*` 负责路径、解析、默认值等通用辅助能力。

## 安装

```bash
composer require hongxunpan/validator
```

> 当前仓库仍在开发中；在正式发布前，更推荐通过本地 path repository 或指定 ref 方式接入验证。

## 兼容性

当前目标兼容版本：

- PHP: `>=5.6`

这意味着包内实现会避免依赖仅在 PHP 7+/8+ 中才可用的语法糖与类型语法。

## 测试

当前仓库先采用轻量自维护测试运行器，避免在 core 仍快速演进时过早把测试基建绑定到高版本 PHPUnit 约束上，同时继续保持 `php >=5.6` 的兼容目标。

```bash
composer test
```

或直接执行：

```bash
php tests/TestRunner.php
```

## 路线图

后续将优先补齐：

- 规则定义解析器；
- presence / transform / assert / collection 核心规则；
- `ValidationResult`；
- `ArrayAccess` 结果写入能力；
- backend 适配示例与最小验证。

## License

本项目采用 [MIT License](./LICENSE)。
