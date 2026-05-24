<?php
/**
 * MOHAA Stats Schemas
 *
 * Centralized schema definitions for API responses.
 * Enforces strict typing and data integrity.
 */

if (!defined('SMF'))
    die('No direct access...');

class MohaaStatsSchemas
{
    /**
     * Main validation entry point
     */
    public static function validate(string $schemaType, $data): array
    {
        if (!is_array($data)) {
            // If data is null or not an array, return default for the schema
            return self::getDefault($schemaType);
        }

        $schema = self::getSchema($schemaType);
        if (empty($schema)) {
            return $data; // No schema defined, return as is (or empty array?)
        }

        return self::applySchema($schema, $data);
    }

    /**
     * Apply schema recursively
     */
    private static function applySchema(array $schema, array $data): array
    {
        $cleanData = [];

        foreach ($schema as $key => $definition) {
            $type = $definition['type'] ?? 'string';
            $default = $definition['default'] ?? null;
            $value = $data[$key] ?? $default;

            // Handle missing required fields by using default
            if (!array_key_exists($key, $data) && $value === null) {
                // If no default provided, determine based on type
                $value = self::getTypeDefault($type);
            }

            // Recursive validation for nested arrays/lists
            if ($type === 'schema') {
                $subSchemaType = $definition['schema_type'] ?? '';
                if ($value === null || !is_array($value)) {
                    $value = self::getDefault($subSchemaType);
                } else {
                    $value = self::validate($subSchemaType, $value);
                }
            } elseif ($type === 'list') {
                $itemSchemaType = $definition['schema_type'] ?? '';
                if (!is_array($value)) {
                    $value = [];
                }
                $cleanList = [];
                foreach ($value as $item) {
                    if (is_array($item)) {
                        $cleanList[] = self::validate($itemSchemaType, $item);
                    }
                }
                $value = $cleanList;
            } else {
                // Strict Casting
                $value = self::castValue($value, $type);
            }

            $cleanData[$key] = $value;
        }

        return $cleanData;
    }

    /**
     * Get default value for a schema type (usually empty array)
     */
    private static function getDefault(string $schemaType): array
    {
        // For object-like schemas, we could return a structure with defaults.
        // But for simplicity, we return empty array and let the validator fill defaults
        // when applied to an empty array.
        $schema = self::getSchema($schemaType);
        if ($schema) {
            return self::applySchema($schema, []);
        }
        return [];
    }

    /**
     * Get default value for a primitive type
     */
    private static function getTypeDefault(string $type)
    {
        return match ($type) {
            'int' => 0,
            'float' => 0.0,
            'bool' => false,
            'array', 'list', 'schema' => [],
            default => '',
        };
    }

    /**
     * Strict casting
     */
    private static function castValue($value, string $type)
    {
        if ($value === null) {
            return self::getTypeDefault($type);
        }

        return match ($type) {
            'int' => (int)$value,
            'float' => (float)$value,
            'bool' => (bool)$value,
            'string' => (string)$value,
            'array' => (array)$value,
            default => $value,
        };
    }

    /**
     * Schema Definitions
     */
    private static function getSchema(string $type): array
    {
        $schemas = [
            // Global Stats
            'GlobalStats' => [
                'total_kills' => ['type' => 'int'],
                'total_players' => ['type' => 'int'],
                'total_matches' => ['type' => 'int'],
                'total_playtime' => ['type' => 'int'], // seconds
                'active_servers' => ['type' => 'int'],
                'active_players_24h' => ['type' => 'int'],
            ],

            // Player Basic
            'PlayerStats' => [
                'id' => ['type' => 'int'], // Internal DB ID if any
                'name' => ['type' => 'string'],
                'guid' => ['type' => 'string'],
                'rank' => ['type' => 'int'],
                'score' => ['type' => 'int'],
                'kills' => ['type' => 'int'],
                'deaths' => ['type' => 'int'],
                'assists' => ['type' => 'int'],
                'rounds_played' => ['type' => 'int'],
                'time_played' => ['type' => 'int'],
                'wins' => ['type' => 'int'],
                'losses' => ['type' => 'int'],
                'draws' => ['type' => 'int'],
                'accuracy' => ['type' => 'float'],
                'spm' => ['type' => 'float'],
                'kdr' => ['type' => 'float'],
                'kpm' => ['type' => 'float'],
                'win_loss_ratio' => ['type' => 'float'],
                'headshots' => ['type' => 'int'],
                'suicides' => ['type' => 'int'],
                'team_kills' => ['type' => 'int'],
                'is_online' => ['type' => 'bool'],
                'is_active' => ['type' => 'bool'],
                'is_vip' => ['type' => 'bool'],
                'last_seen' => ['type' => 'string'],
                'joined_at' => ['type' => 'string'],
            ],

            // Leaderboard
            'Leaderboard' => [
                'total' => ['type' => 'int'],
                'page' => ['type' => 'int'],
                'limit' => ['type' => 'int'],
                'players' => ['type' => 'list', 'schema_type' => 'PlayerStats'],
                'period' => ['type' => 'string'],
                'stat' => ['type' => 'string'],
            ],

            // Matches List
            'MatchSummary' => [
                'id' => ['type' => 'string'],
                'map_name' => ['type' => 'string'],
                'server_name' => ['type' => 'string'],
                'gametype' => ['type' => 'string'],
                'start_time' => ['type' => 'string'],
                'end_time' => ['type' => 'string'],
                'duration' => ['type' => 'int'],
                'players_count' => ['type' => 'int'],
                'winner' => ['type' => 'string'], // Team name or guid
                'is_live' => ['type' => 'bool'],
            ],

            'MatchList' => [
                'total' => ['type' => 'int'],
                'list' => ['type' => 'list', 'schema_type' => 'MatchSummary'],
            ],

            // Match Detail
            'MatchDetail' => [
                'info' => ['type' => 'schema', 'schema_type' => 'MatchSummary'],
                'stats' => ['type' => 'list', 'schema_type' => 'PlayerMatchStats'], // Detailed stats per player in match
                // Add teams etc if needed
            ],

            'PlayerMatchStats' => [
                'name' => ['type' => 'string'],
                'guid' => ['type' => 'string'],
                'team' => ['type' => 'string'],
                'kills' => ['type' => 'int'],
                'deaths' => ['type' => 'int'],
                'score' => ['type' => 'int'],
                'ping' => ['type' => 'int'],
            ],

            // Map Stats
            'MapStats' => [
                'id' => ['type' => 'string'],
                'name' => ['type' => 'string'],
                'total_matches' => ['type' => 'int'],
                'total_playtime' => ['type' => 'int'],
                'average_duration' => ['type' => 'float'],
                'popularity' => ['type' => 'float'], // %
            ],

            'MapStatsList' => [
                 // Often API returns array of maps directly or wrapped.
                 // Assuming list of MapStats for getMapStats based on usage
            ],

            // Weapon Stats
            'WeaponStats' => [
                'id' => ['type' => 'string'],
                'name' => ['type' => 'string'],
                'category' => ['type' => 'string'],
                'kills' => ['type' => 'int'],
                'shots' => ['type' => 'int'],
                'hits' => ['type' => 'int'],
                'accuracy' => ['type' => 'float'],
                'headshots' => ['type' => 'int'],
                'damage' => ['type' => 'float'],
            ],

            // Server Activity
            'ActivityPoint' => [
                'hour' => ['type' => 'int'],
                'count' => ['type' => 'int'],
                'avg_players' => ['type' => 'float'],
            ],

            'ServerActivity' => [
                'activity' => ['type' => 'list', 'schema_type' => 'ActivityPoint'],
            ],

            // Auth
            'LoginHistoryItem' => [
                'attempt_at' => ['type' => 'string'],
                'server_name' => ['type' => 'string'],
                'player_ip' => ['type' => 'string'],
                'success' => ['type' => 'bool'],
                'failure_reason' => ['type' => 'string'],
            ],
            'LoginHistory' => [
                'history' => ['type' => 'list', 'schema_type' => 'LoginHistoryItem'],
            ],

            'TrustedIPItem' => [
                'id' => ['type' => 'int'],
                'ip_address' => ['type' => 'string'],
                'last_used_at' => ['type' => 'string'],
                'added_at' => ['type' => 'string'],
            ],
            'TrustedIPs' => [
                'trusted_ips' => ['type' => 'list', 'schema_type' => 'TrustedIPItem'],
            ],

            'PendingIPItem' => [
                'id' => ['type' => 'int'],
                'requested_at' => ['type' => 'string'],
                'server_name' => ['type' => 'string'],
                'ip_address' => ['type' => 'string'],
            ],
            'PendingIPs' => [
                'pending_ips' => ['type' => 'list', 'schema_type' => 'PendingIPItem'],
            ],

            // Generic Responses
            'ClaimInit' => [
                'code' => ['type' => 'string'],
                'expires_in' => ['type' => 'int'],
            ],
            'DeviceAuth' => [
                'user_code' => ['type' => 'string'],
                'verification_url' => ['type' => 'string'],
                'expires_in' => ['type' => 'int'],
            ],
            'GenericSuccess' => [
                'success' => ['type' => 'bool'],
                'message' => ['type' => 'string'],
            ],

             // Leaderboard Cards
            'LeaderboardCard' => [
                'title' => ['type' => 'string'],
                'player' => ['type' => 'string'], // Name
                'guid' => ['type' => 'string'],
                'value' => ['type' => 'string'], // Display value
                'avatar' => ['type' => 'string'],
            ],
            'LeaderboardCards' => [
                'cards' => ['type' => 'list', 'schema_type' => 'LeaderboardCard'],
            ],
        ];

        return $schemas[$type] ?? [];
    }
}
