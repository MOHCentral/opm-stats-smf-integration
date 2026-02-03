<?php

if (!defined('SMF'))
    die('No direct access...');

class MohaaStatsSchemas
{
    private static array $schemas = [
        // --- Core Objects ---
        'player_rank' => [
            'type' => 'object',
            'fields' => [
                'rank' => ['type' => 'int', 'default' => 0],
                'name' => ['type' => 'string', 'default' => 'Unknown'],
                'guid' => ['type' => 'string', 'default' => ''],
                'kills' => ['type' => 'int', 'default' => 0],
                'deaths' => ['type' => 'int', 'default' => 0],
                'kdr' => ['type' => 'float', 'default' => 0.0],
                'score' => ['type' => 'int', 'default' => 0],
                'accuracy' => ['type' => 'float', 'default' => 0.0],
                'headshots' => ['type' => 'int', 'default' => 0],
                'rounds' => ['type' => 'int', 'default' => 0],
                'time_played' => ['type' => 'int', 'default' => 0],
                'is_online' => ['type' => 'bool', 'default' => false],
                'is_vip' => ['type' => 'bool', 'default' => false],
            ]
        ],
        'match_summary' => [
            'type' => 'object',
            'fields' => [
                'id' => ['type' => 'string', 'default' => ''],
                'map' => ['type' => 'string', 'default' => ''],
                'map_name' => ['type' => 'string', 'default' => ''],
                'server' => ['type' => 'string', 'default' => ''],
                'time' => ['type' => 'string', 'default' => ''], // or int/timestamp
                'players' => ['type' => 'int', 'default' => 0],
                'duration' => ['type' => 'int', 'default' => 0],
                'winner' => ['type' => 'string', 'default' => ''],
            ]
        ],
        'map_stat' => [
             'type' => 'object',
             'fields' => [
                 'id' => ['type' => 'string', 'default' => ''],
                 'name' => ['type' => 'string', 'default' => ''],
                 'count' => ['type' => 'int', 'default' => 0], // Times played
                 'popularity' => ['type' => 'float', 'default' => 0.0],
             ]
        ],
        'weapon_stat' => [
             'type' => 'object',
             'fields' => [
                 'id' => ['type' => 'string', 'default' => ''],
                 'name' => ['type' => 'string', 'default' => ''],
                 'kills' => ['type' => 'int', 'default' => 0],
                 'accuracy' => ['type' => 'float', 'default' => 0.0],
             ]
        ],
        'achievement' => [
             'type' => 'object',
             'fields' => [
                 'id' => ['type' => 'int', 'default' => 0],
                 'name' => ['type' => 'string', 'default' => ''],
                 'description' => ['type' => 'string', 'default' => ''],
                 'points' => ['type' => 'int', 'default' => 0],
                 'icon' => ['type' => 'string', 'default' => ''],
                 'unlocked_at' => ['type' => 'string', 'default' => ''],
             ]
        ],
        'activity_point' => [
            'type' => 'object',
            'fields' => [
                'hour' => ['type' => 'int', 'default' => 0],
                'count' => ['type' => 'int', 'default' => 0],
            ]
        ],
        'heatmap_point' => [
            'type' => 'object',
            'fields' => [
                'x' => ['type' => 'float', 'default' => 0.0],
                'y' => ['type' => 'float', 'default' => 0.0],
                'value' => ['type' => 'float', 'default' => 0.0],
                'count' => ['type' => 'int', 'default' => 0],
            ]
        ],
        'playstyle_data' => [
            'type' => 'object',
            'fields' => [
                'style' => ['type' => 'string', 'default' => 'Unknown'],
                'spm' => ['type' => 'float', 'default' => 0.0],
                'kpm' => ['type' => 'float', 'default' => 0.0],
                'accuracy' => ['type' => 'float', 'default' => 0.0],
            ]
        ],
        'player_deep_stats' => [
            'type' => 'object',
            'fields' => [
                'combat' => ['type' => 'object', 'default' => []], // Flexible for now
                'movement' => ['type' => 'object', 'default' => []],
                'weapons' => ['type' => 'object', 'default' => []],
                'maps' => ['type' => 'object', 'default' => []],
            ]
        ],
        'login_history_entry' => [
            'type' => 'object',
            'fields' => [
                'attempt_at' => ['type' => 'string', 'default' => ''],
                'server_name' => ['type' => 'string', 'default' => ''],
                'player_ip' => ['type' => 'string', 'default' => ''],
                'success' => ['type' => 'bool', 'default' => false],
                'failure_reason' => ['type' => 'string', 'default' => null],
            ]
        ],
        'trusted_ip' => [
            'type' => 'object',
            'fields' => [
                'id' => ['type' => 'int', 'default' => 0],
                'ip_address' => ['type' => 'string', 'default' => ''],
                'last_used_at' => ['type' => 'string', 'default' => ''],
            ]
        ],
        'pending_ip' => [
            'type' => 'object',
            'fields' => [
                'id' => ['type' => 'int', 'default' => 0],
                'requested_at' => ['type' => 'string', 'default' => ''],
                'server_name' => ['type' => 'string', 'default' => ''],
                'ip_address' => ['type' => 'string', 'default' => ''],
            ]
        ],

        // --- API Responses ---
        'global_stats' => [
            'type' => 'object',
            'fields' => [
                'total_kills' => ['type' => 'int', 'default' => 0],
                'total_players' => ['type' => 'int', 'default' => 0],
                'total_matches' => ['type' => 'int', 'default' => 0],
                'active_players' => ['type' => 'int', 'default' => 0],
            ]
        ],
        'leaderboard' => [
            'type' => 'object',
            'fields' => [
                'players' => ['type' => 'array_of_player_rank', 'default' => []],
                'total' => ['type' => 'int', 'default' => 0],
                'page' => ['type' => 'int', 'default' => 0],
                'limit' => ['type' => 'int', 'default' => 0],
            ]
        ],
        'player_stats' => [ // Extends player_rank basically, but specific to /stats/player
            'type' => 'object',
            'fields' => [
                'name' => ['type' => 'string', 'default' => 'Unknown'],
                'guid' => ['type' => 'string', 'default' => ''],
                'kills' => ['type' => 'int', 'default' => 0],
                'deaths' => ['type' => 'int', 'default' => 0],
                'kdr' => ['type' => 'float', 'default' => 0.0],
                'wlr' => ['type' => 'float', 'default' => 0.0],
                'score' => ['type' => 'int', 'default' => 0],
                'rank' => ['type' => 'int', 'default' => 0],
                'spm' => ['type' => 'float', 'default' => 0.0],
                'accuracy' => ['type' => 'float', 'default' => 0.0],
                'headshots' => ['type' => 'int', 'default' => 0],
                'time_played' => ['type' => 'int', 'default' => 0],
                'last_seen' => ['type' => 'string', 'default' => ''],
                'is_online' => ['type' => 'bool', 'default' => false],
                'is_vip' => ['type' => 'bool', 'default' => false],
            ]
        ],
        'player_matches' => [
            'type' => 'object',
            'fields' => [
                'list' => ['type' => 'array_of_match_summary', 'default' => []],
                'total' => ['type' => 'int', 'default' => 0],
            ]
        ],
        'player_performance' => [
             'type' => 'object',
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
        'matches_list' => [
            'type' => 'object',
            'fields' => [
                'list' => ['type' => 'array_of_match_summary', 'default' => []],
                'total' => ['type' => 'int', 'default' => 0],
            ]
        ],
        'match_details' => [
            'type' => 'object',
            'fields' => [
                'info' => ['type' => 'object', 'default' => [], 'fields' => [
                    'match_id' => ['type' => 'string', 'default' => ''],
                    'map_name' => ['type' => 'string', 'default' => ''],
                    'server_name' => ['type' => 'string', 'default' => ''],
                    'winner' => ['type' => 'string', 'default' => ''],
                ]],
                'stats' => ['type' => 'array', 'default' => []], // Flexible for now
                'teams' => ['type' => 'array', 'default' => []],
            ]
        ],
        'heatmap_data' => [
            'type' => 'array_of_heatmap_point',
            'default' => []
        ],
        'live_matches' => [
            'type' => 'array_of_match_summary',
            'default' => []
        ],
        'maps_list' => [
            'type' => 'array_of_map_stat',
            'default' => []
        ],
        'map_details' => [
            'type' => 'object',
            'fields' => [
                'id' => ['type' => 'string', 'default' => ''],
                'name' => ['type' => 'string', 'default' => ''],
                'total_matches' => ['type' => 'int', 'default' => 0],
                'total_playtime' => ['type' => 'int', 'default' => 0],
            ]
        ],
        'weapons_list' => [
            'type' => 'array_of_weapon_stat',
            'default' => []
        ],
        'weapon_details' => [
             'type' => 'object',
             'fields' => [
                 'id' => ['type' => 'string', 'default' => ''],
                 'name' => ['type' => 'string', 'default' => ''],
                 'total_kills' => ['type' => 'int', 'default' => 0],
                 'avg_accuracy' => ['type' => 'float', 'default' => 0.0],
             ]
        ],
        'auth_init' => [
            'type' => 'object',
            'fields' => [
                'code' => ['type' => 'string', 'default' => ''],
                'expires_in' => ['type' => 'int', 'default' => 0],
            ]
        ],
        'auth_device' => [
            'type' => 'object',
            'fields' => [
                'user_code' => ['type' => 'string', 'default' => ''],
                'verification_uri' => ['type' => 'string', 'default' => ''],
                'expires_in' => ['type' => 'int', 'default' => 0],
            ]
        ],
        'auth_history' => [
            'type' => 'object',
            'fields' => [
                'history' => ['type' => 'array_of_login_history_entry', 'default' => []],
            ]
        ],
        'auth_trusted_ips' => [
            'type' => 'object',
            'fields' => [
                'trusted_ips' => ['type' => 'array_of_trusted_ip', 'default' => []],
            ]
        ],
        'auth_pending_ips' => [
            'type' => 'object',
            'fields' => [
                'pending_ips' => ['type' => 'array_of_pending_ip', 'default' => []],
            ]
        ],
        'generic_success' => [
            'type' => 'object',
            'fields' => [
                'success' => ['type' => 'bool', 'default' => false],
                'message' => ['type' => 'string', 'default' => ''],
            ]
        ],
        'global_activity' => [
            'type' => 'object',
            'fields' => [
                'activity' => ['type' => 'array_of_activity_point', 'default' => []]
            ]
        ],
        'leaderboard_cards' => [
            'type' => 'object',
            'fields' => [
                'cards' => ['type' => 'array', 'default' => []] // Flexible
            ]
        ],
        'achievements_list' => [
             'type' => 'object', // Usually {achievements: [...]} or just [...]? Check mock.
             // Mock says response(['achievements' => []]);
             'fields' => [
                 'achievements' => ['type' => 'array_of_achievement', 'default' => []]
             ]
        ],
    ];

    public static function validate(?array $data, string $schema): array
    {
        if ($data === null) {
            $data = [];
        }

        // Handle array root types (e.g. array_of_match_summary)
        if (strpos($schema, 'array_of_') === 0) {
            $subSchemaName = substr($schema, 9); // Remove 'array_of_'
            if (!is_array($data)) return [];

            $result = [];
            foreach ($data as $item) {
                if (is_array($item)) {
                    $result[] = self::validate($item, $subSchemaName);
                }
            }
            return $result;
        }

        if (!isset(self::$schemas[$schema])) {
            // Fallback for undefined schemas or generic array
            return $data;
        }

        $schemaDef = self::$schemas[$schema];

        // If the schema definition is for an array or primitive type (not a structured object with fields)
        if ($schemaDef['type'] !== 'object' || !isset($schemaDef['fields'])) {
            return self::castValue($data, $schemaDef);
        }

        $result = [];

        if ($schemaDef['type'] === 'object' && isset($schemaDef['fields'])) {
            foreach ($schemaDef['fields'] as $fieldName => $fieldDef) {
                $value = $data[$fieldName] ?? null;
                $result[$fieldName] = self::castValue($value, $fieldDef);
            }
            // Strict validation: Ignore extra fields.
        }

        return $result;
    }

    private static function castValue($value, array $fieldDef)
    {
        $type = $fieldDef['type'];
        $default = $fieldDef['default'] ?? null;

        if ($value === null) {
            if ($default !== null) return $default;
            // If no default and value is null, return appropriate zero-value based on type
            return match($type) {
                'int' => 0,
                'float' => 0.0,
                'string' => '',
                'bool' => false,
                'array' => [],
                'object' => [],
                default => null
            };
        }

        if (strpos($type, 'array_of_') === 0) {
            $subSchemaName = substr($type, 9);
            if (!is_array($value)) return [];
            $result = [];
            foreach ($value as $item) {
                $result[] = self::validate($item, $subSchemaName);
            }
            return $result;
        }

        // Nested object with inline schema
        if ($type === 'object' && isset($fieldDef['fields'])) {
             if (!is_array($value)) return $default ?? [];
             $nestedResult = [];
             foreach ($fieldDef['fields'] as $k => $f) {
                 $nestedResult[$k] = self::castValue($value[$k] ?? null, $f);
             }
             return $nestedResult;
        }

        return match($type) {
            'int' => (int)$value,
            'float' => (float)$value,
            'string' => (string)$value,
            'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'array', 'object' => is_array($value) ? $value : ($default ?? []),
            default => $value
        };
    }
}
