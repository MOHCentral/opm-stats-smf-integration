<?php
/**
 * MOHAA Stats Core - Database Uninstallation Script
 *
 * @package MohaaStats
 */

if (file_exists(dirname(__FILE__) . '/SSI.php') && !defined('SMF'))
	require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF'))
	die('<b>Error:</b> Cannot execute - please run via SMF Package Manager.');

global $smcFunc, $db_prefix;

// We typically do NOT drop tables on uninstall to preserve data if the user
// is just reinstalling or updating. 
// However, if there was a specific "Remove Data" checkbox, we would check it here.

// For now, we leave the tables intact.
// If you want to force remove, uncomment below:

/*
$smcFunc['db_drop_table']('{db_prefix}mohaa_identities');
$smcFunc['db_drop_table']('{db_prefix}mohaa_achievement_defs');
$smcFunc['db_drop_table']('{db_prefix}mohaa_player_achievements');
$smcFunc['db_drop_table']('{db_prefix}mohaa_teams');
$smcFunc['db_drop_table']('{db_prefix}mohaa_team_members');
$smcFunc['db_drop_table']('{db_prefix}mohaa_team_invites');
*/

if (SMF == 'SSI')
	echo 'Database uninstallation complete!';
