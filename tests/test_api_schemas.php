<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../smf-plugins/mohaa_stats_core/Sources/MohaaStats/MohaaStatsAPI.php';

$api = new MohaaStatsAPIClient();
$failures = 0;
$passes = 0;

function assertStrictType(mixed $value, string $expectedType, string $name): void
{
    global $failures, $passes;
    $actualType = gettype($value);

    if ($actualType === $expectedType) {
        $displayValue = is_scalar($value) ? $value : gettype($value);
        echo "[PASS] $name is strictly $expectedType ($displayValue)\n";
        $passes++;
    } elseif ($expectedType === 'float' && ($actualType === 'double' || $actualType === 'integer')) {
        // PHP gettype returns 'double' for floats.
        if ($actualType === 'integer') {
             $displayValue = is_scalar($value) ? $value : gettype($value);
             echo "[FAIL] $name expected $expectedType, got $actualType ($displayValue)\n";
             $failures++;
        } else {
             $displayValue = is_scalar($value) ? $value : gettype($value);
             echo "[PASS] $name is strictly float/double ($displayValue)\n";
             $passes++;
        }
    } else {
        echo "[FAIL] $name expected $expectedType, got $actualType\n";
        if (is_scalar($value)) echo "Value: $value\n";
        $failures++;
    }
}

echo "Starting Schema Compliance Tests...\n";

// 1. Test Player Stats Strictness
echo "\nTesting Player Stats Schema...\n";
$player = $api->getPlayerStats('12345');
// Mock returns: ['name' => 'TestPlayer', 'guid' => '12345', 'kills' => 100]

assertStrictType($player['name'], 'string', 'player.name');
assertStrictType($player['kills'], 'integer', 'player.kills');
assertStrictType($player['kdr'], 'double', 'player.kdr'); // Default 0.0
assertStrictType($player['is_vip'], 'boolean', 'player.is_vip'); // Default false

// 2. Test Casting Logic with CAST_TEST
echo "\nTesting Type Casting Logic...\n";
$castTest = $api->getPlayerStats('CAST_TEST');
// Mock returns: ['name' => 'CastingTester', 'kills' => '999', 'accuracy' => '25.5', 'is_online' => '1']

assertStrictType($castTest['kills'], 'integer', 'castTest.kills'); // '999' -> 999
assertStrictType($castTest['accuracy'], 'double', 'castTest.accuracy'); // '25.5' -> 25.5
assertStrictType($castTest['is_online'], 'boolean', 'castTest.is_online'); // '1' -> true

// 3. Test Missing Fields / Defaults
echo "\nTesting Defaults for Missing Fields...\n";
$player = $api->getPlayerStats('12345'); // Mock doesn't return 'rounds', 'wins', etc.
assertStrictType($player['rounds'], 'integer', 'player.rounds (default)');
assertStrictType($player['kpm'], 'double', 'player.kpm (default)');

// 4. Test Nested Structures (Deep Stats)
echo "\nTesting Nested Structures (Deep Stats)...\n";
$deep = $api->getPlayerDeepStats('12345');
assertStrictType($deep['combat'], 'array', 'deep.combat');
assertStrictType($deep['movement'], 'array', 'deep.movement');

// 5. Test Lists (Global Leaderboard)
echo "\nTesting Lists (Leaderboard)...\n";
$lb = $api->getLeaderboard();
// Mock: ['players' => [], 'total' => 0, 'page' => 0]
assertStrictType($lb['players'], 'array', 'leaderboard.players');
assertStrictType($lb['total'], 'integer', 'leaderboard.total');

// 6. Test Auth History (Array of Objects)
echo "\nTesting Auth History (Array of Objects)...\n";
$hist = $api->getLoginHistory(1);
// Mock: ['history' => [['attempt_at' => ..., 'success' => true, ...], ...]]
assertStrictType($hist['history'], 'array', 'login_history.history');
if (!empty($hist['history'])) {
    $item = $hist['history'][0];
    assertStrictType($item['attempt_at'], 'string', 'history[0].attempt_at');
    assertStrictType($item['success'], 'boolean', 'history[0].success');
    // failure_reason is nullable string. In mock item 0 it is null.
    if ($item['failure_reason'] === null) {
        echo "[PASS] history[0].failure_reason is null (allowed)\n";
        $passes++;
    } else {
        assertStrictType($item['failure_reason'], 'string', 'history[0].failure_reason');
    }
}

// 7. Test Resilience - Invalid JSON
echo "\nTesting Resilience (Invalid JSON)...\n";
$resJson = $api->getPlayerStats('ERROR_JSON');
if ($resJson === null) {
    echo "[PASS] ERROR_JSON returned null\n";
    $passes++;
} else {
    echo "[FAIL] ERROR_JSON returned " . gettype($resJson) . "\n";
    $failures++;
}

echo "\n------------------------------------------------\n";
echo "Schema Tests Completed.\n";
echo "Passes: $passes\n";
echo "Failures: $failures\n";

if ($failures > 0) {
    exit(1);
}
exit(0);
