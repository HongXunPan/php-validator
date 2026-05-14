<?php

$vendorAutoloadFile = dirname(__DIR__) . '/vendor/autoload.php';
if (is_file($vendorAutoloadFile)) {
    require_once $vendorAutoloadFile;
}

spl_autoload_register(function ($class) {
    $prefix = 'HongXunPan\\Validator\\';
    if (strpos($class, $prefix) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $relativePath = str_replace('\\', '/', $relativeClass) . '.php';
    $classFile = dirname(__DIR__) . '/src/' . $relativePath;

    if (is_file($classFile)) {
        require_once $classFile;
    }
});

require_once __DIR__ . '/AssertionFailedException.php';
require_once __DIR__ . '/TestCase.php';

$fixturesDirectory = __DIR__ . '/Fixtures';
if (is_dir($fixturesDirectory)) {
    $fixtureIterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($fixturesDirectory, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($fixtureIterator as $fixtureFile) {
        if ($fixtureFile->isFile() && substr($fixtureFile->getFilename(), -4) === '.php') {
            require_once $fixtureFile->getPathname();
        }
    }
}
