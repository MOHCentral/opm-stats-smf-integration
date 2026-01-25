<?php
// Simple Mock API Server
header('Content-Type: application/json');

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Helper to send response
function response($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

// Router
if (strpos($uri, '/api/v1/stats/global/activity') !== false) {
    response(['activity' => []]);
}
if (strpos($uri, '/api/v1/stats/global') !== false) {
    response(['total_kills' => 1000, 'total_players' => 50]);
}
if (strpos($uri, '/api/v1/stats/leaderboard/global') !== false) {
    response(['players' => [], 'total' => 0, 'page' => 0]);
}
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
        response([]);
    }
    // Base player info
    response(['name' => 'TestPlayer', 'guid' => '12345']);
}
if (strpos($uri, '/api/v1/achievements/player/') !== false) {
    response([]);
}
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
if (strpos($uri, '/api/v1/auth/claim/init') !== false) {
    response(['code' => '123456', 'expires_in' => 600]);
}
if (strpos($uri, '/api/v1/auth/device') !== false) {
    response(['user_code' => 'ABC-DEF', 'expires_in' => 600]);
}
if (strpos($uri, '/api/v1/achievements') !== false) {
    response([]);
}

response(['error' => 'Not Found'], 404);
