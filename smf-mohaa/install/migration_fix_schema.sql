-- =============================================================================
-- MOHAA Stats Plugin - Schema Fix Migration
-- 
-- This script DROPS and RECREATES tables to fix schema mismatches.
-- WARNING: This will DELETE all existing data in these tables!
-- 
-- Run in phpMyAdmin or via: mysql -u smf -p smf < migration_fix_schema.sql
-- =============================================================================

-- =============================================================================
-- PART 1: Identity Tables
-- =============================================================================

DROP TABLE IF EXISTS smf_mohaa_identities;
CREATE TABLE smf_mohaa_identities (
    id_identity INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_member INT UNSIGNED NOT NULL DEFAULT 0,
    player_guid VARCHAR(64) NOT NULL,
    player_name VARCHAR(255) DEFAULT '',
    linked_date INT UNSIGNED DEFAULT 0,
    verified TINYINT UNSIGNED DEFAULT 0,
    UNIQUE KEY unique_guid (player_guid),
    INDEX idx_member (id_member)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS smf_mohaa_claim_codes;
CREATE TABLE smf_mohaa_claim_codes (
    id_claim INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_member INT UNSIGNED NOT NULL DEFAULT 0,
    claim_code VARCHAR(16) NOT NULL,
    created_at INT UNSIGNED DEFAULT 0,
    expires_at INT UNSIGNED DEFAULT 0,
    used TINYINT UNSIGNED DEFAULT 0,
    UNIQUE KEY unique_code (claim_code),
    INDEX idx_member (id_member)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS smf_mohaa_device_tokens;
CREATE TABLE smf_mohaa_device_tokens (
    id_token INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_member INT UNSIGNED NOT NULL DEFAULT 0,
    user_code VARCHAR(16) NOT NULL,
    device_code VARCHAR(64) DEFAULT '',
    created_at INT UNSIGNED DEFAULT 0,
    expires_at INT UNSIGNED DEFAULT 0,
    verified TINYINT UNSIGNED DEFAULT 0,
    UNIQUE KEY unique_user_code (user_code),
    INDEX idx_member (id_member),
    INDEX idx_device (device_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================================
-- PART 2: Achievement Tables
-- =============================================================================

DROP TABLE IF EXISTS smf_mohaa_achievement_progress;
DROP TABLE IF EXISTS smf_mohaa_player_achievements;
DROP TABLE IF EXISTS smf_mohaa_achievement_defs;

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

CREATE TABLE smf_mohaa_player_achievements (
    id_unlock INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_member INT UNSIGNED NOT NULL DEFAULT 0,
    player_guid VARCHAR(64) DEFAULT '',
    id_achievement INT UNSIGNED NOT NULL DEFAULT 0,
    unlocked_date INT UNSIGNED DEFAULT 0,
    progress INT UNSIGNED DEFAULT 0,
    UNIQUE KEY unique_unlock (id_member, id_achievement),
    UNIQUE KEY unique_guid_ach (player_guid, id_achievement),
    INDEX idx_member (id_member),
    INDEX idx_achievement (id_achievement)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE smf_mohaa_achievement_progress (
    id_progress INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_member INT UNSIGNED NOT NULL DEFAULT 0,
    id_achievement INT UNSIGNED NOT NULL DEFAULT 0,
    current_progress INT UNSIGNED DEFAULT 0,
    updated_at INT UNSIGNED DEFAULT 0,
    UNIQUE KEY unique_progress (id_member, id_achievement),
    INDEX idx_member (id_member)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================================
-- PART 3: Teams Tables
-- =============================================================================

DROP TABLE IF EXISTS smf_mohaa_team_challenges;
DROP TABLE IF EXISTS smf_mohaa_team_matches;
DROP TABLE IF EXISTS smf_mohaa_team_invites;
DROP TABLE IF EXISTS smf_mohaa_team_members;
DROP TABLE IF EXISTS smf_mohaa_teams;

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

CREATE TABLE smf_mohaa_team_members (
    id_team INT UNSIGNED NOT NULL,
    id_member INT UNSIGNED NOT NULL,
    role VARCHAR(20) DEFAULT 'member',
    joined_date INT UNSIGNED DEFAULT 0,
    status VARCHAR(20) DEFAULT 'active',
    PRIMARY KEY (id_team, id_member),
    INDEX idx_member (id_member)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

CREATE TABLE smf_mohaa_team_matches (
    id_match INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_team INT UNSIGNED NOT NULL,
    id_opponent INT UNSIGNED NOT NULL,
    match_date INT UNSIGNED DEFAULT 0,
    result VARCHAR(10) DEFAULT '',
    map VARCHAR(50) DEFAULT '',
    score_us INT UNSIGNED DEFAULT 0,
    score_them INT UNSIGNED DEFAULT 0,
    INDEX idx_team (id_team)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================================
-- PART 4: Tournament Tables
-- =============================================================================

DROP TABLE IF EXISTS smf_mohaa_tournament_matches;
DROP TABLE IF EXISTS smf_mohaa_tournament_registrations;
DROP TABLE IF EXISTS smf_mohaa_tournaments;

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
    created_at INT UNSIGNED DEFAULT 0,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE smf_mohaa_tournament_registrations (
    id_tournament INT UNSIGNED NOT NULL,
    id_team INT UNSIGNED NOT NULL,
    seed INT UNSIGNED DEFAULT 0,
    registration_date INT UNSIGNED DEFAULT 0,
    status VARCHAR(20) DEFAULT 'approved',
    PRIMARY KEY (id_tournament, id_team),
    INDEX idx_team (id_team)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
-- PART 5: Seed Achievement Data
-- =============================================================================

INSERT INTO smf_mohaa_achievement_defs (code, name, description, category, tier, icon, requirement_type, requirement_value, points) VALUES
('surgical', 'The Surgeon', 'Achieve 100 Headshots in a single tournament event.', 'tactical', 4, 'surgical', 'headshots_tournament', 100, 500),
('unstoppable', 'Unstoppable Force', 'Win 10 matches in a row without a single loss.', 'dedication', 5, 'unstoppable', 'win_streak', 10, 1000),
('survivalist', 'Survivalist', 'Complete a full match with < 10% HP remaining and 0 deaths.', 'hardcore', 3, 'survivalist', 'hp_survival', 1, 250),
('first_blood', 'First Blood', 'Get your first kill.', 'basic', 1, 'blood', 'kills', 1, 10),
('centurion', 'Centurion', 'Reach 100 kills.', 'basic', 2, 'sword', 'kills', 100, 50),
('thousand_souls', '1000 Souls', 'Reach 1000 kills.', 'basic', 3, 'skull', 'kills', 1000, 200),
('headhunter', 'Headhunter', 'Get 50 headshots.', 'precision', 2, 'target', 'headshots', 50, 75),
('sharpshooter', 'Sharpshooter', 'Achieve 50% accuracy over 1000 shots.', 'precision', 3, 'crosshair', 'accuracy', 50, 150),
('marathon', 'Marathon Runner', 'Run 100 kilometers total.', 'movement', 2, 'runner', 'distance', 100000, 50),
('veteran', 'Veteran', 'Play for 24 hours total.', 'dedication', 2, 'medal', 'playtime', 86400, 100),
('ghost', 'Ghost', 'Complete 5 matches with 0 deaths.', 'hardcore', 4, 'ghost', 'no_death_matches', 5, 300),
('rampage', 'Rampage', 'Get 10 kills without dying.', 'combat', 3, 'fire', 'kill_streak', 10, 150);

SELECT 'Schema migration complete!' as status;
