-- =============================================================================
-- MOHAA Stats Plugin - Complete Installation SQL
-- 
-- Run this ONCE in phpMyAdmin AFTER SMF is installed
-- Database: smf (or your SMF database name)
-- 
-- THIS IS THE SINGLE SOURCE OF TRUTH FOR ALL MOHAA PLUGIN TABLES
-- Matches PHP code in Sources/MohaaAchievements.php, MohaaTeams.php, MohaaTournaments.php
-- =============================================================================

-- =============================================================================
-- PART 1: SMF Integration Hooks (smf_settings table)
-- =============================================================================

-- Delete any existing MOHAA entries first
DELETE FROM smf_settings WHERE variable LIKE 'mohaa%';
DELETE FROM smf_settings WHERE variable = 'integrate_pre_include' AND value LIKE '%MohaaStats%';
DELETE FROM smf_settings WHERE variable = 'integrate_actions' AND value LIKE '%MohaaStats%';
DELETE FROM smf_settings WHERE variable = 'integrate_menu_buttons' AND value LIKE '%MohaaStats%';
DELETE FROM smf_settings WHERE variable = 'integrate_admin_areas' AND value LIKE '%MohaaStats%';

-- Insert integration hooks
-- NOTE: API URL should NOT include /api/v1 - PHP adds it automatically
-- Set the actual URL via environment variable MOHAA_API_URL or Admin settings
INSERT INTO smf_settings (variable, value) VALUES 
    ('integrate_pre_include', '$sourcedir/MohaaStats/MohaaStats.php'),
    ('integrate_actions', 'MohaaStats_Actions'),
    ('integrate_menu_buttons', 'MohaaStats_MenuButtons'),
    ('integrate_admin_areas', 'MohaaStats_AdminAreas'),
    ('mohaa_stats_installed', '1'),
    ('mohaa_stats_enabled', '1'),
    ('mohaa_stats_api_url', 'http://localhost:8084'),
    ('mohaa_stats_cache_ttl', '300');

-- =============================================================================
-- PART 2: Core Identity Tables
-- =============================================================================

-- Player identity linking (GUID to SMF member)
CREATE TABLE IF NOT EXISTS smf_mohaa_identities (
    id_identity INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_member INT UNSIGNED NOT NULL,
    player_guid VARCHAR(64) NOT NULL,
    player_name VARCHAR(100),
    linked_date INT UNSIGNED DEFAULT 0,
    verified TINYINT(1) DEFAULT 0,
    UNIQUE KEY unique_guid (player_guid),
    INDEX idx_member (id_member)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Claim codes for linking
CREATE TABLE IF NOT EXISTS smf_mohaa_claim_codes (
    id_claim INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_member INT UNSIGNED NOT NULL,
    claim_code VARCHAR(16) NOT NULL,
    created_at INT UNSIGNED DEFAULT 0,
    expires_at INT UNSIGNED DEFAULT 0,
    used TINYINT(1) DEFAULT 0,
    UNIQUE KEY unique_code (claim_code),
    INDEX idx_member (id_member)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Device tokens for in-game auth
CREATE TABLE IF NOT EXISTS smf_mohaa_device_tokens (
    id_token INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_member INT UNSIGNED NOT NULL,
    user_code VARCHAR(16) NOT NULL,
    device_code VARCHAR(64),
    created_at INT UNSIGNED DEFAULT 0,
    expires_at INT UNSIGNED DEFAULT 0,
    verified TINYINT(1) DEFAULT 0,
    UNIQUE KEY unique_user_code (user_code),
    INDEX idx_member (id_member),
    INDEX idx_device (device_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================================
-- PART 3: Achievement System (matches MohaaAchievements.php)
-- =============================================================================

DROP TABLE IF EXISTS smf_mohaa_achievement_defs;
DROP TABLE IF EXISTS smf_mohaa_player_achievements;
DROP TABLE IF EXISTS smf_mohaa_achievement_progress;

-- Achievement definitions
-- PHP uses: id_achievement, code, name, description, category, tier (INT 1-5), icon, requirement_type, requirement_value, points, is_hidden, sort_order
CREATE TABLE smf_mohaa_achievement_defs (
    id_achievement INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(50) NOT NULL DEFAULT 'basic',
    tier INT UNSIGNED NOT NULL DEFAULT 1,
    icon VARCHAR(50) NOT NULL DEFAULT 'trophy',
    requirement_type VARCHAR(50) NOT NULL,
    requirement_value INT UNSIGNED NOT NULL DEFAULT 1,
    points INT UNSIGNED NOT NULL DEFAULT 10,
    is_hidden TINYINT UNSIGNED NOT NULL DEFAULT 0,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    UNIQUE KEY unique_code (code),
    INDEX idx_category (category),
    INDEX idx_tier (tier)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Player achievement unlocks
-- PHP uses: id_achievement, id_member, unlocked_date
CREATE TABLE smf_mohaa_player_achievements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_member INT UNSIGNED NOT NULL,
    id_achievement INT UNSIGNED NOT NULL,
    unlocked_date INT UNSIGNED DEFAULT 0,
    UNIQUE KEY unique_unlock (id_member, id_achievement),
    INDEX idx_member (id_member),
    INDEX idx_achievement (id_achievement)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Achievement progress tracking
-- PHP uses: id_achievement, id_member, current_progress
CREATE TABLE smf_mohaa_achievement_progress (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_member INT UNSIGNED NOT NULL,
    id_achievement INT UNSIGNED NOT NULL,
    current_progress INT UNSIGNED DEFAULT 0,
    UNIQUE KEY unique_progress (id_member, id_achievement),
    INDEX idx_member (id_member)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================================
-- PART 4: Teams System (matches MohaaTeams.php)
-- =============================================================================

DROP TABLE IF EXISTS smf_mohaa_teams;
DROP TABLE IF EXISTS smf_mohaa_team_members;
DROP TABLE IF EXISTS smf_mohaa_team_invites;
DROP TABLE IF EXISTS smf_mohaa_team_matches;
DROP TABLE IF EXISTS smf_mohaa_team_challenges;

-- Teams
-- PHP uses: id_team, team_name, team_tag, description, logo_url, website, id_captain, founded_date, status, rating, wins, losses, recruiting
CREATE TABLE smf_mohaa_teams (
    id_team INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    team_name VARCHAR(255) NOT NULL,
    team_tag VARCHAR(10) DEFAULT '',
    description TEXT,
    logo_url VARCHAR(255) DEFAULT '',
    website VARCHAR(255) DEFAULT '',
    id_captain INT UNSIGNED DEFAULT 0,
    founded_date INT UNSIGNED DEFAULT 0,
    status VARCHAR(20) DEFAULT 'active',
    rating INT DEFAULT 1000,
    wins INT UNSIGNED DEFAULT 0,
    losses INT UNSIGNED DEFAULT 0,
    recruiting TINYINT UNSIGNED DEFAULT 0,
    UNIQUE KEY unique_name (team_name),
    INDEX idx_status (status),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Team members
-- PHP uses: id_team, id_member, role, joined_date, status (composite PK)
CREATE TABLE smf_mohaa_team_members (
    id_team INT UNSIGNED NOT NULL,
    id_member INT UNSIGNED NOT NULL,
    role VARCHAR(20) DEFAULT 'member',
    joined_date INT UNSIGNED DEFAULT 0,
    status VARCHAR(20) DEFAULT 'active',
    PRIMARY KEY (id_team, id_member),
    INDEX idx_member (id_member)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Team invites
-- PHP uses: id_invite, id_team, id_member, id_inviter, invite_type, status, created_date
CREATE TABLE smf_mohaa_team_invites (
    id_invite INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_team INT UNSIGNED NOT NULL,
    id_member INT UNSIGNED NOT NULL,
    id_inviter INT UNSIGNED NOT NULL,
    invite_type VARCHAR(20) DEFAULT 'invite',
    status VARCHAR(20) DEFAULT 'pending',
    created_date INT UNSIGNED DEFAULT 0,
    INDEX idx_team (id_team),
    INDEX idx_member (id_member)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Team matches (history)
-- PHP uses: id_match, id_team, id_opponent, match_date, result, map, score_us, score_them
CREATE TABLE smf_mohaa_team_matches (
    id_match INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_team INT UNSIGNED NOT NULL,
    id_opponent INT UNSIGNED NOT NULL,
    match_date INT UNSIGNED DEFAULT 0,
    result VARCHAR(10) DEFAULT 'win',
    map VARCHAR(100) DEFAULT '',
    score_us INT DEFAULT 0,
    score_them INT DEFAULT 0,
    INDEX idx_team (id_team),
    INDEX idx_match_date (match_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Team challenges
-- PHP uses: id_challenge, id_team_challenger, id_team_target, challenge_date, match_date, game_mode, map, status
CREATE TABLE smf_mohaa_team_challenges (
    id_challenge INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_team_challenger INT UNSIGNED NOT NULL,
    id_team_target INT UNSIGNED NOT NULL,
    challenge_date INT UNSIGNED DEFAULT 0,
    match_date INT UNSIGNED DEFAULT 0,
    game_mode VARCHAR(50) DEFAULT 'tdm',
    map VARCHAR(100) DEFAULT '',
    status VARCHAR(20) DEFAULT 'pending',
    INDEX idx_challenger (id_team_challenger),
    INDEX idx_target (id_team_target)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================================
-- PART 5: Tournaments System (matches MohaaTournaments.php)
-- =============================================================================

DROP TABLE IF EXISTS smf_mohaa_tournaments;
DROP TABLE IF EXISTS smf_mohaa_tournament_registrations;
DROP TABLE IF EXISTS smf_mohaa_tournament_matches;

-- Tournaments
-- PHP uses: id_tournament, name, description, format, game_type, max_teams, status, tournament_start, tournament_end
CREATE TABLE smf_mohaa_tournaments (
    id_tournament INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    format VARCHAR(20) DEFAULT 'single_elim',
    game_type VARCHAR(50) DEFAULT 'tdm',
    max_teams INT UNSIGNED DEFAULT 16,
    status VARCHAR(20) DEFAULT 'open',
    tournament_start INT UNSIGNED DEFAULT 0,
    tournament_end INT UNSIGNED DEFAULT 0,
    prize_description TEXT,
    rules TEXT,
    created_by INT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tournament registrations
-- PHP uses: id_tournament, id_team, seed, registration_date, status (composite PK)
CREATE TABLE smf_mohaa_tournament_registrations (
    id_tournament INT UNSIGNED NOT NULL,
    id_team INT UNSIGNED NOT NULL,
    seed INT UNSIGNED DEFAULT 0,
    registration_date INT UNSIGNED DEFAULT 0,
    status VARCHAR(20) DEFAULT 'approved',
    PRIMARY KEY (id_tournament, id_team),
    INDEX idx_team (id_team)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tournament matches
-- PHP uses: id_match, id_tournament, round, bracket_group, id_team_a, id_team_b, score_a, score_b, winner_id, match_date
CREATE TABLE smf_mohaa_tournament_matches (
    id_match INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_tournament INT UNSIGNED NOT NULL,
    round INT UNSIGNED DEFAULT 1,
    bracket_group INT UNSIGNED DEFAULT 0,
    id_team_a INT UNSIGNED DEFAULT 0,
    id_team_b INT UNSIGNED DEFAULT 0,
    score_a INT UNSIGNED DEFAULT 0,
    score_b INT UNSIGNED DEFAULT 0,
    winner_id INT UNSIGNED DEFAULT 0,
    match_date INT UNSIGNED DEFAULT 0,
    INDEX idx_tournament (id_tournament),
    INDEX idx_team_a (id_team_a),
    INDEX idx_team_b (id_team_b)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================================
-- PART 6: Seed Achievement Data
-- tier: 1=bronze, 2=silver, 3=gold, 4=platinum, 5=diamond
-- =============================================================================

INSERT INTO smf_mohaa_achievement_defs (code, name, description, category, tier, icon, requirement_type, requirement_value, points, is_hidden, sort_order) VALUES
-- Combat achievements
('first_blood', 'First Blood', 'Get your first kill', 'combat', 1, 'blood', 'kills', 1, 10, 0, 1),
('soldier', 'Soldier', 'Get 100 kills', 'combat', 1, 'medal', 'kills', 100, 25, 0, 2),
('warrior', 'Warrior', 'Get 500 kills', 'combat', 2, 'sword', 'kills', 500, 50, 0, 3),
('veteran', 'Veteran', 'Get 1000 kills', 'combat', 3, 'star', 'kills', 1000, 100, 0, 4),
('legend', 'Legend', 'Get 5000 kills', 'combat', 4, 'crown', 'kills', 5000, 250, 0, 5),
('god_of_war', 'God of War', 'Get 10000 kills', 'combat', 5, 'lightning', 'kills', 10000, 500, 0, 6),
-- Precision achievements
('sharpshooter', 'Sharpshooter', 'Get 50 headshots', 'precision', 1, 'target', 'headshots', 50, 25, 0, 1),
('marksman', 'Marksman', 'Get 250 headshots', 'precision', 2, 'crosshair', 'headshots', 250, 75, 0, 2),
('sniper_elite', 'Sniper Elite', 'Get 1000 headshots', 'precision', 3, 'skull', 'headshots', 1000, 150, 0, 3),
('deadeye', 'Deadeye', 'Get 5000 headshots', 'precision', 4, 'eye', 'headshots', 5000, 300, 0, 4),
-- Survival achievements
('survivor', 'Survivor', 'Play 10 matches', 'survival', 1, 'shield', 'matches', 10, 15, 0, 1),
('hardened', 'Hardened', 'Play 100 matches', 'survival', 2, 'armor', 'matches', 100, 50, 0, 2),
('immortal', 'Immortal', 'Play 500 matches', 'survival', 3, 'infinity', 'matches', 500, 150, 0, 3),
-- Dedication achievements
('rookie', 'Rookie', 'Play for 1 hour total', 'dedication', 1, 'clock', 'playtime_hours', 1, 10, 0, 1),
('dedicated', 'Dedicated', 'Play for 24 hours total', 'dedication', 1, 'timer', 'playtime_hours', 24, 25, 0, 2),
('addicted', 'Addicted', 'Play for 100 hours total', 'dedication', 2, 'gamepad', 'playtime_hours', 100, 75, 0, 3),
('no_life', 'No Life', 'Play for 500 hours total', 'dedication', 3, 'zombie', 'playtime_hours', 500, 200, 0, 4),
-- Skill achievements
('kd_positive', 'Going Positive', 'Achieve a K/D ratio above 1.0', 'skill', 1, 'thumbsup', 'kd_ratio', 1, 20, 0, 1),
('kd_master', 'K/D Master', 'Achieve a K/D ratio above 2.0', 'skill', 2, 'fire', 'kd_ratio', 2, 75, 0, 2),
('unstoppable', 'Unstoppable', 'Achieve a K/D ratio above 3.0', 'skill', 3, 'rocket', 'kd_ratio', 3, 150, 0, 3),
-- Streak achievements
('triple_kill', 'Triple Kill', 'Get 3 kills without dying', 'streak', 1, 'x3', 'killstreak', 3, 20, 0, 1),
('rampage', 'Rampage', 'Get 5 kills without dying', 'streak', 2, 'x5', 'killstreak', 5, 50, 0, 2),
('godlike', 'Godlike', 'Get 10 kills without dying', 'streak', 3, 'x10', 'killstreak', 10, 100, 0, 3),
('unkillable', 'Unkillable', 'Get 20 kills without dying', 'streak', 4, 'x20', 'killstreak', 20, 250, 0, 4),
-- Tactical achievements (from contextual badges)
('surgical', 'The Surgeon', 'Achieve 100 Headshots in a single tournament event.', 'tactical', 4, 'surgical', 'headshots_tournament', 100, 500, 0, 1),
('ghost', 'Ghost', 'Win a round without being seen or taking damage.', 'tactical', 4, 'ghost', 'stealth_round', 1, 500, 0, 2),
-- Objective achievements
('guardian', 'The Guardian', 'Defend the objective for a total of 5 minutes in one game.', 'objective', 2, 'guardian', 'defense_time', 300, 100, 0, 1),
-- Weapon achievements
('trigger_happy', 'Spray & Pray', 'Fire 10,000 rounds of ammunition.', 'weapon', 1, 'trigger_happy', 'shots_fired', 10000, 25, 0, 1),
('resourceful', 'Scavenger', 'Pick up 50 enemy weapons in your career.', 'weapon', 1, 'resourceful', 'pickup_weapons', 50, 50, 0, 2),
-- Hardcore achievements
('survivalist', 'Survivalist', 'Complete a full match with < 10% HP remaining and 0 deaths.', 'hardcore', 3, 'survivalist', 'hp_survival', 1, 250, 0, 1),
-- Troll achievements
('pacifist', 'Pacifist', 'Win a match with 0 kills and 0 deaths (Objective focus).', 'troll', 3, 'pacifist', 'pacifist_win', 1, 300, 0, 1);

-- =============================================================================
-- INSTALLATION COMPLETE!
-- 
-- Next steps:
-- 1. Clear SMF cache: Admin -> Maintenance -> Forum Maintenance -> Rebuild Cache
-- 2. Verify plugin appears in menu
-- 3. Check Admin -> Configuration -> MOHAA Stats settings
-- =============================================================================
