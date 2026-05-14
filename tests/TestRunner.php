<?php

require_once __DIR__ . '/bootstrap.php';

$testDirectory = __DIR__ . '/Cases';
$testFiles = array();

if (is_dir($testDirectory)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($testDirectory, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $testFile) {
        if ($testFile->isFile() && substr($testFile->getFilename(), -8) === 'Test.php') {
            $testFiles[] = $testFile->getPathname();
        }
    }
}

sort($testFiles);

foreach ($testFiles as $testFile) {
    require_once $testFile;
}

$testCaseClass = 'HongXunPan\\Validator\\Tests\\TestCase';
$assertionFailedClass = 'HongXunPan\\Validator\\Tests\\AssertionFailedException';
$testClasses = array();

foreach (get_declared_classes() as $declaredClass) {
    if (is_subclass_of($declaredClass, $testCaseClass)) {
        $testClasses[] = $declaredClass;
    }
}

sort($testClasses);

$totalCount = 0;
$passedCount = 0;
$failedCount = 0;

foreach ($testClasses as $testClass) {
    $reflection = new ReflectionClass($testClass);
    $methods = array();

    foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
        if ($method->class === $testClass && strpos($method->name, 'test') === 0) {
            $methods[] = $method->name;
        }
    }

    sort($methods);

    foreach ($methods as $methodName) {
        $totalCount++;
        $testInstance = new $testClass();

        try {
            $testInstance->run($methodName);
            $passedCount++;
            echo '[PASS] ' . $testClass . '::' . $methodName . PHP_EOL;
        } catch (\Exception $exception) {
            $failedCount++;

            $prefix = is_a($exception, $assertionFailedClass)
                ? '[FAIL]'
                : '[ERROR]';

            echo $prefix . ' ' . $testClass . '::' . $methodName . PHP_EOL;
            echo '  ' . get_class($exception) . ': ' . $exception->getMessage() . PHP_EOL;
        }
    }
}

echo PHP_EOL;
echo 'Total: ' . $totalCount . ', Passed: ' . $passedCount . ', Failed: ' . $failedCount . PHP_EOL;

exit($failedCount > 0 ? 1 : 0);
