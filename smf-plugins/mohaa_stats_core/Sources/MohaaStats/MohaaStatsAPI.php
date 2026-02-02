<?php
declare(strict_types=1);
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
            $handles[$key] = ['handle' => $ch, 'url' => $url, 'cacheKey' => $cacheKey];
            curl_multi_add_handle($mh, $ch);
        }
        
        if (!empty($handles)) {
            $running = null;
            do { curl_multi_exec($mh, $running); curl_multi_select($mh); } while ($running > 0);
            
            foreach ($handles as $key => $info) {
                $response = curl_multi_getcontent($info['handle']);
                $httpCode = curl_getinfo($info['handle'], CURLINFO_HTTP_CODE);
                if ($httpCode === 200 && $response !== false) {
                    $data = json_decode($response, true);
                    if (is_array($data)) {
                        $data = $this->_validateAndCast($data);
                    }
                    $results[$key] = $data;
                    cache_put_data($info['cacheKey'], $data, $this->cacheDuration);
                } else { $results[$key] = null; }
                curl_multi_remove_handle($mh, $info['handle']);
                curl_close($info['handle']);
            }
            curl_multi_close($mh);
        }
        return $results;
    }
    
    private function get(string $endpoint, array $params = []): ?array
    {
        $url = $this->baseUrl . '/api/v1' . $endpoint;
        if (!empty($params)) $url .= '?' . http_build_query($params);
        $cacheKey = 'mohaa_api_' . md5($url);
        $cached = cache_get_data($cacheKey, $this->cacheDuration);
        if ($cached !== null) return $cached;
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout, CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_HTTPHEADER => ['Accept: application/json', 'X-Server-Token: ' . $this->serverToken],
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode !== 200 || $response === false) return null;
        $data = json_decode($response, true);
        if (is_array($data)) {
            $data = $this->_validateAndCast($data);
        }
        cache_put_data($cacheKey, $data, $this->cacheDuration);
        return $data;
    }
    
    private function post(string $endpoint, array $data = []): ?array
    {
        $url = $this->baseUrl . '/api/v1' . $endpoint;
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_POST => true, CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => ['Accept: application/json', 'Content-Type: application/json', 'X-Server-Token: ' . $this->serverToken],
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode < 200 || $httpCode >= 300 || $response === false) return null;
        $data = json_decode($response, true);
        if (is_array($data)) {
            $data = $this->_validateAndCast($data);
        }
        return $data;
    }

    private function delete(string $endpoint, array $data = []): ?array
    {
        $url = $this->baseUrl . '/api/v1' . $endpoint;
        if (!empty($data)) $url .= '?' . http_build_query($data);
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPHEADER => ['Accept: application/json', 'X-Server-Token: ' . $this->serverToken],
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode < 200 || $httpCode >= 300 || $response === false) return null;
        $data = json_decode($response, true);
        if (is_array($data)) {
            $data = $this->_validateAndCast($data);
        }
        return $data;
    }
    
    public function clearCache(): void { clean_cache('mohaa_api_'); }
    public function getGlobalStats(): ?array { return $this->get('/stats/global'); }
    public function getLeaderboard(string $stat = 'kills', int $limit = 25, int $offset = 0, string $period = 'all'): ?array { return $this->get('/stats/leaderboard/global', ['stat'=>$stat,'limit'=>$limit,'offset'=>$offset,'period'=>$period]); }
    public function getLeaderboardCount(string $stat = 'kills', string $period = 'all'): int { $data = $this->get('/stats/leaderboard/global', ['stat'=>$stat,'period'=>$period,'count_only'=>true]); return (int)($data['total'] ?? 0); }
    public function getPlayerStats(string $guid): ?array { return $this->get('/stats/player/' . urlencode($guid)); }
    public function getPlayerDeepStats(string $guid): ?array { return $this->get('/stats/player/' . urlencode($guid) . '/deep'); }
    public function getPlayerWeapons(string $guid): ?array { return $this->get('/stats/player/' . urlencode($guid) . '/weapons'); }
    public function getPlayerMatches(string $guid, int $limit = 10, int $offset = 0): ?array { return $this->get('/stats/player/' . urlencode($guid) . '/matches', ['limit'=>$limit,'offset'=>$offset]); }
    public function getPlayerAchievements(string $guid): ?array { return $this->get('/achievements/player/' . urlencode($guid)); }
    public function getRecentMatches(int $limit = 20, int $offset = 0): ?array { return $this->get('/stats/matches', ['limit'=>$limit,'offset'=>$offset]); }
    public function getMatchCount(): int { $data = $this->get('/stats/matches', ['count_only'=>true]); return (int)($data['total'] ?? 0); }
    public function getMatchDetails(string $matchId): ?array { return $this->get('/stats/match/' . urlencode($matchId)); }
    public function getLiveMatches(): ?array { $orig = $this->cacheDuration; $this->cacheDuration = 10; $data = $this->get('/stats/live/matches'); $this->cacheDuration = $orig; return $data; }
    public function getMapStats(): ?array { return $this->get('/stats/maps'); }
    public function getMapDetails(string $mapId): ?array { return $this->get('/stats/map/' . urlencode($mapId)); }
    public function getMapHeatmap(string $mapId, string $type = 'kills'): ?array { return $this->get('/stats/map/' . urlencode($mapId) . '/heatmap', ['type'=>$type]); }
    public function getMapsList(): ?array { return $this->get('/stats/maps/list'); }
    public function getWeaponsList(): ?array { return $this->get('/stats/weapons/list'); }
    public function getWeaponStats(string $weaponId): ?array { return $this->get('/stats/weapon/' . urlencode($weaponId)); }
    public function getWeaponLeaderboard(string $weaponId, int $limit = 25): ?array { return $this->get('/stats/weapon/' . urlencode($weaponId) . '/leaderboard', ['limit'=>$limit]); }
    public function getMapLeaderboard(string $mapId, int $limit = 25): ?array { return $this->get('/stats/map/' . urlencode($mapId) . '/leaderboard', ['limit'=>$limit]); }
    public function getMatchHeatmap(string $matchId, string $type = 'kills'): ?array { return $this->get('/stats/match/' . urlencode($matchId) . '/heatmap', ['type'=>$type]); }
    public function initClaim(int $forumUserId): ?array { return $this->post('/auth/claim/init', ['forum_user_id'=>$forumUserId]); }
    public function initDeviceAuth(int $forumUserId, bool $force = false): ?array { return $this->post('/auth/device', ['forum_user_id'=>$forumUserId, 'force'=>$force]); }

    public function getLoginHistory(int $forumUserId): ?array { return $this->get('/auth/history/' . $forumUserId); }
    public function getTrustedIPs(int $forumUserId): ?array { return $this->get('/auth/trusted-ips/' . $forumUserId); }
    public function getPendingIPApprovals(int $forumUserId): ?array { return $this->get('/auth/pending-ips/' . $forumUserId); }
    public function deleteTrustedIP(int $forumUserId, int $ipId): ?array { return $this->delete('/auth/trusted-ip/' . $ipId, ['forum_user_id'=>$forumUserId]); }
    public function resolvePendingIP(int $forumUserId, int $approvalId, string $action): ?array { return $this->post('/auth/pending-ip/resolve', ['forum_user_id'=>$forumUserId, 'approval_id'=>$approvalId, 'action'=>$action]); }

    // Server Stats
    public function getGlobalActivity(): ?array { return $this->get('/stats/global/activity'); }
    public function getMapPopularity(): ?array { return $this->get('/stats/maps/popularity'); }
    
    // Stubs
    public function getAchievements(): ?array { return $this->get('/achievements/'); }
    public function getAchievement(int $id): ?array { return $this->get('/achievements/' . $id); }
    public function getRecentAchievements(): ?array { return $this->get('/achievements/recent'); }
    public function getAchievementLeaderboard(): ?array { return $this->get('/achievements/leaderboard'); }

    public function getActivePlayers(int $hours): ?array { return []; }
    public function getPlayerRank(string $guid): ?int { return null; }
    public function getPlayerPerformance(string $guid, int $days): ?array { return $this->get('/stats/player/' . urlencode($guid) . '/performance'); }
    public function getPlayerHistory(string $guid): ?array { return $this->get('/stats/player/' . urlencode($guid) . '/performance'); }
    public function getPlayerPlaystyle(string $guid): ?array { return $this->get('/stats/player/' . urlencode($guid) . '/playstyle'); }
    public function getMatchReport(string $matchId): ?array { return $this->get('/stats/match/' . urlencode($matchId) . '/advanced'); }

    public function getPlayerMapStats(string $guid): ?array { return []; }
    public function getPlayerComparisons(string $guid): ?array { return []; }
    public function getHeadToHead(string $guid1, string $guid2): ?array { return []; }
    public function getLeaderboardCards(): ?array { return $this->get('/stats/leaderboard/cards'); }

    private function _validateAndCast(array $data): array
    {
        $intKeys = [
            'kills', 'deaths', 'count', 'limit', 'offset', 'total', 'page',
            'rank', 'score', 'wins', 'losses', 'draws', 'shots', 'hits', 'headshots',
            'rounds', 'time_played', 'assist', 'assists', 'suicides', 'team_kills',
            'expires_in', 'ttl', 'forum_user_id', 'cache_time', 'start_time', 'end_time',
            'points', 'rounds_played', 'flags_captured', 'obj_returned', 'obj_stolen',
            'ip_id', 'approval_id', 'id'
        ];
        $floatKeys = [
            'accuracy', 'kdr', 'wl_ratio', 'kd_ratio', 'damage', 'percent',
            'spm', 'kpm', 'win_loss_ratio', 'score_per_minute'
        ];
        $boolKeys = ['is_active', 'success', 'count_only', 'online', 'is_online', 'is_vip', 'verified'];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->_validateAndCast($value);
            } elseif (in_array($key, $intKeys, true) && is_numeric($value)) {
                $data[$key] = (int)$value;
            } elseif (in_array($key, $floatKeys, true) && is_numeric($value)) {
                $data[$key] = (float)$value;
            } elseif (in_array($key, $boolKeys, true)) {
                $data[$key] = (bool)$value;
            }
        }
        return $data;
    }
}

function MohaaStats_APIProxy(): void
{
    global $modSettings;
    if (empty($modSettings['mohaa_stats_enabled'])) {
        http_response_code(503);
        die(json_encode(['error'=>'disabled']));
    }
    header('Content-Type: application/json');

    try {
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
            default => 'invalid_endpoint',
        };

        if ($result === 'invalid_endpoint') {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid endpoint']);
            obExit(false);
        }

        if ($result === null) {
            http_response_code(502);
            echo json_encode(['error' => 'Upstream API failed or returned empty response']);
            obExit(false);
        }

        echo json_encode($result);
        obExit(false);

    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Internal Server Error', 'message' => $e->getMessage()]);
        obExit(false);
    }
}
