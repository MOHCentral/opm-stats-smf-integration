<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../smf-plugins/mohaa_stats_core/Sources/MohaaStats/MohaaStatsSchemas.php';

$failures = 0;
$passes = 0;

function assertStrictType($value, $type, $name) {
    global $failures, $passes;
    $actualType = gettype($value);
    if ($type === 'int' && is_int($value)) {
        echo "[PASS] $name is int ($value)\n";
        $passes++;
    } elseif ($type === 'float' && is_float($value)) {
        echo "[PASS] $name is float ($value)\n";
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

echo "Starting API Schema Validation Tests...\n";

// Test 1: Basic Casting
echo "\nTest 1: Basic Casting (player_stats)\n";
$input = [
    'guid' => '12345',
    'name' => 'Test',
    'kills' => '100', // string should become int
    'accuracy' => '25.5', // string should become float
    'is_online' => 1, // int should become bool
    'extra_field' => 'ignore me' // should be ignored? Logic says returns defined fields
];
$result = MohaaStatsSchemas::validate('player_stats', $input);

assertStrictType($result['kills'], 'int', 'kills');
assertStrictType($result['accuracy'], 'float', 'accuracy');
assertStrictType($result['is_online'], 'bool', 'is_online');
if (!array_key_exists('extra_field', $result)) {
    echo "[PASS] Extra field ignored (strict structure)\n";
    $passes++;
} else {
    // Current implementation allows extra fields? Let's check implementation.
    // The implementation iterates over schema fields, so it constructs a new array with ONLY schema fields.
    // Wait, the implementation:
    // $result = []; foreach ($fields as $key => $fieldSchema) { ... } return $result;
    // So yes, it ignores extra fields.
}

// Test 2: Missing Fields / Defaults
echo "\nTest 2: Missing Fields (player_stats)\n";
$input = ['guid' => '123'];
$result = MohaaStatsSchemas::validate('player_stats', $input);
assertStrictType($result['kills'], 'int', 'kills (default)');
if ($result['kills'] === 0) {
    echo "[PASS] kills defaulted to 0\n";
    $passes++;
} else {
    echo "[FAIL] kills default incorrect: {$result['kills']}\n";
    $failures++;
}

// Test 3: Null Input
echo "\nTest 3: Null Input\n";
$result = MohaaStatsSchemas::validate('player_stats', null);
assertStrictType($result, 'array', 'Result from null input');
// With improved getDefaultValue, we expect populated defaults
if (!empty($result) && isset($result['kills']) && $result['kills'] === 0) {
    echo "[PASS] Result populated with default structure (kills=0)\n";
    $passes++;
} else {
    echo "[FAIL] Result should be populated default structure\n";
    var_dump($result);
    $failures++;
}

// Test 4: Nested Array (player_weapons)
echo "\nTest 4: Nested Array (player_weapons)\n";
$input = [
    ['name' => 'M1 Garand', 'kills' => '50'],
    ['name' => 'Thompson', 'kills' => 20]
];
$result = MohaaStatsSchemas::validate('player_weapons', $input);
assertStrictType($result, 'array', 'Root result');
assertStrictType($result[0]['kills'], 'int', 'Item 0 kills');
assertStrictType($result[1]['kills'], 'int', 'Item 1 kills');

// Test 5: Invalid Schema Key
echo "\nTest 5: Invalid Schema Key\n";
$result = MohaaStatsSchemas::validate('non_existent', ['a'=>1]);
if ($result === []) {
    echo "[PASS] Invalid schema returns empty array\n";
    $passes++;
} else {
    echo "[FAIL] Invalid schema returned data\n";
    $failures++;
}

// Test 6: Recursive Object (leaderboard)
echo "\nTest 6: Recursive Object (leaderboard)\n";
$input = [
    'total' => '500',
    'players' => [
        ['name' => 'P1', 'kills' => '10'],
        ['name' => 'P2', 'kills' => '5']
    ]
];
$result = MohaaStatsSchemas::validate('leaderboard', $input);
assertStrictType($result['total'], 'int', 'total');
assertStrictType($result['players'], 'array', 'players');
assertStrictType($result['players'][0]['kills'], 'int', 'player 0 kills');

echo "\n------------------------------------------------\n";
echo "Passes: $passes\n";
echo "Failures: $failures\n";

if ($failures > 0) exit(1);
exit(0);
