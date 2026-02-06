<?php
/**
 * Unit Tests for Schema Validation
 */

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../smf-plugins/mohaa_stats_core/Sources/MohaaStats/MohaaStatsSchemas.php';

function test($name, $condition) {
    echo $name . ": " . ($condition ? "PASS" : "FAIL") . "\n";
    if (!$condition) exit(1);
}

echo "Running Schema Validation Tests...\n";

// Test 1: Basic Type Casting
$schema = ['type' => 'int'];
test("Cast String to Int", MohaaStatsSchemas::validate("123", $schema) === 123);
test("Cast Float to Int", MohaaStatsSchemas::validate(12.5, $schema) === 12);
test("Default for Null Int", MohaaStatsSchemas::validate(null, $schema) === 0);

$schema = ['type' => 'float'];
test("Cast String to Float", MohaaStatsSchemas::validate("12.5", $schema) === 12.5);

$schema = ['type' => 'bool'];
test("Cast Int to Bool", MohaaStatsSchemas::validate(1, $schema) === true);
test("Cast String to Bool", MohaaStatsSchemas::validate("true", $schema) === true);

// Test 2: Object Validation
$schema = [
    'type' => 'object',
    'properties' => [
        'id' => ['type' => 'int'],
        'name' => ['type' => 'string'],
        'optional' => ['type' => 'int', 'required' => false]
    ]
];

$data = ['id' => '50', 'name' => 'Test', 'extra' => 'should go'];
$result = MohaaStatsSchemas::validate($data, $schema);

test("Object Validation - Valid Field", $result['id'] === 50);
test("Object Validation - Valid String", $result['name'] === 'Test');
test("Object Validation - Strip Extra", !isset($result['extra']));
test("Object Validation - Optional Missing", !isset($result['optional']));

// Test 3: Object Validation - Missing Required
$data = ['name' => 'Test'];
$result = MohaaStatsSchemas::validate($data, $schema);
test("Object Validation - Missing Required Default", $result['id'] === 0);

// Test 4: Array Validation
$schema = [
    'type' => 'array',
    'items' => [
        'type' => 'object',
        'properties' => ['val' => ['type' => 'int']]
    ]
];

$data = [['val' => '1'], ['val' => 2]];
$result = MohaaStatsSchemas::validate($data, $schema);

test("Array Validation - Item 1", $result[0]['val'] === 1);
test("Array Validation - Item 2", $result[1]['val'] === 2);

// Test 5: Invalid Input for Array
$result = MohaaStatsSchemas::validate("not an array", $schema);
test("Invalid Array Input", $result === []);

echo "Schema Validation Tests Completed.\n";
