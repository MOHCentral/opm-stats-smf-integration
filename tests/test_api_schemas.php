<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../smf-plugins/mohaa_stats_core/Sources/MohaaStats/MohaaStatsAPI.php';

$api = new MohaaStatsAPIClient();
$failures = 0;
$passes = 0;

function assertStrictType($value, $type, $name) {
    global $failures, $passes;
    $actualType = gettype($value);

    $pass = false;
    if ($type === 'int' && is_int($value)) $pass = true;
    elseif ($type === 'float' && (is_float($value) || is_int($value))) $pass = true;
    elseif ($type === 'string' && is_string($value)) $pass = true;
    elseif ($type === 'bool' && is_bool($value)) $pass = true;
    elseif ($type === 'array' && is_array($value)) $pass = true;

    if ($pass) {
        echo "[PASS] $name is $type (" . json_encode($value) . ")\n";
        $passes++;
    } else {
        echo "[FAIL] $name expected $type, got $actualType (" . json_encode($value) . ")\n";
        $failures++;
    }
}

echo "Starting Schema Validation & Resilience Tests...\n";

// 1. Resilience Tests (API Failure)
echo "\n--- Resilience Tests ---\n";

// The API client currently returns null on HTTP errors (500, 404).
// This is compliant if the UI handles nulls, but for strictness we want to see how it handles malformed data too.

// ERROR_JSON: Mock returns "This is not JSON..." with 200 OK.
// json_decode returns null.
// validate(null) should return default structure (empty array usually).
// Let's verify this behavior.
echo "Testing ERROR_JSON (Invalid JSON response)...\n";
// Note: We need a method that uses a schema. getPlayerStats uses 'player_stats'.
$resJson = $api->getPlayerStats('ERROR_JSON');

if (is_array($resJson)) {
    echo "[PASS] ERROR_JSON returned array (Safe Default)\n";
    // Check if it has defaults
    if (isset($resJson['kills']) && $resJson['kills'] === 0) {
        echo "[PASS] Returned default values (kills=0)\n";
        $passes++;
    } else {
         echo "[WARN] Returned array but missing default values? " . json_encode($resJson) . "\n";
    }
    $passes++;
} else {
    // If json_decode fails, MohaaStatsAPI returns null?
    // In MohaaStatsAPI::get:
    // $data = json_decode($response, true);
    // if ($schema) { $data = MohaaStatsSchemas::validate($data, $schema); }
    // If json_decode returns null, validate(null, schema) is called.
    // MohaaStatsSchemas::validate(null) -> if (!is_array($data)) $data = [];
    // Then it iterates schema and fills defaults.
    // So it SHOULD return a default structure!
    echo "[FAIL] ERROR_JSON returned " . gettype($resJson) . " (" . json_encode($resJson) . "). Expected array with defaults.\n";
    $failures++;
}

// 2. Schema Casting Tests
echo "\n--- Schema Casting Tests ---\n";
// CAST_TEST endpoint returns: {'kills': '999', 'accuracy': '25.5', 'is_online': '1'}
$castTest = $api->getPlayerStats('CAST_TEST');

if ($castTest) {
    assertStrictType($castTest['kills'], 'int', 'kills');
    assertStrictType($castTest['accuracy'], 'float', 'accuracy');
    assertStrictType($castTest['is_online'], 'bool', 'is_online');

    if ($castTest['kills'] === 999 && abs($castTest['accuracy'] - 25.5) < 0.001 && $castTest['is_online'] === true) {
         echo "[PASS] Values casted correctly\n";
         $passes++;
    } else {
         echo "[FAIL] Values casted but incorrect values: " . json_encode($castTest) . "\n";
         $failures++;
    }
} else {
    echo "[FAIL] CAST_TEST returned null\n";
    $failures++;
}

// 3. Structure Tests (Global Stats)
echo "\n--- Global Stats Structure ---\n";
$global = $api->getGlobalStats();
if ($global) {
    assertStrictType($global['total_kills'], 'int', 'total_kills');
    assertStrictType($global['total_players'], 'int', 'total_players');
} else {
    echo "[FAIL] getGlobalStats returned null\n";
    $failures++;
}

// 4. List Structure Tests (Leaderboard)
echo "\n--- Leaderboard Structure ---\n";
$lb = $api->getLeaderboard();
if ($lb) {
    assertStrictType($lb['players'], 'array', 'players list');
    assertStrictType($lb['total'], 'int', 'total');
} else {
    echo "[FAIL] getLeaderboard returned null\n";
    $failures++;
}

// 5. Array Root Test (Weapons List)
echo "\n--- Weapons List (Array Root) ---\n";
$weapons = $api->getWeaponsList();
if (is_array($weapons)) {
    echo "[PASS] getWeaponsList returned array\n";
    $passes++;
} else {
    echo "[FAIL] getWeaponsList returned non-array\n";
    $failures++;
}

// 6. Nested Objects (Auth History)
echo "\n--- Auth History (Nested) ---\n";
$hist = $api->getLoginHistory(1);
if ($hist && isset($hist['history'])) {
    assertStrictType($hist['history'], 'array', 'history list');
    if (!empty($hist['history'])) {
        $item = $hist['history'][0];
        assertStrictType($item['success'], 'bool', 'history item success');
        assertStrictType($item['server_name'], 'string', 'history item server_name');
    } else {
        echo "[WARN] history list is empty, cannot test item structure\n";
    }
} else {
    echo "[FAIL] getLoginHistory returned invalid structure\n";
    $failures++;
}

echo "\nTests Completed. Passes: $passes, Failures: $failures\n";
exit($failures > 0 ? 1 : 0);
