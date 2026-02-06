<?php

if (!defined('SMF'))
    die('No direct access...');

class MohaaStatsSchemas
{
    // Schema Definitions
    private static array $schemas = [
        // Global
        'global_stats' => [
            'total_kills' => ['type' => 'int', 'default' => 0],
            'total_players' => ['type' => 'int', 'default' => 0],
        ],
        'global_activity' => [
            'activity' => ['type' => 'array_of_activity_point', 'default' => []],
        ],
        'activity_point' => [
            'hour' => ['type' => 'int', 'default' => 0],
            'count' => ['type' => 'int', 'default' => 0],
        ],

        // Leaderboards
        'leaderboard' => [
            'total' => ['type' => 'int', 'default' => 0],
            'page' => ['type' => 'int', 'default' => 0],
            'players' => ['type' => 'array_of_player_summary', 'default' => []],
        ],
        'leaderboard_count' => [
            'total' => ['type' => 'int', 'default' => 0],
        ],
        'leaderboard_cards' => [
            'cards' => ['type' => 'array_of_leaderboard_card', 'default' => []],
        ],
        'leaderboard_card' => [
            'title' => ['type' => 'string', 'default' => ''],
            'player' => ['type' => 'string', 'default' => ''],
            'value' => ['type' => 'string', 'default' => ''],
            'guid' => ['type' => 'string', 'default' => ''],
        ],

        // Player
        'player_summary' => [
            'name' => ['type' => 'string', 'default' => 'Unknown'],
            'guid' => ['type' => 'string', 'default' => ''],
            'rank' => ['type' => 'int', 'default' => 0],
            'kills' => ['type' => 'int', 'default' => 0],
            'deaths' => ['type' => 'int', 'default' => 0],
            'kdr' => ['type' => 'float', 'default' => 0.0],
            'score' => ['type' => 'int', 'default' => 0],
        ],
        'player_stats' => [
            'name' => ['type' => 'string', 'default' => 'Unknown'],
            'guid' => ['type' => 'string', 'default' => ''],
            'kills' => ['type' => 'int', 'default' => 0],
            'deaths' => ['type' => 'int', 'default' => 0],
            'kdr' => ['type' => 'float', 'default' => 0.0],
            'accuracy' => ['type' => 'float', 'default' => 0.0],
            'headshots' => ['type' => 'int', 'default' => 0],
            'rounds' => ['type' => 'int', 'default' => 0],
            'wins' => ['type' => 'int', 'default' => 0],
            'losses' => ['type' => 'int', 'default' => 0],
            'time_played' => ['type' => 'int', 'default' => 0],
            'last_seen' => ['type' => 'string', 'default' => ''],
            'is_online' => ['type' => 'bool', 'default' => false],
            'is_active' => ['type' => 'bool', 'default' => false],
        ],
        'player_deep_stats' => [
            'combat' => ['type' => 'array', 'default' => []], // Structure dependent on API
            'movement' => ['type' => 'array', 'default' => []],
        ],
        'player_weapons' => [ // Root is array
            'type' => 'array_root',
            'item_schema' => 'weapon_stats'
        ],
        'player_matches' => [
            'total' => ['type' => 'int', 'default' => 0],
            'list' => ['type' => 'array_of_match_summary', 'default' => []],
        ],
        'player_achievements' => [ // Root is array
            'type' => 'array_root',
            'item_schema' => 'achievement'
        ],
        'player_performance' => [
            'spm' => ['type' => 'float', 'default' => 0.0],
            'kpm' => ['type' => 'float', 'default' => 0.0],
            'kd_ratio' => ['type' => 'float', 'default' => 0.0],
            'win_loss_ratio' => ['type' => 'float', 'default' => 0.0],
            'points' => ['type' => 'int', 'default' => 0],
            'rounds_played' => ['type' => 'int', 'default' => 0],
            'is_vip' => ['type' => 'bool', 'default' => false],
        ],
        'player_playstyle' => [
            'style' => ['type' => 'string', 'default' => 'Unknown'],
            'spm' => ['type' => 'float', 'default' => 0.0],
        ],

        // Matches
        'match_summary' => [
            'id' => ['type' => 'string', 'default' => ''],
            'map' => ['type' => 'string', 'default' => ''],
            'server' => ['type' => 'string', 'default' => ''],
            'time' => ['type' => 'string', 'default' => ''],
            'players' => ['type' => 'int', 'default' => 0],
        ],
        'matches_list' => [
            'total' => ['type' => 'int', 'default' => 0],
            'list' => ['type' => 'array_of_match_summary', 'default' => []],
        ],
        'match_count' => [
             'total' => ['type' => 'int', 'default' => 0],
        ],
        'match_details' => [
            'info' => ['type' => 'match_info', 'default' => []],
            'stats' => ['type' => 'match_stats', 'default' => []],
        ],
        'match_info' => [
            'id' => ['type' => 'string', 'default' => ''],
            'map_name' => ['type' => 'string', 'default' => ''],
            'server_name' => ['type' => 'string', 'default' => ''],
            'start_time' => ['type' => 'string', 'default' => ''],
            'end_time' => ['type' => 'string', 'default' => ''],
            'duration' => ['type' => 'int', 'default' => 0],
        ],
        'match_stats' => [
            'team1_score' => ['type' => 'int', 'default' => 0],
            'team2_score' => ['type' => 'int', 'default' => 0],
            // More fields as needed
        ],
        'live_matches' => [ // Root is array
            'type' => 'array_root',
            'item_schema' => 'live_match'
        ],
        'live_match' => [
            'id' => ['type' => 'string', 'default' => ''],
            'map' => ['type' => 'string', 'default' => ''],
            'server' => ['type' => 'string', 'default' => ''],
            'players_count' => ['type' => 'int', 'default' => 0],
        ],
        'match_heatmap' => [ // Root is array
            'type' => 'array_root',
            'item_schema' => 'heatmap_point'
        ],
        'heatmap_point' => [
            'x' => ['type' => 'float', 'default' => 0.0],
            'y' => ['type' => 'float', 'default' => 0.0],
            'count' => ['type' => 'int', 'default' => 0],
        ],

        // Maps
        'map_stats' => [ // This endpoint returns an array of maps usually, but API says `getMapStats`
             'type' => 'array_root',
             'item_schema' => 'map_summary'
        ],
        'map_summary' => [
            'id' => ['type' => 'string', 'default' => ''],
            'name' => ['type' => 'string', 'default' => ''],
            'matches' => ['type' => 'int', 'default' => 0],
            'popularity' => ['type' => 'float', 'default' => 0.0],
        ],
        'maps_list' => [ // Root is array of strings or simple objects? Mock says []
            'type' => 'array_root',
            'item_schema' => 'map_simple'
        ],
        'map_simple' => [
             'id' => ['type' => 'string', 'default' => ''],
             'name' => ['type' => 'string', 'default' => ''],
        ],
        'map_popularity' => [ // Root is array
            'type' => 'array_root',
            'item_schema' => 'map_summary' // Assuming similar structure
        ],
        'map_details' => [
            'id' => ['type' => 'string', 'default' => ''],
            'name' => ['type' => 'string', 'default' => ''],
            'total_matches' => ['type' => 'int', 'default' => 0],
            'average_duration' => ['type' => 'int', 'default' => 0],
        ],
        'map_leaderboard' => [
             'total' => ['type' => 'int', 'default' => 0],
             'players' => ['type' => 'array_of_player_summary', 'default' => []],
        ],

        // Weapons
        'weapons_list' => [ // Root is array
            'type' => 'array_root',
            'item_schema' => 'weapon_simple'
        ],
        'weapon_simple' => [
             'id' => ['type' => 'string', 'default' => ''],
             'name' => ['type' => 'string', 'default' => ''],
        ],
        'weapon_stats' => [
            'id' => ['type' => 'string', 'default' => ''],
            'name' => ['type' => 'string', 'default' => ''],
            'kills' => ['type' => 'int', 'default' => 0],
            'accuracy' => ['type' => 'float', 'default' => 0.0],
            'headshots' => ['type' => 'int', 'default' => 0],
        ],
        'weapon_leaderboard' => [
            'total' => ['type' => 'int', 'default' => 0],
            'players' => ['type' => 'array_of_player_summary', 'default' => []],
        ],

        // Auth
        'auth_init' => [
            'code' => ['type' => 'string', 'default' => ''],
            'expires_in' => ['type' => 'int', 'default' => 0],
        ],
        'auth_device' => [
            'user_code' => ['type' => 'string', 'default' => ''],
            'expires_in' => ['type' => 'int', 'default' => 0],
        ],
        'auth_history' => [
            'history' => ['type' => 'array_of_login_record', 'default' => []],
        ],
        'login_record' => [
            'attempt_at' => ['type' => 'string', 'default' => ''],
            'server_name' => ['type' => 'string', 'default' => ''],
            'player_ip' => ['type' => 'string', 'default' => ''],
            'success' => ['type' => 'bool', 'default' => false],
            'failure_reason' => ['type' => 'string', 'default' => ''],
        ],
        'auth_trusted_ips' => [
            'trusted_ips' => ['type' => 'array_of_trusted_ip', 'default' => []],
        ],
        'trusted_ip' => [
            'id' => ['type' => 'int', 'default' => 0],
            'ip_address' => ['type' => 'string', 'default' => ''],
            'last_used_at' => ['type' => 'string', 'default' => ''],
        ],
        'auth_pending_ips' => [
            'pending_ips' => ['type' => 'array_of_pending_ip', 'default' => []],
        ],
        'pending_ip' => [
            'id' => ['type' => 'int', 'default' => 0],
            'requested_at' => ['type' => 'string', 'default' => ''],
            'server_name' => ['type' => 'string', 'default' => ''],
            'ip_address' => ['type' => 'string', 'default' => ''],
        ],
        'auth_success' => [
            'success' => ['type' => 'bool', 'default' => false],
        ],

        // Achievements
        'achievements_list' => [ // Root array
            'type' => 'array_root',
            'item_schema' => 'achievement'
        ],
        'achievement' => [
            'id' => ['type' => 'int', 'default' => 0],
            'name' => ['type' => 'string', 'default' => ''],
            'description' => ['type' => 'string', 'default' => ''],
            'points' => ['type' => 'int', 'default' => 0],
            'date_awarded' => ['type' => 'string', 'default' => ''],
        ],
        'achievements_recent' => [
             'achievements' => ['type' => 'array_of_achievement', 'default' => []],
        ],
        'achievements_leaderboard' => [
             'players' => ['type' => 'array_of_player_summary', 'default' => []],
        ],

        // General
        'success_response' => [
            'success' => ['type' => 'bool', 'default' => false],
        ],
    ];

    /**
     * Validate and cast data against a schema.
     *
     * @param mixed $data The input data (usually from json_decode)
     * @param string|array $schema The schema key or definition array
     * @return mixed strictly typed data or default structure
     */
    public static function validate($data, $schema)
    {
        // Resolve schema if it's a string key
        if (is_string($schema)) {
            // Check for array_of_ prefix
            if (strpos($schema, 'array_of_') === 0) {
                $itemSchema = substr($schema, 9);
                if (!is_array($data)) {
                    return [];
                }
                $result = [];
                foreach ($data as $item) {
                    $result[] = self::validate($item, $itemSchema);
                }
                return $result;
            }

            if (!isset(self::$schemas[$schema])) {
                // Fallback: if schema not found, return data as is or empty array?
                // For safety, log error (if possible) and return empty array to prevent crashes
                return [];
            }
            $schemaDef = self::$schemas[$schema];
        } else {
            $schemaDef = $schema;
        }

        // Handle Root Array types (where the API returns [{},{}] instead of {"items":[]})
        if (isset($schemaDef['type']) && $schemaDef['type'] === 'array_root') {
            if (!is_array($data)) {
                return [];
            }
            $result = [];
            $itemSchema = $schemaDef['item_schema'];
            foreach ($data as $item) {
                $result[] = self::validate($item, $itemSchema);
            }
            return $result;
        }

        // Validate Object/Associative Array
        if (!is_array($data)) {
            // If data is null or not an array, use defaults from schema
            $data = [];
        }

        $result = [];
        foreach ($schemaDef as $field => $rules) {
            // Skip meta fields like 'type' if mixed in (though usually we separate them)
            if ($field === 'type' || $field === 'item_schema') continue;

            $value = $data[$field] ?? $rules['default'];
            $type = $rules['type'];

            // Recursive validation for nested objects
            if (is_array($type) || (is_string($type) && isset(self::$schemas[$type]))) {
                 $result[$field] = self::validate($value, $type);
                 continue;
            }
            if (strpos($type, 'array_of_') === 0) {
                 $result[$field] = self::validate($value, $type);
                 continue;
            }

            // Strict Casting
            switch ($type) {
                case 'int':
                    $result[$field] = (int)$value;
                    break;
                case 'float':
                    $result[$field] = (float)$value;
                    break;
                case 'string':
                    $result[$field] = (string)$value;
                    break;
                case 'bool':
                    $result[$field] = (bool)$value;
                    break;
                case 'array':
                    $result[$field] = is_array($value) ? $value : [];
                    break;
                default:
                    $result[$field] = $value; // Should not happen if schema is correct
            }
        }

        return $result;
    }

    public static function getSchema(string $key): array {
        return self::$schemas[$key] ?? [];
    }
}
