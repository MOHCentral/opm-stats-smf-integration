<?php
// TestMohaaStatsAPI.php

require_once __DIR__ . '/mocks/smf_env.php';
require_once __DIR__ . '/../mohaa_stats_core/Sources/MohaaStats/MohaaStatsAPI.php';

class MockMohaaStatsAPIClient extends MohaaStatsAPIClient
{
    public array $mockResponses = [];
    public array $lastRequest = [];

    protected function performRequest(string $url, array $options): ?string
    {
        // Extract Endpoint and Method to match key
        $path = parse_url($url, PHP_URL_PATH); // /api/v1/stats/global
        // Remove base url if present (in tests base is http://mock-api.com)
        // But parse_url gives path relative to root usually.

        // baseUrl is http://mock-api.com
        // request url is http://mock-api.com/api/v1...
        // so path is /api/v1...

        $endpoint = str_replace('/api/v1', '', $path);

        $method = isset($options[CURLOPT_POST]) && $options[CURLOPT_POST] ? 'POST' : 'GET';

        if ($method === 'POST') {
             $data = json_decode($options[CURLOPT_POSTFIELDS] ?? '[]', true);
             $this->lastRequest = ['method' => 'POST', 'endpoint' => $endpoint, 'data' => $data];
             $key = 'POST ' . $endpoint;
        } else {
             $query = parse_url($url, PHP_URL_QUERY);
             parse_str($query ?? '', $params);
             $this->lastRequest = ['method' => 'GET', 'endpoint' => $endpoint, 'params' => $params];
             $key = 'GET ' . $endpoint;
        }

        if (array_key_exists($key, $this->mockResponses)) {
            $response = $this->mockResponses[$key];
            if ($response instanceof Exception) {
                throw $response;
            }
            return json_encode($response);
        }
        return null; // Simulate 404/Error
    }

    public function getMultiple(array $requests): array
    {
         // We emulate parallel requests by serial calls to our mocked single request
         // This ensures that `request` -> `performRequest` (mocked) -> `enforceSchema` flow is exercised.
         $results = [];
         foreach ($requests as $key => $request) {
             $endpoint = $request['endpoint'];
             $params = $request['params'] ?? [];
             $schema = $request['schema'] ?? '';

             // We call the public/protected `get` method which uses `request`
             // But `get` is protected. We are in subclass, so we can call it.
             $results[$key] = $this->get($endpoint, $params, $schema);
         }
         return $results;
    }
}

class TestMohaaStatsAPI
{
    private $api;

    public function run()
    {
        echo "Running Tests...\n";

        $methods = get_class_methods($this);
        $passed = 0;
        $failed = 0;

        foreach ($methods as $method) {
            if (strpos($method, 'test') === 0) {
                $this->setUp();
                try {
                    $this->$method();
                    echo "[PASS] $method\n";
                    $passed++;
                } catch (Exception $e) {
                    echo "[FAIL] $method: " . $e->getMessage() . "\n";
                    $failed++;
                } catch (Error $e) {
                    echo "[FAIL] $method: " . $e->getMessage() . "\n";
                    $failed++;
                }
            }
        }

        echo "\nResults: $passed Passed, $failed Failed.\n";
    }

    public function setUp()
    {
        $this->api = new MockMohaaStatsAPIClient();
    }

    private function assertEquals($expected, $actual) {
        // Use json_encode comparison for simplicity with arrays
        if (json_encode($expected) !== json_encode($actual)) {
            throw new Exception("Expected " . json_encode($expected) . ", got " . json_encode($actual));
        }
    }

    // --- Happy Path Tests ---

    public function testGetGlobalStats()
    {
        $expected = ['total_players' => 100, 'total_matches' => 50];
        $this->api->mockResponses['GET /stats/global'] = $expected;
        $result = $this->api->getGlobalStats();

        // Result will be casted. Keys not in schema might be dropped if schema was strict?
        // My schema enforcement implementation:
        // foreach ($schema as $key => $type) ... $result[$key] = ...
        // So yes, ONLY keys in schema are returned.
        // My schema for GlobalStats has total_players and total_matches.
        // Wait, does it?
        // 'GlobalStats' => ['total_kills', 'total_players', 'total_matches', 'total_headshots', 'total_achievements_unlocked']
        // So expected needs to include all keys or they will be defaulted to 0.

        $expectedFull = array_merge([
            'total_kills' => 0, 'total_players' => 0, 'total_matches' => 0,
            'total_headshots' => 0, 'total_achievements_unlocked' => 0
        ], $expected);

        $this->assertEquals($expectedFull, $result);
    }

    public function testGetLeaderboard()
    {
        $input = ['players' => [['name' => 'P1', 'kills' => 10]], 'total' => 1];
        $this->api->mockResponses['GET /stats/leaderboard/global'] = $input;
        $result = $this->api->getLeaderboard('kills', 10, 0, 'all');

        // Schema fills defaults
        $this->assertEquals(1, $result['total']);
        $this->assertEquals('P1', $result['players'][0]['name']);
        $this->assertEquals(10, $result['players'][0]['kills']);
        // Verify default fields
        $this->assertEquals(0, $result['players'][0]['deaths']);
    }

    public function testGetPlayerStats()
    {
        $guid = '123';
        $input = ['name' => 'P1', 'kills' => 100];
        $this->api->mockResponses['GET /stats/player/' . $guid] = $input;
        $result = $this->api->getPlayerStats($guid);

        $this->assertEquals('P1', $result['name']);
        $this->assertEquals(100, $result['kills']);
        $this->assertEquals(0, $result['deaths']); // Default
    }

    public function testGetRecentMatches()
    {
        $input = ['list' => [['id' => 'm1']], 'total' => 1];
        $this->api->mockResponses['GET /stats/matches'] = $input;
        $result = $this->api->getRecentMatches(10, 0);
        $this->assertEquals('m1', $result['list'][0]['id']);
    }

    public function testInitClaim()
    {
        $userId = 1;
        $input = ['code' => 'ABC'];
        $this->api->mockResponses['POST /auth/claim/init'] = $input;
        $result = $this->api->initClaim($userId);
        $this->assertEquals('ABC', $result['code']);
    }

    public function testGetLiveMatches()
    {
        $input = [['server_name' => 'S1']];
        $this->api->mockResponses['GET /stats/live/matches'] = $input;
        $result = $this->api->getLiveMatches();
        $this->assertEquals('S1', $result[0]['server_name']);
    }

    // --- Failure / Edge Case Tests ---

    public function testApiFailureReturnsNull()
    {
        // No mock response = failure
        $result = $this->api->getGlobalStats();
        $this->assertEquals(null, $result);
    }

    public function testTypeEnforcement()
    {
         // API returns strings instead of ints
         $this->api->mockResponses['GET /stats/global'] = ['total_players' => "100", 'total_matches' => "50"];
         $result = $this->api->getGlobalStats();

         // We expect the Client to cast these to ints
         if (!is_int($result['total_players'])) {
             throw new Exception("Type enforcement failed: 'total_players' is " . gettype($result['total_players']));
         }
         if (!is_int($result['total_matches'])) {
             throw new Exception("Type enforcement failed: 'total_matches' is " . gettype($result['total_matches']));
         }
         $this->assertEquals(100, $result['total_players']);
    }

    public function testSchemaValidation()
    {
        // Missing 'total_matches' key
        $this->api->mockResponses['GET /stats/global'] = ['total_players' => 100];
        $result = $this->api->getGlobalStats();

        // We expect the Client to fill in missing keys with defaults
        if (!array_key_exists('total_matches', $result)) {
             throw new Exception("Schema validation failed: 'total_matches' key missing");
        }
        if ($result['total_matches'] !== 0) { // Default for int is 0
             throw new Exception("Schema validation failed: 'total_matches' default is " . $result['total_matches']);
        }
    }

    public function testLeaderboardSchema()
    {
        // Malformed leaderboard response (null players)
        $this->api->mockResponses['GET /stats/leaderboard/global'] = ['players' => null];
        $result = $this->api->getLeaderboard();

        if (!is_array($result['players'])) {
            throw new Exception("Leaderboard 'players' should be array");
        }
        if (!is_int($result['total'])) {
            throw new Exception("Leaderboard 'total' should be int");
        }
        $this->assertEquals([], $result['players']);
    }
}

// Run the tests
$test = new TestMohaaStatsAPI();
$test->run();
