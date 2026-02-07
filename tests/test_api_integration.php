<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../smf-plugins/mohaa_stats_core/Sources/MohaaStats/MohaaStatsAPI.php';

$api = new MohaaStatsAPIClient();

echo "Testing API Methods...\n";

// Test 1: getGlobalStats (Should pass)
$stats = $api->getGlobalStats();
if ($stats && isset($stats['total_kills'])) {
    echo "PASS: getGlobalStats\n";
} else {
    echo "FAIL: getGlobalStats\n";
}

// Test 2: New Methods
$weaponStats = $api->getWeaponLeaderboard('garand');
if (is_array($weaponStats)) {
    echo "PASS: getWeaponLeaderboard\n";
} else {
    echo "FAIL: getWeaponLeaderboard\n";
}

$mapStats = $api->getMapLeaderboard('obj_team2');
if (is_array($mapStats)) {
    echo "PASS: getMapLeaderboard\n";
} else {
    echo "FAIL: getMapLeaderboard\n";
}

$heatmap = $api->getMatchHeatmap('123', 'kills');
if (is_array($heatmap)) {
    echo "PASS: getMatchHeatmap\n";
} else {
    echo "FAIL: getMatchHeatmap\n";
}

// Test 3: Type Casting
// getWeaponStats returns 'kills'. Mock returns it as string "100". API Client should cast to int.
$wStats = $api->getWeaponStats('garand');
if ($wStats && isset($wStats['kills'])) {
    if (is_int($wStats['kills'])) {
        echo "PASS: Weapon kills is int (" . $wStats['kills'] . ")\n";
    } else {
        echo "FAIL: Weapon kills is " . gettype($wStats['kills']) . " (Expected int)\n";
    }
} else {
    echo "FAIL: getWeaponStats returned invalid data\n";
}

echo "Done.\n";

// Test 4: Resilience
echo "\n--- Test 4: Resilience ---\n";
$match = $api->getMatchDetails('ERROR_404');
if ($match !== null && is_array($match) && isset($match['info']['map_name'])) {
    echo "PASS: Resilience (404 returns default structure)\n";
    if ($match['info']['map_name'] === 'Unknown') echo "PASS: Default value correct\n";
    else echo "FAIL: Default value incorrect (got {$match['info']['map_name']})\n";
} else {
    echo "FAIL: Resilience (returned null or invalid)\n";
    var_dump($match);
}
