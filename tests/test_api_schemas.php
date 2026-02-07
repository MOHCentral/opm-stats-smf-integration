<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../smf-plugins/mohaa_stats_core/Sources/MohaaStats/MohaaStatsSchemas.php';

echo "Testing MohaaStatsSchemas...\n";

function assertStrictType($value, $type, $name) {
    $actualType = gettype($value);
    if ($actualType !== $type) {
        if ($type === 'float' && ($actualType === 'double')) {
             // gettype returns double for float
        } else {
            echo "FAIL: $name is $actualType, expected $type\n";
            return;
        }
    }
    echo "PASS: $name is $type\n";
}

// Test 1: Basic Int Validation
echo "\n--- Test 1: Basic Int ---\n";
$schema = ['val' => ['type' => 'int', 'default' => 0]];
$input = ['val' => "123"];
$result = MohaaStatsSchemas::validate($input, $schema);
assertStrictType($result['val'], 'integer', 'Int casting');
if ($result['val'] !== 123) echo "FAIL: Value mismatch (expected 123, got {$result['val']})\n";

// Test 2: Missing Field Default
echo "\n--- Test 2: Defaults ---\n";
$schema = ['val' => ['type' => 'int', 'default' => 99]];
$input = [];
$result = MohaaStatsSchemas::validate($input, $schema);
if ($result['val'] === 99) echo "PASS: Default value used\n";
else echo "FAIL: Default value not used (got {$result['val']})\n";

// Test 3: Nested Object
echo "\n--- Test 3: Nested Object ---\n";
$schema = [
    'user' => [
        'name' => ['type' => 'string', 'default' => 'Anon'],
        'age' => ['type' => 'int', 'default' => 0]
    ]
];
$input = ['user' => ['name' => 'John']]; // Missing age
$result = MohaaStatsSchemas::validate($input, $schema);
if ($result['user']['name'] === 'John' && $result['user']['age'] === 0) echo "PASS: Nested partial data filled\n";
else echo "FAIL: Nested data incorrect\n";

// Test 4: Array of Objects
echo "\n--- Test 4: Array of Objects ---\n";
$schema = ['players' => ['type' => 'array_of_PLAYER_BASIC']];
$input = ['players' => [
    ['name' => 'P1', 'kills' => '10'],
    ['name' => 'P2'] // Missing kills
]];
$result = MohaaStatsSchemas::validate($input, $schema);

if (count($result['players']) === 2) echo "PASS: Array count correct\n";
else echo "FAIL: Array count wrong\n";

if ($result['players'][0]['kills'] === 10) echo "PASS: P1 kills casted\n";
else echo "FAIL: P1 kills wrong\n";

if ($result['players'][1]['kills'] === 0) echo "PASS: P2 kills default\n";
else echo "FAIL: P2 kills wrong\n";


// Test 5: Null Input
echo "\n--- Test 5: Null Input ---\n";
$result = MohaaStatsSchemas::validate(null, MohaaStatsSchemas::GLOBAL_STATS);
if ($result['total_kills'] === 0) echo "PASS: Null input generates default structure\n";
else echo "FAIL: Null input handling failed\n";

// Test 6: Extra Fields
echo "\n--- Test 6: Extra Fields ---\n";
$schema = ['val' => ['type' => 'int', 'default' => 0]];
$input = ['val' => 1, 'extra' => 'hacker'];
$result = MohaaStatsSchemas::validate($input, $schema);
if (!isset($result['extra'])) echo "PASS: Extra fields stripped\n";
else echo "FAIL: Extra fields preserved\n";

echo "\nDone.\n";
