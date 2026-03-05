<?php

declare(strict_types=1);

use App\Support\DevLayer\ErceeDevLayerPolicy;

require __DIR__.'/../../vendor/autoload.php';

$contractPath = __DIR__.'/../../docs/guides/dev/ercee-dev-layer-policy.contract.json';
if (! is_file($contractPath)) {
    fwrite(STDERR, "Missing contract file: {$contractPath}\n");
    exit(1);
}

$contract = json_decode((string) file_get_contents($contractPath), true);
if (! is_array($contract)) {
    fwrite(STDERR, "Invalid JSON in contract file.\n");
    exit(1);
}

$requiredVariables = [
    'ERCEE_DEV_LAYER',
    'ERCEE_LOG_LEVEL',
    'ERCEE_PUBLIC_DEBUG',
    'ERCEE_RUNTIME_PROFILE',
];

$variables = array_keys($contract['variables'] ?? []);
sort($variables);
sort($requiredVariables);
if ($variables !== $requiredVariables) {
    fwrite(STDERR, "Contract variables mismatch.\n");
    exit(1);
}

$expectedMatrix = $contract['behavior_matrix'] ?? [];
if (! is_array($expectedMatrix)) {
    fwrite(STDERR, "Missing behavior matrix in contract.\n");
    exit(1);
}

$resolved = ErceeDevLayerPolicy::resolve(['APP_ENV' => 'local']);
$actualMatrix = $resolved['behavior_matrix'] ?? [];

if ($actualMatrix !== $expectedMatrix) {
    fwrite(STDERR, "Behavior matrix in PHP policy does not match contract JSON.\n");
    exit(1);
}

$prod = ErceeDevLayerPolicy::resolve([
    'APP_ENV' => 'production',
    'ERCEE_RUNTIME_PROFILE' => 'prod',
    'ERCEE_DEV_LAYER' => 'true',
    'ERCEE_LOG_LEVEL' => 'debug',
    'ERCEE_PUBLIC_DEBUG' => 'true',
]);

if ($prod['can_write_debug_logs'] !== false) {
    fwrite(STDERR, "Prod profile must not allow debug logs.\n");
    exit(1);
}

if ($prod['public_debug_enabled'] !== false) {
    fwrite(STDERR, "Prod profile must not allow public debug output.\n");
    exit(1);
}

echo "Dev-layer policy validation passed.\n";
