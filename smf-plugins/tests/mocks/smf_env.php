<?php
// smf_env.php

if (!defined('SMF')) {
    define('SMF', '1');
}

// Global Variables
global $modSettings, $txt, $context, $scripturl, $user_info, $smcFunc, $settings;

$modSettings = [
    'mohaa_stats_enabled' => 1,
    'mohaa_stats_api_url' => 'http://mock-api.com',
    'mohaa_stats_server_token' => 'mock_token',
    'mohaa_stats_cache_duration' => 60,
    'mohaa_stats_api_timeout' => 3,
];

$txt = [];
$context = [];
$scripturl = 'http://localhost/index.php';
$user_info = ['id' => 1, 'is_guest' => false];
$smcFunc = [
    'db_query' => function() { return true; },
    'db_num_rows' => function() { return 0; },
    'db_free_result' => function() { },
    'db_fetch_assoc' => function() { return null; },
];
$settings = [];

// Functions

if (!function_exists('cache_get_data')) {
    function cache_get_data($key, $ttl = 120) {
        return null; // Always return null to force API calls
    }
}

if (!function_exists('cache_put_data')) {
    function cache_put_data($key, $data, $ttl = 120) {
        return;
    }
}

if (!function_exists('clean_cache')) {
    function clean_cache($type = '') {
        return;
    }
}

if (!function_exists('loadLanguage')) {
    function loadLanguage($lang) {}
}

if (!function_exists('loadTemplate')) {
    function loadTemplate($template) {}
}

if (!function_exists('redirectexit')) {
    function redirectexit($url = '') {
        echo "Redirected to: $url\n";
    }
}

if (!function_exists('fatal_lang_error')) {
    function fatal_lang_error($error, $log = true) {
        throw new Exception("Fatal Lang Error: $error");
    }
}

if (!function_exists('fatal_error')) {
    function fatal_error($error, $log = true) {
        throw new Exception("Fatal Error: $error");
    }
}

if (!function_exists('loadCSSFile')) {
    function loadCSSFile($fileName, $params = []) {}
}

if (!function_exists('loadJavaScriptFile')) {
    function loadJavaScriptFile($fileName, $params = []) {}
}

if (!function_exists('addInlineJavaScript')) {
    function addInlineJavaScript($js, $defer = false) {}
}

if (!function_exists('JavaScriptEscape')) {
    function JavaScriptEscape($string) { return json_encode($string); }
}

if (!function_exists('checkSession')) {
    function checkSession($type = 'post') {}
}

if (!function_exists('obExit')) {
    function obExit($header = null) {}
}

if (!function_exists('constructPageIndex')) {
    function constructPageIndex($base_url, &$start, $max_value, $num_per_page) {
        return "Page Index HTML";
    }
}
