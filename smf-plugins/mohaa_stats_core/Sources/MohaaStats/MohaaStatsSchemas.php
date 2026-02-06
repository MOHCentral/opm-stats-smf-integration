<?php
declare(strict_types=1);

/**
 * MOHAA Stats API Schemas
 * Centralized schema definitions and validation logic.
 */

if (!defined('SMF'))
    die('No direct access...');

class MohaaStatsSchemas
{
    private static array $schemas = [
        'global_stats' => [
            'total_kills' => ['type' => 'int', 'default' => 0],
            'total_players' => ['type' => 'int', 'default' => 0],
            'total_matches' => ['type' => 'int', 'default' => 0],
            'total_time' => ['type' => 'int', 'default' => 0],
        ],
        'leaderboard' => [
            'total' => ['type' => 'int', 'default' => 0],
            'page' => ['type' => 'int', 'default' => 0],
            'players' => ['type' => 'array_of_player_summary', 'default' => []],
        ],
        'player_summary' => [
            'rank' => ['type' => 'int', 'default' => 0],
            'name' => ['type' => 'string', 'default' => 'Unknown'],
            'guid' => ['type' => 'string', 'default' => ''],
            'kills' => ['type' => 'int', 'default' => 0],
            'deaths' => ['type' => 'int', 'default' => 0],
            'kdr' => ['type' => 'float', 'default' => 0.0],
            'accuracy' => ['type' => 'float', 'default' => 0.0],
            'score' => ['type' => 'int', 'default' => 0],
        ],
        'player' => [
            'guid' => ['type' => 'string', 'default' => ''],
            'name' => ['type' => 'string', 'default' => 'Unknown'],
            'kills' => ['type' => 'int', 'default' => 0],
            'deaths' => ['type' => 'int', 'default' => 0],
            'kdr' => ['type' => 'float', 'default' => 0.0],
            'accuracy' => ['type' => 'float', 'default' => 0.0],
            'rounds' => ['type' => 'int', 'default' => 0],
            'wins' => ['type' => 'int', 'default' => 0],
            'losses' => ['type' => 'int', 'default' => 0],
            'win_loss_ratio' => ['type' => 'float', 'default' => 0.0],
            'headshots' => ['type' => 'int', 'default' => 0],
            'score' => ['type' => 'int', 'default' => 0],
            'spm' => ['type' => 'float', 'default' => 0.0],
            'kpm' => ['type' => 'float', 'default' => 0.0],
            'time_played' => ['type' => 'int', 'default' => 0],
            'last_seen' => ['type' => 'string', 'default' => ''],
            'is_online' => ['type' => 'bool', 'default' => false],
            'is_vip' => ['type' => 'bool', 'default' => false],
        ],
        'player_deep' => [
             'combat' => ['type' => 'array', 'default' => []],
             'movement' => ['type' => 'array', 'default' => []],
        ],
        'matches_list' => [
            'total' => ['type' => 'int', 'default' => 0],
            'list' => ['type' => 'array_of_match_summary', 'default' => []],
        ],
        'match_summary' => [
            'id' => ['type' => 'string', 'default' => ''],
            'map' => ['type' => 'string', 'default' => ''],
            'gametype' => ['type' => 'string', 'default' => ''],
            'server_name' => ['type' => 'string', 'default' => ''],
            'time_start' => ['type' => 'string', 'default' => ''],
            'time_end' => ['type' => 'string', 'default' => ''],
            'duration' => ['type' => 'int', 'default' => 0],
            'player_count' => ['type' => 'int', 'default' => 0],
        ],
        'match' => [
            'info' => ['type' => 'match_info', 'default' => []],
            'stats' => ['type' => 'array', 'default' => []],
        ],
        'match_info' => [
            'id' => ['type' => 'string', 'default' => ''],
            'map_name' => ['type' => 'string', 'default' => ''],
            'server_name' => ['type' => 'string', 'default' => ''],
            'gametype' => ['type' => 'string', 'default' => ''],
            'time_start' => ['type' => 'string', 'default' => ''],
            'duration' => ['type' => 'int', 'default' => 0],
        ],
        'simple_count' => [
            'total' => ['type' => 'int', 'default' => 0],
        ],
        'map_details' => [
             'id' => ['type' => 'string', 'default' => ''],
             'name' => ['type' => 'string', 'default' => ''],
             'total_matches' => ['type' => 'int', 'default' => 0],
        ],
        'weapon_stats' => [
            'name' => ['type' => 'string', 'default' => 'Unknown'],
            'kills' => ['type' => 'int', 'default' => 0],
            'shots' => ['type' => 'int', 'default' => 0],
            'hits' => ['type' => 'int', 'default' => 0],
            'accuracy' => ['type' => 'float', 'default' => 0.0],
        ],
        'auth_history_item' => [
            'attempt_at' => ['type' => 'string', 'default' => ''],
            'server_name' => ['type' => 'string', 'default' => 'Unknown'],
            'player_ip' => ['type' => 'string', 'default' => ''],
            'success' => ['type' => 'bool', 'default' => false],
            'failure_reason' => ['type' => 'string', 'default' => null, 'nullable' => true],
        ],
        'auth_trusted_ip' => [
            'id' => ['type' => 'int', 'default' => 0],
            'ip_address' => ['type' => 'string', 'default' => ''],
            'last_used_at' => ['type' => 'string', 'default' => ''],
        ],
        'auth_pending_ip' => [
            'id' => ['type' => 'int', 'default' => 0],
            'requested_at' => ['type' => 'string', 'default' => ''],
            'server_name' => ['type' => 'string', 'default' => 'Unknown'],
            'ip_address' => ['type' => 'string', 'default' => ''],
        ],
        'login_history' => [
            'history' => ['type' => 'array_of_auth_history_item', 'default' => []],
        ],
        'trusted_ips' => [
            'trusted_ips' => ['type' => 'array_of_auth_trusted_ip', 'default' => []],
        ],
        'pending_ips' => [
            'pending_ips' => ['type' => 'array_of_auth_pending_ip', 'default' => []],
        ],
        'auth_claim_init' => [
            'code' => ['type' => 'string', 'default' => ''],
            'expires_in' => ['type' => 'int', 'default' => 0],
        ],
        'auth_device_init' => [
            'user_code' => ['type' => 'string', 'default' => ''],
            'expires_in' => ['type' => 'int', 'default' => 0],
            'verification_uri' => ['type' => 'string', 'default' => ''],
        ],
        'simple_success' => [
            'success' => ['type' => 'bool', 'default' => false],
        ],
        'global_activity' => [
            'activity' => ['type' => 'array', 'default' => []],
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
        'leaderboard_cards' => [
             'cards' => ['type' => 'array', 'default' => []],
        ],
        'achievement' => [
            'id' => ['type' => 'int', 'default' => 0],
            'name' => ['type' => 'string', 'default' => ''],
            'description' => ['type' => 'string', 'default' => ''],
        ],
        'achievement_leaderboard' => [
             'players' => ['type' => 'array', 'default' => []],
        ],
        'recent_achievements' => [
             'achievements' => ['type' => 'array', 'default' => []],
        ],
        'match_advanced' => [
             'info' => ['type' => 'match_info', 'default' => []],
             'stats' => ['type' => 'array', 'default' => []],
        ],
        'map_stat_summary' => [
            'id' => ['type' => 'string', 'default' => ''],
            'name' => ['type' => 'string', 'default' => 'Unknown'],
            'count' => ['type' => 'int', 'default' => 0],
            'kills' => ['type' => 'int', 'default' => 0],
            'avg_time' => ['type' => 'float', 'default' => 0.0],
        ],
        'weapon_basic' => [
            'id' => ['type' => 'string', 'default' => ''],
            'name' => ['type' => 'string', 'default' => 'Unknown'],
        ],
        'map_basic' => [
             'id' => ['type' => 'string', 'default' => ''],
             'name' => ['type' => 'string', 'default' => 'Unknown'],
        ],
        'heatmap_point' => [
            'x' => ['type' => 'float', 'default' => 0.0],
            'y' => ['type' => 'float', 'default' => 0.0],
            'count' => ['type' => 'int', 'default' => 0],
            'type' => ['type' => 'string', 'default' => ''],
        ],
        'map_popularity' => [
             'id' => ['type' => 'string', 'default' => ''],
             'name' => ['type' => 'string', 'default' => 'Unknown'],
             'count' => ['type' => 'int', 'default' => 0],
             'percent' => ['type' => 'float', 'default' => 0.0],
        ],
    ];

    public static function validate(mixed $data, string|array $schemaDef): mixed
    {
        // Handle array_of_* and array_root string definitions
        if (is_string($schemaDef)) {
            if (str_starts_with($schemaDef, 'array_of_')) {
                 $itemSchema = substr($schemaDef, 9);
                 if (!is_array($data)) return [];
                 $result = [];
                 foreach ($data as $item) {
                     $result[] = self::validate($item, $itemSchema);
                 }
                 return $result;
            }
            if ($schemaDef === 'array_root') {
                return is_array($data) ? $data : [];
            }

            // Look up predefined schema
            $definedSchema = self::$schemas[$schemaDef] ?? null;
            if ($definedSchema !== null) {
                return self::validate($data, $definedSchema);
            }

            // If schema not found, this is an issue. Return as is (or default empty array if we want strictness)
            // But for now, returning data is safer than breaking if I missed a schema definition.
            return $data;
        }

        // Schema is an array definition (map)
        // Ensure data is an array
        if (!is_array($data)) {
            // If data is null or scalar but we expect an object structure, return defaults.
            $data = [];
        }

        $result = [];
        foreach ($schemaDef as $field => $rules) {
            $type = $rules['type'] ?? 'string';
            $default = $rules['default'] ?? null;
            $nullable = $rules['nullable'] ?? false;

            $value = $data[$field] ?? $default;

            // Handle nulls
            if ($value === null) {
                if ($nullable) {
                    $result[$field] = null;
                    continue;
                }
                // Use default if value is null and not nullable
                $value = $default;
            }

            // Recursive validation
            if (isset(self::$schemas[$type]) || str_starts_with($type, 'array_of_')) {
                 $result[$field] = self::validate($value, $type);
                 continue;
            }

            // Strict casting
            switch ($type) {
                case 'int':
                    $result[$field] = (int)$value;
                    break;
                case 'float':
                    $result[$field] = (float)$value;
                    break;
                case 'bool':
                    $result[$field] = (bool)$value;
                    break;
                case 'string':
                    $result[$field] = (string)$value;
                    break;
                case 'array':
                    $result[$field] = is_array($value) ? $value : [];
                    break;
                default:
                    $result[$field] = $value;
            }
        }

        return $result;
    }
}
