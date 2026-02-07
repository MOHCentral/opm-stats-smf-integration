<?php
declare(strict_types=1);

if (!defined('SMF'))
    die('No direct access...');

class MohaaStatsSchemas
{
    // Basic types
    public const TYPE_INT = 'int';
    public const TYPE_FLOAT = 'float';
    public const TYPE_BOOL = 'bool';
    public const TYPE_STRING = 'string';
    public const TYPE_ARRAY = 'array';

    // Global Stats
    public const GLOBAL_STATS = [
        'total_kills' => ['type' => self::TYPE_INT, 'default' => 0],
        'total_players' => ['type' => self::TYPE_INT, 'default' => 0],
        'total_rounds' => ['type' => self::TYPE_INT, 'default' => 0],
        'total_playtime' => ['type' => self::TYPE_INT, 'default' => 0],
    ];

    // Player Basic
    public const PLAYER_BASIC = [
        'guid' => ['type' => self::TYPE_STRING, 'default' => ''],
        'name' => ['type' => self::TYPE_STRING, 'default' => 'Unknown'],
        'kills' => ['type' => self::TYPE_INT, 'default' => 0],
        'deaths' => ['type' => self::TYPE_INT, 'default' => 0],
        'rounds' => ['type' => self::TYPE_INT, 'default' => 0],
        'accuracy' => ['type' => self::TYPE_FLOAT, 'default' => 0.0],
        'kdr' => ['type' => self::TYPE_FLOAT, 'default' => 0.0],
        'headshots' => ['type' => self::TYPE_INT, 'default' => 0],
        'is_online' => ['type' => self::TYPE_BOOL, 'default' => false],
        'rank' => ['type' => self::TYPE_INT, 'default' => 0],
        'score' => ['type' => self::TYPE_INT, 'default' => 0],
        'wins' => ['type' => self::TYPE_INT, 'default' => 0],
        'losses' => ['type' => self::TYPE_INT, 'default' => 0],
    ];

    // Player Deep
    public const PLAYER_DEEP = [
        'combat' => ['type' => self::TYPE_ARRAY, 'default' => []],
        'movement' => ['type' => self::TYPE_ARRAY, 'default' => []],
        'accuracy' => ['type' => self::TYPE_ARRAY, 'default' => []],
        'session' => ['type' => self::TYPE_ARRAY, 'default' => []],
        'rivals' => ['type' => self::TYPE_ARRAY, 'default' => []],
        'stance' => ['type' => self::TYPE_ARRAY, 'default' => []],
    ];

    // Player Playstyle
    public const PLAYER_PLAYSTYLE = [
        'style' => ['type' => self::TYPE_STRING, 'default' => 'Unknown'],
        'spm' => ['type' => self::TYPE_FLOAT, 'default' => 0.0],
    ];

    // Leaderboard
    public const LEADERBOARD_RESPONSE = [
        'players' => ['type' => 'array_of_PLAYER_BASIC'],
        'total' => ['type' => self::TYPE_INT, 'default' => 0],
        'page' => ['type' => self::TYPE_INT, 'default' => 0],
    ];

    // Matches
    public const MATCH_BASIC = [
        'id' => ['type' => self::TYPE_STRING, 'default' => ''],
        'map_name' => ['type' => self::TYPE_STRING, 'default' => 'Unknown'],
        'server_name' => ['type' => self::TYPE_STRING, 'default' => 'Unknown'],
        'start_time' => ['type' => self::TYPE_INT, 'default' => 0],
        'end_time' => ['type' => self::TYPE_INT, 'default' => 0],
        'duration' => ['type' => self::TYPE_INT, 'default' => 0],
        'winner' => ['type' => self::TYPE_STRING, 'default' => ''],
        'score_axis' => ['type' => self::TYPE_INT, 'default' => 0],
        'score_allies' => ['type' => self::TYPE_INT, 'default' => 0],
    ];

    public const MATCH_LIST = [
        'list' => ['type' => 'array_of_MATCH_BASIC'],
        'total' => ['type' => self::TYPE_INT, 'default' => 0],
    ];

    // Live Match
    public const LIVE_MATCH = [
        'id' => ['type' => self::TYPE_STRING, 'default' => ''],
        'server_name' => ['type' => self::TYPE_STRING, 'default' => 'Unknown'],
        'map_name' => ['type' => self::TYPE_STRING, 'default' => 'Unknown'],
        'players' => ['type' => self::TYPE_INT, 'default' => 0],
        'max_players' => ['type' => self::TYPE_INT, 'default' => 0],
        'time_remaining' => ['type' => self::TYPE_INT, 'default' => 0],
    ];

    // Match Detail
    public const MATCH_DETAIL = [
        'info' => self::MATCH_BASIC,
        'stats' => ['type' => self::TYPE_ARRAY, 'default' => []],
        'heatmap_data' => ['type' => self::TYPE_ARRAY, 'default' => []],
    ];

    // Map Stats
    public const MAP_BASIC = [
        'id' => ['type' => self::TYPE_STRING, 'default' => ''],
        'name' => ['type' => self::TYPE_STRING, 'default' => 'Unknown'],
        'rounds_played' => ['type' => self::TYPE_INT, 'default' => 0],
        'popularity' => ['type' => self::TYPE_FLOAT, 'default' => 0.0],
    ];

    // Map Detail
    public const MAP_DETAIL = [
        'info' => self::MAP_BASIC,
        'heatmap_kills' => ['type' => 'array_of_HEATMAP_POINT'],
        'heatmap_deaths' => ['type' => 'array_of_HEATMAP_POINT'],
    ];

    public const HEATMAP_POINT = [
        'x' => ['type' => self::TYPE_FLOAT, 'default' => 0.0],
        'y' => ['type' => self::TYPE_FLOAT, 'default' => 0.0],
        'value' => ['type' => self::TYPE_FLOAT, 'default' => 0.0],
    ];

    // Weapon Stats
    public const WEAPON_BASIC = [
        'id' => ['type' => self::TYPE_STRING, 'default' => ''],
        'name' => ['type' => self::TYPE_STRING, 'default' => 'Unknown'],
        'kills' => ['type' => self::TYPE_INT, 'default' => 0],
        'usage_percent' => ['type' => self::TYPE_FLOAT, 'default' => 0.0],
        'accuracy' => ['type' => self::TYPE_FLOAT, 'default' => 0.0],
    ];

    // Auth
    public const AUTH_INIT = [
        'code' => ['type' => self::TYPE_STRING, 'default' => ''],
        'expires_in' => ['type' => self::TYPE_INT, 'default' => 0],
        'user_code' => ['type' => self::TYPE_STRING, 'default' => ''],
    ];

    // New Auth Endpoints
    public const LOGIN_HISTORY_ENTRY = [
        'attempt_at' => ['type' => self::TYPE_STRING, 'default' => ''],
        'server_name' => ['type' => self::TYPE_STRING, 'default' => 'Unknown'],
        'player_ip' => ['type' => self::TYPE_STRING, 'default' => ''],
        'success' => ['type' => self::TYPE_BOOL, 'default' => false],
        'failure_reason' => ['type' => self::TYPE_STRING, 'default' => null], // Nullable string
    ];

    public const LOGIN_HISTORY_RESPONSE = [
        'history' => ['type' => 'array_of_LOGIN_HISTORY_ENTRY'],
    ];

    public const TRUSTED_IP_ENTRY = [
        'id' => ['type' => self::TYPE_INT, 'default' => 0],
        'ip_address' => ['type' => self::TYPE_STRING, 'default' => ''],
        'last_used_at' => ['type' => self::TYPE_STRING, 'default' => ''],
    ];

    public const TRUSTED_IPS_RESPONSE = [
        'trusted_ips' => ['type' => 'array_of_TRUSTED_IP_ENTRY'],
    ];

    public const PENDING_IP_ENTRY = [
        'id' => ['type' => self::TYPE_INT, 'default' => 0],
        'requested_at' => ['type' => self::TYPE_STRING, 'default' => ''],
        'server_name' => ['type' => self::TYPE_STRING, 'default' => 'Unknown'],
        'ip_address' => ['type' => self::TYPE_STRING, 'default' => ''],
    ];

    public const PENDING_IPS_RESPONSE = [
        'pending_ips' => ['type' => 'array_of_PENDING_IP_ENTRY'],
    ];

    public const GENERIC_SUCCESS = [
        'success' => ['type' => self::TYPE_BOOL, 'default' => false],
    ];

    // Validation Logic
    public static function validate(mixed $data, array $schema): mixed
    {
        // If data is null/missing, return default structure
        if ($data === null) {
            return self::generateDefault($schema);
        }

        // If this is a leaf node definition (has 'type')
        if (isset($schema['type'])) {
            $type = $schema['type'];

            // Handle array of objects
            if (strpos($type, 'array_of_') === 0) {
                 $subType = substr($type, 9);
                 if (!is_array($data)) return [];

                 $result = [];
                 $subSchema = constant('self::' . strtoupper($subType));
                 foreach ($data as $item) {
                     $result[] = self::validate($item, $subSchema);
                 }
                 return $result;
            }

            // Handle scalar types
            return match($type) {
                self::TYPE_INT => (int)$data,
                self::TYPE_FLOAT => (float)$data,
                self::TYPE_BOOL => (bool)$data,
                self::TYPE_STRING => (string)$data,
                self::TYPE_ARRAY => is_array($data) ? $data : [],
                default => $data,
            };
        }

        // This is a nested object definition (associative array)
        if (!is_array($data)) {
            $data = []; // Reset to empty array if invalid type so we can build default structure
        }

        $result = [];
        foreach ($schema as $key => $definition) {
            $value = $data[$key] ?? null;
            $result[$key] = self::validate($value, $definition);
        }

        return $result;
    }

    private static function generateDefault(array $schema): mixed
    {
        if (isset($schema['type'])) {
             if (isset($schema['default'])) return $schema['default'];

             $type = $schema['type'];
             if (strpos($type, 'array_of_') === 0) return [];

             return match($type) {
                self::TYPE_INT => 0,
                self::TYPE_FLOAT => 0.0,
                self::TYPE_BOOL => false,
                self::TYPE_STRING => '',
                self::TYPE_ARRAY => [],
                default => null,
            };
        }

        $result = [];
        foreach ($schema as $key => $definition) {
            $result[$key] = self::generateDefault($definition);
        }
        return $result;
    }
}
