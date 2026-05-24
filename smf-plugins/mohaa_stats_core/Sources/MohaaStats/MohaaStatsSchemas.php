<?php
/**
 * MOHAA Stats API Schemas
 *
 * STRICT validation schemas for the OpenMOHAA Stats API.
 * Ensures data integrity and type safety before data touches the application.
 */

if (!defined('SMF'))
    die('No direct access...');

class MohaaStatsSchemas
{
    public static function get(string $endpoint): array
    {
        $schemas = [
            'global_stats' => [
                'type' => 'object',
                'properties' => [
                    'total_kills' => ['type' => 'int'],
                    'total_players' => ['type' => 'int'],
                    'active_servers' => ['type' => 'int', 'required' => false],
                    'total_matches' => ['type' => 'int', 'required' => false],
                ]
            ],
            'leaderboard' => [
                'type' => 'object',
                'properties' => [
                    'players' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'name' => ['type' => 'string'],
                                'rank' => ['type' => 'int'],
                                'kills' => ['type' => 'int'],
                                'deaths' => ['type' => 'int'],
                                'kdr' => ['type' => 'float'],
                                'accuracy' => ['type' => 'float'],
                                'headshots' => ['type' => 'int'],
                                'rounds' => ['type' => 'int'],
                                'wins' => ['type' => 'int'],
                                'losses' => ['type' => 'int'],
                                'guid' => ['type' => 'string', 'required' => false],
                            ]
                        ]
                    ],
                    'total' => ['type' => 'int'],
                    'page' => ['type' => 'int'],
                ]
            ],
            'player_info' => [
                'type' => 'object',
                'properties' => [
                    'guid' => ['type' => 'string'],
                    'name' => ['type' => 'string'],
                    'kills' => ['type' => 'int'],
                    'deaths' => ['type' => 'int'],
                    'kdr' => ['type' => 'float'],
                    'accuracy' => ['type' => 'float'],
                    'wins' => ['type' => 'int'],
                    'losses' => ['type' => 'int'],
                    'rounds' => ['type' => 'int'],
                    'time_played' => ['type' => 'int'],
                    'is_online' => ['type' => 'bool', 'required' => false],
                    'is_vip' => ['type' => 'bool', 'required' => false],
                ]
            ],
            'player_deep' => [
                'type' => 'object',
                'properties' => [
                    'combat' => ['type' => 'object', 'required' => false],
                    'movement' => ['type' => 'object', 'required' => false],
                    'accuracy' => ['type' => 'object', 'required' => false],
                    'session' => ['type' => 'object', 'required' => false],
                    'rivals' => ['type' => 'array', 'required' => false],
                    'stance' => ['type' => 'object', 'required' => false],
                ]
            ],
            'player_weapons' => [
                'type' => 'array',
                'items' => [
                    'type' => 'object',
                    'properties' => [
                        'name' => ['type' => 'string'],
                        'kills' => ['type' => 'int'],
                        'shots' => ['type' => 'int'],
                        'hits' => ['type' => 'int'],
                        'accuracy' => ['type' => 'float'],
                        'headshots' => ['type' => 'int'],
                        'damage' => ['type' => 'float', 'required' => false],
                    ]
                ]
            ],
            'player_matches' => [
                'type' => 'object',
                'properties' => [
                    'list' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'id' => ['type' => 'string'],
                                'map_name' => ['type' => 'string'],
                                'server_name' => ['type' => 'string'],
                                'date' => ['type' => 'string'],
                                'kills' => ['type' => 'int'],
                                'deaths' => ['type' => 'int'],
                                'result' => ['type' => 'string'],
                            ]
                        ]
                    ],
                    'total' => ['type' => 'int'],
                ]
            ],
            'player_playstyle' => [
                'type' => 'object',
                'properties' => [
                    'style' => ['type' => 'string'],
                    'spm' => ['type' => 'float'],
                    'kpm' => ['type' => 'float'],
                ]
            ],
            'player_performance' => [
                'type' => 'object',
                'properties' => [
                    'spm' => ['type' => 'float', 'required' => false],
                    'kpm' => ['type' => 'float', 'required' => false],
                    'kd_ratio' => ['type' => 'float', 'required' => false],
                    'win_loss_ratio' => ['type' => 'float', 'required' => false],
                    'points' => ['type' => 'int', 'required' => false],
                    'rounds_played' => ['type' => 'int', 'required' => false],
                    'is_vip' => ['type' => 'bool', 'required' => false],
                ]
            ],
            'recent_matches' => [
                'type' => 'object',
                'properties' => [
                    'list' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'id' => ['type' => 'string'],
                                'map' => ['type' => 'string'],
                                'server' => ['type' => 'string'],
                                'players' => ['type' => 'int'],
                                'time' => ['type' => 'string'],
                            ]
                        ]
                    ],
                    'total' => ['type' => 'int'],
                ]
            ],
            'live_matches' => [
                'type' => 'array',
                'items' => [
                    'type' => 'object',
                    'properties' => [
                        'server_name' => ['type' => 'string'],
                        'map_name' => ['type' => 'string'],
                        'players_count' => ['type' => 'int'],
                        'max_players' => ['type' => 'int'],
                        'ip' => ['type' => 'string', 'required' => false],
                        'port' => ['type' => 'int', 'required' => false],
                    ]
                ]
            ],
            'map_stats' => [
                'type' => 'array',
                'items' => [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'string'],
                        'name' => ['type' => 'string'],
                        'count' => ['type' => 'int'],
                    ]
                ]
            ],
            'map_detail' => [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'string'],
                    'name' => ['type' => 'string'],
                    'matches' => ['type' => 'int', 'required' => false],
                    'total_time' => ['type' => 'int', 'required' => false],
                ]
            ],
            'heatmap' => [
                'type' => 'array',
                'items' => [
                    'type' => 'object',
                    'properties' => [
                        'x' => ['type' => 'float'],
                        'y' => ['type' => 'float'],
                        'value' => ['type' => 'float', 'required' => false],
                    ]
                ]
            ],
            'match_detail' => [
                'type' => 'object',
                'properties' => [
                    'info' => [
                        'type' => 'object',
                        'properties' => [
                            'map_name' => ['type' => 'string'],
                            'server_name' => ['type' => 'string', 'required' => false],
                            'date' => ['type' => 'string', 'required' => false],
                            'duration' => ['type' => 'int', 'required' => false],
                        ]
                    ],
                    'stats' => ['type' => 'array', 'required' => false],
                    'heatmap_data' => ['type' => 'object', 'required' => false]
                ]
            ],
            'weapon_stats' => [
                'type' => 'object',
                'properties' => [
                    'name' => ['type' => 'string'],
                    'kills' => ['type' => 'int'],
                    'accuracy' => ['type' => 'float', 'required' => false],
                    'headshots' => ['type' => 'int', 'required' => false],
                    'image' => ['type' => 'string', 'required' => false],
                ]
            ],
            'achievement_detail' => [
                 'type' => 'object',
                 'properties' => [
                     'id' => ['type' => 'int'],
                     'name' => ['type' => 'string'],
                     'description' => ['type' => 'string', 'required' => false],
                     'points' => ['type' => 'int', 'required' => false],
                 ]
            ],
             // Auth
            'auth_claim' => [
                'type' => 'object',
                'properties' => [
                    'code' => ['type' => 'string'],
                    'expires_in' => ['type' => 'int'],
                ]
            ],
            'auth_device' => [
                'type' => 'object',
                'properties' => [
                    'user_code' => ['type' => 'string'],
                    'expires_in' => ['type' => 'int'],
                ]
            ],
            'auth_history' => [
                'type' => 'object',
                'properties' => [
                    'history' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'attempt_at' => ['type' => 'string'],
                                'server_name' => ['type' => 'string'],
                                'player_ip' => ['type' => 'string'],
                                'success' => ['type' => 'bool'],
                                'failure_reason' => ['type' => 'string', 'nullable' => true, 'required' => false],
                            ]
                        ]
                    ]
                ]
            ],
            'trusted_ips' => [
                'type' => 'object',
                'properties' => [
                    'trusted_ips' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'id' => ['type' => 'int'],
                                'ip_address' => ['type' => 'string'],
                                'last_used_at' => ['type' => 'string'],
                            ]
                        ]
                    ]
                ]
            ],
            'pending_ips' => [
                'type' => 'object',
                'properties' => [
                    'pending_ips' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'id' => ['type' => 'int'],
                                'ip_address' => ['type' => 'string'],
                                'requested_at' => ['type' => 'string'],
                                'server_name' => ['type' => 'string'],
                            ]
                        ]
                    ]
                ]
            ],
            'generic_success' => [
                'type' => 'object',
                'properties' => [
                    'success' => ['type' => 'bool']
                ]
            ],
            'server_activity' => [
                'type' => 'object',
                'properties' => [
                    'activity' => ['type' => 'array', 'items' => ['type' => 'object']]
                ]
            ],
            'leaderboard_cards' => [
                'type' => 'object',
                'properties' => [
                    'cards' => ['type' => 'array', 'items' => ['type' => 'object']]
                ]
            ],
             // Fallback/Generic
            'any_array' => [
                'type' => 'array',
                'items' => ['type' => 'any']
            ],
            // Removed any_object in favor of strict schemas, or define it with allow_extra logic?
            // If we keep any_object as "permissive", validation logic needs update.
            // But strict is better. I have replaced usages with specific schemas.
        ];

        return $schemas[$endpoint] ?? ['type' => 'any'];
    }

    public static function validate(mixed $data, array $schema): mixed
    {
        // Handle nulls
        if ($data === null) {
            if (!empty($schema['nullable'])) {
                return null;
            }
            return self::getDefault($schema['type'] ?? 'string');
        }

        $type = $schema['type'] ?? 'string';

        if ($type === 'any') {
            return $data;
        }

        if ($type === 'int') {
            return (int)$data;
        }

        if ($type === 'float') {
            return (float)$data;
        }

        if ($type === 'string') {
            return (string)$data;
        }

        if ($type === 'bool') {
            return (bool)$data;
        }

        if ($type === 'array') {
            if (!is_array($data)) {
                return [];
            }

            if (isset($schema['items'])) {
                $result = [];
                $itemSchema = $schema['items'];
                // If itemSchema is 'any', we preserve the item as is?
                // validate will handle 'any'.
                foreach ($data as $item) {
                    $result[] = self::validate($item, $itemSchema);
                }
                return $result;
            }

            return $data;
        }

        if ($type === 'object') {
            if (!is_array($data)) {
                return [];
            }

            $result = [];
            $properties = $schema['properties'] ?? [];

            // Validate defined properties
            foreach ($properties as $key => $propSchema) {
                if (array_key_exists($key, $data)) {
                    $result[$key] = self::validate($data[$key], $propSchema);
                } else {
                    if (($propSchema['required'] ?? true) === false) {
                        continue;
                    } else {
                        $result[$key] = self::getDefault($propSchema['type'] ?? 'string');
                    }
                }
            }

            // STRICT MODE: We only return what is in properties.
            // If properties is empty (like old any_object), it returns empty array.

            return $result;
        }

        return $data;
    }

    private static function getDefault(string $type): mixed
    {
        return match($type) {
            'int' => 0,
            'float' => 0.0,
            'bool' => false,
            'array' => [],
            'object' => [],
            default => '',
        };
    }
}
