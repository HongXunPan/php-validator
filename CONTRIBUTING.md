# 贡献说明

本文档用于说明 `hongxunpan/validator` 当前阶段的最小贡献方式与提交前约束。

当前仓库仍处于 **pre-1.0** 阶段，欢迎补文档、补测试、补 canonical rule、补工程化设施，但请先理解本包的公开边界与内部边界，不要直接把某个项目的历史 helper 契约反向灌回 core。

---

## 1. 开始之前先看什么

建议按下面顺序建立上下文：

1. `README.zh-CN.md`
2. `README.md`
3. `docs/公开契约与稳定性承诺.zh-CN.md`
4. `CHANGELOG.md`
5. `docs/发布检查清单.zh-CN.md`
6. `docs/性能与基准说明.zh-CN.md`
7. `docs/高价值 canonical 示例.zh-CN.md`
8. `src/` 与 `tests/` 当前实现

如果改动涉及规则模型、internal 分层或 data 生命周期，请先确认现有 README 与公开契约文档是否已覆盖该行为，再决定是否需要继续扩展文档。

---

## 2. 当前公开边界

提交前请先区分：

### 2.1 稳定公开面

当前稳定公开面主要包括：

- `Validator`
- `ValidationKernel`
- `ValidationResult`
- `RuleInterface / AbstractRule / AbstractPresenceRule / AbstractValueRule`
- `PresenceRuleInterface / ValueRuleInterface`
- `ConditionalPresenceRuleInterface / ValueMaterializationRuleInterface / DependentValueRuleInterface`
- `RuleContext / ValidationOptions / RuleResult`
- `ValidatedDataWriterInterface / ArrayAccessValidatedDataWriter`

### 2.2 非稳定内部面

以下内容默认不承诺稳定：

- `Internal/*`
- `ValidationKernel::catalog()`
- `Rule\CoreRules`
- 其他未在 README / docs 中明确列为公开契约的辅助方法

因此：

- 若你的改动影响公开行为，必须同步 README / docs / CHANGELOG；
- 若你的改动只发生在 `Internal/*`，可以不对外承诺稳定，但仍应保证测试通过。

---

## 3. 如何跑验证

### 3.1 最小测试

```bash
composer test
```

或：

```bash
php tests/TestRunner.php
```

### 3.2 静态分析

```bash
composer analyse
```

说明：

- 静态分析当前使用 `phpstan`
- 静态分析在现代 PHP 工具链上运行
- PHP 5.6 运行兼容性主要由多版本测试矩阵兜底

### 3.3 CI 行为

当前 CI 默认包含：

- 多 PHP 版本测试矩阵
- 静态分析 job

提交前至少应保证本地能跑通：

- `composer test`

如果改动涉及类型、继承、方法签名或目录迁移，建议额外本地跑：

- `composer analyse`

---

## 4. 新增或修改 Rule 的基本要求

### 4.1 默认扩展方式

新增 rule 时，优先沿用当前公开扩展模型：

1. 编写 Rule 类；
2. 确认它实现：
   - `PresenceRuleInterface`
   - 或 `ValueRuleInterface`
3. 如有需要，再补：
   - `ConditionalPresenceRuleInterface`
   - `ValueMaterializationRuleInterface`
   - `DependentValueRuleInterface`
4. 通过 `Validator` 子类的：
   - `extraRules`
   - `ruleAliases`
   - `ruleMessages`
   暴露给使用侧。

### 4.2 不要这样做

不要：

- 把项目特有 legacy helper 行为直接升格成 core 默认契约；
- 让 `ValidationResult` 为兼容旧项目而回退成数组优先心智；
- 新增一批 `Internal/*` 之外的公开类型，却不同步文档；
- 为了某个项目的 ORM / 业务规则，把 `unique / exists` 一类能力直接推回 core。

### 4.3 新增 rule 时建议同步补的内容

- 对应测试
- 如对外可见，再补 README 示例
- 如属于公开行为变更，再补 CHANGELOG

---

## 5. 改文档时的同步要求

出现以下情况时，请同步 README / docs / CHANGELOG：

### 5.1 必须同步 README 的场景

- 新增公开入口
- 修改公开返回值语义
- 新增公开推荐用法
- 新增首次上手必须知道的能力

### 5.2 必须同步 `docs/公开契约与稳定性承诺` 的场景

- public / internal 边界变化
- 某个原本内部的能力升级为公开契约
- 某个公开能力需要明确“虽 public 但不承诺稳定”

### 5.3 必须同步 `CHANGELOG` 的场景

- 公开能力新增
- 公开行为变化
- 关键工程化设施新增
- 重要 bug fix

### 5.4 建议同步 `docs/性能与基准说明` 的场景

- 明显改变 internal 运行期分配模型
- 新增额外遍历轮次
- 引入正式 benchmark
- 为性能原因调整默认结构取舍

### 5.5 建议同步 `docs/高价值 canonical 示例` 的场景

- 新增一个外部使用者很可能直接照抄的 canonical 规则组合
- 某个 README 最小示例已不足以表达真实推荐用法
- 某个公开规则的推荐组合或参数写法发生变化
- 新增的示例能够明显提升“第一次接入就能用”的完整感

---

## 6. 提交前检查清单

提交前建议至少过一遍下面清单：

- [ ] `composer test` 通过
- [ ] 如适用，`composer analyse` 通过
- [ ] 没有把 IDE / 本地缓存噪音带进仓库
- [ ] 若改了公开行为，README 已同步
- [ ] 若改了 public/internal 边界，契约文档已同步
- [ ] 若改了对外可见行为，CHANGELOG 已同步
- [ ] 没有把项目级 adapter / helper 兼容逻辑错误地推回 core

---

## 7. 当前阶段更欢迎的贡献类型

当前阶段更欢迎：

- 补 README / docs
- 补测试
- 补 CI / 工程化设施
- 补 canonical core rule
- 补稳定的错误边界与结果边界收口

相对不欢迎：

- 未经讨论就扩大 public API
- 未经文档化就引入新的默认契约
- 为某个单项目历史包袱牺牲 core 的长期边界

---

## 8. 发版前额外检查

如果本轮目标已经接近打 tag 或正式对外发布，请再额外阅读：

- `docs/发布检查清单.zh-CN.md`

不要只看测试通过就直接发版。
