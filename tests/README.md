# tests

当前目录用于放置共享包测试基建与测试用例。

当前约定：

- `bootstrap.php`：测试启动入口与最小自动加载；
- `TestCase.php`：轻量断言基类；
- `TestRunner.php`：递归发现并执行 `Cases/*Test.php`；
- `Cases/`：正式测试用例；
- `Fixtures/`：测试桩、假 handler、假 source、假 keyword。

执行方式：

```bash
composer test
```

或：

```bash
php tests/TestRunner.php
```
