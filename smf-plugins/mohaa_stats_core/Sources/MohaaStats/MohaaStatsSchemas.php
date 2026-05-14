<?php
declare(strict_types=1);

if (!defined('SMF'))
    die('No direct access...');

class MohaaStatsSchemas
{
    private static array $schemas = [
        // Global Stats
        'global_stats' => [
            'type' => 'object',
            'fields' => [
                'total_kills' => ['type' => 'int'],
                'total_deaths' => ['type' => 'int'],
                'total_rounds' => ['type' => 'int'],
                'total_time' => ['type' => 'int'],
                'total_players' => ['type' => 'int'],
                'active_servers' => ['type' => 'int'],
                'total_shots' => ['type' => 'int'],
                'total_hits' => ['type' => 'int'],
            ]
        ],

        // Leaderboard
        'leaderboard' => [
            'type' => 'object',
            'fields' => [
                'total' => ['type' => 'int'],
                'page' => ['type' => 'int'],
                'limit' => ['type' => 'int'],
                'period' => ['type' => 'string'],
                'stat' => ['type' => 'string'],
                'players' => [
                    'type' => 'array_root',
                    'item_schema' => [
                        'type' => 'object',
                        'fields' => [ // Merged common fields below dynamically or explicit here
                            'guid' => ['type' => 'string'],
                            'name' => ['type' => 'string'],
                            'rank' => ['type' => 'int'],
                            'value' => ['type' => 'float'], // The stat value being ranked
                            'kills' => ['type' => 'int'],
                            'deaths' => ['type' => 'int'],
                            'kdr' => ['type' => 'float'],
                            'time_played' => ['type' => 'int'],
                        ]
                    ]
                ]
            ]
        ],

        'leaderboard_count' => [
            'type' => 'object',
            'fields' => [
                'total' => ['type' => 'int']
            ]
        ],

        // Player Stats
        'player_stats' => [
            'type' => 'object',
            'fields' => [
                'guid' => ['type' => 'string'],
                'name' => ['type' => 'string'],
                'rank' => ['type' => 'int'],
                'score' => ['type' => 'int'],
                'kills' => ['type' => 'int'],
                'deaths' => ['type' => 'int'],
                'kdr' => ['type' => 'float'],
                'accuracy' => ['type' => 'float'],
                'rounds' => ['type' => 'int'],
                'wins' => ['type' => 'int'],
                'losses' => ['type' => 'int'],
                'time_played' => ['type' => 'int'],
                'headshots' => ['type' => 'int'],
                'shots' => ['type' => 'int'],
                'hits' => ['type' => 'int'],
                'suicides' => ['type' => 'int'],
                'team_kills' => ['type' => 'int'],
                'is_online' => ['type' => 'bool'],
                'last_seen' => ['type' => 'string'],
                'first_seen' => ['type' => 'string'],
                // Add commonly used casting test fields
                'is_vip' => ['type' => 'bool'],
                'verified' => ['type' => 'bool'],
            ]
        ],

        'player_deep_stats' => [
            'type' => 'object',
            'fields' => [
                'combat' => ['type' => 'array'], // Can specify deeper if known
                'movement' => ['type' => 'array'],
                'objectives' => ['type' => 'array'],
                'weapons' => ['type' => 'array_root', 'item_schema' => ['type' => 'object', 'fields' => ['name' => ['type' => 'string'], 'kills' => ['type' => 'int']]]],
                'maps' => ['type' => 'array_root', 'item_schema' => ['type' => 'object', 'fields' => ['name' => ['type' => 'string'], 'rounds' => ['type' => 'int']]]],
            ]
        ],

        'player_weapons' => [
            'type' => 'array_root',
            'item_schema' => [
                'type' => 'object',
                'fields' => [
                    'id' => ['type' => 'string'],
                    'name' => ['type' => 'string'],
                    'kills' => ['type' => 'int'],
                    'deaths' => ['type' => 'int'],
                    'shots' => ['type' => 'int'],
                    'hits' => ['type' => 'int'],
                    'accuracy' => ['type' => 'float'],
                    'kdr' => ['type' => 'float'],
                    'time_played' => ['type' => 'int'],
                ]
            ]
        ],

        'player_matches' => [
            'type' => 'object',
            'fields' => [
                'total' => ['type' => 'int'],
                'list' => [
                    'type' => 'array_root',
                    'item_schema' => [
                        'type' => 'object',
                        'fields' => [
                            'id' => ['type' => 'string'],
                            'map_name' => ['type' => 'string'],
                            'server_name' => ['type' => 'string'],
                            'time_played' => ['type' => 'int'],
                            'kills' => ['type' => 'int'],
                            'deaths' => ['type' => 'int'],
                            'result' => ['type' => 'string'], // win/loss
                            'date' => ['type' => 'string'],
                        ]
                    ]
                ]
            ]
        ],

        'player_achievements' => [
            'type' => 'array_root',
            'item_schema' => [
                'type' => 'object',
                'fields' => [
                    'id' => ['type' => 'int'],
                    'name' => ['type' => 'string'],
                    'description' => ['type' => 'string'],
                    'unlocked_at' => ['type' => 'string'],
                    'icon' => ['type' => 'string'],
                ]
            ]
        ],

        // Matches
        'recent_matches' => [
            'type' => 'object',
            'fields' => [
                'total' => ['type' => 'int'],
                'list' => [
                    'type' => 'array_root',
                    'item_schema' => [
                        'type' => 'object',
                        'fields' => [
                            'id' => ['type' => 'string'],
                            'map_name' => ['type' => 'string'],
                            'gametype' => ['type' => 'string'],
                            'server_name' => ['type' => 'string'],
                            'start_time' => ['type' => 'string'],
                            'end_time' => ['type' => 'string'],
                            'duration' => ['type' => 'int'],
                            'player_count' => ['type' => 'int'],
                        ]
                    ]
                ]
            ]
        ],

        'match_count' => [
            'type' => 'object',
            'fields' => ['total' => ['type' => 'int']]
        ],

        'match_details' => [
            'type' => 'object',
            'fields' => [
                'info' => [
                    'type' => 'object',
                    'fields' => [
                        'id' => ['type' => 'string'],
                        'map_name' => ['type' => 'string'],
                        'server_name' => ['type' => 'string'],
                        'gametype' => ['type' => 'string'],
                        'start_time' => ['type' => 'string'],
                        'end_time' => ['type' => 'string'],
                        'duration' => ['type' => 'int'],
                        'winner' => ['type' => 'string'],
                    ]
                ],
                'stats' => [
                    'type' => 'array_root',
                    'item_schema' => [
                        'type' => 'object',
                        'fields' => [
                            'guid' => ['type' => 'string'],
                            'name' => ['type' => 'string'],
                            'team' => ['type' => 'string'],
                            'kills' => ['type' => 'int'],
                            'deaths' => ['type' => 'int'],
                            'score' => ['type' => 'int'],
                            'kdr' => ['type' => 'float'],
                            'accuracy' => ['type' => 'float'],
                        ]
                    ]
                ]
            ]
        ],

        'live_matches' => [
            'type' => 'array_root',
            'item_schema' => [
                'type' => 'object',
                'fields' => [
                    'server_name' => ['type' => 'string'],
                    'ip' => ['type' => 'string'],
                    'port' => ['type' => 'int'],
                    'map_name' => ['type' => 'string'],
                    'gametype' => ['type' => 'string'],
                    'players' => ['type' => 'int'],
                    'max_players' => ['type' => 'int'],
                    'time_left' => ['type' => 'string'],
                ]
            ]
        ],

        // Maps
        'map_stats' => [ // stats/maps (list of map stats)
            'type' => 'array_root',
            'item_schema' => [
                'type' => 'object',
                'fields' => [
                    'id' => ['type' => 'string'],
                    'name' => ['type' => 'string'],
                    'rounds' => ['type' => 'int'],
                    'time_played' => ['type' => 'int'],
                    'kills' => ['type' => 'int'],
                    'popularity' => ['type' => 'float'],
                ]
            ]
        ],

        'map_details' => [
            'type' => 'object',
            'fields' => [
                'id' => ['type' => 'string'],
                'name' => ['type' => 'string'],
                'image' => ['type' => 'string'],
                'rounds' => ['type' => 'int'],
                'time_played' => ['type' => 'int'],
                'kills' => ['type' => 'int'],
                'avg_duration' => ['type' => 'float'],
            ]
        ],

        'map_heatmap' => [
            'type' => 'array_root',
            'item_schema' => [
                'type' => 'object',
                'fields' => [
                    'x' => ['type' => 'float'],
                    'y' => ['type' => 'float'],
                    'value' => ['type' => 'float'],
                ]
            ]
        ],

        'maps_list' => [ // Simple list of names? or objects?
            'type' => 'array_root',
            'item_schema' => ['type' => 'string'] // Assuming string list based on name
        ],

        'weapons_list' => [
            'type' => 'array_root',
            'item_schema' => [
                'type' => 'object',
                'fields' => [
                    'id' => ['type' => 'string'],
                    'name' => ['type' => 'string'],
                    'category' => ['type' => 'string'],
                ]
            ]
        ],

        'weapon_stats' => [
            'type' => 'object',
            'fields' => [
                'id' => ['type' => 'string'],
                'name' => ['type' => 'string'],
                'kills' => ['type' => 'int'],
                'deaths' => ['type' => 'int'], // deaths by this weapon
                'accuracy' => ['type' => 'float'],
                'shots' => ['type' => 'int'],
                'hits' => ['type' => 'int'],
                'headshots' => ['type' => 'int'],
                'kdr' => ['type' => 'float'],
            ]
        ],

        'weapon_leaderboard' => [
            'type' => 'object',
            'fields' => [
                'total' => ['type' => 'int'],
                'players' => [
                    'type' => 'array_root',
                    'item_schema' => [
                        'type' => 'object',
                        'fields' => [
                            'guid' => ['type' => 'string'],
                            'name' => ['type' => 'string'],
                            'rank' => ['type' => 'int'],
                            'kills' => ['type' => 'int'],
                            'accuracy' => ['type' => 'float'],
                        ]
                    ]
                ]
            ]
        ],

        'map_leaderboard' => [
            'type' => 'object',
            'fields' => [
                'total' => ['type' => 'int'],
                'players' => [
                    'type' => 'array_root',
                    'item_schema' => [
                        'type' => 'object',
                        'fields' => [
                            'guid' => ['type' => 'string'],
                            'name' => ['type' => 'string'],
                            'rank' => ['type' => 'int'],
                            'rounds' => ['type' => 'int'],
                            'kills' => ['type' => 'int'],
                        ]
                    ]
                ]
            ]
        ],

        'match_heatmap' => [
            'type' => 'array_root',
            'item_schema' => [
                'type' => 'object',
                'fields' => [
                    'x' => ['type' => 'float'],
                    'y' => ['type' => 'float'],
                    'value' => ['type' => 'float'],
                ]
            ]
        ],

        // Auth
        'auth_init_claim' => [
            'type' => 'object',
            'fields' => [
                'code' => ['type' => 'string'],
                'expires_in' => ['type' => 'int'],
                'url' => ['type' => 'string'],
            ]
        ],

        'auth_device' => [
            'type' => 'object',
            'fields' => [
                'user_code' => ['type' => 'string'],
                'device_code' => ['type' => 'string'],
                'verification_uri' => ['type' => 'string'],
                'expires_in' => ['type' => 'int'],
            ]
        ],

        'auth_history' => [
            'type' => 'object',
            'fields' => [
                'history' => [
                    'type' => 'array_root',
                    'item_schema' => [
                        'type' => 'object',
                        'fields' => [
                            'attempt_at' => ['type' => 'string'],
                            'server_name' => ['type' => 'string'],
                            'player_ip' => ['type' => 'string'],
                            'success' => ['type' => 'bool'],
                            'failure_reason' => ['type' => 'string'], // can be null, string default ''
                        ]
                    ]
                ]
            ]
        ],

        'auth_trusted_ips' => [
            'type' => 'object',
            'fields' => [
                'trusted_ips' => [
                    'type' => 'array_root',
                    'item_schema' => [
                        'type' => 'object',
                        'fields' => [
                            'id' => ['type' => 'int'],
                            'ip_address' => ['type' => 'string'],
                            'added_at' => ['type' => 'string'],
                            'last_used_at' => ['type' => 'string'],
                        ]
                    ]
                ]
            ]
        ],

        'auth_pending_ips' => [
            'type' => 'object',
            'fields' => [
                'pending_ips' => [
                    'type' => 'array_root',
                    'item_schema' => [
                        'type' => 'object',
                        'fields' => [
                            'id' => ['type' => 'int'],
                            'ip_address' => ['type' => 'string'],
                            'server_name' => ['type' => 'string'],
                            'requested_at' => ['type' => 'string'],
                        ]
                    ]
                ]
            ]
        ],

        'auth_delete_ip' => ['type' => 'object', 'fields' => ['success' => ['type' => 'bool']]],
        'auth_resolve_ip' => ['type' => 'object', 'fields' => ['success' => ['type' => 'bool']]],

        // Extra
        'global_activity' => [
            'type' => 'object',
            'fields' => [
                'activity' => [
                    'type' => 'array_root',
                    'item_schema' => [
                        'type' => 'object',
                        'fields' => [
                            'hour' => ['type' => 'int'],
                            'count' => ['type' => 'int'],
                        ]
                    ]
                ]
            ]
        ],

        'map_popularity' => [
             'type' => 'array_root', // List of maps sorted by popularity?
             'item_schema' => [
                 'type' => 'object',
                 'fields' => [
                     'name' => ['type' => 'string'],
                     'count' => ['type' => 'int'],
                     'percent' => ['type' => 'float'],
                 ]
             ]
        ],

        'achievements_list' => [
            'type' => 'array_root',
            'item_schema' => [
                'type' => 'object',
                'fields' => [
                    'id' => ['type' => 'int'],
                    'name' => ['type' => 'string'],
                    'description' => ['type' => 'string'],
                ]
            ]
        ],

        'achievement_details' => [
            'type' => 'object',
            'fields' => [
                'id' => ['type' => 'int'],
                'name' => ['type' => 'string'],
                'description' => ['type' => 'string'],
                'points' => ['type' => 'int'],
                'icon' => ['type' => 'string'],
            ]
        ],

        'recent_achievements' => [
             'type' => 'object',
             'fields' => [
                 'achievements' => [
                     'type' => 'array_root',
                     'item_schema' => [
                         'type' => 'object',
                         'fields' => [
                             'player_name' => ['type' => 'string'],
                             'achievement_name' => ['type' => 'string'],
                             'unlocked_at' => ['type' => 'string'],
                         ]
                     ]
                 ]
             ]
        ],

        'achievement_leaderboard' => [
            'type' => 'object',
            'fields' => [
                'players' => [
                    'type' => 'array_root',
                    'item_schema' => [
                        'type' => 'object',
                        'fields' => [
                            'name' => ['type' => 'string'],
                            'achievements_count' => ['type' => 'int'],
                            'points' => ['type' => 'int'],
                        ]
                    ]
                ]
            ]
        ],

        'player_performance' => [
            'type' => 'object',
            'fields' => [
                'spm' => ['type' => 'float'],
                'kpm' => ['type' => 'float'],
                'kd_ratio' => ['type' => 'float'],
                'win_loss_ratio' => ['type' => 'float'],
                'accuracy' => ['type' => 'float'],
                'points' => ['type' => 'int'],
                'rounds_played' => ['type' => 'int'],
                'is_vip' => ['type' => 'bool'],
            ]
        ],

        'player_playstyle' => [
            'type' => 'object',
            'fields' => [
                'style' => ['type' => 'string'],
                'spm' => ['type' => 'float'],
                'description' => ['type' => 'string'],
            ]
        ],

        'match_report' => [
            'type' => 'object',
            'fields' => [
                'info' => ['type' => 'object', 'fields' => ['map_name' => ['type' => 'string']]], // minimal
                'stats' => ['type' => 'array_root', 'item_schema' => ['type' => 'object', 'fields' => ['name' => ['type' => 'string']]]],
            ]
        ],

        'leaderboard_cards' => [
            'type' => 'object',
            'fields' => [
                'cards' => [
                    'type' => 'array_root',
                    'item_schema' => [
                        'type' => 'object',
                        'fields' => [
                            'title' => ['type' => 'string'],
                            'player' => ['type' => 'string'],
                            'value' => ['type' => 'string'], // Value can be mixed type, so string safe
                            'stat' => ['type' => 'string'],
                        ]
                    ]
                ]
            ]
        ],
    ];

    public static function validate(string $schemaKey, $data): array
    {
        if (!isset(self::$schemas[$schemaKey])) {
            // If schema missing but we want to be strict, we might return empty array.
            // But to allow flexibility during dev, maybe allow data?
            // "Validation ... intercepts invalid data ... and returns strictly typed default structures"
            // If we don't know the structure, we can't guarantee strict typing.
            // Thus, we should return an empty array or throw error.
            // I'll return empty array to prevent crashes.
            return [];
        }

        return self::applySchema(self::$schemas[$schemaKey], $data);
    }

    private static function applySchema(array $schema, $data)
    {
        $type = $schema['type'] ?? 'string';

        // Null Handling
        if ($data === null) {
            // Apply defaults based on type
            return self::getDefaultValue($type, $schema);
        }

        switch ($type) {
            case 'int':
                return (int)$data;
            case 'float':
                return (float)$data;
            case 'bool':
                return (bool)$data;
            case 'string':
                return (string)$data;

            case 'array':
            case 'object': // Single Object
                if (!is_array($data)) return [];

                // If fields are defined, strict validation
                if (isset($schema['fields'])) {
                    $result = [];
                    foreach ($schema['fields'] as $key => $fieldSchema) {
                        $val = $data[$key] ?? null;
                        $result[$key] = self::applySchema($fieldSchema, $val);
                    }
                    return $result;
                }
                // Otherwise pass-through (generic array/object)
                return $data;

            case 'array_root': // List of items
                if (!is_array($data)) return [];
                $result = [];
                $itemSchema = $schema['item_schema'] ?? ['type' => 'string'];
                foreach ($data as $item) {
                    $result[] = self::applySchema($itemSchema, $item);
                }
                return $result;

            default:
                return $data;
        }
    }

    private static function getDefaultValue(string $type, array $schema)
    {
        if (isset($schema['default'])) return $schema['default'];

        if ($type === 'object' && isset($schema['fields'])) {
            $defaults = [];
            foreach ($schema['fields'] as $key => $fieldSchema) {
                $fieldType = $fieldSchema['type'] ?? 'string';
                $defaults[$key] = self::getDefaultValue($fieldType, $fieldSchema);
            }
            return $defaults;
        }

        return match ($type) {
            'int' => 0,
            'float' => 0.0,
            'bool' => false,
            'string' => '',
            'array', 'array_root' => [],
            'object' => [],
            default => null,
        };
    }
}
