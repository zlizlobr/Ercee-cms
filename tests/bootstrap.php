<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

$appEnv = getenv('APP_ENV') ?: 'unknown';
$dbConnection = getenv('DB_CONNECTION') ?: '';
$dbDatabase = getenv('DB_DATABASE') ?: '';
$appConfigCache = getenv('APP_CONFIG_CACHE') ?: '';
$projectRoot = realpath(__DIR__.'/..') ?: dirname(__DIR__);
$defaultConfigCache = $projectRoot.'/bootstrap/cache/config.php';
$testingDbDir = $projectRoot.'/storage/testing/';
$safeScriptMode = (getenv('ERCEE_TEST_SAFE') ?: '') === '1';

if ($appEnv !== 'testing') {
    fwrite(STDERR, "[TEST-GUARD] Refusing to run tests outside APP_ENV=testing.\n");
    exit(1);
}

$normalizedConfigCache = str_replace('\\', '/', $appConfigCache);
$normalizedDefaultConfigCache = str_replace('\\', '/', $defaultConfigCache);

if ($appConfigCache === '' || $normalizedConfigCache === $normalizedDefaultConfigCache) {
    fwrite(STDERR, "[TEST-GUARD] Unsafe APP_CONFIG_CACHE. Use isolated test config cache path.\n");
    exit(1);
}

if ($dbConnection !== 'sqlite') {
    fwrite(STDERR, "[TEST-GUARD] Only sqlite test database is allowed for this project.\n");
    exit(1);
}

$isMemory = $dbDatabase === ':memory:';
$isTestingFileDb = str_starts_with($dbDatabase, $testingDbDir);

if (! $isMemory && ! $isTestingFileDb) {
    fwrite(STDERR, "[TEST-GUARD] Refusing to run tests against non-test database: {$dbDatabase}\n");
    exit(1);
}

if ($isTestingFileDb && ! $safeScriptMode) {
    fwrite(STDERR, "[TEST-GUARD] File-based test DB requires scripts/test-safe.sh (ERCEE_TEST_SAFE=1).\n");
    exit(1);
}
