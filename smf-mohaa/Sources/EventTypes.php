<?php
/**
 * Event Type Constants
 * 
 * GENERATED FILE - DO NOT EDIT DIRECTLY
 * Generated at: 2026-02-02T08:30:48.524343
 * Source: opm-stats-api/web/static/openapi.yaml
 * 
 * To add new event types, edit openapi.yaml and run `make generate-types`
 */

namespace MohaaSMF;

/**
 * Canonical event type constants for the OpenMOHAA Stats system.
 * Use these constants instead of string literals to ensure type safety.
 */
class EventTypes
{
    public const GAME_INIT = 'game_init';
    public const GAME_START = 'game_start';
    public const GAME_END = 'game_end';
    public const MATCH_START = 'match_start';
    public const MATCH_END = 'match_end';
    public const MATCH_OUTCOME = 'match_outcome';
    public const ROUND_START = 'round_start';
    public const ROUND_END = 'round_end';
    public const WARMUP_START = 'warmup_start';
    public const WARMUP_END = 'warmup_end';
    public const INTERMISSION_START = 'intermission_start';
    public const PLAYER_KILL = 'player_kill';
    public const DEATH = 'death';
    public const DAMAGE = 'damage';
    public const PLAYER_PAIN = 'player_pain';
    public const HEADSHOT = 'headshot';
    public const PLAYER_SUICIDE = 'player_suicide';
    public const PLAYER_CRUSHED = 'player_crushed';
    public const PLAYER_TELEFRAGGED = 'player_telefragged';
    public const PLAYER_ROADKILL = 'player_roadkill';
    public const PLAYER_BASH = 'player_bash';
    public const PLAYER_TEAMKILL = 'player_teamkill';
    public const WEAPON_FIRE = 'weapon_fire';
    public const WEAPON_HIT = 'weapon_hit';
    public const WEAPON_CHANGE = 'weapon_change';
    public const RELOAD = 'reload';
    public const WEAPON_RELOAD_DONE = 'weapon_reload_done';
    public const WEAPON_READY = 'weapon_ready';
    public const WEAPON_NO_AMMO = 'weapon_no_ammo';
    public const WEAPON_HOLSTER = 'weapon_holster';
    public const WEAPON_RAISE = 'weapon_raise';
    public const WEAPON_DROP = 'weapon_drop';
    public const GRENADE_THROW = 'grenade_throw';
    public const GRENADE_EXPLODE = 'grenade_explode';
    public const JUMP = 'jump';
    public const LAND = 'land';
    public const CROUCH = 'crouch';
    public const PRONE = 'prone';
    public const PLAYER_STAND = 'player_stand';
    public const PLAYER_SPAWN = 'player_spawn';
    public const PLAYER_RESPAWN = 'player_respawn';
    public const DISTANCE = 'distance';
    public const PLAYER_MOVEMENT = 'player_movement';
    public const LADDER_MOUNT = 'ladder_mount';
    public const LADDER_DISMOUNT = 'ladder_dismount';
    public const USE = 'use';
    public const PLAYER_USE_OBJECT_START = 'player_use_object_start';
    public const PLAYER_USE_OBJECT_FINISH = 'player_use_object_finish';
    public const PLAYER_SPECTATE = 'player_spectate';
    public const PLAYER_FREEZE = 'player_freeze';
    public const CHAT = 'chat';
    public const ITEM_PICKUP = 'item_pickup';
    public const ITEM_DROP = 'item_drop';
    public const ITEM_RESPAWN = 'item_respawn';
    public const HEALTH_PICKUP = 'health_pickup';
    public const AMMO_PICKUP = 'ammo_pickup';
    public const ARMOR_PICKUP = 'armor_pickup';
    public const VEHICLE_ENTER = 'vehicle_enter';
    public const VEHICLE_EXIT = 'vehicle_exit';
    public const VEHICLE_DEATH = 'vehicle_death';
    public const VEHICLE_CRASH = 'vehicle_crash';
    public const VEHICLE_CHANGE = 'vehicle_change';
    public const TURRET_ENTER = 'turret_enter';
    public const TURRET_EXIT = 'turret_exit';
    public const SERVER_INIT = 'server_init';
    public const SERVER_START = 'server_start';
    public const SERVER_SHUTDOWN = 'server_shutdown';
    public const SERVER_SPAWNED = 'server_spawned';
    public const SERVER_CONSOLE_COMMAND = 'server_console_command';
    public const HEARTBEAT = 'heartbeat';
    public const MAP_INIT = 'map_init';
    public const MAP_START = 'map_start';
    public const MAP_READY = 'map_ready';
    public const MAP_SHUTDOWN = 'map_shutdown';
    public const MAP_LOAD_START = 'map_load_start';
    public const MAP_LOAD_END = 'map_load_end';
    public const MAP_CHANGE_START = 'map_change_start';
    public const MAP_RESTART = 'map_restart';
    public const TEAM_JOIN = 'team_join';
    public const TEAM_CHANGE = 'team_change';
    public const TEAM_WIN = 'team_win';
    public const VOTE_START = 'vote_start';
    public const VOTE_PASSED = 'vote_passed';
    public const VOTE_FAILED = 'vote_failed';
    public const CONNECT = 'connect';
    public const DISCONNECT = 'disconnect';
    public const CLIENT_BEGIN = 'client_begin';
    public const CLIENT_USERINFO_CHANGED = 'client_userinfo_changed';
    public const PLAYER_INACTIVITY_DROP = 'player_inactivity_drop';
    public const DOOR_OPEN = 'door_open';
    public const DOOR_CLOSE = 'door_close';
    public const EXPLOSION = 'explosion';
    public const ACTOR_SPAWN = 'actor_spawn';
    public const ACTOR_KILLED = 'actor_killed';
    public const BOT_SPAWN = 'bot_spawn';
    public const BOT_KILLED = 'bot_killed';
    public const BOT_ROAM = 'bot_roam';
    public const BOT_CURIOUS = 'bot_curious';
    public const BOT_ATTACK = 'bot_attack';
    public const OBJECTIVE_UPDATE = 'objective_update';
    public const OBJECTIVE_CAPTURE = 'objective_capture';
    public const SCORE_CHANGE = 'score_change';
    public const TEAMKILL_KICK = 'teamkill_kick';
    public const PLAYER_AUTH = 'player_auth';
    public const ACCURACY_SUMMARY = 'accuracy_summary';
    public const IDENTITY_CLAIM = 'identity_claim';

    /**
     * Map of aliases to canonical event types.
     * Use this for display or when receiving data from external sources.
     */
    public const ALIASES = [
    ];

    /**
     * Normalize an event type to its canonical form.
     */
    public static function normalize(string $eventType): string
    {
        return self::ALIASES[$eventType] ?? $eventType;
    }

    /**
     * Check if an event type is valid (canonical or alias).
     */
    public static function isValid(string $eventType): bool
    {
        static $all = null;
        if ($all === null) {
            $reflection = new \ReflectionClass(self::class);
            $constants = $reflection->getConstants();
            $all = array_flip(array_filter($constants, fn($v) => is_string($v) && $v !== ''));
        }
        return isset($all[$eventType]) || isset(self::ALIASES[$eventType]);
    }
}
