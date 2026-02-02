<?php
declare(strict_types=1);
/**
 * Debug API Connection Test
 * Run: php debug_api_test.php
 */

echo "=== MOHAA Stats API Connection Test ===\n\n";

$apiUrl = 'http://localhost:8080';

$endpoints = [
    '/health' => 'Health Check',
    '/api/v1/stats/global' => 'Global Stats',
    '/api/v1/stats/leaderboard' => 'Leaderboard',
    '/api/v1/stats/player/war-room-test-001' => 'Player Stats',
    '/api/v1/stats/weapons' => 'Weapons',
    '/api/v1/stats/maps' => 'Maps',
    '/api/v1/stats/gametypes' => 'Game Types',
    '/api/v1/servers/' => 'Servers',
    '/api/v1/stats/teams/performance' => 'Teams',
    '/api/v1/stats/leaderboard/cards' => 'Leaderboard Cards',
];

$passed = 0;
$failed = 0;

foreach ($endpoints as $endpoint => $name) {
    $url = $apiUrl . $endpoint;
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_CONNECTTIMEOUT => 2,
        CURLOPT_HTTPHEADER => ['Accept: application/json'],
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($httpCode === 200 && $response !== false) {
        $data = json_decode($response, true);
        $preview = substr($response, 0, 100);
        echo "✅ $name ($endpoint)\n";
        echo "   HTTP $httpCode - " . strlen($response) . " bytes\n";
        echo "   Preview: " . $preview . "...\n\n";
        $passed++;
    } else {
        echo "❌ $name ($endpoint)\n";
        echo "   HTTP $httpCode - Error: $error\n";
        echo "   Response: " . substr($response, 0, 200) . "\n\n";
        $failed++;
    }
}

echo "=== SUMMARY ===\n";
echo "Passed: $passed / " . count($endpoints) . "\n";
echo "Failed: $failed / " . count($endpoints) . "\n";

if ($failed === 0) {
    echo "\n✅ All API endpoints are accessible from PHP!\n";
} else {
    echo "\n⚠️  Some endpoints failed. Check API server.\n";
}
