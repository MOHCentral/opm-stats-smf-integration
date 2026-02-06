<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../smf-plugins/mohaa_stats_core/Sources/MohaaStats/MohaaStatsSchemas.php';

echo "Starting Schema Validation Tests...\n";

$passes = 0;
$failures = 0;

function assertStrictType($value, $expectedType, $name) {
    global $passes, $failures;
    $actualType = gettype($value);

    // PHP gettype returns 'double' for float
    if ($expectedType === 'float' && $actualType === 'double') $actualType = 'float';

    if ($actualType === $expectedType) {
        echo "[PASS] $name is strictly $expectedType\n";
        $passes++;
    } else {
        echo "[FAIL] $name expected $expectedType, got $actualType\n";
        $failures++;
    }
}

function assertValue($value, $expectedValue, $name) {
    global $passes, $failures;
    if ($value === $expectedValue) {
        echo "[PASS] $name has expected value\n";
        $passes++;
    } else {
        echo "[FAIL] $name value mismatch. Expected " . json_encode($expectedValue) . ", got " . json_encode($value) . "\n";
        $failures++;
    }
}

// 1. Test Primitives & Casting
echo "\nTest 1: Primitives Casting\n";
$schema = 'player_rank';
$input = [
    'rank' => '10',         // String -> Int
    'name' => 'TestPlayer',
    'kills' => 50,
    'kdr' => '1.5',         // String -> Float
    'is_vip' => 1,          // Int -> Bool
    'accuracy' => '25.5',   // String -> Float
    'extra_field' => 'should_be_removed'
];

$result = MohaaStatsSchemas::validate($input, 'player_rank');

assertStrictType($result['rank'], 'integer', 'rank');
assertValue($result['rank'], 10, 'rank value');

assertStrictType($result['kdr'], 'float', 'kdr');
assertValue($result['kdr'], 1.5, 'kdr value');

assertStrictType($result['is_vip'], 'boolean', 'is_vip');
assertValue($result['is_vip'], true, 'is_vip value');

if (array_key_exists('extra_field', $result)) {
    echo "[FAIL] Extra field was not removed\n";
    $failures++;
} else {
    echo "[PASS] Extra field removed\n";
    $passes++;
}

// 2. Test Null Handling & Defaults
echo "\nTest 2: Null Handling\n";
$input = null; // API returned null or missing
$result = MohaaStatsSchemas::validate($input, 'player_rank');

assertStrictType($result['kills'], 'integer', 'kills (default)');
assertValue($result['kills'], 0, 'kills value');
assertStrictType($result['name'], 'string', 'name (default)');
assertValue($result['name'], 'Unknown', 'name value');

// 3. Test Nested Arrays (Leaderboard)
echo "\nTest 3: Nested Arrays (Leaderboard)\n";
$input = [
    'total' => '100',
    'players' => [
        [
            'name' => 'P1',
            'kills' => '500'
        ],
        [
            'name' => 'P2',
            'kills' => 250
        ]
    ]
];

$result = MohaaStatsSchemas::validate($input, 'leaderboard');

assertStrictType($result['total'], 'integer', 'total');
assertStrictType($result['players'], 'array', 'players list');
assertValue(count($result['players']), 2, 'players count');

assertStrictType($result['players'][0]['kills'], 'integer', 'P1 kills');
assertValue($result['players'][0]['kills'], 500, 'P1 kills value');

// 4. Test Recursive Array List (Maps List)
echo "\nTest 4: Recursive Array List\n";
$input = [
    ['id' => 'map1', 'count' => '10'],
    ['id' => 'map2', 'count' => 20]
];
// maps_list is array_of_map_stat
$result = MohaaStatsSchemas::validate($input, 'maps_list');

assertStrictType($result, 'array', 'maps_list');
assertValue(count($result), 2, 'maps count');
assertStrictType($result[0]['count'], 'integer', 'map1 count');

// 5. Test Deeply Nested Structure (Match Details)
echo "\nTest 5: Deeply Nested (Match Details)\n";
$input = [
    'info' => [
        'map_name' => 'obj_team2',
        'winner' => 'Allies'
    ]
];
$result = MohaaStatsSchemas::validate($input, 'match_details');

assertStrictType($result['info'], 'array', 'match info');
assertValue($result['info']['map_name'], 'obj_team2', 'map name');
assertValue($result['info']['winner'], 'Allies', 'winner');
// Check missing nested field default
assertValue($result['info']['match_id'], '', 'missing match_id default');


// Summary
echo "\n------------------------------------------------\n";
echo "Schema Tests Completed.\n";
echo "Passes: $passes\n";
echo "Failures: $failures\n";

if ($failures > 0) {
    exit(1);
}
exit(0);
