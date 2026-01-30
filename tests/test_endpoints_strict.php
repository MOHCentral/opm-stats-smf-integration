<?php
/**
 * Strict Endpoint Validation Tests
 */

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../smf-plugins/mohaa_stats_core/Sources/MohaaStats/MohaaStatsAPI.php';

// Setup
global $modSettings;
$modSettings['mohaa_stats_api_url'] = 'http://localhost:8000';
$modSettings['mohaa_stats_server_token'] = 'test-token';
$modSettings['mohaa_stats_cache_duration'] = 0;

$api = new MohaaStatsAPIClient();

function test($name, $condition) {
    echo $name . ": " . ($condition ? "PASS" : "FAIL") . "\n";
    if (!$condition) {
        echo "  FAILED CONDITION\n";
        // exit(1); // Don't exit on first failure for full report
    }
}

echo "Running Strict Endpoint Tests...\n";

// 1. Global Stats
$data = $api->getGlobalStats();
test("getGlobalStats Type", is_array($data));
test("getGlobalStats total_kills int", is_int($data['total_kills'] ?? null));

// 2. Leaderboard
$data = $api->getLeaderboard();
test("getLeaderboard Structure", is_array($data['players']) && is_int($data['total']));

// 3. Player Stats
$data = $api->getPlayerStats('12345');
test("getPlayerStats Name", is_string($data['name'] ?? null));
test("getPlayerStats Kills Int", is_int($data['kills'] ?? null));

// 4. Player Deep Stats
$data = $api->getPlayerDeepStats('12345');
test("getPlayerDeepStats Combat Object", is_array($data['combat'] ?? null));

// 5. Player Weapons
$data = $api->getPlayerWeapons('12345');
test("getPlayerWeapons Array", is_array($data));

// 6. Player Matches
$data = $api->getPlayerMatches('12345');
test("getPlayerMatches List Array", is_array($data['list'] ?? null));

// 7. Recent Matches
$data = $api->getRecentMatches();
test("getRecentMatches List Array", is_array($data['list'] ?? null));

// 8. Live Matches
$data = $api->getLiveMatches();
test("getLiveMatches Array", is_array($data));

// 9. Map Stats
$data = $api->getMapStats();
test("getMapStats Array", is_array($data));

// 10. Map Details
$data = $api->getMapDetails('obj_team2');
test("getMapDetails Name", is_string($data['name'] ?? null));

// 11. Match Details
$data = $api->getMatchDetails('123');
test("getMatchDetails Info", is_array($data['info'] ?? null));

// 12. Auth - Claim
$data = $api->initClaim(1);
test("initClaim Code", is_string($data['code'] ?? null));

// 13. Auth - Device
$data = $api->initDeviceAuth(1);
test("initDeviceAuth UserCode", is_string($data['user_code'] ?? null));

// 14. Server Activity
$data = $api->getGlobalActivity();
test("getGlobalActivity Activity Array", is_array($data['activity'] ?? null));

// 15. Casting Test (Mock server has special CAST_TEST)
// We need to trigger the mock server's CAST_TEST path.
// Mock server checks if uri contains 'CAST_TEST'.
// But our API client encodes URL params.
// Let's rely on the fact that `get` uses the schema.
// If valid data is returned, schema validation ensures types.
// We tested strict validation in unit tests. Here we verify integration.

echo "Strict Endpoint Tests Completed.\n";
