<?php
/**
 * MOHAA Stats System - Master Installer
 * 
 * Run this from browser: http://your-forum/Sources/mohaa_master_install.php
 * Or from CLI: php -f /path/to/smf/Sources/mohaa_master_install.php
 * 
 * @package MohaaStats
 * @version 1.0.0
 */

// Standalone mode - find SSI.php
$ssi_paths = [
    dirname(__FILE__) . '/../../../SSI.php',      // install/ directory
    dirname(__FILE__) . '/../../SSI.php',         // Sources/MohaaStats/
    dirname(__FILE__) . '/../SSI.php',            // Sources/
    dirname(__FILE__) . '/SSI.php',               // root
    '/var/www/smf/SSI.php',                       // common location
];

$ssi_found = false;
foreach ($ssi_paths as $path) {
    if (file_exists($path)) {
        require_once($path);
        $ssi_found = true;
        break;
    }
}

if (!$ssi_found && !defined('SMF')) {
    die('<b>Error:</b> Could not find SSI.php. Please run from within SMF directory.');
}

global $smcFunc, $modSettings, $db_prefix, $sourcedir;

// Output header
echo "<html><head><title>MOHAA Stats Installer</title>";
echo "<style>
body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; padding: 40px; background: #1a1a2e; color: #eee; }
.container { max-width: 800px; margin: 0 auto; }
h1 { color: #4ecca3; }
h2 { color: #64b5f6; border-bottom: 1px solid #333; padding-bottom: 10px; margin-top: 30px; }
.success { color: #4ecca3; }
.error { color: #ff6b6b; }
.warning { color: #ffd93d; }
.info { color: #64b5f6; }
pre { background: #0f0f1a; padding: 15px; border-radius: 8px; overflow-x: auto; }
.step { background: #16213e; padding: 15px; margin: 10px 0; border-radius: 8px; border-left: 4px solid #4ecca3; }
.btn { display: inline-block; padding: 10px 20px; background: #4ecca3; color: #1a1a2e; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 5px; }
.btn:hover { background: #3db892; }
</style></head><body><div class='container'>";
echo "<h1>🎮 MOHAA Stats System - Master Installer</h1>";

$errors = [];
$success = [];

// ============================================================================
// STEP 1: Create Database Tables
// ============================================================================
echo "<h2>Step 1: Database Tables</h2>";

$tables = [
    // Identity linking table
    [
        'name' => 'mohaa_identities',
        'columns' => [
            ['name' => 'id_identity', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'auto' => true],
            ['name' => 'id_member', 'type' => 'mediumint', 'size' => 8, 'unsigned' => true, 'default' => 0],
            ['name' => 'player_guid', 'type' => 'varchar', 'size' => 64, 'default' => ''],
            ['name' => 'player_name', 'type' => 'varchar', 'size' => 255, 'default' => ''],
            ['name' => 'linked_date', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 0],
            ['name' => 'verified', 'type' => 'tinyint', 'size' => 1, 'unsigned' => true, 'default' => 0],
        ],
        'indexes' => [
            ['type' => 'primary', 'columns' => ['id_identity']],
            ['type' => 'index', 'columns' => ['id_member']],
            ['type' => 'unique', 'columns' => ['player_guid']],
        ],
    ],
    // Claim codes
    [
        'name' => 'mohaa_claim_codes',
        'columns' => [
            ['name' => 'id_claim', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'auto' => true],
            ['name' => 'id_member', 'type' => 'mediumint', 'size' => 8, 'unsigned' => true, 'default' => 0],
            ['name' => 'claim_code', 'type' => 'varchar', 'size' => 16, 'default' => ''],
            ['name' => 'created_at', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 0],
            ['name' => 'expires_at', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 0],
            ['name' => 'used', 'type' => 'tinyint', 'size' => 1, 'unsigned' => true, 'default' => 0],
        ],
        'indexes' => [
            ['type' => 'primary', 'columns' => ['id_claim']],
            ['type' => 'unique', 'columns' => ['claim_code']],
            ['type' => 'index', 'columns' => ['id_member']],
        ],
    ],
    // Device tokens
    [
        'name' => 'mohaa_device_tokens',
        'columns' => [
            ['name' => 'id_token', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'auto' => true],
            ['name' => 'id_member', 'type' => 'mediumint', 'size' => 8, 'unsigned' => true, 'default' => 0],
            ['name' => 'user_code', 'type' => 'varchar', 'size' => 16, 'default' => ''],
            ['name' => 'device_code', 'type' => 'varchar', 'size' => 64, 'default' => ''],
            ['name' => 'created_at', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 0],
            ['name' => 'expires_at', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 0],
            ['name' => 'verified', 'type' => 'tinyint', 'size' => 1, 'unsigned' => true, 'default' => 0],
        ],
        'indexes' => [
            ['type' => 'primary', 'columns' => ['id_token']],
            ['type' => 'unique', 'columns' => ['user_code']],
            ['type' => 'index', 'columns' => ['device_code']],
        ],
    ],
    // Achievement definitions
    [
        'name' => 'mohaa_achievement_defs',
        'columns' => [
            ['name' => 'id_achievement', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'auto' => true],
            ['name' => 'code', 'type' => 'varchar', 'size' => 50, 'default' => ''],
            ['name' => 'name', 'type' => 'varchar', 'size' => 255, 'default' => ''],
            ['name' => 'description', 'type' => 'text'],
            ['name' => 'category', 'type' => 'varchar', 'size' => 50, 'default' => 'basic'],
            ['name' => 'tier', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 1],
            ['name' => 'icon', 'type' => 'varchar', 'size' => 50, 'default' => 'trophy'],
            ['name' => 'requirement_type', 'type' => 'varchar', 'size' => 50, 'default' => ''],
            ['name' => 'requirement_value', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 1],
            ['name' => 'points', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 10],
            ['name' => 'is_hidden', 'type' => 'tinyint', 'size' => 1, 'unsigned' => true, 'default' => 0],
            ['name' => 'sort_order', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 0],
        ],
        'indexes' => [
            ['type' => 'primary', 'columns' => ['id_achievement']],
            ['type' => 'unique', 'columns' => ['code']],
        ],
    ],
    // Player achievements
    [
        'name' => 'mohaa_player_achievements',
        'columns' => [
            ['name' => 'id_unlock', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'auto' => true],
            ['name' => 'player_guid', 'type' => 'varchar', 'size' => 64, 'default' => ''],
            ['name' => 'id_achievement', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 0],
            ['name' => 'unlocked_at', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 0],
            ['name' => 'progress', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 0],
        ],
        'indexes' => [
            ['type' => 'primary', 'columns' => ['id_unlock']],
            ['type' => 'unique', 'columns' => ['player_guid', 'id_achievement']],
        ],
    ],
    // Teams
    [
        'name' => 'mohaa_teams',
        'columns' => [
            ['name' => 'id_team', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'auto' => true],
            ['name' => 'name', 'type' => 'varchar', 'size' => 100, 'default' => ''],
            ['name' => 'tag', 'type' => 'varchar', 'size' => 10, 'default' => ''],
            ['name' => 'description', 'type' => 'text'],
            ['name' => 'logo_url', 'type' => 'varchar', 'size' => 255, 'default' => ''],
            ['name' => 'id_leader', 'type' => 'mediumint', 'size' => 8, 'unsigned' => true, 'default' => 0],
            ['name' => 'created_at', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 0],
            ['name' => 'is_active', 'type' => 'tinyint', 'size' => 1, 'unsigned' => true, 'default' => 1],
        ],
        'indexes' => [
            ['type' => 'primary', 'columns' => ['id_team']],
            ['type' => 'unique', 'columns' => ['tag']],
        ],
    ],
    // Team members
    [
        'name' => 'mohaa_team_members',
        'columns' => [
            ['name' => 'id_membership', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'auto' => true],
            ['name' => 'id_team', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 0],
            ['name' => 'id_member', 'type' => 'mediumint', 'size' => 8, 'unsigned' => true, 'default' => 0],
            ['name' => 'role', 'type' => 'varchar', 'size' => 20, 'default' => 'member'],
            ['name' => 'joined_at', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 0],
        ],
        'indexes' => [
            ['type' => 'primary', 'columns' => ['id_membership']],
            ['type' => 'unique', 'columns' => ['id_member']],
            ['type' => 'index', 'columns' => ['id_team']],
        ],
    ],
    // Tournaments
    [
        'name' => 'mohaa_tournaments',
        'columns' => [
            ['name' => 'id_tournament', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'auto' => true],
            ['name' => 'name', 'type' => 'varchar', 'size' => 255, 'default' => ''],
            ['name' => 'description', 'type' => 'text'],
            ['name' => 'format', 'type' => 'varchar', 'size' => 20, 'default' => 'single_elim'],
            ['name' => 'game_type', 'type' => 'varchar', 'size' => 20, 'default' => 'tdm'],
            ['name' => 'max_teams', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 16],
            ['name' => 'start_date', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 0],
            ['name' => 'end_date', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 0],
            ['name' => 'status', 'type' => 'varchar', 'size' => 20, 'default' => 'pending'],
            ['name' => 'created_by', 'type' => 'mediumint', 'size' => 8, 'unsigned' => true, 'default' => 0],
            ['name' => 'created_at', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 0],
        ],
        'indexes' => [
            ['type' => 'primary', 'columns' => ['id_tournament']],
        ],
    ],
];

foreach ($tables as $table) {
    try {
        $smcFunc['db_create_table'](
            '{db_prefix}' . $table['name'],
            $table['columns'],
            $table['indexes'],
            [],
            'ignore'
        );
        echo "<div class='step'><span class='success'>✓</span> Created table: <b>{$table['name']}</b></div>";
        $success[] = "Table {$table['name']} created";
    } catch (Exception $e) {
        echo "<div class='step'><span class='error'>✗</span> Failed to create table: <b>{$table['name']}</b> - " . $e->getMessage() . "</div>";
        $errors[] = "Table {$table['name']}: " . $e->getMessage();
    }
}

// ============================================================================
// STEP 2: Register Hooks
// ============================================================================
echo "<h2>Step 2: Register Hooks</h2>";

$hooks = [
    'integrate_actions' => [
        'MohaaPlayers.php|MohaaPlayers_Actions',
        'MohaaServers.php|MohaaServers_Actions',
        'MohaaAchievements.php|MohaaAchievements_Actions',
        'MohaaTournaments.php|MohaaTournaments_Actions',
        'MohaaTeams.php|MohaaTeams_Actions',
        'MohaaPredictions.php|MohaaPredictions_Actions',
        'MohaaComparison.php|MohaaComparison_Actions',
    ],
    'integrate_menu_buttons' => 'MohaaPlayers.php|MohaaPlayers_MenuButtons',
    'integrate_profile_areas' => [
        'MohaaPlayers.php|MohaaPlayers_ProfileAreas',
        'MohaaAchievements.php|MohaaAchievements_ProfileAreas',
        'MohaaTeams.php|MohaaTeams_ProfileAreas',
    ],
    'integrate_admin_areas' => 'MohaaTournaments.php|MohaaTournaments_AdminAreas',
];

foreach ($hooks as $hook => $functions) {
    if (!is_array($functions)) {
        $functions = [$functions];
    }
    
    foreach ($functions as $function) {
        add_integration_function($hook, $function, true);
        echo "<div class='step'><span class='success'>✓</span> Hook: <b>$hook</b> → $function</div>";
    }
}

// ============================================================================
// STEP 3: Configure Settings
// ============================================================================
echo "<h2>Step 3: Configure Settings</h2>";

$settings = [
    'mohaa_stats_enabled' => 1,
    'mohaa_api_url' => 'http://localhost:8080/api/v1',
    'mohaa_api_timeout' => 10,
    'mohaa_cache_duration' => 60,
    'mohaa_live_cache_duration' => 10,
    'mohaa_rate_limit' => 100,
    'mohaa_leaderboard_limit' => 25,
    'mohaa_recent_matches_limit' => 10,
    'mohaa_show_heatmaps' => 1,
    'mohaa_show_achievements' => 1,
    'mohaa_show_in_profile' => 1,
    'mohaa_allow_linking' => 1,
    'mohaa_max_identities' => 3,
    'mohaa_claim_expiry' => 10,
    'mohaa_token_expiry' => 10,
];

updateSettings($settings);

foreach ($settings as $key => $value) {
    echo "<div class='step'><span class='success'>✓</span> Setting: <b>$key</b> = $value</div>";
}

// ============================================================================
// STEP 4: Seed Achievement Definitions
// ============================================================================
echo "<h2>Step 4: Seed Achievement Definitions</h2>";

$achievements = [
    ['surgical', 'The Surgeon', 'Achieve 100 Headshots in a single tournament event.', 'tactical', 4, 'surgical', 'headshots_tournament', 100, 500],
    ['unstoppable', 'Unstoppable Force', 'Win 10 matches in a row without a single loss.', 'dedication', 5, 'unstoppable', 'win_streak', 10, 1000],
    ['survivalist', 'Survivalist', 'Complete a full match with < 10% HP remaining and 0 deaths.', 'hardcore', 3, 'survivalist', 'hp_survival', 1, 250],
    ['first_blood', 'First Blood', 'Get your first kill.', 'basic', 1, 'blood', 'kills', 1, 10],
    ['centurion', 'Centurion', 'Reach 100 kills.', 'basic', 2, 'sword', 'kills', 100, 50],
    ['thousand_souls', '1000 Souls', 'Reach 1000 kills.', 'basic', 3, 'skull', 'kills', 1000, 200],
    ['headhunter', 'Headhunter', 'Get 50 headshots.', 'precision', 2, 'target', 'headshots', 50, 75],
    ['sharpshooter', 'Sharpshooter', 'Achieve 50% accuracy over 1000 shots.', 'precision', 3, 'crosshair', 'accuracy', 50, 150],
    ['marathon', 'Marathon Runner', 'Run 100 kilometers total.', 'movement', 2, 'runner', 'distance', 100000, 50],
    ['veteran', 'Veteran', 'Play for 24 hours total.', 'dedication', 2, 'medal', 'playtime', 86400, 100],
];

$inserted = 0;
foreach ($achievements as $ach) {
    $smcFunc['db_insert']('ignore',
        '{db_prefix}mohaa_achievement_defs',
        ['code' => 'string', 'name' => 'string', 'description' => 'string', 'category' => 'string', 
         'tier' => 'int', 'icon' => 'string', 'requirement_type' => 'string', 
         'requirement_value' => 'int', 'points' => 'int'],
        $ach,
        ['id_achievement']
    );
    $inserted++;
}

echo "<div class='step'><span class='success'>✓</span> Inserted <b>$inserted</b> achievement definitions</div>";

// ============================================================================
// STEP 5: Verify API Connection
// ============================================================================
echo "<h2>Step 5: Verify API Connection</h2>";

$api_url = $modSettings['mohaa_api_url'] ?? 'http://localhost:8080/api/v1';
$health_url = str_replace('/api/v1', '/health', $api_url);

$ch = curl_init($health_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200 && strpos($response, 'ok') !== false) {
    echo "<div class='step'><span class='success'>✓</span> API Connection: <b>OK</b> ($health_url)</div>";
} else {
    echo "<div class='step'><span class='warning'>⚠</span> API Connection: <b>FAILED</b> - Make sure Docker is running</div>";
    echo "<pre>curl http://localhost:8080/health</pre>";
}

// ============================================================================
// Summary
// ============================================================================
echo "<h2>Installation Summary</h2>";

if (empty($errors)) {
    echo "<div class='step' style='border-color: #4ecca3; background: #1e3a2f;'>";
    echo "<span class='success'>🎉 Installation Complete!</span><br><br>";
    echo "All tables, hooks, and settings have been configured.<br><br>";
    echo "<b>Next Steps:</b><br>";
    echo "1. Ensure API is running: <code>cd opm-stats-api && docker compose up -d</code><br>";
    echo "2. Visit: <a href='index.php?action=mohaastats' class='btn'>Stats Dashboard</a>";
    echo "</div>";
} else {
    echo "<div class='step' style='border-color: #ff6b6b;'>";
    echo "<span class='error'>⚠ Installation completed with errors:</span><br>";
    foreach ($errors as $error) {
        echo "- $error<br>";
    }
    echo "</div>";
}

echo "</div></body></html>";
?>
