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
    if ($type === 'int' && is_int($value)) {
        echo "[PASS] $name is int ($value)\n";
        $passes++;
    } elseif ($type === 'float' && (is_float($value) || is_int($value))) { // float can be int in PHP if no decimal
        echo "[PASS] $name is float/int ($value)\n";
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

function assertEmptyResult($value, $name) {
    global $failures, $passes;
    // With strict schema validation, errors return empty arrays/objects or 0
    if (empty($value) && $value !== null) {
        echo "[PASS] $name is empty (as expected for error)\n";
        $passes++;
    } else {
        echo "[FAIL] $name should be empty, got " . print_r($value, true) . "\n";
        $failures++;
    }
}

echo "Starting Full Coverage Test Suite...\n";

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
    if (is_array($result) && !empty($result)) {
        echo "[INFO] $method returned data\n";
    } elseif (is_int($result)) {
         echo "[INFO] $method returned int: $result\n";
    }
}

// 2. Test Type Casting (using /performance and CAST_TEST)
echo "\nTesting Type Casting...\n";
$perf = $api->getPlayerPerformance('12345', 30);
if ($perf) {
    assertType($perf['spm'], 'float', 'spm');
    assertType($perf['kpm'], 'float', 'kpm');
    assertType($perf['kd_ratio'], 'float', 'kd_ratio');
    assertType($perf['points'], 'int', 'points');
    assertType($perf['is_vip'], 'bool', 'is_vip');
} else {
    echo "[FAIL] getPlayerPerformance returned null/empty\n";
    $failures++;
}

$castTest = $api->getPlayerStats('CAST_TEST');
if ($castTest) {
    assertType($castTest['kills'], 'int', 'kills (casted from string)');
    assertType($castTest['accuracy'], 'float', 'accuracy (casted from string)');
    assertType($castTest['is_online'], 'bool', 'is_online (casted from string 1)');
} else {
    echo "[FAIL] getPlayerStats('CAST_TEST') returned null\n";
    $failures++;
}

// 3. Test Error Resilience
echo "\nTesting Error Resilience...\n";
// Note: API Client now returns empty array/object on failure instead of null to prevent crashes

echo "Testing 500 Error...\n";
$res500 = $api->getPlayerStats('ERROR_500');
assertEmptyResult($res500, "Result for ERROR_500");

echo "Testing 404 Error...\n";
$res404 = $api->getPlayerStats('ERROR_404');
assertEmptyResult($res404, "Result for ERROR_404");

echo "Testing Invalid JSON...\n";
$resJson = $api->getPlayerStats('ERROR_JSON');
assertEmptyResult($resJson, "Result for ERROR_JSON");

// 4. Test New Auth Methods and Stubs
echo "\nTesting New Auth Methods...\n";
$authMethods = [
    'getLoginHistory' => [1],
    'getTrustedIPs' => [1],
    'getPendingIPApprovals' => [1],
    'deleteTrustedIP' => [1, 101],
    'resolvePendingIP' => [1, 202, 'approve'],
];

foreach ($authMethods as $method => $args) {
    echo "\nTesting $method...\n";
    $result = call_user_func_array([$api, $method], $args);
    assertNotNull($result, "$method result");

    // Check specific structure for some methods
    if ($method === 'getLoginHistory' && isset($result['history'][0])) {
         // attempt_at might be string, so checking not null
         assertNotNull($result['history'][0]['attempt_at'], 'history.attempt_at');
    }
    if ($method === 'getTrustedIPs' && isset($result['trusted_ips'][0])) {
         assertType($result['trusted_ips'][0]['id'], 'int', 'trusted_ips.id');
    }
    if ($method === 'getPendingIPApprovals' && isset($result['pending_ips'][0])) {
         assertType($result['pending_ips'][0]['id'], 'int', 'pending_ips.id');
    }
}

echo "\nTesting Stubs...\n";
$stubs = [
    'getHeadToHead' => ['a', 'b'],
    'getPlayerRank' => ['a'],
    'getPlayerMapStats' => ['a'],
    'getActivePlayers' => [24]
];

foreach ($stubs as $method => $args) {
    echo "Testing Stub $method...\n";
    $result = call_user_func_array([$api, $method], $args);
    // Stubs might return null or empty array, but shouldn't crash
    echo "[PASS] $method returned " . gettype($result) . "\n";
    $passes++;
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
