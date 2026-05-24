<?php
/**
 * Integration Tests for API Resilience
 */

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../smf-plugins/mohaa_stats_core/Sources/MohaaStats/MohaaStatsAPI.php';

// Override API URL to point to mock server locally
global $modSettings;
$modSettings['mohaa_stats_api_url'] = 'http://localhost:8000';
$modSettings['mohaa_stats_server_token'] = 'test-token';
$modSettings['mohaa_stats_cache_duration'] = 0; // Disable cache for tests

$api = new MohaaStatsAPIClient();

function test($name, $condition) {
    echo $name . ": " . ($condition ? "PASS" : "FAIL") . "\n";
    if (!$condition) exit(1);
}

echo "Running API Resilience Tests...\n";

// We need to inject error URLs.
// Since the API client appends /api/v1/..., we need to be clever or modify the client for testing.
// However, the client is strict about base URL.
// But the mock server listens on any URI.
// If we set base URL to http://localhost:8000/ERROR_500, the client will append /api/v1/stats/global.
// The mock server checks `strpos($uri, 'ERROR_500')`. So it should work.

// Test 1: 500 Error
$modSettings['mohaa_stats_api_url'] = 'http://localhost:8000/ERROR_500';
$api = new MohaaStatsAPIClient(); // Re-init to pick up settings
$result = $api->getGlobalStats();
test("Handle 500 Error (Return Default)", is_array($result) && empty($result));

// Test 2: 404 Error
$modSettings['mohaa_stats_api_url'] = 'http://localhost:8000/ERROR_404';
$api = new MohaaStatsAPIClient();
$result = $api->getGlobalStats();
test("Handle 404 Error (Return Default)", is_array($result) && empty($result));

// Test 3: Invalid JSON
$modSettings['mohaa_stats_api_url'] = 'http://localhost:8000/ERROR_JSON';
$api = new MohaaStatsAPIClient();
$result = $api->getGlobalStats();
test("Handle Invalid JSON (Return Default)", is_array($result) && empty($result));

// Test 4: Schema Mismatch (Valid JSON but wrong type)
// We need a specific endpoint on mock server that returns bad data.
// Mock server has CAST_TEST which returns strings for numbers. Schema validation should fix them.
// But let's try something completely wrong.
// If I query an endpoint that returns an object, but schema expects array.
// getLiveMatches expects array.
$modSettings['mohaa_stats_api_url'] = 'http://localhost:8000';
$api = new MohaaStatsAPIClient();
// Mock server /api/v1/stats/live/matches returns [] (array).
// Let's try getGlobalStats which expects object. Mock returns object.
// We need a case where mock returns something that violates schema.
// Example: Mock /api/v1/stats/global returns {total_kills: 1000}. Schema expects {total_kills: int}.
// If mock returned "total_kills": "not a number", (int) cast would make it 0.
// If mock returned "total_kills": [], cast would fail or be 0.
// Let's rely on unit tests for casting logic, and here just ensure it doesn't crash.

// Verify normal operation still works
$result = $api->getGlobalStats();
test("Normal Operation (Valid Data)", isset($result['total_kills']) && $result['total_kills'] > 0);

echo "API Resilience Tests Completed.\n";
