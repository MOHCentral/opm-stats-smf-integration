<?php
declare(strict_types=1);
/**
 * MOHAA Stats Core - Database Installation Script
 *
 * This script runs automatically when installing the SMF Package.
 * It builds the necessary tables for:
 * 1. Player Identity (GUID Registry)
 * 2. Achievements
 * 3. Teams & Tournaments
 *
 * @package MohaaStats
 */

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF'))
	die('<b>Error:</b> Cannot execute - please run via SMF Package Manager.');

global $smcFunc, $db_prefix;

// =============================================================================
// 1. Identity Linking Tables
// =============================================================================

// mohaa_identities: Links SMF Member IDs to In-Game GUIDs
$smcFunc['db_create_table']('{db_prefix}mohaa_identities',
	array(
		array('name' => 'id', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'auto' => true),
		array('name' => 'id_member', 'type' => 'int', 'size' => 10, 'unsigned' => true),
		array('name' => 'guid', 'type' => 'varchar', 'size' => 64),
		array('name' => 'verified', 'type' => 'tinyint', 'size' => 1, 'default' => 0),
		array('name' => 'verification_token', 'type' => 'varchar', 'size' => 64, 'default' => ''),
		array('name' => 'linked_at', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 0),
	),
	array(
		array('type' => 'primary', 'columns' => array('id')),
		array('type' => 'unique', 'columns' => array('guid')),
		array('type' => 'index', 'columns' => array('id_member')),
	),
	array(),
	'ignore'
);

// =============================================================================
// 2. Achievements Tables
// =============================================================================

// mohaa_achievement_defs: Definitions of all badges
$smcFunc['db_create_table']('{db_prefix}mohaa_achievement_defs',
	array(
		array('name' => 'id_achievement', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'auto' => true),
		array('name' => 'code', 'type' => 'varchar', 'size' => 64),
		array('name' => 'name', 'type' => 'varchar', 'size' => 100),
		array('name' => 'description', 'type' => 'text'),
		array('name' => 'category', 'type' => 'varchar', 'size' => 50, 'default' => 'basic'),
		array('name' => 'tier', 'type' => 'tinyint', 'size' => 3, 'unsigned' => true, 'default' => 1),
		array('name' => 'icon', 'type' => 'varchar', 'size' => 100),
		array('name' => 'requirement_type', 'type' => 'varchar', 'size' => 50),
		array('name' => 'requirement_value', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 1),
		array('name' => 'points', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 10),
		array('name' => 'is_hidden', 'type' => 'tinyint', 'size' => 1, 'default' => 0),
		array('name' => 'sort_order', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 0),
	),
	array(
		array('type' => 'primary', 'columns' => array('id_achievement')),
		array('type' => 'unique', 'columns' => array('code')),
		array('type' => 'index', 'columns' => array('category')),
	),
	array(),
	'ignore'
);

// mohaa_player_achievements: Unlocked instances
$smcFunc['db_create_table']('{db_prefix}mohaa_player_achievements',
	array(
		array('name' => 'id_unlock', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'auto' => true),
		array('name' => 'id_member', 'type' => 'int', 'size' => 10, 'unsigned' => true),
		array('name' => 'player_guid', 'type' => 'varchar', 'size' => 64),
		array('name' => 'id_achievement', 'type' => 'int', 'size' => 10, 'unsigned' => true),
		array('name' => 'unlocked_date', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 0),
		array('name' => 'match_id', 'type' => 'varchar', 'size' => 64, 'default' => ''),
		array('name' => 'progress', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 0),
	),
	array(
		array('type' => 'primary', 'columns' => array('id_unlock')),
		array('type' => 'unique', 'columns' => array('id_member', 'id_achievement')),
		array('type' => 'index', 'columns' => array('id_member')),
		array('type' => 'index', 'columns' => array('unlocked_date')),
	),
	array(),
	'ignore'
);

// =============================================================================
// 3. Teams Tables
// =============================================================================

// mohaa_teams: Clan/Team profiles
$smcFunc['db_create_table']('{db_prefix}mohaa_teams',
	array(
		array('name' => 'id_team', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'auto' => true),
		array('name' => 'team_name', 'type' => 'varchar', 'size' => 255),
		array('name' => 'team_tag', 'type' => 'varchar', 'size' => 10),
		array('name' => 'description', 'type' => 'text'),
		array('name' => 'logo_url', 'type' => 'varchar', 'size' => 255, 'default' => ''),
		array('name' => 'website', 'type' => 'varchar', 'size' => 255, 'default' => ''),
		array('name' => 'id_captain', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 0),
		array('name' => 'founded_date', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 0),
		array('name' => 'status', 'type' => 'varchar', 'size' => 20, 'default' => 'active'),
		array('name' => 'rating', 'type' => 'int', 'size' => 10, 'default' => 1000),
		array('name' => 'wins', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 0),
		array('name' => 'losses', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 0),
		array('name' => 'recruiting', 'type' => 'tinyint', 'size' => 4, 'unsigned' => true, 'default' => 0),
	),
	array(
		array('type' => 'primary', 'columns' => array('id_team')),
		array('type' => 'unique', 'columns' => array('team_name')),
		array('type' => 'index', 'columns' => array('rating')),
	),
	array(),
	'ignore'
);

// mohaa_team_members: Roster
$smcFunc['db_create_table']('{db_prefix}mohaa_team_members',
	array(
		array('name' => 'id_team', 'type' => 'int', 'size' => 10, 'unsigned' => true),
		array('name' => 'id_member', 'type' => 'int', 'size' => 10, 'unsigned' => true),
		array('name' => 'role', 'type' => 'varchar', 'size' => 20, 'default' => 'member'),
		array('name' => 'joined_date', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 0),
		array('name' => 'status', 'type' => 'varchar', 'size' => 20, 'default' => 'active'),
	),
	array(
		array('type' => 'primary', 'columns' => array('id_team', 'id_member')),
		array('type' => 'index', 'columns' => array('id_member')),
	),
	array(),
	'ignore'
);

// mohaa_team_invites: Invites/Requests
$smcFunc['db_create_table']('{db_prefix}mohaa_team_invites',
	array(
		array('name' => 'id_invite', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'auto' => true),
		array('name' => 'id_team', 'type' => 'int', 'size' => 10, 'unsigned' => true),
		array('name' => 'id_member', 'type' => 'int', 'size' => 10, 'unsigned' => true),
		array('name' => 'id_inviter', 'type' => 'int', 'size' => 10, 'unsigned' => true),
		array('name' => 'invite_type', 'type' => 'varchar', 'size' => 20, 'default' => 'invite'),
		array('name' => 'status', 'type' => 'varchar', 'size' => 20, 'default' => 'pending'),
		array('name' => 'created_date', 'type' => 'int', 'size' => 10, 'unsigned' => true, 'default' => 0),
	),
	array(
		array('type' => 'primary', 'columns' => array('id_invite')),
		array('type' => 'index', 'columns' => array('id_team')),
		array('type' => 'index', 'columns' => array('id_member')),
	),
	array(),
	'ignore'
);

// =============================================================================
// 4. Achievement Definitions (Initial Seed)
// =============================================================================
// We only insert if the table is empty to avoid overwriting custom changes.

$request = $smcFunc['db_query']('', 'SELECT COUNT(*) FROM {db_prefix}mohaa_achievement_defs');
list($count) = $smcFunc['db_fetch_row']($request);
$smcFunc['db_free_result']($request);

if ($count == 0) {
	$achievements = array(
		array('first_blood', 'First Blood', 'Get your first kill', 'basic', 1, 'medal_bronze', 'kills', 1, 5),
		array('marksman', 'Marksman', 'Get 100 kills', 'basic', 1, 'medal_bronze', 'kills', 100, 10),
		array('soldier', 'Soldier', 'Get 500 kills', 'basic', 1, 'medal_silver', 'kills', 500, 25),
		array('veteran', 'Veteran', 'Get 1,000 kills', 'basic', 1, 'medal_gold', 'kills', 1000, 50),
		array('legend', 'Legend', 'Get 10,000 kills', 'basic', 1, 'medal_diamond', 'kills', 10000, 250),
		array('thompson_expert', 'Thompson Expert', 'Get 500 kills with Thompson', 'weapon', 2, 'weapon_thompson', 'weapon_kills_thompson', 500, 50),
		array('headhunter', 'Headhunter', 'Get 100 headshots', 'tactical', 3, 'headshot', 'headshots', 100, 25),
		array('surgical_precision', 'Surgical Precision', 'Achieve 50% headshot ratio', 'tactical', 3, 'precision', 'headshot_ratio_50', 1, 150),
		array('unstoppable', 'Unstoppable', 'Get a 10 killstreak', 'tactical', 3, 'streak_10', 'killstreak_10', 1, 75),
		array('grave_dancer', 'Grave Dancer', 'Teabag 10 victims', 'humiliation', 4, 'teabag', 'teabags', 10, 25),
	);

	foreach ($achievements as $a) {
		$smcFunc['db_insert']('ignore',
			'{db_prefix}mohaa_achievement_defs',
			array('code' => 'string', 'name' => 'string', 'description' => 'string', 'category' => 'string', 'tier' => 'int', 'icon' => 'string', 'requirement_type' => 'string', 'requirement_value' => 'int', 'points' => 'int'),
			array($a[0], $a[1], $a[2], $a[3], $a[4], $a[5], $a[6], $a[7], $a[8]),
			array('id_achievement')
		);
	}
}

// =============================================================================
// 5. Default Configuration Settings
// =============================================================================

// We check if settings exist, if not, we insert defaults.
$defaults = array(
	'mohaa_stats_enabled' => '1',
	'mohaa_stats_api_url' => 'http://localhost:8080', // Docker/Local default
	'mohaa_stats_menu_title' => 'Stats',
	'mohaa_stats_show_in_profile' => '1',
	'mohaa_stats_allow_linking' => '1',
	'mohaa_stats_leaderboard_limit' => '25',
	'mohaa_stats_recent_matches_limit' => '10',
	'mohaa_stats_rate_limit' => '600',
);

$updates = array();
foreach ($defaults as $variable => $value) {
	// Only add if not already set (preserve existing config on upgrade)
	$request = $smcFunc['db_query']('', 'SELECT value FROM {db_prefix}settings WHERE variable = {string:variable}', array(
		'variable' => $variable,
	));
	if ($smcFunc['db_num_rows']($request) == 0) {
		$updates[$variable] = $value;
	}
	$smcFunc['db_free_result']($request);
}

if (!empty($updates)) {
	updateSettings($updates);
}

if (SMF == 'SSI')
	echo 'Database installation complete!';

