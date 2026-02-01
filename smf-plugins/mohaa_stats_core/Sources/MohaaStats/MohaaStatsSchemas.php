<?php
/**
 * MOHAA Stats API Schemas
 * Defines strict schemas for API responses to ensure data integrity.
 */

if (!defined('SMF'))
    die('No direct access...');

class MohaaStatsSchemas
{
    public static function validate(mixed $data, string $schemaName): ?array
    {
        if ($data === null) return null;
        if (!is_array($data)) return []; // Enforce array type for schema validation

        $schema = self::getSchema($schemaName);
        if (empty($schema)) {
            // If no schema defined, we cannot validate safely, but to avoid breakage we return data.
            // However, strict rigour implies we should probably warn or filter.
            // For now, let's treat unknown schema as "pass-through but cast known types" logic from before?
            // No, the instruction is strict. If no schema, we assume generic object or array?
            // Let's return the data as is if schema not found, but we should define all schemas.
            return $data;
        }

        // Handle Array Root (List responses)
        if (isset($schema['type']) && $schema['type'] === 'array_root') {
            if (!is_array($data)) return [];
            $validatedList = [];
            $itemSchema = $schema['array_item_schema'] ?? '';
            foreach ($data as $item) {
                if ($itemSchema) {
                    $vItem = self::validate($item, $itemSchema);
                    if ($vItem !== null) {
                        $validatedList[] = $vItem;
                    }
                } else {
                    $validatedList[] = $item;
                }
            }
            return $validatedList;
        }

        $validated = [];

        // 1. Check Required Fields
        if (isset($schema['required'])) {
            foreach ($schema['required'] as $reqField) {
                if (!array_key_exists($reqField, $data)) {
                    // Critical failure: missing required field.
                    // Return null to indicate invalid response.
                    return null;
                }
            }
        }

        // 2. Validate and Cast Fields
        if (isset($schema['fields'])) {
            foreach ($schema['fields'] as $field => $type) {
                if (!array_key_exists($field, $data)) {
                    // Optional field missing: set default
                    $validated[$field] = self::getDefault($type);
                    continue;
                }

                $value = $data[$field];

                if (is_array($type)) {
                     // Inline nested schema definition (rare but possible)
                     // Not implemented for simplicity, use schema names
                     $validated[$field] = $value;
                } elseif ($type === 'array') {
                    if (!is_array($value)) {
                        $validated[$field] = [];
                    } else {
                        if (isset($schema['array_item_schema'])) {
                            $validated[$field] = [];
                            foreach ($value as $item) {
                                // Recursively validate items
                                $vItem = self::validate($item, $schema['array_item_schema']);
                                if ($vItem !== null) {
                                    $validated[$field][] = $vItem;
                                }
                            }
                        } else {
                            $validated[$field] = $value; // No schema for items, pass through
                        }
                    }
                } elseif ($type === 'int') {
                    $validated[$field] = (int)$value;
                } elseif ($type === 'float') {
                    $validated[$field] = (float)$value;
                } elseif ($type === 'string') {
                    $validated[$field] = (string)$value;
                } elseif ($type === 'bool') {
                    $validated[$field] = (bool)$value;
                } else {
                    // Type is another schema name (nested object)
                    if (is_array($value)) {
                        $vNested = self::validate($value, $type);
                        $validated[$field] = $vNested ?? self::getDefault('array'); // If nested invalid, return empty array?
                        // Or if it was supposed to be an object, we should probably return a default object structure.
                        // But since arrays are used for objects in PHP usually, empty array is safe.
                        // Actually, if it's a specific schema, we might want default values for that schema.
                        if ($vNested === null) {
                             // If nested validation failed (e.g. missing required),
                             // we can either fail whole or provide partial.
                             // Let's provide an empty structure of that type if possible, or just []
                             $validated[$field] = [];
                        }
                    } else {
                         // Expected object (schema) but got something else
                         $validated[$field] = [];
                    }
                }
            }
        }

        // Pass through extra fields? No, strict schema means only defined fields.
        // If we want to allow extra fields, we would merge $data into $validated.
        // User said "Schema Validation: Ensure the plugin code matches the current JSON response structure".
        // Usually implies strictness. I will only return fields defined in schema to prevent pollution.

        return $validated;
    }

    private static function getDefault(string $type)
    {
        return match($type) {
            'int' => 0,
            'float' => 0.0,
            'string' => '',
            'bool' => false,
            'array' => [],
            default => [] // For nested schemas
        };
    }

    private static function getSchema(string $name): array
    {
        static $schemas = null;
        if ($schemas === null) {
            $schemas = [
                'global_stats' => [
                    'fields' => ['total_kills' => 'int', 'total_players' => 'int', 'total_matches' => 'int'],
                    'required' => []
                ],
                'leaderboard_item' => [
                    'fields' => [
                        'rank' => 'int', 'name' => 'string', 'guid' => 'string', 'kills' => 'int', 'deaths' => 'int',
                        'kdr' => 'float', 'wins' => 'int', 'losses' => 'int', 'score' => 'int', 'time_played' => 'int',
                        'accuracy' => 'float', 'headshots' => 'int'
                    ],
                    'required' => ['guid'] // minimal requirement
                ],
                'leaderboard' => [
                    'fields' => ['players' => 'array', 'total' => 'int', 'page' => 'int', 'limit' => 'int'],
                    'array_item_schema' => 'leaderboard_item',
                    'required' => ['players']
                ],
                'leaderboard_count' => [
                    'fields' => ['total' => 'int'],
                    'required' => ['total']
                ],
                'player_stats' => [
                    'fields' => [
                        'name' => 'string', 'guid' => 'string', 'kills' => 'int', 'deaths' => 'int', 'kdr' => 'float',
                        'wins' => 'int', 'losses' => 'int', 'wl_ratio' => 'float', 'accuracy' => 'float',
                        'headshots' => 'int', 'rounds_played' => 'int', 'time_played' => 'int', 'score' => 'int',
                        'spm' => 'float', 'rank' => 'int', 'is_vip' => 'bool', 'is_online' => 'bool',
                        'last_seen' => 'string'
                    ],
                    'required' => ['guid']
                ],
                'player_deep_stats' => [
                    'fields' => ['combat' => 'array', 'movement' => 'array', 'weapons' => 'array', 'awards' => 'array'],
                    'required' => []
                ],
                'weapon_stats' => [
                    'fields' => ['name' => 'string', 'id' => 'string', 'kills' => 'int', 'shots' => 'int', 'hits' => 'int', 'accuracy' => 'float', 'damage' => 'int'],
                    'required' => ['name']
                ],
                'player_weapons' => [
                     // It's a list of weapons directly? Or {weapons: []}?
                     // MohaaStatsAPI used getPlayerWeapons -> /stats/player/.../weapons.
                     // Mock server returns []. So it's an array of weapon stats.
                     // But our validate function expects an object (assoc array) as root usually.
                     // If the root response is a list, we need to handle that.
                     // The current `validate` logic assumes $data is an assoc array with keys matching fields.
                     // If root is array of items, we need a "root_array" concept or wrapper.
                     // Let's assume the API returns objects. If it returns list, we might need a special handler or schema type.
                     // Checking Mock API: response([]). It returns a JSON array.
                     // My `validate` logic `if (isset($schema['fields']))` expects fields.
                     // I need to support root-level arrays.
                     'type' => 'array_root',
                     'array_item_schema' => 'weapon_stats'
                ],
                'match_summary' => [
                    'fields' => ['id' => 'string', 'map' => 'string', 'time' => 'string', 'players' => 'int', 'winner' => 'string', 'score' => 'string'],
                    'required' => ['id']
                ],
                'matches_list' => [
                    'fields' => ['list' => 'array', 'total' => 'int'],
                    'array_item_schema' => 'match_summary',
                    'required' => ['list']
                ],
                'match_count' => [
                    'fields' => ['total' => 'int'],
                    'required' => ['total']
                ],
                'match_info' => [
                    'fields' => ['map_name' => 'string', 'time_start' => 'string', 'time_end' => 'string', 'duration' => 'int', 'winner' => 'string'],
                    'required' => []
                ],
                'match_details' => [
                    'fields' => ['info' => 'match_info', 'stats' => 'array', 'teams' => 'array'],
                    'required' => []
                ],
                'live_matches' => [
                    'type' => 'array_root',
                    'array_item_schema' => 'match_summary' // reusing match_summary
                ],
                'map_stats_item' => [
                     'fields' => ['name' => 'string', 'id' => 'string', 'matches' => 'int', 'popularity' => 'float'],
                     'required' => ['id']
                ],
                'map_stats' => [
                    'type' => 'array_root',
                    'array_item_schema' => 'map_stats_item'
                ],
                'map_details' => [
                    'fields' => ['name' => 'string', 'id' => 'string', 'description' => 'string', 'image' => 'string'],
                    'required' => ['id']
                ],
                'heatmap_point' => [
                    'fields' => ['x' => 'float', 'y' => 'float', 'value' => 'float'],
                    'required' => ['x', 'y']
                ],
                'map_heatmap' => [
                    'type' => 'array_root',
                    'array_item_schema' => 'heatmap_point'
                ],
                'simple_string_item' => [
                    'fields' => ['name' => 'string', 'id' => 'string'], // Generic
                    'required' => []
                ],
                'maps_list' => [
                    'type' => 'array_root',
                    'array_item_schema' => 'simple_string_item'
                ],
                'weapons_list' => [
                    'type' => 'array_root',
                    'array_item_schema' => 'simple_string_item'
                ],
                'weapon_leaderboard' => [
                    'fields' => ['players' => 'array', 'total' => 'int'],
                    'array_item_schema' => 'leaderboard_item',
                    'required' => ['players']
                ],
                'map_leaderboard' => [
                    'fields' => ['players' => 'array', 'total' => 'int'],
                    'array_item_schema' => 'leaderboard_item',
                    'required' => ['players']
                ],
                'match_heatmap' => [
                    'type' => 'array_root',
                    'array_item_schema' => 'heatmap_point'
                ],
                'claim_init' => [
                    'fields' => ['code' => 'string', 'expires_in' => 'int'],
                    'required' => ['code']
                ],
                'device_auth' => [
                    'fields' => ['user_code' => 'string', 'expires_in' => 'int'],
                    'required' => ['user_code']
                ],
                'login_attempt' => [
                    'fields' => ['attempt_at' => 'string', 'server_name' => 'string', 'player_ip' => 'string', 'success' => 'bool', 'failure_reason' => 'string'],
                    'required' => ['attempt_at']
                ],
                'login_history' => [
                    'fields' => ['history' => 'array'],
                    'array_item_schema' => 'login_attempt',
                    'required' => ['history']
                ],
                'trusted_ip' => [
                    'fields' => ['id' => 'int', 'ip_address' => 'string', 'last_used_at' => 'string'],
                    'required' => ['id', 'ip_address']
                ],
                'trusted_ips' => [
                    'fields' => ['trusted_ips' => 'array'],
                    'array_item_schema' => 'trusted_ip',
                    'required' => ['trusted_ips']
                ],
                'pending_ip' => [
                    'fields' => ['id' => 'int', 'requested_at' => 'string', 'server_name' => 'string', 'ip_address' => 'string'],
                    'required' => ['id']
                ],
                'pending_ips' => [
                    'fields' => ['pending_ips' => 'array'],
                    'array_item_schema' => 'pending_ip',
                    'required' => ['pending_ips']
                ],
                'simple_success' => [
                    'fields' => ['success' => 'bool'],
                    'required' => []
                ],
                'global_activity' => [
                    'fields' => ['activity' => 'array'],
                    'required' => []
                ],
                'map_popularity' => [
                    'type' => 'array_root',
                    'array_item_schema' => 'map_stats_item'
                ],
                'achievement_item' => [
                    'fields' => ['id' => 'int', 'name' => 'string', 'description' => 'string', 'points' => 'int', 'icon' => 'string'],
                    'required' => ['id']
                ],
                'achievements_list' => [
                    'type' => 'array_root',
                    'array_item_schema' => 'achievement_item'
                ],
                'achievement_details' => [
                    'fields' => ['id' => 'int', 'name' => 'string', 'description' => 'string', 'points' => 'int', 'icon' => 'string', 'players_earned' => 'int'],
                    'required' => ['id']
                ],
                'recent_achievements' => [
                     'fields' => ['achievements' => 'array'],
                     'array_item_schema' => 'achievement_item'
                ],
                'achievement_leaderboard' => [
                    'fields' => ['players' => 'array'],
                    'array_item_schema' => 'leaderboard_item'
                ],
                'player_performance' => [
                    'fields' => ['spm' => 'float', 'kpm' => 'float', 'kd_ratio' => 'float', 'points' => 'int', 'rounds_played' => 'int', 'win_loss_ratio' => 'float', 'is_vip' => 'bool'],
                    'required' => []
                ],
                'player_playstyle' => [
                    'fields' => ['style' => 'string', 'spm' => 'float', 'class' => 'string'],
                    'required' => []
                ],
                'match_report' => [
                     'fields' => ['info' => 'match_info', 'stats' => 'array'],
                     'required' => []
                ],
                'card_item' => [
                    'fields' => ['title' => 'string', 'player' => 'string', 'value' => 'string', 'guid' => 'string'],
                    'required' => ['title']
                ],
                'leaderboard_cards' => [
                    'fields' => ['cards' => 'array'],
                    'array_item_schema' => 'card_item',
                    'required' => ['cards']
                ],
            ];
        }
        return $schemas[$name] ?? [];
    }
}
