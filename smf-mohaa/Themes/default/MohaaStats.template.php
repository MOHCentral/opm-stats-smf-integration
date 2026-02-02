<?php
declare(strict_types=1);
/**
 * MOHAA Stats - Main Templates
 *
 * @package MohaaStats
 * @version 1.0.0
 */

/**
 * Main dashboard template - The War Room
 */
function template_mohaa_stats_main()
{
    global $context, $txt, $scripturl, $user_info;
    
    // Load achievement widget template
    if (function_exists('template_mohaa_achievement_widget') === false) {
        loadTemplate('MohaaAchievementsWidget');
    }
    
    echo '
    <div class="mohaa-war-room">
        <div class="war-room-header">
            <div class="header-title">
                <h1>⚔️ THE WAR ROOM</h1>
                <span class="header-subtitle">Medal of Honor Allied Assault - Live Combat Statistics</span>
            </div>
            <div class="header-actions">';
    
    if (!$user_info['is_guest']) {
        echo '
                <a href="', $scripturl, '?action=mohaastats;sa=link" class="button">🔗 Link Identity</a>';
    }
    
    echo '
            </div>
        </div>
        
        <div class="mohaa-stats-grid">';
    
    // Global stats cards - Enhanced with icons and animations
    if (!empty($context['mohaa_stats']['global'])) {
        $stats = $context['mohaa_stats']['global'];
        
        echo '
            <div class="mohaa-stat-cards animated">
                <div class="mohaa-stat-card kills">
                    <div class="card-icon">💀</div>
                    <div class="card-content">
                        <div class="stat-value counter" data-target="', $stats['total_kills'] ?? 0, '">', number_format($stats['total_kills'] ?? 0), '</div>
                        <div class="stat-label">', $txt['mohaa_kills'], '</div>
                    </div>
                </div>
                <div class="mohaa-stat-card players">
                    <div class="card-icon">👥</div>
                    <div class="card-content">
                        <div class="stat-value counter" data-target="', $stats['total_players'] ?? 0, '">', number_format($stats['total_players'] ?? 0), '</div>
                        <div class="stat-label">Soldiers</div>
                    </div>
                </div>
                <div class="mohaa-stat-card matches">
                    <div class="card-icon">🎮</div>
                    <div class="card-content">
                        <div class="stat-value counter" data-target="', $stats['total_matches'] ?? 0, '">', number_format($stats['total_matches'] ?? 0), '</div>
                        <div class="stat-label">', $txt['mohaa_matches_played'], '</div>
                    </div>
                </div>
                <div class="mohaa-stat-card headshots">
                    <div class="card-icon">🎯</div>
                    <div class="card-content">
                        <div class="stat-value counter" data-target="', $stats['total_headshots'] ?? 0, '">', number_format($stats['total_headshots'] ?? 0), '</div>
                        <div class="stat-label">', $txt['mohaa_headshots'], '</div>
                    </div>
                </div>
                <div class="mohaa-stat-card achievements">
                    <div class="card-icon">🏆</div>
                    <div class="card-content">
                        <div class="stat-value counter" data-target="', $stats['total_achievements_unlocked'] ?? 0, '">', number_format($stats['total_achievements_unlocked'] ?? 0), '</div>
                        <div class="stat-label">Achievements Unlocked</div>
                    </div>
                </div>
            </div>';
    }
    
    echo '
            <div class="mohaa-main-content">
                <div class="mohaa-left-column">';
    
    // Top Players
    echo '
                    <div class="mohaa-panel">
                        <h3 class="category_header">', $txt['mohaa_leaderboards'], '</h3>
                        <div class="windowbg">';
    
    if (!empty($context['mohaa_stats']['top_players']['players'])) {
        echo '
                            <table class="mohaa-leaderboard-mini">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Player</th>
                                        <th>', $txt['mohaa_kills'], '</th>
                                    </tr>
                                </thead>
                                <tbody>';
        
        foreach ($context['mohaa_stats']['top_players']['players'] as $i => $player) {
            $rankClass = $i < 3 ? ' rank-' . ($i + 1) : '';
            echo '
                                    <tr>
                                        <td class="rank', $rankClass, '">#', $i + 1, '</td>
                                        <td><a href="', $scripturl, '?action=mohaastats;sa=player;guid=', urlencode($player['player_id'] ?? ''), '">', htmlspecialchars($player['player_name'] ?? 'Unknown'), '</a></td>
                                        <td>', number_format($player['kills'] ?? 0), '</td>
                                    </tr>';
        }
        
        echo '
                                </tbody>
                            </table>
                            <div class="mohaa-view-all">
                                <a href="', $scripturl, '?action=mohaastats;sa=leaderboards">', $txt['mohaa_leaderboards'], ' &rarr;</a>
                            </div>';
    } else {
        echo '<p class="centertext">', $txt['mohaa_api_error'], '</p>';
    }
    
    echo '
                        </div>
                    </div>';
    
    // Recent Matches
    echo '
                    <div class="mohaa-panel">
                        <h3 class="category_header">', $txt['mohaa_matches'], '</h3>
                        <div class="windowbg">';
    
    if (!empty($context['mohaa_stats']['recent_matches'])) {
        echo '
                            <ul class="mohaa-match-list">';
        
        foreach ($context['mohaa_stats']['recent_matches'] as $match) {
            echo '
                                <li class="mohaa-match-item">
                                    <a href="', $scripturl, '?action=mohaastats;sa=match;id=', urlencode($match['id']), '">
                                        <span class="match-map">', htmlspecialchars($match['map_name']), '</span>
                                        <span class="match-mode">', htmlspecialchars($match['game_mode']), '</span>
                                        <span class="match-players">', $match['player_count'], ' players</span>
                                        <span class="match-time">', timeformat($match['end_time']), '</span>
                                    </a>
                                </li>';
        }
        
        echo '
                            </ul>
                            <div class="mohaa-view-all">
                                <a href="', $scripturl, '?action=mohaastats;sa=matches">', $txt['mohaa_matches'], ' &rarr;</a>
                            </div>';
    } else {
        echo '<p class="centertext">', $txt['mohaa_api_error'], '</p>';
    }
    
    echo '
                        </div>
                    </div>
                </div>';
    
    // Right column - Live matches AND Achievement Widget
    echo '
                <div class="mohaa-right-column">';
    
    // Achievement Widget - Link to achievements system
    if (!$user_info['is_guest'] && !empty($context['mohaa_stats']['achievement_widget'])) {
        echo '
                    <div class="mohaa-panel">';
        template_mohaa_achievement_widget();
        echo '
                    </div>';
    } else {
        // Show global achievement stats for guests
        echo '
                    <div class="mohaa-panel">
                        <h3 class="category_header">🏆 Achievements</h3>
                        <div class="windowbg">
                            <div class="achievement-promo">
                                <div class="promo-icon">🎖️</div>
                                <p>Over <strong>540+</strong> achievements to unlock!</p>
                                <p class="promo-tiers">10 Tiers: Bronze → Immortal</p>
                                <a href="', $scripturl, '?action=mohaachievements" class="button">Explore Achievements</a>
                            </div>
                        </div>
                    </div>';
    }
    
    // Live Matches Panel
    echo '
                    <div class="mohaa-panel">
                        <h3 class="category_header">🔴 ', $txt['mohaa_live'], '</h3>
                        <div class="windowbg" id="mohaa-live-matches">';
    
    template_mohaa_live_matches_content();
    
    echo '
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-refresh live matches every 15 seconds
        setInterval(function() {
            fetch("', $scripturl, '?action=mohaaapi;endpoint=live")
                .then(r => r.json())
                .then(data => {
                    // Update live matches panel
                    document.getElementById("mohaa-live-matches").innerHTML = 
                        data.length ? data.map(m => `<div class="live-match">...</div>`).join("") : "', $txt['mohaa_no_live_matches'], '";
                });
        }, 15000);
    </script>';
}

/**
 * Live matches content (can be used standalone or in dashboard)
 */
function template_mohaa_live_matches_content()
{
    global $context, $txt, $scripturl;
    
    if (empty($context['mohaa_stats']['live_matches'])) {
        echo '<p class="centertext">', $txt['mohaa_no_live_matches'], '</p>';
        return;
    }
    
    foreach ($context['mohaa_stats']['live_matches'] as $match) {
        echo '
            <div class="mohaa-live-match">
                <div class="live-indicator"><span class="pulse"></span> LIVE</div>
                <div class="live-server">', htmlspecialchars($match['server_name']), '</div>
                <div class="live-map">', htmlspecialchars($match['map_name']), '</div>
                <div class="live-players">', $match['player_count'], '/', $match['max_players'], ' ', $txt['mohaa_players_online'], '</div>';
        
        if (!empty($match['team_match'])) {
            echo '
                <div class="live-score">
                    <span class="team-allies">', $match['allies_score'], '</span>
                    <span class="vs">vs</span>
                    <span class="team-axis">', $match['axis_score'], '</span>
                </div>';
        }
        
        echo '
            </div>';
    }
}

/**
 * Live matches page (standalone page for mohaastats;sa=live)
 */
function template_mohaa_live()
{
    global $context, $txt, $scripturl;
    
    echo '
    <div class="mohaa-live-page">
        <h2 class="category_header">', $txt['mohaa_live'] ?? 'Live Matches', '</h2>';
    
    if (empty($context['mohaa_live_matches'])) {
        echo '
        <div class="windowbg centertext">
            <p>', $txt['mohaa_no_live_matches'] ?? 'No live matches at the moment.', '</p>
            <p><a href="', $scripturl, '?action=mohaaservers">', $txt['mohaa_browse_servers'] ?? 'Browse Servers', '</a></p>
        </div>';
    } else {
        echo '
        <div class="windowbg">';
        
        foreach ($context['mohaa_live_matches'] as $match) {
            echo '
            <div class="mohaa-live-match">
                <div class="live-indicator"><span class="pulse"></span> LIVE</div>
                <div class="live-server">', htmlspecialchars($match['server_name'] ?? 'Unknown Server'), '</div>
                <div class="live-map">', htmlspecialchars($match['map_name'] ?? 'Unknown Map'), '</div>
                <div class="live-players">', $match['player_count'] ?? 0, '/', $match['max_players'] ?? 0, ' ', $txt['mohaa_players_online'] ?? 'Players', '</div>';
            
            if (!empty($match['team_match'])) {
                echo '
                <div class="live-score">
                    <span class="team-allies">', $match['allies_score'] ?? 0, '</span>
                    <span class="vs">vs</span>
                    <span class="team-axis">', $match['axis_score'] ?? 0, '</span>
                </div>';
            }
            
            echo '
            </div>';
        }
        
        echo '
        </div>';
    }
    
    echo '
    </div>
    
    <style>
        .mohaa-live-page { margin: 1em 0; }
        .mohaa-live-match { 
            display: flex; 
            align-items: center; 
            gap: 1em; 
            padding: 1em; 
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .mohaa-live-match:last-child { border-bottom: none; }
        .live-indicator { 
            color: #f44336; 
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 0.5em;
        }
        .pulse {
            width: 10px;
            height: 10px;
            background: #f44336;
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
        .live-server { font-weight: bold; flex: 1; }
        .live-map { color: #aaa; }
        .live-players { color: #4caf50; }
        .live-score { display: flex; gap: 0.5em; font-size: 1.2em; }
        .team-allies { color: #4caf50; }
        .team-axis { color: #f44336; }
        .vs { color: #888; }
    </style>
    
    <script>
        // Auto-refresh live matches every 15 seconds
        setTimeout(function() {
            location.reload();
        }, 15000);
    </script>';
}

/**
 * Leaderboards template
 */
function template_mohaa_leaderboards()
{
    global $context, $txt, $scripturl;
    
    $currentStat = $context['mohaa_leaderboard']['stat'];
    $currentPeriod = $context['mohaa_leaderboard']['period'];
    
    echo '
    <div class="mohaa-leaderboards">
        <h2 class="category_header">', $txt['mohaa_leaderboards'], '</h2>
        
        <div class="windowbg mohaa-filters">
            <form action="', $scripturl, '?action=mohaastats;sa=leaderboards" method="get">
                <input type="hidden" name="action" value="mohaastats">
                <input type="hidden" name="sa" value="leaderboards">
                
                <label>', $txt['mohaa_leaderboard_stat'], ':
                    <select name="stat" onchange="this.form.submit()">
                        <option value="kills"', $currentStat === 'kills' ? ' selected' : '', '>', $txt['mohaa_stat_kills'], '</option>
                        <option value="kd"', $currentStat === 'kd' ? ' selected' : '', '>', $txt['mohaa_stat_kd'], '</option>
                        <option value="score"', $currentStat === 'score' ? ' selected' : '', '>', $txt['mohaa_stat_score'], '</option>
                        <option value="headshots"', $currentStat === 'headshots' ? ' selected' : '', '>', $txt['mohaa_stat_headshots'], '</option>
                        <option value="accuracy"', $currentStat === 'accuracy' ? ' selected' : '', '>', $txt['mohaa_stat_accuracy'], '</option>
                    </select>
                </label>
                
                <label>', $txt['mohaa_leaderboard_period'], ':
                    <select name="period" onchange="this.form.submit()">
                        <option value="all"', $currentPeriod === 'all' ? ' selected' : '', '>', $txt['mohaa_period_all'], '</option>
                        <option value="month"', $currentPeriod === 'month' ? ' selected' : '', '>', $txt['mohaa_period_month'], '</option>
                        <option value="week"', $currentPeriod === 'week' ? ' selected' : '', '>', $txt['mohaa_period_week'], '</option>
                        <option value="day"', $currentPeriod === 'day' ? ' selected' : '', '>', $txt['mohaa_period_day'], '</option>
                    </select>
                </label>
            </form>
        </div>
        
        <div class="windowbg">';
    
    $players = $context['mohaa_leaderboard']['players'] ?? [];

    echo '
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community@31.0.0/styles/ag-grid.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community@31.0.0/styles/ag-theme-alpine.css">
        <script src="https://cdn.jsdelivr.net/npm/ag-grid-community@31.0.0/dist/ag-grid-community.min.js"></script>

        <style>
            #grid-leaderboard {
                height: 600px;
                width: 100%;
                border-radius: 8px;
                overflow: hidden;
            }
        </style>

        <div class="windowbg" style="padding: 0; border: none; background: transparent;">
            <div id="grid-leaderboard" class="ag-theme-alpine"></div>
        </div>';

    echo '
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const leaderboardData = ', json_encode($players), ';
            const gridOptions = {
                rowData: leaderboardData,
                columnDefs: [
                    { 
                        headerName: "#", 
                        valueGetter: "node.rowIndex + 1", 
                        width: 70, 
                        pinned: "left",
                        cellStyle: { fontWeight: "bold", textAlign: "center" }
                    },
                    { 
                        headerName: "Player", 
                        field: "player_name", 
                        flex: 2, 
                        pinned: "left",
                        cellRenderer: params => {
                            if (!params.data.player_id) return params.value;
                            return `<a href="', $scripturl, '?action=mohaastats;sa=player;guid=${encodeURIComponent(params.data.player_id)}" style="font-weight:600; color:#3498db;">${params.value || "Unknown"}</a>`;
                        }
                    },
                    { 
                        headerName: "', $txt['mohaa_' . $currentStat] ?? ucfirst($currentStat), '", 
                        field: "value", 
                        flex: 1,
                        cellStyle: { fontWeight: "bold", color: "#e67e22" }
                    },
                    { headerName: "Kills", field: "kills", flex: 1 },
                    { headerName: "Deaths", field: "deaths", flex: 1 },
                    { 
                        headerName: "K/D", 
                        valueGetter: params => {
                            if (!params.data.deaths || params.data.deaths === 0) return params.data.kills || 0;
                            return (params.data.kills / params.data.deaths).toFixed(2);
                        },
                        flex: 1
                    }
                ],
                defaultColDef: {
                    sortable: true,
                    resizable: true,
                    filter: true,
                },
                pagination: true,
                paginationPageSize: 20
            };
            new agGrid.Grid(document.querySelector("#grid-leaderboard"), gridOptions);
        });
    </script>';
    
    echo '
        </div>
    </div>';
}

/**
 * Matches list template
 */
function template_mohaa_matches_list()
{
    global $context, $txt, $scripturl;
    
    $matches = $context['mohaa_matches']['list'] ?? [];
    
    // Process matches for JS
    foreach ($matches as &$m) {
        $m['formatted_duration'] = gmdate('i:s', $m['duration'] ?? 0);
        $m['formatted_date'] = timeformat($m['ended_at'] ?? $m['started_at'] ?? time());
        // Short ID for display
        $m['short_id'] = '#' . substr($m['id'] ?? 'N/A', 0, 8) . '...';
    }

    echo '
    <div class="mohaa-matches-list">
        <div class="mohaa-header-premium">
            <h2 class="category_header">', $txt['mohaa_matches'] ?? 'Recent Matches', '</h2>
        </div>
        
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community@31.0.0/styles/ag-grid.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community@31.0.0/styles/ag-theme-alpine.css">
        <script src="https://cdn.jsdelivr.net/npm/ag-grid-community@31.0.0/dist/ag-grid-community.min.js"></script>

        <style>
            .mohaa-header-premium {
                margin-bottom: 20px;
            }
            #grid-matches {
                height: 600px;
                width: 100%;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }
            .ag-theme-alpine {
                --ag-header-background-color: #f8f9fa;
                --ag-header-foreground-color: #2d3436;
                --ag-row-hover-color: #f1f2f6;
                --ag-selected-row-background-color: #e3f2fd;
            }
        </style>

        <div class="windowbg" style="padding: 0; border: none; background: transparent;">
            <div id="grid-matches" class="ag-theme-alpine"></div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const matchesData = ', json_encode($matches), ';
            const gridOptions = {
                rowData: matchesData,
                columnDefs: [
                    { 
                        headerName: "Match ID", 
                        field: "id", 
                        flex: 1,
                        cellRenderer: params => {
                            if (!params.value) return "N/A";
                            const shortId = params.value.substring(0, 8) + "...";
                            return `<a href="', $scripturl, '?action=mohaastats;sa=match;id=${params.value}" style="font-weight:600; color:#3498db;">#${shortId}</a>`;
                        }
                    },
                    { headerName: "Map", field: "map", flex: 1, filter: true },
                    { headerName: "Game Type", field: "game_type", width: 120, filter: true },
                    { headerName: "Server", field: "server_name", flex: 1.5, filter: true },
                    { headerName: "Players", field: "player_count", width: 100, sortable: true },
                    { headerName: "Duration", field: "formatted_duration", width: 100 },
                    { headerName: "Date", field: "formatted_date", flex: 1.2, sort: "desc", comparator: (v1, v2, nodeA, nodeB) => {
                        const tA = nodeA.data.ended_at || nodeA.data.started_at || 0;
                        const tB = nodeB.data.ended_at || nodeB.data.started_at || 0;
                        return tA - tB;
                    }}
                ],
                defaultColDef: {
                    sortable: true,
                    resizable: true,
                    filter: "agTextColumnFilter",
                },
                pagination: true,
                paginationPageSize: 20,
                paginationPageSizeSelector: [10, 20, 50, 100],
                domLayout: "normal",
                animateRows: true
            };

            const gridDiv = document.querySelector("#grid-matches");
            new agGrid.Grid(gridDiv, gridOptions);
        });
    </script>';
}

/**
 * Player profile template
 */
function template_mohaa_player()
{
    global $context, $txt, $scripturl;
    
    $player = $context['mohaa_player']['info'];
    
    echo '
    <div class="mohaa-player-profile">
        <h2 class="category_header">', htmlspecialchars($player['name']), '</h2>
        
        <div class="mohaa-player-header windowbg">
            <div class="player-avatar">
                <span class="avatar-letter">', strtoupper(substr($player['name'], 0, 1)), '</span>
            </div>
            <div class="player-info">
                <h3>', htmlspecialchars($player['name']), '</h3>
                <div class="player-meta">
                    <span class="player-rank">', $txt['mohaa_rank'], ' #', $player['rank'] ?? 'N/A', '</span>';
    
    if (!empty($player['verified'])) {
        echo '
                    <span class="verified-badge">', $txt['mohaa_verified_player'], '</span>';
    }
    
    echo '
                    <span class="last-seen">', $txt['mohaa_last_seen'], ': ', timeformat($player['last_active'] ?? time()), '</span>
                </div>
            </div>
        </div>
        
        <div class="mohaa-stat-cards">
            <div class="mohaa-stat-card">
                <div class="stat-value">', number_format($player['kills'] ?? 0), '</div>
                <div class="stat-label">', $txt['mohaa_kills'], '</div>
            </div>
            <div class="mohaa-stat-card">
                <div class="stat-value">', number_format($player['deaths'] ?? 0), '</div>
                <div class="stat-label">', $txt['mohaa_deaths'], '</div>
            </div>
            <div class="mohaa-stat-card">
                <div class="stat-value kd-', ($player['kd'] ?? 0) >= 1 ? 'positive' : 'negative', '">', number_format($player['kd'] ?? 0, 2), '</div>
                <div class="stat-label">', $txt['mohaa_kd_ratio'], '</div>
            </div>
            <div class="mohaa-stat-card">
                <div class="stat-value">', number_format($player['headshots'] ?? 0), '</div>
                <div class="stat-label">', $txt['mohaa_headshots'], '</div>
            </div>
            <div class="mohaa-stat-card">
                <div class="stat-value">', number_format($player['accuracy'] ?? 0, 1), '%</div>
                <div class="stat-label">', $txt['mohaa_accuracy'], '</div>
            </div>
            <div class="mohaa-stat-card">
                <div class="stat-value">', number_format($player['matches'] ?? 0), '</div>
                <div class="stat-label">', $txt['mohaa_matches_played'], '</div>
            </div>
        </div>';
    
    // Tabs
    echo '
        <div class="mohaa-tabs">
            <button class="tab-button active" data-tab="overview">', $txt['mohaa_player_overview'], '</button>
            <button class="tab-button" data-tab="weapons">', $txt['mohaa_player_weapons'], '</button>
            <button class="tab-button" data-tab="matches">', $txt['mohaa_player_matches'], '</button>
            <button class="tab-button" data-tab="achievements">', $txt['mohaa_player_achievements'], '</button>
        </div>';
    
    // Tab content - Overview
    echo '
        <div class="mohaa-tab-content" id="tab-overview">
            <div class="windowbg">
                <h4>Performance Chart</h4>
                <div id="player-performance-chart"></div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
            <script>
            document.addEventListener("DOMContentLoaded", function() {
                var matches = ' . json_encode(array_reverse($context['mohaa_player']['matches'] ?? [])) . ';
                if (matches.length > 0) {
                    var labels = matches.map(m => m.map_name);
                    var kds = matches.map(m => parseFloat(m.kd).toFixed(2));
                    var kills = matches.map(m => parseInt(m.kills));

                    var options = {
                        series: [{
                            name: "K/D Ratio",
                            type: "line",
                            data: kds
                        }, {
                            name: "Kills",
                            type: "column",
                            data: kills
                        }],
                        chart: {
                            height: 300,
                            type: "line",
                            toolbar: { show: false }
                        },
                        stroke: { width: [3, 0], curve: "smooth" },
                        plotOptions: { bar: { borderRadius: 4, columnWidth: "40%" } },
                        dataLabels: { enabled: false },
                        labels: labels,
                        xaxis: { labels: { show: false } }, // Hide map names if too many
                        colors: ["#FFA000", "#1976D2"],
                        yaxis: [{
                            title: { text: "K/D Ratio", style: { color: "#FFA000" } },
                            labels: { style: { colors: "#FFA000" } },
                            min: 0
                        }, {
                            opposite: true,
                            title: { text: "Kills", style: { color: "#1976D2" } },
                            labels: { style: { colors: "#1976D2" } }
                        }],
                        grid: { borderColor: "#f1f1f1" },
                        tooltip: { theme: "light" }
                    };

                    new ApexCharts(document.querySelector("#player-performance-chart"), options).render();
                } else {
                    document.getElementById("player-performance-chart").innerHTML = "<p class=\'centertext\'>Not enough data for performance chart.</p>";
                }
            });
            </script>
            </div>
        </div>';
    
    // Tab content - Weapons
    echo '
        <div class="mohaa-tab-content" id="tab-weapons" style="display:none;">
            <div class="windowbg" style="padding: 0;">
                <div id="grid-player-weapons" class="ag-theme-alpine" style="height: 400px; width: 100%;"></div>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const weaponData = ', json_encode($context['mohaa_player']['weapons'] ?? []), ';
                    const gridOptions = {
                        rowData: weaponData,
                        columnDefs: [
                            { field: "name", headerName: "Weapon", flex: 1, pinned: "left" },
                            { field: "kills", headerName: "Kills", width: 100, sort: "desc", valueFormatter: p => p.value.toLocaleString() },
                            { field: "headshots", headerName: "HS", width: 100 },
                            { field: "accuracy", headerName: "Accuracy", width: 110, valueFormatter: p => parseFloat(p.value).toFixed(1) + "%" },
                            { field: "hs_rate", headerName: "HS Rate", width: 110, valueFormatter: p => parseFloat(p.value).toFixed(1) + "%" }
                        ],
                        defaultColDef: { sortable: true, resizable: true },
                        domLayout: "autoHeight"
                    };
                    new agGrid.Grid(document.querySelector("#grid-player-weapons"), gridOptions);
                });
            </script>
        </div>';
    
    // Tab content - Matches
    echo '
        <div class="mohaa-tab-content" id="tab-matches" style="display:none;">
            <div class="windowbg">';
    
    if (!empty($context['mohaa_player']['matches'])) {
        echo '
                <ul class="mohaa-match-history">';
        
        foreach ($context['mohaa_player']['matches'] as $match) {
            $kdClass = ($match['kd'] ?? 0) >= 1 ? 'positive' : 'negative';
            
            echo '
                    <li class="match-item">
                        <a href="', $scripturl, '?action=mohaastats;sa=match;id=', urlencode($match['match_id']), '">
                            <div class="match-kd ', $kdClass, '">', number_format($match['kd'] ?? 0, 1), '</div>
                            <div class="match-details">
                                <span class="match-map">', htmlspecialchars($match['map_name']), '</span>
                                <span class="match-stats">', $match['kills'], 'K / ', $match['deaths'], 'D</span>
                            </div>
                            <div class="match-result">';
            
            if (isset($match['is_win'])) {
                echo $match['is_win'] ? '<span class="win">WIN</span>' : '<span class="loss">LOSS</span>';
            }
            
            echo '
                            </div>
                            <div class="match-time">', timeformat($match['played_at']), '</div>
                        </a>
                    </li>';
        }
        
        echo '
                </ul>';
    }
    
    echo '
            </div>
        </div>';
    
    // Tab content - Achievements
    echo '
        <div class="mohaa-tab-content" id="tab-achievements" style="display:none;">
            <div class="windowbg mohaa-achievements-grid">';
    
    if (!empty($context['mohaa_player']['achievements'])) {
        foreach ($context['mohaa_player']['achievements'] as $achievement) {
            $unlockedClass = $achievement['unlocked'] ? 'unlocked' : 'locked';
            
            echo '
                <div class="achievement-card ', $unlockedClass, '">
                    <div class="achievement-icon">', $achievement['unlocked'] ? '🏆' : '🔒', '</div>
                    <div class="achievement-info">
                        <h5>', htmlspecialchars($achievement['name']), '</h5>
                        <p>', htmlspecialchars($achievement['description']), '</p>';
            
            if ($achievement['unlocked']) {
                echo '<span class="unlocked-date">', timeformat($achievement['unlocked_at']), '</span>';
            } elseif (!empty($achievement['progress'])) {
                echo '<div class="progress-bar"><div class="progress" style="width:', $achievement['progress'], '%"></div></div>';
            }
            
            echo '
                    </div>
                </div>';
        }
    }
    
    echo '
            </div>
        </div>
    </div>
    
    <script>
        // Tab switching
        document.querySelectorAll(".tab-button").forEach(btn => {
            btn.addEventListener("click", function() {
                document.querySelectorAll(".tab-button").forEach(b => b.classList.remove("active"));
                document.querySelectorAll(".mohaa-tab-content").forEach(c => c.style.display = "none");
                this.classList.add("active");
                document.getElementById("tab-" + this.dataset.tab).style.display = "block";
            });
        });
    </script>';
}

/**
 * Link identity template
 */
function template_mohaa_link_identity()
{
    global $context, $txt, $scripturl;
    
    echo '
    <div class="mohaa-link-identity">
        <h2 class="category_header">', $txt['mohaa_link_identity'], '</h2>
        
        <div class="windowbg">
            <h3>', $txt['mohaa_linked_identities'], '</h3>';
    
    if (!empty($context['mohaa_identities'])) {
        echo '
            <table class="table_grid">
                <thead>
                    <tr class="title_bar">
                        <th>Player Name</th>
                        <th>GUID</th>
                        <th>Linked</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($context['mohaa_identities'] as $identity) {
            echo '
                    <tr class="windowbg">
                        <td>', htmlspecialchars($identity['player_name']), '</td>
                        <td><code>', htmlspecialchars($identity['player_guid']), '</code></td>
                        <td>', timeformat($identity['linked_at']), '</td>
                        <td>
                            <form action="', $scripturl, '?action=mohaastats;sa=link" method="post" style="display:inline;">
                                <input type="hidden" name="action_type" value="unlink">
                                <input type="hidden" name="identity_id" value="', $identity['id_identity'], '">
                                <input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
                                <button type="submit" class="button" onclick="return confirm(\'', $txt['mohaa_unlink_confirm'], '\')">', $txt['mohaa_unlink'], '</button>
                            </form>
                        </td>
                    </tr>';
        }
        
        echo '
                </tbody>
            </table>';
    } else {
        echo '<p>', $txt['mohaa_no_identities'], '</p>';
    }
    
    echo '
        </div>
        
        <div class="windowbg">
            <h3>', $txt['mohaa_generate_token'], '</h3>
            <p>Generate a token to authenticate from the game client.</p>';
    
    if (!empty($context['mohaa_token'])) {
        echo '
            <div class="mohaa-token-box">
                <p>', $txt['mohaa_token_instructions'], '</p>
                <code class="token">login ', htmlspecialchars($context['mohaa_token']), '</code>
                <p class="expires">', $txt['mohaa_token_expires'], ': ', $context['mohaa_token_expires'], 's</p>
            </div>';
    } else {
        echo '
            <form action="', $scripturl, '?action=mohaastats;sa=link" method="post">
                <input type="hidden" name="action_type" value="generate_token">
                <input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
                <button type="submit" class="button">', $txt['mohaa_generate_token'], '</button>
            </form>';
    }
    
    echo '
        </div>
        
        <div class="windowbg">
            <h3>', $txt['mohaa_generate_claim'], '</h3>
            <p>Generate a claim code to permanently link your game identity.</p>';
    
    if (!empty($context['mohaa_claim_code'])) {
        echo '
            <div class="mohaa-claim-box">
                <p>', $txt['mohaa_claim_instructions'], '</p>
                <code class="claim-code">claim ', htmlspecialchars($context['mohaa_claim_code']), '</code>
            </div>';
    } else {
        echo '
            <form action="', $scripturl, '?action=mohaastats;sa=link" method="post">
                <input type="hidden" name="action_type" value="generate_claim">
                <input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
                <button type="submit" class="button">', $txt['mohaa_generate_claim'], '</button>
            </form>';
    }
    
    echo '
        </div>
    </div>';
}

/**
 * Maps page - shows all maps with stats
 */
function template_mohaa_maps()
{
    global $context, $txt, $scripturl;
    
    $maps = $context['mohaa_maps'] ?? [];
    
    echo '
    <style>
        .maps-page { padding: 20px 0; }
        .maps-header {
            background: linear-gradient(135deg, #1a5f2a 0%, #2d8a3e 100%);
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 25px;
            color: white;
            text-align: center;
        }
        .maps-header h1 { margin: 0; font-size: 2.2em; text-transform: uppercase; letter-spacing: 2px; }
        .maps-header p { margin: 10px 0 0; opacity: 0.8; }
        .maps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        .map-card {
            background: #fff;
            border: 1px solid #e1e4e8;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .map-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        .map-image {
            height: 140px;
            background: linear-gradient(135deg, #3a4a5c 0%, #2c3e50 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3em;
        }
        .map-name {
            padding: 15px;
            font-weight: 700;
            font-size: 1.1em;
            border-bottom: 1px solid #e1e4e8;
        }
        .map-stats {
            padding: 15px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .map-stat {
            text-align: center;
        }
        .map-stat-value { font-size: 1.3em; font-weight: bold; color: #2c3e50; }
        .map-stat-label { font-size: 0.85em; color: #7f8c8d; }
        .map-footer {
            padding: 12px 15px;
            background: #f8f9fa;
            border-top: 1px solid #e1e4e8;
            text-align: center;
        }
        .map-footer a { color: #3498db; text-decoration: none; font-weight: 600; }
        .empty-maps { text-align: center; padding: 40px; color: #7f8c8d; }
    </style>
    
    <div class="maps-page">
        <div class="maps-header">
            <h1>🗺️ ', $txt['mohaa_maps'] ?? 'Battle Maps', '</h1>
            <p>Explore the battlefields of Medal of Honor</p>
        </div>
        
        <div class="maps-grid">';
    
    if (empty($maps)) {
        echo '
            <div class="empty-maps">
                <p>📍 No map data available yet.</p>
                <p>Map statistics will appear once games are played.</p>
            </div>';
    } else {
        foreach ($maps as $map) {
            $mapName = $map['name'] ?? $map['map_name'] ?? 'Unknown';
            $displayName = ucwords(str_replace(['_', '-'], ' ', $mapName));
            
            echo '
            <div class="map-card">
                <div class="map-image">🏔️</div>
                <div class="map-name">', htmlspecialchars($displayName), '</div>
                <div class="map-stats">
                    <div class="map-stat">
                        <div class="map-stat-value">', number_format($map['total_kills'] ?? 0), '</div>
                        <div class="map-stat-label">Kills</div>
                    </div>
                    <div class="map-stat">
                        <div class="map-stat-value">', number_format($map['matches_played'] ?? 0), '</div>
                        <div class="map-stat-label">Matches</div>
                    </div>
                    <div class="map-stat">
                        <div class="map-stat-value">', number_format($map['unique_players'] ?? 0), '</div>
                        <div class="map-stat-label">Players</div>
                    </div>
                    <div class="map-stat">
                        <div class="map-stat-value">', isset($map['avg_playtime']) ? floor($map['avg_playtime'] / 60) . 'm' : '0m', '</div>
                        <div class="map-stat-label">Avg Time</div>
                    </div>
                </div>
                <div class="map-footer">
                    <a href="', $scripturl, '?action=mohaastats;sa=map;name=', urlencode($mapName), '">View Details →</a>
                </div>
            </div>';
        }
    }
    
    echo '
        </div>
    </div>';
}

/**
 * Weapons page - shows all weapons with stats
 */
function template_mohaa_weapons()
{
    global $context, $txt, $scripturl;
    
    $weapons = $context['mohaa_weapons'] ?? [];
    
    echo '
    <style>
        .weapons-page { padding: 20px 0; }
        .weapons-header {
            background: linear-gradient(135deg, #8e44ad 0%, #9b59b6 100%);
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 25px;
            color: white;
            text-align: center;
        }
        .weapons-header h1 { margin: 0; font-size: 2.2em; text-transform: uppercase; }
        .weapons-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        .weapon-card {
            background: #fff;
            border: 1px solid #e1e4e8;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.2s;
        }
        .weapon-card:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,0.12); }
        .weapon-icon { height: 80px; display: flex; align-items: center; justify-content: center; font-size: 2.5em; background: #f8f9fa; }
        .weapon-name { padding: 12px; font-weight: 700; text-align: center; border-bottom: 1px solid #e1e4e8; }
        .weapon-stats { padding: 15px; }
        .weapon-stat { display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid #f0f0f0; }
        .weapon-stat:last-child { border-bottom: none; }
        .weapon-stat-label { color: #7f8c8d; }
        .weapon-stat-value { font-weight: bold; color: #2c3e50; }
        .empty-weapons { text-align: center; padding: 40px; color: #7f8c8d; }
    </style>
    
    <div class="weapons-page">
        <div class="weapons-header">
            <h1>🔫 ', $txt['mohaa_weapons'] ?? 'Arsenal', '</h1>
        </div>
        
        <div class="weapons-grid">';
    
    if (empty($weapons)) {
        echo '
            <div class="empty-weapons">
                <p>🔫 No weapon data available yet.</p>
            </div>';
    } else {
        foreach ($weapons as $weapon) {
            $weaponName = $weapon['name'] ?? $weapon['weapon_name'] ?? 'Unknown';
            
            echo '
            <div class="weapon-card">
                <div class="weapon-icon">🔫</div>
                <div class="weapon-name">', htmlspecialchars($weaponName), '</div>
                <div class="weapon-stats">
                    <div class="weapon-stat">
                        <span class="weapon-stat-label">Kills</span>
                        <span class="weapon-stat-value">', number_format($weapon['total_kills'] ?? 0), '</span>
                    </div>
                    <div class="weapon-stat">
                        <span class="weapon-stat-label">Headshots</span>
                        <span class="weapon-stat-value">', number_format($weapon['headshots'] ?? 0), '</span>
                    </div>
                    <div class="weapon-stat">
                        <span class="weapon-stat-label">Accuracy</span>
                        <span class="weapon-stat-value">', number_format($weapon['accuracy'] ?? 0, 1), '%</span>
                    </div>
                </div>
            </div>';
        }
    }
    
    echo '
        </div>
    </div>';
}

/**
 * Game Types page
 */
function template_mohaa_gametypes()
{
    global $context, $txt, $scripturl;
    
    $gametypes = $context['mohaa_gametypes'] ?? [];
    
    echo '
    <style>
        .gametypes-page { padding: 20px 0; }
        .gametypes-header {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 25px;
            color: white;
            text-align: center;
        }
        .gametypes-header h1 { margin: 0; font-size: 2.2em; text-transform: uppercase; }
        .gametypes-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .gametype-card {
            background: #fff;
            border: 1px solid #e1e4e8;
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            transition: transform 0.2s;
        }
        .gametype-card:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,0.12); }
        .gametype-icon { font-size: 3em; margin-bottom: 15px; }
        .gametype-name { font-size: 1.3em; font-weight: 700; margin-bottom: 10px; }
        .gametype-desc { color: #7f8c8d; margin-bottom: 15px; }
        .gametype-stats { display: flex; justify-content: center; gap: 30px; }
        .gametype-stat-value { font-size: 1.5em; font-weight: bold; color: #2c3e50; }
        .gametype-stat-label { font-size: 0.85em; color: #95a5a6; }
        .empty-gametypes { text-align: center; padding: 40px; color: #7f8c8d; }
    </style>
    
    <div class="gametypes-page">
        <div class="gametypes-header">
            <h1>🎮 ', $txt['mohaa_gametypes'] ?? 'Game Modes', '</h1>
        </div>
        
        <div class="gametypes-grid">';
    
    $icons = [
        'ffa' => '⚔️', 'dm' => '⚔️', 'deathmatch' => '⚔️',
        'tdm' => '👥', 'team' => '👥',
        'obj' => '🎯', 'objective' => '🎯',
        'lib' => '🏃', 'liberation' => '🏃',
        'dem' => '💣', 'demolition' => '💣',
    ];
    
    if (empty($gametypes)) {
        echo '
            <div class="empty-gametypes">
                <p>🎮 No game mode data available yet.</p>
            </div>';
    } else {
        foreach ($gametypes as $gt) {
            $name = strtolower($gt['name'] ?? $gt['gametype'] ?? 'unknown');
            $icon = $icons[$name] ?? '🎮';
            $displayName = ucwords(str_replace('_', ' ', $gt['name'] ?? $gt['gametype'] ?? 'Unknown'));
            
            echo '
            <div class="gametype-card">
                <div class="gametype-icon">', $icon, '</div>
                <div class="gametype-name">', htmlspecialchars($displayName), '</div>
                <div class="gametype-stats">
                    <div>
                        <div class="gametype-stat-value">', number_format($gt['matches_played'] ?? 0), '</div>
                        <div class="gametype-stat-label">Matches</div>
                    </div>
                    <div>
                        <div class="gametype-stat-value">', number_format($gt['total_players'] ?? 0), '</div>
                        <div class="gametype-stat-label">Players</div>
                    </div>
                </div>
            </div>';
        }
    }
    
    echo '
        </div>
    </div>';
}

/**
 * Helper: Format stat value based on type
 */
function mohaa_format_stat($value, $type)
{
    switch ($type) {
        case 'kd':
        case 'accuracy':
            return number_format($value, 2);
        case 'playtime':
            return floor($value / 3600) . 'h';
        default:
            return number_format($value);
    }
}


