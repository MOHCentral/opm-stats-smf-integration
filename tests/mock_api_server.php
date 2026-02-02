<?php
declare(strict_types=1);
// Simple Mock API Server
header('Content-Type: application/json');

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Chaos / Error Injection
if (strpos($uri, 'ERROR_500') !== false) {
    http_response_code(500);
    echo json_encode(['error' => 'Simulated Internal Server Error']);
    exit;
}
if (strpos($uri, 'ERROR_404') !== false) {
    http_response_code(404);
    echo json_encode(['error' => 'Simulated Not Found']);
    exit;
}
if (strpos($uri, 'ERROR_JSON') !== false) {
    http_response_code(200);
    echo "This is not JSON { [ } ]";
    exit;
}

// Helper to send response
function response($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

// Router

// Global Stats
if (strpos($uri, '/api/v1/stats/global/activity') !== false) {
    response(['activity' => [['hour' => 12, 'count' => 50]]]);
}
if (strpos($uri, '/api/v1/stats/leaderboard/cards') !== false) {
    response(['cards' => [['title' => 'Top Killer', 'player' => 'TestPlayer']]]);
}
if (strpos($uri, '/api/v1/stats/leaderboard/global') !== false) {
    response(['players' => [], 'total' => 0, 'page' => 0]);
}
if (strpos($uri, '/api/v1/stats/global') !== false) {
    response(['total_kills' => 1000, 'total_players' => 50]);
}

// Player Stats
if (strpos($uri, '/api/v1/stats/player/') !== false) {
    if (strpos($uri, '/deep') !== false) {
        response(['combat' => [], 'movement' => []]);
    }
    if (strpos($uri, '/weapons') !== false) {
        response([]); // Array of weapons
    }
    if (strpos($uri, '/matches') !== false) {
        response(['list' => [], 'total' => 0]);
    }
    if (strpos($uri, '/playstyle') !== false) {
        response(['style' => 'Rusher', 'spm' => 150.5]);
    }
    if (strpos($uri, '/performance') !== false) {
        // Return string values to test casting
        response([
            'spm' => '245.5',
            'kpm' => '1.2',
            'kd_ratio' => '2.5',
            'win_loss_ratio' => '1.1',
            'points' => '5000',
            'rounds_played' => '100',
            'is_vip' => 1 // Test boolean casting from int/string
        ]);
    }

    // Casting Test Case
    if (strpos($uri, 'CAST_TEST') !== false) {
        response([
            'name' => 'CastingTester',
            'kills' => '999',
            'accuracy' => '25.5',
            'is_online' => '1'
        ]);
    }

    // Base player info
    response(['name' => 'TestPlayer', 'guid' => '12345', 'kills' => 100]);
}

// Achievements
if (strpos($uri, '/api/v1/achievements/player/') !== false) {
    response([]);
}
if (strpos($uri, '/api/v1/achievements/leaderboard') !== false) {
    response(['players' => []]);
}
if (strpos($uri, '/api/v1/achievements/recent') !== false) {
    response(['achievements' => []]);
}
if (strpos($uri, '/api/v1/achievements/') !== false) {
    // Single achievement or list
    response(['id' => 1, 'name' => 'First Blood']);
}
if (strpos($uri, '/api/v1/achievements') !== false) {
    response([]);
}

// Matches
if (strpos($uri, '/api/v1/stats/matches') !== false) {
    response(['list' => [], 'total' => 0]);
}
if (strpos($uri, '/api/v1/stats/match/') !== false) {
    if (strpos($uri, '/heatmap') !== false) {
        response([]);
    }
    // Advanced or basic
    if (strpos($uri, '/advanced') !== false) {
        response(['info' => ['map_name' => 'obj_team2'], 'stats' => []]);
    }
    response(['info' => ['map_name' => 'obj_team2']]);
}
if (strpos($uri, '/api/v1/stats/live/matches') !== false) {
    response([]);
}

// Maps
if (strpos($uri, '/api/v1/stats/maps/popularity') !== false) {
    response([]);
}
if (strpos($uri, '/api/v1/stats/maps/list') !== false) {
    response([]);
}
if (strpos($uri, '/api/v1/stats/maps') !== false) {
    response([]);
}
if (strpos($uri, '/api/v1/stats/map/') !== false) {
    if (strpos($uri, '/leaderboard') !== false) {
        response(['players' => [], 'total' => 0]);
    }
    if (strpos($uri, '/heatmap') !== false) {
        response([]);
    }
    response(['name' => 'Southern France', 'id' => 'obj_team2']);
}

// Weapons
if (strpos($uri, '/api/v1/stats/weapons/list') !== false) {
    response([]);
}
if (strpos($uri, '/api/v1/stats/weapon/') !== false) {
    if (strpos($uri, '/leaderboard') !== false) {
        response(['players' => [], 'total' => 0]);
    }
    // Return string for kills to test casting
    response(['name' => 'M1 Garand', 'kills' => '100']);
}

// Auth
if (strpos($uri, '/api/v1/auth/claim/init') !== false) {
    response(['code' => '123456', 'expires_in' => 600]);
}
if (strpos($uri, '/api/v1/auth/device') !== false) {
    response(['user_code' => 'ABC-DEF', 'expires_in' => 600]);
}

// Auth New Endpoints
if (strpos($uri, '/api/v1/auth/history/') !== false) {
    response(['history' => [
        [
            'attempt_at' => '2023-01-01T12:00:00Z',
            'server_name' => 'MOHAA Server 1',
            'player_ip' => '192.168.1.1',
            'success' => true,
            'failure_reason' => null
        ],
        [
            'attempt_at' => '2023-01-02T12:00:00Z',
            'server_name' => 'MOHAA Server 2',
            'player_ip' => '192.168.1.2',
            'success' => false,
            'failure_reason' => 'Invalid Token'
        ]
    ]]);
}
if (strpos($uri, '/api/v1/auth/trusted-ips/') !== false) {
    response(['trusted_ips' => [
        [
            'id' => 101,
            'ip_address' => '10.0.0.1',
            'last_used_at' => '2023-01-05T10:00:00Z'
        ]
    ]]);
}
if (strpos($uri, '/api/v1/auth/pending-ips/') !== false) {
    response(['pending_ips' => [
        [
            'id' => 202,
            'requested_at' => '2023-01-06T09:00:00Z',
            'server_name' => 'MOHAA Server 3',
            'ip_address' => '10.0.0.5'
        ]
    ]]);
}
if (strpos($uri, '/api/v1/auth/trusted-ip/') !== false && $method === 'DELETE') {
    response(['success' => true]);
}
if (strpos($uri, '/api/v1/auth/pending-ip/resolve') !== false && $method === 'POST') {
    response(['success' => true]);
}

response(['error' => 'Not Found'], 404);
