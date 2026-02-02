<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../smf-plugins/mohaa_stats_core/Sources/MohaaStats/MohaaStats.php';

// Mock SMF Global Variables
$context = [];
$txt = ['mohaa_stats_disabled' => 'Stats Disabled'];
$scripturl = 'http://localhost/index.php';
$settings = [];
$user_info = ['is_guest' => false, 'id' => 1];

// Capture Output
function capture_output($callback) {
    ob_start();
    try {
        $callback();
    } catch (Throwable $e) {
        echo "EXCEPTION: " . $e->getMessage();
    }
    return ob_get_clean();
}

// Error Handler to catch Notices/Warnings
function errorHandler($errno, $errstr, $errfile, $errline) {
    echo "[ERROR][$errno] $errstr in $errfile:$errline\n";
    // Don't kill script, just log
    return true;
}
set_error_handler("errorHandler");

echo "Starting Integration Logic Tests...\n";

// Test 1: Leaderboards with API Failure (500)
echo "\nTesting MohaaStats_Leaderboards with API Failure...\n";
$_GET['sa'] = 'leaderboards';
$_GET['stat'] = 'kills';
// We need to simulate API returning null.
// Since we can't easily inject the API client instance into MohaaStats functions (they instantiate new MohaaStatsAPIClient),
// we will rely on the Mock Server responding with errors for specific inputs,
// OR we can rely on the fact that we can't easily force getLeaderboard to return null via arguments unless we change the URL in the client.

// However, MohaaStatsAPIClient uses $modSettings['mohaa_stats_api_url'].
// We can point it to a non-existent URL to force connection error -> null.
$originalUrl = $modSettings['mohaa_stats_api_url'];
$modSettings['mohaa_stats_api_url'] = 'http://localhost:9999'; // Port closed

capture_output(function() {
    MohaaStats_Leaderboards();
});

// Restore URL
$modSettings['mohaa_stats_api_url'] = $originalUrl;

// Test 2: Leaderboard Count with Failure
// This is called inside MohaaStatsAPIClient methods.
// We can test the API client methods directly for the ones that had bugs (returning int)
echo "\nTesting API Client Helper Methods with Failure...\n";
$api = new MohaaStatsAPIClient();
// Force failure by temporarily breaking URL
$property = new ReflectionProperty($api, 'baseUrl');
$property->setAccessible(true);
$property->setValue($api, 'http://localhost:9999');

try {
    $count = $api->getLeaderboardCount('kills');
    echo "getLeaderboardCount result: $count\n";
} catch (Throwable $e) {
    echo "getLeaderboardCount Exception: " . $e->getMessage() . "\n";
}

try {
    $count = $api->getMatchCount();
    echo "getMatchCount result: $count\n";
} catch (Throwable $e) {
    echo "getMatchCount Exception: " . $e->getMessage() . "\n";
}

// Test 3: Weapon Leaderboard with API Failure
echo "\nTesting MohaaStats_WeaponLeaderboard with API Failure...\n";
$_GET['sa'] = 'weapons';
$_GET['weapon'] = 'garand';
$modSettings['mohaa_stats_api_url'] = 'http://localhost:9999';

capture_output(function() {
    MohaaStats_WeaponLeaderboard();
});

global $context;
if ($context['mohaa_weapon_data'] === null) {
    echo "FAIL: mohaa_weapon_data is NULL (Potential Template Crash)\n";
} elseif (is_array($context['mohaa_weapon_data'])) {
    echo "PASS: mohaa_weapon_data is array (safe)\n";
} else {
    echo "FAIL: mohaa_weapon_data is " . gettype($context['mohaa_weapon_data']) . "\n";
}

// Test 4: Dashboard with API Failure (MainPage)
echo "\nTesting MohaaStats_MainPage with API Failure...\n";
$_GET['sa'] = 'main';
$modSettings['mohaa_stats_api_url'] = 'http://localhost:9999';

capture_output(function() {
    MohaaStats_MainPage();
});

if (is_array($context['mohaa_stats']['global'])) {
    echo "PASS: mohaa_stats['global'] is array\n";
} else {
    echo "FAIL: mohaa_stats['global'] is " . gettype($context['mohaa_stats']['global']) . "\n";
}

// Test 5: Leaderboard with API Failure (Hardened Check)
echo "\nTesting MohaaStats_Leaderboards (Hardened) with API Failure...\n";
$_GET['sa'] = 'leaderboards';
unset($_GET['stat']); // Dashboard mode or default
// Force specific stat to trigger getLeaderboard
$_GET['stat'] = 'kills';

capture_output(function() {
    MohaaStats_Leaderboards();
});

if (is_array($context['mohaa_leaderboard']['players'])) {
    echo "PASS: mohaa_leaderboard['players'] is array\n";
} else {
    echo "FAIL: mohaa_leaderboard['players'] is " . gettype($context['mohaa_leaderboard']['players']) . "\n";
}

echo "\nTests Completed.\n";
