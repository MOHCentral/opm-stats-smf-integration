<?php
/**
 * MOHAA Stats API Client - OPTIMIZED with parallel requests
 */

if (!defined('SMF'))
    die('No direct access...');

class MohaaStatsAPIClient
{
    private string $baseUrl;
    private string $serverToken;
    private int $cacheDuration;
    private int $timeout;
    
    // Schema Definitions for Validation & Type Casting
    private array $schemas = [
        'GlobalStats' => [
            'total_kills' => 'int', 'total_players' => 'int', 'total_matches' => 'int',
            'total_headshots' => 'int', 'total_achievements_unlocked' => 'int'
        ],
        'Leaderboard' => [
            'total' => 'int', 'page' => 'int', 'limit' => 'int',
            'players' => ['type' => 'array', 'items' => [
                'name' => 'string', 'guid' => 'string', 'kills' => 'int', 'deaths' => 'int',
                'kd' => 'float', 'headshots' => 'int', 'accuracy' => 'float', 'value' => 'float',
                'rank' => 'int', 'points' => 'int'
            ]]
        ],
        'Player' => [
             'name' => 'string', 'guid' => 'string', 'rank' => 'int', 'kills' => 'int', 'deaths' => 'int', 'kd' => 'float',
             'headshots' => 'int', 'accuracy' => 'float', 'matches' => 'int', 'last_active' => 'int',
             'verified' => 'bool'
        ],
        'MatchList' => [
            'total' => 'int',
            'list' => ['type' => 'array', 'items' => [
                'id' => 'string', 'map_name' => 'string', 'game_mode' => 'string',
                'player_count' => 'int', 'end_time' => 'int'
            ]]
        ],
        'MatchDetail' => [
             'info' => [
                 'id' => 'string', 'map_name' => 'string', 'game_mode' => 'string', 'player_count' => 'int', 'end_time' => 'int'
             ],
             'heatmap_data' => ['type' => 'array', 'items' => ['x' => 'int', 'y' => 'int', 'count' => 'int']]
        ],
        'LiveMatches' => [
             'type' => 'array', 'items' => [
                 'server_name' => 'string', 'map_name' => 'string', 'player_count' => 'int', 'max_players' => 'int',
                 'allies_score' => 'int', 'axis_score' => 'int', 'team_match' => 'bool'
             ]
        ],
        'MapStats' => [
             'type' => 'array', 'items' => [
                 'id' => 'string', 'name' => 'string', 'count' => 'int'
             ]
        ],
        'MapDetail' => [
             'id' => 'string', 'name' => 'string', 'count' => 'int'
        ],
        'MapsList' => ['type' => 'array', 'items' => ['id' => 'string', 'name' => 'string']],
        'WeaponsList' => ['type' => 'array', 'items' => ['id' => 'string', 'name' => 'string']],
        'WeaponStats' => ['id' => 'string', 'name' => 'string', 'kills' => 'int', 'headshots' => 'int', 'accuracy' => 'float'],
        'AuthToken' => ['user_code' => 'string', 'expires_in' => 'int'],
        'AuthClaim' => ['code' => 'string', 'expires_in' => 'int'],
        'ServerActivity' => ['type' => 'array', 'items' => ['time' => 'int', 'count' => 'int']],
        'MapPopularity' => ['type' => 'array', 'items' => ['map' => 'string', 'count' => 'int']],
        'DeepStats' => [
            'combat' => 'array',
            'movement' => 'array',
            'accuracy' => 'array',
            'session' => 'array',
            'rivals' => 'array',
            'stance' => 'array'
        ],
        'AchievementList' => ['type' => 'array', 'items' => [
            'id' => 'int', 'code' => 'string', 'name' => 'string', 'description' => 'string', 'points' => 'int', 'icon' => 'string'
        ]],
        'AchievementDetail' => [
            'id' => 'int', 'code' => 'string', 'name' => 'string', 'description' => 'string', 'points' => 'int', 'icon' => 'string',
            'unlocked_count' => 'int', 'percentage' => 'float'
        ],
        'LeaderboardCards' => ['type' => 'array', 'items' => [
            'title' => 'string', 'stat' => 'string', 'icon' => 'string',
            'players' => ['type' => 'array', 'items' => ['name'=>'string', 'guid'=>'string', 'value'=>'string', 'rank'=>'int']]
        ]],
        'MatchAdvanced' => [
             'info' => 'array',
             'stats' => 'array',
             'timeline' => 'array'
        ],
        'PlayerPerformance' => ['type' => 'array', 'items' => ['date'=>'string', 'kills'=>'int', 'kd'=>'float']],
        'PlayerPlaystyle' => ['class' => 'string', 'fav_weapon' => 'string', 'aggressive' => 'float'],
    ];

    public function __construct()
    {
        global $modSettings;
        $this->baseUrl = $modSettings['mohaa_stats_api_url'] ?? 'http://localhost:8080';
        $this->serverToken = $modSettings['mohaa_stats_server_token'] ?? '';
        $this->cacheDuration = (int)($modSettings['mohaa_stats_cache_duration'] ?? 60);
        $this->timeout = (int)($modSettings['mohaa_stats_api_timeout'] ?? 3);
    }
    
    public function getMultiple(array $requests): array
    {
        $results = [];
        $handles = [];
        $mh = curl_multi_init();
        
        foreach ($requests as $key => $request) {
            $endpoint = $request['endpoint'];
            $params = $request['params'] ?? [];
            $schemaKey = $request['schema'] ?? '';

            $url = $this->baseUrl . '/api/v1' . $endpoint;
            if (!empty($params)) $url .= '?' . http_build_query($params);
            
            $cacheKey = 'mohaa_api_' . md5($url);
            $cached = cache_get_data($cacheKey, $this->cacheDuration);
            if ($cached !== null) { $results[$key] = $cached; continue; }
            
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $this->timeout, CURLOPT_CONNECTTIMEOUT => 2,
                CURLOPT_HTTPHEADER => ['Accept: application/json', 'X-Server-Token: ' . $this->serverToken],
            ]);
            $handles[$key] = ['handle' => $ch, 'url' => $url, 'cacheKey' => $cacheKey, 'schema' => $schemaKey];
            curl_multi_add_handle($mh, $ch);
        }
        
        if (!empty($handles)) {
            $running = null;
            do { curl_multi_exec($mh, $running); curl_multi_select($mh); } while ($running > 0);
            
            foreach ($handles as $key => $info) {
                $response = curl_multi_getcontent($info['handle']);
                $httpCode = curl_getinfo($info['handle'], CURLINFO_HTTP_CODE);
                $data = null;

                if ($httpCode >= 200 && $httpCode < 300 && $response !== false) {
                    $decoded = json_decode($response, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        // Cast Data if Schema Provided
                        if (!empty($info['schema'])) {
                            $decoded = $this->enforceSchema($decoded, $info['schema']);
                        }
                        $data = $decoded;
                        cache_put_data($info['cacheKey'], $data, $this->cacheDuration);
                    }
                }

                $results[$key] = $data;
                curl_multi_remove_handle($mh, $info['handle']);
                curl_close($info['handle']);
            }
            curl_multi_close($mh);
        }
        return $results;
    }

    protected function request(string $method, string $endpoint, array $data = [], string $schemaKey = ''): mixed
    {
        $url = $this->baseUrl . '/api/v1' . $endpoint;
        if ($method === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
        }

        // Cache check for GET
        $cacheKey = 'mohaa_api_' . md5($url);
        if ($method === 'GET') {
            $cached = cache_get_data($cacheKey, $this->cacheDuration);
            if ($cached !== null) return $cached;
        }

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_HTTPHEADER => ['Accept: application/json', 'X-Server-Token: ' . $this->serverToken],
        ];

        if ($method === 'POST') {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
            $options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
        }

        $response = $this->performRequest($url, $options);

        if ($response === null) {
            return null;
        }

        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        // Apply Schema Validation & Casting
        if (!empty($schemaKey)) {
            $decoded = $this->enforceSchema($decoded, $schemaKey);
        }

        // Cache success for GET
        if ($method === 'GET' && $decoded !== null) {
            cache_put_data($cacheKey, $decoded, $this->cacheDuration);
        }

        return $decoded;
    }

    protected function performRequest(string $url, array $options): ?string
    {
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) return null;
        if ($httpCode < 200 || $httpCode >= 300) return null;

        return $response;
    }

    protected function get(string $endpoint, array $params = [], string $schemaKey = ''): ?array
    {
        return $this->request('GET', $endpoint, $params, $schemaKey);
    }

    protected function post(string $endpoint, array $data = [], string $schemaKey = ''): ?array
    {
        return $this->request('POST', $endpoint, $data, $schemaKey);
    }

    // --- Schema Enforcement Helpers ---

    private function enforceSchema($data, string $schemaKey)
    {
        if (!isset($this->schemas[$schemaKey])) return $data;
        $schema = $this->schemas[$schemaKey];

        // Handle root array schema
        if (isset($schema['type']) && $schema['type'] === 'array') {
             if (!is_array($data)) return [];
             $result = [];
             foreach ($data as $item) {
                 $result[] = $this->castWithSchema($item, $schema['items']);
             }
             return $result;
        }

        if (!is_array($data)) return $this->castWithSchema([], $schema);

        return $this->castWithSchema($data, $schema);
    }
    
    private function castWithSchema($data, $schema) {
        if (!is_array($schema)) return $data;
        $result = [];
        foreach ($schema as $key => $type) {
             $value = $data[$key] ?? null;
             if (is_array($type)) {
                 if (($type['type']??'') === 'array') {
                     $result[$key] = [];
                     if (is_array($value)) {
                         foreach ($value as $item) {
                             if (isset($type['items'])) {
                                 $result[$key][] = $this->castWithSchema($item, $type['items']);
                             } else {
                                 $result[$key][] = $item;
                             }
                         }
                     }
                 } else {
                     // Nested object recursion
                     $result[$key] = $this->castWithSchema($value ?? [], $type);
                 }
             } else {
                 $result[$key] = $this->castValue($value, $type);
             }
        }
        return $result;
    }

    private function castValue($value, $type) {
        if ($value === null) {
            return match($type) {
                'int' => 0,
                'float' => 0.0,
                'string' => '',
                'bool' => false,
                'array' => [],
                default => null
            };
        }
        return match($type) {
            'int' => (int)$value,
            'float' => (float)$value,
            'string' => (string)$value,
            'bool' => (bool)$value,
            'array' => (array)$value,
            default => $value
        };
    }

    // --- Public Methods with Schema Binding ---

    public function clearCache(): void { clean_cache('mohaa_api_'); }
    public function getGlobalStats(): ?array { return $this->get('/stats/global', [], 'GlobalStats'); }
    public function getLeaderboard(string $stat = 'kills', int $limit = 25, int $offset = 0, string $period = 'all'): ?array { return $this->get('/stats/leaderboard/global', ['stat'=>$stat,'limit'=>$limit,'offset'=>$offset,'period'=>$period], 'Leaderboard'); }
    public function getLeaderboardCount(string $stat = 'kills', string $period = 'all'): int { $data = $this->get('/stats/leaderboard/global', ['stat'=>$stat,'period'=>$period,'count_only'=>true], 'Leaderboard'); return $data['total'] ?? 0; }
    public function getPlayerStats(string $guid): ?array { return $this->get('/stats/player/' . urlencode($guid), [], 'Player'); }
    public function getPlayerDeepStats(string $guid): ?array { return $this->get('/stats/player/' . urlencode($guid) . '/deep', [], 'DeepStats'); }
    public function getPlayerWeapons(string $guid): ?array { return $this->get('/stats/player/' . urlencode($guid) . '/weapons'); }
    public function getPlayerMatches(string $guid, int $limit = 10, int $offset = 0): ?array { return $this->get('/stats/player/' . urlencode($guid) . '/matches', ['limit'=>$limit,'offset'=>$offset], 'MatchList'); }
    public function getPlayerAchievements(string $guid): ?array { return $this->get('/achievements/player/' . urlencode($guid)); }
    public function getRecentMatches(int $limit = 20, int $offset = 0): ?array { return $this->get('/stats/matches', ['limit'=>$limit,'offset'=>$offset], 'MatchList'); }
    public function getMatchCount(): int { $data = $this->get('/stats/matches', ['count_only'=>true], 'MatchList'); return $data['total'] ?? 0; }
    public function getMatchDetails(string $matchId): ?array { return $this->get('/stats/match/' . urlencode($matchId), [], 'MatchDetail'); }
    public function getLiveMatches(): ?array { $orig = $this->cacheDuration; $this->cacheDuration = 10; $data = $this->get('/stats/live/matches', [], 'LiveMatches'); $this->cacheDuration = $orig; return $data; }
    public function getMapStats(): ?array { return $this->get('/stats/maps', [], 'MapStats'); }
    public function getMapDetails(string $mapId): ?array { return $this->get('/stats/map/' . urlencode($mapId), [], 'MapDetail'); }
    public function getMapHeatmap(string $mapId, string $type = 'kills'): ?array { return $this->get('/stats/map/' . urlencode($mapId) . '/heatmap', ['type'=>$type]); }
    public function getMapsList(): ?array { return $this->get('/stats/maps/list', [], 'MapsList'); }
    public function getWeaponsList(): ?array { return $this->get('/stats/weapons/list', [], 'WeaponsList'); }
    public function getWeaponStats(string $weaponId): ?array { return $this->get('/stats/weapon/' . urlencode($weaponId), [], 'WeaponStats'); }
    public function initClaim(int $forumUserId): ?array { return $this->post('/auth/claim/init', ['forum_user_id'=>$forumUserId], 'AuthClaim'); }
    public function initDeviceAuth(int $forumUserId): ?array { return $this->post('/auth/device', ['forum_user_id'=>$forumUserId], 'AuthToken'); }

    // Server Stats
    public function getGlobalActivity(): ?array { return $this->get('/stats/global/activity', [], 'ServerActivity'); }
    public function getMapPopularity(): ?array { return $this->get('/stats/maps/popularity', [], 'MapPopularity'); }
    
    // Stubs
    public function getAchievements(): ?array { return $this->get('/achievements/', [], 'AchievementList'); }
    public function getAchievement(int $id): ?array { return $this->get('/achievements/' . $id, [], 'AchievementDetail'); }
    public function getRecentAchievements(): ?array { return $this->get('/achievements/recent', [], 'AchievementList'); }
    public function getAchievementLeaderboard(): ?array { return $this->get('/achievements/leaderboard', [], 'Leaderboard'); }

    public function getActivePlayers(int $hours): ?array { return []; }
    public function getPlayerRank(string $guid): ?int { return null; }
    public function getPlayerPerformance(string $guid, int $days): ?array { return $this->get('/stats/player/' . urlencode($guid) . '/performance', [], 'PlayerPerformance'); }
    public function getPlayerHistory(string $guid): ?array { return $this->get('/stats/player/' . urlencode($guid) . '/performance', [], 'PlayerPerformance'); }
    public function getPlayerPlaystyle(string $guid): ?array { return $this->get('/stats/player/' . urlencode($guid) . '/playstyle', [], 'PlayerPlaystyle'); }
    public function getMatchReport(string $matchId): ?array { return $this->get('/stats/match/' . urlencode($matchId) . '/advanced', [], 'MatchAdvanced'); }

    public function getPlayerMapStats(string $guid): ?array { return []; }
    public function getPlayerComparisons(string $guid): ?array { return []; }
    public function getHeadToHead(string $guid1, string $guid2): ?array { return []; }
    public function getLeaderboardCards(): ?array { return $this->get('/stats/leaderboard/cards', [], 'LeaderboardCards'); }
}

function MohaaStats_APIProxy(): void
{
    global $modSettings;
    if (empty($modSettings['mohaa_stats_enabled'])) { http_response_code(503); die(json_encode(['error'=>'disabled'])); }
    header('Content-Type: application/json');
    $endpoint = $_GET['endpoint'] ?? '';
    $api = new MohaaStatsAPIClient();
    $result = match($endpoint) {
        'global-stats' => $api->getGlobalStats(),
        'leaderboard' => $api->getLeaderboard($_GET['stat']??'kills', min(100,max(1,(int)($_GET['limit']??25))), max(0,(int)($_GET['offset']??0)), $_GET['period']??'all'),
        'player' => $api->getPlayerStats($_GET['guid']??''),
        'matches' => $api->getRecentMatches(min(50,max(1,(int)($_GET['limit']??20))), max(0,(int)($_GET['offset']??0))),
        'match' => $api->getMatchDetails($_GET['id']??''),
        'maps' => $api->getMapStats(),
        'live' => $api->getLiveMatches(),
        default => null,
    };
    if ($result === null) { http_response_code(500); die(json_encode(['error'=>'API failed'])); }
    echo json_encode($result);
    obExit(false);
}
