<?php
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
    echo "[FAIL] getPlayerPerformance returned null\n";
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

echo "Testing 500 Error...\n";
$res500 = $api->getPlayerStats('ERROR_500');
assertNull($res500, "Result for ERROR_500");

echo "Testing 404 Error...\n";
$res404 = $api->getPlayerStats('ERROR_404');
assertNull($res404, "Result for ERROR_404");

echo "Testing Invalid JSON...\n";
$resJson = $api->getPlayerStats('ERROR_JSON');
assertNull($resJson, "Result for ERROR_JSON");

// Summary
echo "\n------------------------------------------------\n";
echo "Test Suite Completed.\n";
echo "Passes: $passes\n";
echo "Failures: $failures\n";

if ($failures > 0) {
    exit(1);
}
exit(0);
