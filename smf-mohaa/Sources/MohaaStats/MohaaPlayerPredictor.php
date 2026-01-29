<?php
/**
 * AI-Powered Player Performance Predictions
 * 
 * Machine learning-inspired prediction engine for:
 * - Next match K/D prediction
 * - Win probability calculation
 * - Performance trend forecasting
 * - Optimal playtime suggestions
 *
 * @package MohaaStats
 * @version 2.1.0
 */

if (!defined('SMF'))
    die('No direct access...');

class MohaaPlayerPredictor
{
    private $guid;
    private $api;
    
    public function __construct(string $guid = '')
    {
        $this->guid = $guid;
        require_once(dirname(__FILE__) . '/MohaaStatsAPI.php');
        $this->api = new MohaaStatsAPIClient();
    }
    
    /**
     * Get player GUID from member ID
     */
    public static function getGuidFromMemberId(int $memberId): string
    {
        global $smcFunc;
        
        $request = $smcFunc['db_query']('', '
            SELECT primary_guid
            FROM {db_prefix}mohaa_identities
            WHERE id_member = {int:member_id}
            LIMIT 1',
            ['member_id' => $memberId]
        );
        
        $row = $smcFunc['db_fetch_assoc']($request);
        $guid = $row['primary_guid'] ?? '';
        $smcFunc['db_free_result']($request);
        
        return $guid;
    }
    
    /**
     * Generate all available predictions using the Stats API
     */
    public function generateAllPredictions(): array
    {
        if (empty($this->guid)) {
            return ['error' => 'No GUID set'];
        }

        // Fetch prediction from Go API
        $predictionData = $this->api->getPlayerPredictions($this->guid);
        
        if (!$predictionData) {
            return ['error' => 'Failed to fetch predictions from API'];
        }

        return [
            'player_guid' => $predictionData['guid'],
            'expected_kd' => $predictionData['expected_kd'] ?? 0,
            'trend' => $predictionData['trend'] ?? 'stable',
            'confidence' => ($predictionData['confidence'] ?? 0.5) * 100,
            'predicted_kills' => $predictionData['predicted_kills'] ?? 0,
            'predicted_deaths' => $predictionData['predicted_deaths'] ?? 0,
            'rival_analysis' => $predictionData['rival_analysis'] ?? [],
            'performance_history' => $predictionData['recent_performance'] ?? [],
            'last_updated' => $predictionData['last_updated'] ?? null,
            
            // Legacy stubs or client-side derived fields
            'recommendations' => $this->generateRecommendations($predictionData),
            'outlook' => $this->getOutlookMessage($predictionData)
        ];
    }

    private function generateRecommendations(array $data): array
    {
        $recs = [];
        $trend = $data['trend'] ?? 'stable';
        
        if ($trend === 'declining') {
            $recs[] = [
                'type' => 'warning',
                'description' => 'Your recent performance shows a downward trend. Consider analyzing your death locations or switching tactics.'
            ];
        } elseif ($trend === 'improving') {
            $recs[] = [
                'type' => 'success',
                'description' => 'You are on an upward trend! Your current playstyle is yielding better results.'
            ];
        }

        if (($data['expected_kd'] ?? 0) < 1.0) {
            $recs[] = [
                'type' => 'tip',
                'description' => 'Focus on map survival. Use grenades to clear rooms before entering to boost your survival rate.'
            ];
        }

        return $recs;
    }

    private function getOutlookMessage(array $data): string
    {
        $trend = $data['trend'] ?? 'stable';
        switch ($trend) {
            case 'improving': return 'Strong improvement expected';
            case 'declining': return 'Decline expected - caution advised';
            default: return 'Stable performance anticipated';
        }
    }
}
