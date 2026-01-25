<?php
define('SMF', 1);

$modSettings = [
    'mohaa_stats_api_url' => 'http://localhost:8000', // Mock server URL
    'mohaa_stats_server_token' => 'test_token',
    'mohaa_stats_cache_duration' => 60,
    'mohaa_stats_api_timeout' => 5,
    'mohaa_stats_enabled' => 1
];

$txt = [];
$scripturl = 'http://localhost/index.php';
$context = [];
$user_info = ['is_guest' => false, 'id' => 1];

function cache_get_data($key, $ttl) {
    return null; // Always miss cache for testing
}

function cache_put_data($key, $data, $ttl) {
    // No-op
}

function clean_cache($type) {
    // No-op
}

function loadLanguage($lang) {
    // No-op
}

function fatal_error($msg, $log) {
    echo "FATAL ERROR: $msg\n";
    exit(1);
}

function fatal_lang_error($msg, $log) {
    echo "FATAL LANG ERROR: $msg\n";
    exit(1);
}

function loadCSSFile($file, $params) {}
function loadJavaScriptFile($file, $params) {}
function addInlineJavaScript($js, $defer) {}
function loadTemplate($template) {}
function constructPageIndex($url, $start, $total, $limit) { return ''; }
function redirectexit($url) { echo "REDIRECT: $url\n"; }
function checkSession($type = 'post') {}
function obExit($do_header = null, $do_footer = null, $do_gzip = null, $do_iframe = null) { exit; }
function JavaScriptEscape($string) { return json_encode($string); }

// DB Mocks
$smcFunc = [
    'db_query' => function($identifier, $query, $params = []) {
        return true;
    },
    'db_num_rows' => function($request) {
        return 0;
    },
    'db_free_result' => function($request) {},
    'db_fetch_assoc' => function($request) { return false; }
];
