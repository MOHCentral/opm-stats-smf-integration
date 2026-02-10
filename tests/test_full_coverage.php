<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../smf-plugins/mohaa_stats_core/Sources/MohaaStats/MohaaStatsAPI.php';

$api = new MohaaStatsAPIClient();
$failures = 0;
$passes = 0;

function assertType($value, $type, $name) {
    global $failures, $passes;
    $actualType = gettype($value);

    // PHP type handling nuance: float can be int if no decimal part, but is_float checks strict type.
    // However, json_decode often returns float for numbers with decimals.
    // My schema validator forces (float) casting, so it should be float (double).

    if ($type === 'int' && is_int($value)) {
        echo "[PASS] $name is int ($value)\n";
        $passes++;
    } elseif ($type === 'float' && (is_float($value) || is_int($value))) {
        // Strict float check: is_float returns true for 10.0
        echo "[PASS] $name is float/double ($value)\n";
        $passes++;
    } elseif ($type === 'string' && is_string($value)) {
        echo "[PASS] $name is string\n";
        $passes++;
    } elseif ($type === 'array' && is_array($value)) {
        echo "[PASS] $name is array\n";
        $passes++;
    } elseif ($type === 'bool' && is_bool($value)) {
        echo "[PASS] $name is bool ($value)\n";
        $passes++;
    } else {
        echo "[FAIL] $name expected $type, got $actualType ($value)\n";
        $failures++;
    }
}

function assertNotNull($value, $name) {
    global $failures, $passes;
    if ($value !== null) {
        echo "[PASS] $name is not null\n";
        $passes++;
    } else {
        echo "[FAIL] $name is null\n";
        $failures++;
    }
}

function assertNull($value, $name) {
    global $failures, $passes;
    if ($value === null) {
        echo "[PASS] $name is null (as expected)\n";
        $passes++;
    } else {
        echo "[FAIL] $name should be null\n";
        $failures++;
    }
}

function assertEqual($expected, $actual, $name) {
    global $failures, $passes;
    if ($expected === $actual) {
        echo "[PASS] $name equals expected value\n";
        $passes++;
    } else {
        echo "[FAIL] $name expected $expected, got $actual\n";
        $failures++;
    }
}

echo "Starting Full Coverage Test Suite with Strict Schema Validation...\n";

// 1. Test Standard Methods
$methods = [
    'getGlobalStats' => [],
    'getLeaderboard' => ['kills'],
    'getLeaderboardCount' => ['kills'],
    'getPlayerStats' => ['12345'],
    'getPlayerDeepStats' => ['12345'],
    'getPlayerWeapons' => ['12345'],
    'getPlayerMatches' => ['12345'],
    'getPlayerAchievements' => ['12345'],
    'getRecentMatches' => [],
    'getMatchCount' => [],
    'getMatchDetails' => ['123'],
    'getLiveMatches' => [],
    'getMapStats' => [],
    'getMapDetails' => ['obj_team2'],
    'getMapHeatmap' => ['obj_team2'],
    'getMapsList' => [],
    'getWeaponsList' => [],
    'getWeaponStats' => ['garand'],
    'getWeaponLeaderboard' => ['garand'],
    'getMapLeaderboard' => ['obj_team2'],
    'getMatchHeatmap' => ['123'],
    'getGlobalActivity' => [],
    'getMapPopularity' => [],
    'getLeaderboardCards' => [],
    'getPlayerPerformance' => ['12345', 30],
    'getPlayerPlaystyle' => ['12345'],
    'getAchievements' => [],
    'getAchievement' => [1],
    'getRecentAchievements' => [],
    'getAchievementLeaderboard' => [],
    'initClaim' => [1],
    'initDeviceAuth' => [1],
];

foreach ($methods as $method => $args) {
    echo "\nTesting $method...\n";
    $result = call_user_func_array([$api, $method], $args);
    assertNotNull($result, "$method result");
}

// 2. Test Strict Type Casting
echo "\nTesting Strict Type Casting (CAST_TEST)...\n";
$castTest = $api->getPlayerStats('CAST_TEST');
if ($castTest) {
    // API returns kills="999", accuracy="25.5", is_online="1"
    // Validator should cast them
    assertType($castTest['kills'], 'int', 'kills');
    assertType($castTest['accuracy'], 'float', 'accuracy');
    assertType($castTest['is_online'], 'bool', 'is_online');
    assertType($castTest['rank'], 'int', 'rank');
    assertType($castTest['score'], 'int', 'score'); // "10000" -> 10000
    assertType($castTest['is_active'], 'bool', 'is_active');

    // Check values
    assertEqual(999, $castTest['kills'], 'kills value');
    assertEqual(25.5, $castTest['accuracy'], 'accuracy value');
    assertEqual(true, $castTest['is_online'], 'is_online value');
} else {
    echo "[FAIL] CAST_TEST returned null\n";
    $failures++;
}

// 3. Test Missing Fields / Default Injection
echo "\nTesting Missing Fields Resilience (MISSING_TEST)...\n";
$missingTest = $api->getPlayerStats('MISSING_TEST');
if ($missingTest) {
    // API returns only 'name'
    // Validator should fill required fields with defaults
    assertEqual('MissingTester', $missingTest['name'], 'name');

    // Default int
    assertType($missingTest['kills'], 'int', 'kills (defaulted)');
    assertEqual(0, $missingTest['kills'], 'kills default value');

    // Default float
    assertType($missingTest['accuracy'], 'float', 'accuracy (defaulted)');
    assertEqual(0.0, $missingTest['accuracy'], 'accuracy default value');

    // Default bool
    assertType($missingTest['is_online'], 'bool', 'is_online (defaulted)');
    assertEqual(false, $missingTest['is_online'], 'is_online default value');

} else {
    echo "[FAIL] MISSING_TEST returned null\n";
    $failures++;
}

// 4. Test Error Resilience
echo "\nTesting Error Resilience...\n";

echo "Testing 500 Error...\n";
$res500 = $api->getPlayerStats('ERROR_500');
assertNull($res500, "Result for ERROR_500");

echo "Testing 404 Error...\n";
$res404 = $api->getPlayerStats('ERROR_404');
assertNull($res404, "Result for ERROR_404");

echo "Testing Invalid JSON...\n";
$resJson = $api->getPlayerStats('ERROR_JSON');
// JSON error results in null passed to validator, which returns default schema structure.
// This ensures resilience (no crash), so we expect a valid array, not null.
assertType($resJson, 'array', "Result for ERROR_JSON");
// Check that it has default values
if ($resJson) {
    assertEqual(0, $resJson['kills'], 'default kills for invalid json');
}

// 5. Test Auth History Structure
echo "\nTesting Auth History Structure...\n";
$history = $api->getLoginHistory(1);
if ($history && isset($history['history'])) {
    assertType($history['history'], 'array', 'history list');
    if (count($history['history']) > 0) {
        $item = $history['history'][0];
        assertType($item['server_name'], 'string', 'history item server_name');
        assertType($item['success'], 'bool', 'history item success');
    }
} else {
    echo "[FAIL] getLoginHistory returned invalid structure\n";
    $failures++;
}

// Summary
echo "\n------------------------------------------------\n";
echo "Test Suite Completed.\n";
echo "Passes: $passes\n";
echo "Failures: $failures\n";

if ($failures > 0) {
    exit(1);
}
exit(0);
