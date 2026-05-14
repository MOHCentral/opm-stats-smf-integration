<?php
declare(strict_types=1);

/**
 * MohaaStatsSchemas.php
 *
 * Centralized schema definitions and strict validation logic for the MOHAA Stats API.
 * Ensures data integrity by enforcing types and providing default structures for all API responses.
 */

if (!defined('SMF'))
    die('No direct access...');

class MohaaStatsSchemas
{
    private static array $schemas = [
        // --- Global Stats ---
        'global_stats' => [
            'fields' => [
                'total_kills' => ['type' => 'int', 'default' => 0],
                'total_players' => ['type' => 'int', 'default' => 0],
                'total_matches' => ['type' => 'int', 'default' => 0],
                'active_servers' => ['type' => 'int', 'default' => 0],
            ]
        ],

        // --- Player Stats ---
        'player_stats' => [
            'fields' => [
                'id' => ['type' => 'int', 'default' => 0],
                'guid' => ['type' => 'string', 'default' => ''],
                'name' => ['type' => 'string', 'default' => 'Unknown'],
                'kills' => ['type' => 'int', 'default' => 0],
                'deaths' => ['type' => 'int', 'default' => 0],
                'kdr' => ['type' => 'float', 'default' => 0.0],
                'accuracy' => ['type' => 'float', 'default' => 0.0],
                'rounds' => ['type' => 'int', 'default' => 0],
                'wins' => ['type' => 'int', 'default' => 0],
                'losses' => ['type' => 'int', 'default' => 0],
                'headshots' => ['type' => 'int', 'default' => 0],
                'is_online' => ['type' => 'bool', 'default' => false],
                'rank' => ['type' => 'int', 'default' => 0],
                'score' => ['type' => 'int', 'default' => 0],
                'time_played' => ['type' => 'int', 'default' => 0],
                'last_seen' => ['type' => 'string', 'default' => ''],
                'verified' => ['type' => 'bool', 'default' => false],
                'is_vip' => ['type' => 'bool', 'default' => false],
            ]
        ],
        'player_deep_stats' => [
            'fields' => [
                'combat' => ['type' => 'array', 'default' => [], 'schema' => 'player_combat_stats'], // Nested schema
                'movement' => ['type' => 'array', 'default' => []], // Unstructured array or TODO schema
            ]
        ],
        'player_combat_stats' => [ // Helper schema
            'fields' => [
                'melee_kills' => ['type' => 'int', 'default' => 0],
                'grenade_kills' => ['type' => 'int', 'default' => 0],
                // Add more as discovered
            ]
        ],
        'player_weapons' => [
            'type' => 'array_root', // Root is a list
            'item_schema' => 'weapon_stat_item'
        ],
        'weapon_stat_item' => [
            'fields' => [
                'id' => ['type' => 'string', 'default' => ''],
                'name' => ['type' => 'string', 'default' => ''],
                'kills' => ['type' => 'int', 'default' => 0],
                'shots' => ['type' => 'int', 'default' => 0],
                'hits' => ['type' => 'int', 'default' => 0],
                'accuracy' => ['type' => 'float', 'default' => 0.0],
            ]
        ],
        'player_matches' => [
            'fields' => [
                'total' => ['type' => 'int', 'default' => 0],
                'list' => ['type' => 'array', 'default' => [], 'schema' => 'match_summary_list'],
            ]
        ],
        'match_summary_list' => [
             'type' => 'array_root',
             'item_schema' => 'match_summary'
        ],
        'match_summary' => [
            'fields' => [
                'id' => ['type' => 'string', 'default' => ''],
                'map' => ['type' => 'string', 'default' => ''],
                'server' => ['type' => 'string', 'default' => ''],
                'time' => ['type' => 'string', 'default' => ''],
                'result' => ['type' => 'string', 'default' => ''], // win/loss
                'kills' => ['type' => 'int', 'default' => 0],
                'deaths' => ['type' => 'int', 'default' => 0],
            ]
        ],
        'player_achievements' => [
             'type' => 'array_root',
             'item_schema' => 'achievement_item'
        ],
        'achievement_item' => [
            'fields' => [
                'id' => ['type' => 'int', 'default' => 0],
                'name' => ['type' => 'string', 'default' => ''],
                'description' => ['type' => 'string', 'default' => ''],
                'unlocked_at' => ['type' => 'string', 'default' => ''],
            ]
        ],
        'player_performance' => [
            'fields' => [
                'spm' => ['type' => 'float', 'default' => 0.0],
                'kpm' => ['type' => 'float', 'default' => 0.0],
                'kd_ratio' => ['type' => 'float', 'default' => 0.0],
                'win_loss_ratio' => ['type' => 'float', 'default' => 0.0],
                'points' => ['type' => 'int', 'default' => 0],
                'rounds_played' => ['type' => 'int', 'default' => 0],
                'is_vip' => ['type' => 'bool', 'default' => false],
            ]
        ],
        'player_playstyle' => [
            'fields' => [
                'style' => ['type' => 'string', 'default' => 'Unknown'],
                'spm' => ['type' => 'float', 'default' => 0.0],
            ]
        ],

        // --- Leaderboards ---
        'leaderboard' => [
            'fields' => [
                'total' => ['type' => 'int', 'default' => 0],
                'page' => ['type' => 'int', 'default' => 0],
                'limit' => ['type' => 'int', 'default' => 25],
                'players' => ['type' => 'array', 'default' => [], 'schema' => 'leaderboard_player_list'],
            ]
        ],
        'leaderboard_player_list' => [
            'type' => 'array_root',
            'item_schema' => 'leaderboard_player'
        ],
        'leaderboard_player' => [
            'fields' => [
                'rank' => ['type' => 'int', 'default' => 0],
                'guid' => ['type' => 'string', 'default' => ''],
                'name' => ['type' => 'string', 'default' => 'Unknown'],
                'value' => ['type' => 'float', 'default' => 0.0], // The stat value (kills, etc.)
                // Include common stats that might be in leaderboard views
                'kills' => ['type' => 'int', 'default' => 0],
                'deaths' => ['type' => 'int', 'default' => 0],
            ]
        ],
        'leaderboard_count' => [
            'fields' => [
                'total' => ['type' => 'int', 'default' => 0],
            ]
        ],
        'leaderboard_cards' => [
            'fields' => [
                'cards' => ['type' => 'array', 'default' => [], 'schema' => 'leaderboard_cards_list']
            ]
        ],
        'leaderboard_cards_list' => [
            'type' => 'array_root',
            'item_schema' => 'leaderboard_card'
        ],
        'leaderboard_card' => [
            'fields' => [
                'title' => ['type' => 'string', 'default' => ''],
                'player' => ['type' => 'string', 'default' => ''],
                'value' => ['type' => 'string', 'default' => ''],
                'guid' => ['type' => 'string', 'default' => ''],
            ]
        ],

        // --- Matches ---
        'recent_matches' => [
            'fields' => [
                'total' => ['type' => 'int', 'default' => 0],
                'list' => ['type' => 'array', 'default' => [], 'schema' => 'match_summary_list'],
            ]
        ],
        'match_count' => [
             'fields' => [
                 'total' => ['type' => 'int', 'default' => 0],
             ]
        ],
        'match_details' => [
            'fields' => [
                'info' => ['type' => 'array', 'default' => [], 'schema' => 'match_info'],
                'stats' => ['type' => 'array', 'default' => []], // Complex structure, maybe simplify or add schema later
                'players' => ['type' => 'array', 'default' => []],
            ]
        ],
        'match_info' => [
            'fields' => [
                'id' => ['type' => 'string', 'default' => ''],
                'map_name' => ['type' => 'string', 'default' => ''],
                'server_name' => ['type' => 'string', 'default' => ''],
                'start_time' => ['type' => 'string', 'default' => ''],
                'end_time' => ['type' => 'string', 'default' => ''],
                'duration' => ['type' => 'int', 'default' => 0],
            ]
        ],
        'live_matches' => [
             'type' => 'array_root',
             'item_schema' => 'live_match_item' // Assuming list of matches
        ],
        'live_match_item' => [
            'fields' => [
                'server_name' => ['type' => 'string', 'default' => ''],
                'map_name' => ['type' => 'string', 'default' => ''],
                'players' => ['type' => 'int', 'default' => 0],
                'max_players' => ['type' => 'int', 'default' => 0],
            ]
        ],

        // --- Maps ---
        'map_stats' => [ // Single map stats or list? API says getMapStats returns list usually
             'type' => 'array_root',
             'item_schema' => 'map_stat_item'
        ],
        'map_stat_item' => [
            'fields' => [
                 'id' => ['type' => 'string', 'default' => ''],
                 'name' => ['type' => 'string', 'default' => ''],
                 'times_played' => ['type' => 'int', 'default' => 0],
            ]
        ],
        'map_details' => [
            'fields' => [
                 'id' => ['type' => 'string', 'default' => ''],
                 'name' => ['type' => 'string', 'default' => ''],
                 'description' => ['type' => 'string', 'default' => ''],
                 'image_url' => ['type' => 'string', 'default' => ''],
            ]
        ],
        'map_heatmap' => [
             'type' => 'array_root',
             'item_schema' => 'heatmap_point'
        ],
        'heatmap_point' => [
            'fields' => [
                'x' => ['type' => 'float', 'default' => 0.0],
                'y' => ['type' => 'float', 'default' => 0.0],
                'value' => ['type' => 'float', 'default' => 0.0],
            ]
        ],
        'maps_list' => [ // Simple list of maps
             'type' => 'array_root',
             'item_schema' => 'map_basic'
        ],
        'map_basic' => [
             'fields' => [
                 'id' => ['type' => 'string', 'default' => ''],
                 'name' => ['type' => 'string', 'default' => ''],
             ]
        ],
        'map_popularity' => [
             'type' => 'array_root',
             'item_schema' => 'map_stat_item'
        ],

        // --- Weapons ---
        'weapons_list' => [
             'type' => 'array_root',
             'item_schema' => 'weapon_basic'
        ],
        'weapon_basic' => [
             'fields' => [
                 'id' => ['type' => 'string', 'default' => ''],
                 'name' => ['type' => 'string', 'default' => ''],
             ]
        ],
        'weapon_stats' => [
            'fields' => [
                 'id' => ['type' => 'string', 'default' => ''],
                 'name' => ['type' => 'string', 'default' => ''],
                 'kills' => ['type' => 'int', 'default' => 0],
                 'accuracy' => ['type' => 'float', 'default' => 0.0],
            ]
        ],
        'weapon_leaderboard' => [
            'fields' => [
                'total' => ['type' => 'int', 'default' => 0],
                'players' => ['type' => 'array', 'default' => [], 'schema' => 'leaderboard_player_list'],
            ]
        ],

        // --- Auth ---
        'auth_init_claim' => [
            'fields' => [
                'code' => ['type' => 'string', 'default' => ''],
                'expires_in' => ['type' => 'int', 'default' => 0],
            ]
        ],
        'auth_device' => [
            'fields' => [
                'user_code' => ['type' => 'string', 'default' => ''],
                'verification_url' => ['type' => 'string', 'default' => ''],
                'expires_in' => ['type' => 'int', 'default' => 0],
            ]
        ],
        'login_history' => [
            'fields' => [
                'history' => ['type' => 'array', 'default' => [], 'schema' => 'login_history_list']
            ]
        ],
        'login_history_list' => [
            'type' => 'array_root',
            'item_schema' => 'login_history_item'
        ],
        'login_history_item' => [
            'fields' => [
                'attempt_at' => ['type' => 'string', 'default' => ''],
                'server_name' => ['type' => 'string', 'default' => ''],
                'player_ip' => ['type' => 'string', 'default' => ''],
                'success' => ['type' => 'bool', 'default' => false],
                'failure_reason' => ['type' => 'string', 'default' => ''],
            ]
        ],
        'trusted_ips' => [
            'fields' => [
                'trusted_ips' => ['type' => 'array', 'default' => [], 'schema' => 'trusted_ip_list']
            ]
        ],
        'trusted_ip_list' => [
            'type' => 'array_root',
            'item_schema' => 'trusted_ip_item'
        ],
        'trusted_ip_item' => [
            'fields' => [
                'id' => ['type' => 'int', 'default' => 0],
                'ip_address' => ['type' => 'string', 'default' => ''],
                'last_used_at' => ['type' => 'string', 'default' => ''],
                'added_at' => ['type' => 'string', 'default' => ''],
            ]
        ],
        'pending_ips' => [
            'fields' => [
                'pending_ips' => ['type' => 'array', 'default' => [], 'schema' => 'pending_ip_list']
            ]
        ],
        'pending_ip_list' => [
            'type' => 'array_root',
            'item_schema' => 'pending_ip_item'
        ],
        'pending_ip_item' => [
            'fields' => [
                'id' => ['type' => 'int', 'default' => 0],
                'ip_address' => ['type' => 'string', 'default' => ''],
                'requested_at' => ['type' => 'string', 'default' => ''],
                'server_name' => ['type' => 'string', 'default' => ''],
            ]
        ],
        'auth_action_response' => [
            'fields' => [
                'success' => ['type' => 'bool', 'default' => false],
                'message' => ['type' => 'string', 'default' => ''],
            ]
        ],

        // --- Achievements ---
        'achievements_list' => [
             'type' => 'array_root',
             'item_schema' => 'achievement_item'
        ],
        'achievement_details' => [
            'fields' => [
                'id' => ['type' => 'int', 'default' => 0],
                'name' => ['type' => 'string', 'default' => ''],
                'description' => ['type' => 'string', 'default' => ''],
                'points' => ['type' => 'int', 'default' => 0],
            ]
        ],

        // --- Other ---
        'global_activity' => [
            'fields' => [
                'activity' => ['type' => 'array', 'default' => [], 'schema' => 'activity_list']
            ]
        ],
        'activity_list' => [
            'type' => 'array_root',
            'item_schema' => 'activity_point'
        ],
        'activity_point' => [
            'fields' => [
                'hour' => ['type' => 'int', 'default' => 0],
                'count' => ['type' => 'int', 'default' => 0],
            ]
        ],

        // Empty default for void/unknown
        'empty' => [
            'fields' => []
        ]
    ];

    /**
     * Strictly validates and casts the given data against the specified schema.
     * Returns a fully populated structure with default values for missing/invalid fields.
     *
     * @param mixed $data The raw API response data
     * @param string $schemaName The schema identifier
     * @return array The validated and normalized data
     */
    public static function validate(mixed $data, string $schemaName): array
    {
        if (!isset(self::$schemas[$schemaName])) {
            // Fallback: if schema doesn't exist, return data as is or empty array if null
            // For strictness, we might want to log this or force empty array
            return is_array($data) ? $data : [];
        }

        $schema = self::$schemas[$schemaName];

        // Handle Root Array types
        if (isset($schema['type']) && $schema['type'] === 'array_root') {
            if (!is_array($data)) {
                return []; // Default to empty list
            }

            // If data is associative (an object), wrap it or treat as empty depending on logic.
            // But usually API returns [ {}, {} ].
            if (self::isAssoc($data) && !empty($data)) {
                // If we expected a list but got an object, check if it's an error or just one item?
                // Strict API should return list.
                // We will treat non-list input as invalid for array_root.
                return [];
            }

            $validatedList = [];
            $itemSchema = $schema['item_schema'] ?? null;

            foreach ($data as $item) {
                if ($itemSchema) {
                    $validatedList[] = self::validate($item, $itemSchema);
                } else {
                    $validatedList[] = $item; // Pass through if no item schema
                }
            }
            return $validatedList;
        }

        // Handle Object/Fields types
        if ($data === null || !is_array($data)) {
            $data = [];
        }

        $validatedData = [];
        $fields = $schema['fields'] ?? [];

        foreach ($fields as $key => $def) {
            $value = $data[$key] ?? null;
            $type = $def['type'] ?? 'string';
            $default = $def['default'] ?? null;
            $nestedSchema = $def['schema'] ?? null;

            if ($value === null) {
                $validatedData[$key] = self::resolveDefault($default, $nestedSchema, $type);
                continue;
            }

            // Strict casting
            switch ($type) {
                case 'int':
                    if (is_numeric($value)) {
                        $validatedData[$key] = (int)$value;
                    } else {
                        $validatedData[$key] = (int)$default;
                    }
                    break;
                case 'float':
                    if (is_numeric($value)) {
                        $validatedData[$key] = (float)$value;
                    } else {
                        $validatedData[$key] = (float)$default;
                    }
                    break;
                case 'bool':
                    // Handle "true"/"false", 1/0, etc.
                    $validatedData[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    break;
                case 'string':
                    if (is_string($value) || is_numeric($value)) {
                        $validatedData[$key] = (string)$value;
                    } else {
                        $validatedData[$key] = (string)$default;
                    }
                    break;
                case 'array':
                    if (is_array($value)) {
                        if ($nestedSchema) {
                            $validatedData[$key] = self::validate($value, $nestedSchema);
                        } else {
                            $validatedData[$key] = $value; // Pass through unstructured array
                        }
                    } else {
                        $validatedData[$key] = self::resolveDefault($default, $nestedSchema, $type);
                    }
                    break;
                default:
                    $validatedData[$key] = $value;
            }
        }

        return $validatedData;
    }

    private static function resolveDefault($default, $nestedSchema, $type)
    {
        if ($type === 'array' && $nestedSchema) {
            // Recursive default generation
            return self::validate([], $nestedSchema);
        }
        return $default;
    }

    private static function isAssoc(array $arr): bool
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
