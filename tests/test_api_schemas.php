<?php
declare(strict_types=1);

// Stub SMF constant
define('SMF', 1);

require_once __DIR__ . '/../smf-plugins/mohaa_stats_core/Sources/MohaaStats/MohaaStatsSchemas.php';

$failures = 0;
$passes = 0;

function assertSame($expected, $actual, $message) {
    global $failures, $passes;
    if ($expected === $actual) {
        echo "[PASS] $message\n";
        $passes++;
    } else {
        echo "[FAIL] $message\n";
        echo "Expected: " . json_encode($expected) . "\n";
        echo "Actual:   " . json_encode($actual) . "\n";
        $failures++;
    }
}

function assertTrue($condition, $message) {
    global $failures, $passes;
    if ($condition) {
        echo "[PASS] $message\n";
        $passes++;
    } else {
        echo "[FAIL] $message\n";
        $failures++;
    }
}

echo "Starting Schema Validation Tests...\n";

// 1. Test Global Stats Schema
echo "\nTest Global Stats Schema (Simple Fields)...\n";
$input = [
    'total_kills' => '100', // String should cast to int
    'total_players' => 50,
    // Missing total_matches, active_servers
];
$expected = [
    'total_kills' => 100,
    'total_players' => 50,
    'total_matches' => 0, // Default
    'active_servers' => 0, // Default
];
$result = MohaaStatsSchemas::validate($input, 'global_stats');
assertSame($expected, $result, "Global Stats Partial Input");

// 2. Test Player Stats (Types and Nulls)
echo "\nTest Player Stats (Types)...\n";
$input = [
    'id' => 'abc', // Invalid int -> default 0
    'kdr' => '1.5', // String float -> float 1.5
    'is_online' => 1, // Int bool -> true
    'verified' => 'true', // String bool -> true
];
$result = MohaaStatsSchemas::validate($input, 'player_stats');
assertSame(0, $result['id'], "Invalid Int should be 0");
assertSame(1.5, $result['kdr'], "String float should cast");
assertTrue($result['is_online'] === true, "Int 1 should be true");
assertTrue($result['verified'] === true, "String 'true' should be true");
assertSame('Unknown', $result['name'], "Missing string should be default");

// 3. Test Null Input (Full Default Generation)
echo "\nTest Null Input (Full Default Generation)...\n";
$result = MohaaStatsSchemas::validate(null, 'player_stats');
assertTrue(is_array($result), "Result should be array");
assertSame(0, $result['kills'], "Default kills should be 0");
assertSame('', $result['guid'], "Default guid should be empty string");

// 4. Test Nested Schema (Player Deep Stats)
echo "\nTest Nested Schema...\n";
$input = [
    'combat' => [
        'melee_kills' => 5,
        // Missing grenade_kills
    ]
];
$result = MohaaStatsSchemas::validate($input, 'player_deep_stats');
assertSame(5, $result['combat']['melee_kills'], "Nested field present");
assertSame(0, $result['combat']['grenade_kills'], "Nested field default");

// 5. Test Array Root (Leaderboard)
echo "\nTest Array Root (Leaderboard)...\n";
$input = [
    ['rank' => 1, 'name' => 'Player1', 'value' => 100],
    ['rank' => 2, 'name' => 'Player2'], // Missing value
];
$result = MohaaStatsSchemas::validate($input, 'leaderboard_player_list');
assertTrue(count($result) === 2, "Should have 2 items");
assertSame(100.0, $result[0]['value'], "Item 1 value");
assertSame(0.0, $result[1]['value'], "Item 2 default value");

// 6. Test Array Root with Null/Invalid
echo "\nTest Array Root with Invalid Input...\n";
$result = MohaaStatsSchemas::validate(null, 'leaderboard_player_list');
assertSame([], $result, "Null input for list should be empty array");

$result = MohaaStatsSchemas::validate(['foo' => 'bar'], 'leaderboard_player_list');
assertSame([], $result, "Assoc array input for list should be empty array");

// 7. Test Scalar Input (Should not crash)
echo "\nTest Scalar Input...\n";
$result = MohaaStatsSchemas::validate("scalar string", 'global_stats');
assertTrue(is_array($result), "Scalar input for object schema should result in default array");
assertSame(0, $result['total_kills'], "Default values present");

$result = MohaaStatsSchemas::validate(123, 'leaderboard_player_list');
assertSame([], $result, "Scalar input for array_root should be empty array");

// Summary
echo "\n------------------------------------------------\n";
echo "Passes: $passes\n";
echo "Failures: $failures\n";

if ($failures > 0) {
    exit(1);
}
exit(0);
