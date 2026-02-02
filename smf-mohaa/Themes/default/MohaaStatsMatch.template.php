<?php
declare(strict_types=1);
/**
 * MOHAA Stats - Match Detail Template
 *
 * @package MohaaStats
 * @version 1.0.0
 */

/**
 * Match detail main template
 */
function template_mohaa_stats_match()
{
    global $context, $scripturl, $txt;

    $match = $context['mohaa_match'];
    
    // Load Dependencies
    echo '
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community@31.0.0/styles/ag-grid.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community@31.0.0/styles/ag-theme-alpine.css">
    <script src="https://cdn.jsdelivr.net/npm/ag-grid-community@31.0.0/dist/ag-grid-community.min.js"></script>
    
    <style>
        .mohaa-match-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .mohaa-match-header .stat-label { color: rgba(255,255,255,0.7); font-size: 0.85em; text-transform: uppercase; letter-spacing: 1px; }
        .mohaa-match-header .stat-value { font-size: 1.4em; font-weight: 700; margin-top: 5px; }
        
        .mohaa-tabs { display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid #dfe6e9; padding-bottom: 10px; }
        .tab-button { 
            padding: 10px 20px; 
            border: none; 
            background: transparent; 
            color: #7f8c8d; 
            cursor: pointer; 
            font-weight: 600; 
            border-radius: 8px;
            transition: all 0.2s;
        }
        .tab-button:hover { background: #f8f9fa; color: #2c3e50; }
        .tab-button.active { background: #3498db; color: white; box-shadow: 0 4px 6px rgba(52,152,219,0.3); }
        
        .ag-theme-alpine { 
            --ag-header-background-color: #f8f9fa;
            --ag-header-foreground-color: #2c3e50;
            --ag-row-hover-color: rgba(52, 152, 219, 0.05);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
    </style>';

    echo '
    <div class="mohaa-match-header">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 20px; text-align: center;">
            <div>
                <div class="stat-label">🗺️ ', $txt['mohaa_map'], '</div>
                <div class="stat-value">', htmlspecialchars($match['map_name']), '</div>
            </div>
            <div>
                <div class="stat-label">🎮 ', $txt['mohaa_mode'], '</div>
                <div class="stat-value">', ucfirst($match['game_mode']), '</div>
            </div>
            <div>
                <div class="stat-label">⏱️ ', $txt['mohaa_duration'], '</div>
                <div class="stat-value">', format_duration($match['duration']), '</div>
            </div>
            <div>
                <div class="stat-label">👥 ', $txt['mohaa_players'], '</div>
                <div class="stat-value">', count($match['players'] ?? []), '</div>
            </div>
            <div>
                <div class="stat-label">📅 ', $txt['mohaa_date'], '</div>
                <div class="stat-value" style="font-size: 1.1em;">', timeformat($match['ended_at']), '</div>
            </div>
        </div>';

    // Team scores for team matches
    if (!empty($match['team_match'])) {
        echo '
        <div class="mohaa-team-scores" style="display: flex; justify-content: center; align-items: center; gap: 40px; margin-top: 25px; padding-top: 25px; border-top: 1px solid rgba(255,255,255,0.1);">
            <div class="team-allies" style="text-align: center;">
                <div style="color: #60a5fa; font-size: 3em; font-weight: 800; line-height: 1;">', $match['allies_score'], '</div>
                <div style="text-transform: uppercase; letter-spacing: 2px; font-weight: bold; margin-top: 5px;">Allies</div>
            </div>
            <div style="font-size: 2em; color: rgba(255,255,255,0.3); font-style: italic; font-weight: 300;">vs</div>
            <div class="team-axis" style="text-align: center;">
                <div style="color: #f87171; font-size: 3em; font-weight: 800; line-height: 1;">', $match['axis_score'], '</div>
                <div style="text-transform: uppercase; letter-spacing: 2px; font-weight: bold; margin-top: 5px;">Axis</div>
            </div>
        </div>';
    }

    echo '
    </div>';

    // Tabs
    echo '
    <div class="mohaa-tabs">
        <button class="tab-button active" data-tab="scoreboard">📊 Scoreboard</button>
        <button class="tab-button" data-tab="versus">⚔️ Versus Matrix</button>
        <button class="tab-button" data-tab="timeline">📜 Match Timeline</button>
        <button class="tab-button" data-tab="weapons">🔫 Weapons</button>
        <button class="tab-button" data-tab="heatmap">🗺️ Heatmap</button>
    </div>';

    // Scoreboard Tab
    echo '
    <div id="tab-scoreboard" class="mohaa-tab-content" style="display: block;">
        <div id="grid-scoreboard" class="ag-theme-alpine" style="height: 500px; width: 100%;"></div>
    </div>';

    // Versus Tab
    echo '
    <div id="tab-versus" class="mohaa-tab-content windowbg" style="display: none; padding: 20px;">
        <h4 style="margin-top:0;">Versus Matrix (Who killed Who)</h4>
        <div id="chart-versus" style="min-height: 500px; background: #fff; border-radius: 8px; border: 1px solid #dfe6e9;"></div>
    </div>';

    // Timeline Tab
    echo '
    <div id="tab-timeline" class="mohaa-tab-content windowbg" style="display: none; padding: 20px;">
        <h4 style="margin-top:0;">', $txt['mohaa_timeline'], '</h4>';
    
    template_match_timeline($match['timeline'] ?? []);
    
    echo '
    </div>';

    // Weapons Tab
    echo '
    <div id="tab-weapons" class="mohaa-tab-content windowbg" style="display: none; padding: 20px;">
        <h4 style="margin-top:0;">', $txt['mohaa_weapon_breakdown'], '</h4>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div id="grid-weapons" class="ag-theme-alpine" style="height: 400px;"></div>
            <div id="matchWeaponChart" style="height: 400px; background: #fff; border-radius: 8px; border: 1px solid #dfe6e9;"></div>
        </div>
    </div>';

    // Heatmap Tab
    echo '
    <div id="tab-heatmap" class="mohaa-tab-content windowbg" style="display: none; padding: 20px;">
        <h4 style="margin-top:0;">', $txt['mohaa_heatmap'], '</h4>
        <div class="mohaa-heatmap-controls" style="margin-bottom: 20px; display: flex; gap: 20px;">
            <label style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
                <input type="radio" name="heatmap_type" value="kills" checked onchange="updateHeatmap(this.value)">
                <span style="font-weight: 600;">🔫 ', $txt['mohaa_kills'], '</span>
            </label>
            <label style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
                <input type="radio" name="heatmap_type" value="deaths" onchange="updateHeatmap(this.value)">
                <span style="font-weight: 600;">💀 ', $txt['mohaa_deaths'], '</span>
            </label>
        </div>
        <div id="match-heatmap" class="mohaa-heatmap-container" style="border-radius: 12px; overflow: hidden; border: 1px solid #dfe6e9;"></div>
    </div>';

    // Heatmap data init script
    echo '
    <script>
        var heatmapData = ', json_encode($match['heatmap_data'] ?? []), ';
        var mapImage = "Themes/default/images/mohaastats/maps/', $match['map_name'], '.jpg";
        
        function updateHeatmap(type) {
            if (window.MohaaStats && MohaaStats.initHeatmap) {
                MohaaStats.initHeatmap("match-heatmap", mapImage, heatmapData[type] || [], type);
            }
        }
        
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(() => updateHeatmap("kills"), 100);
        });
    </script>';

    // Inject Scripts for Grids and Charts
    $players = array_values($match['players'] ?? []);
    foreach($players as &$p) {
        $p['kd'] = $p['deaths'] > 0 ? round($p['kills'] / $p['deaths'], 2) : $p['kills'];
    }
    
    $weapons = array_slice(array_values($match['top_weapons'] ?? []), 0, 10);

    $versusData = [];
    if (!empty($match['versus'])) {
        foreach ($match['versus'] as $killer => $victims) {
            $dataPoints = [];
            foreach ($victims as $v) {
                 $dataPoints[] = ['x' => $v['opponent_name'], 'y' => $v['kills']];
            }
            $versusData[] = ['name' => $killer, 'data' => $dataPoints];
        }
    }

    echo '
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Tab handling
            document.querySelectorAll(".tab-button").forEach(b => {
                b.addEventListener("click", e => {
                    document.querySelectorAll(".mohaa-tab-content").forEach(c => c.style.display = "none");
                    document.querySelectorAll(".tab-button").forEach(btn => btn.classList.remove("active"));
                    document.getElementById("tab-" + e.currentTarget.getAttribute("data-tab")).style.display = "block";
                    e.currentTarget.classList.add("active");
                    window.dispatchEvent(new Event("resize"));
                });
            });

            // Scoreboard Grid
            const scoreboardData = ', json_encode($players), ';
            const scoreboardOptions = {
                rowData: scoreboardData,
                columnDefs: [
                    { field: "player_name", headerName: "Player", flex: 2, pinned: "left", cellRenderer: params => {
                        const val = params.value || "Unknown";
                        return `<a href="', $scripturl, '?action=mohaastats;sa=player;guid=${params.data.player_id}" style="font-weight:600; color:#3498db;">${val}</a>`;
                    }},
                    { field: "team", headerName: "Team", width: 100, cellRenderer: params => {
                        const style = params.value === "allies" ? "color:#3b82f6" : (params.value === "axis" ? "color:#ef4444" : "");
                        return `<span style="${style}; font-weight:bold; text-transform:uppercase;">${params.value || "-"}</span>`;
                    }},
                    { field: "score", headerName: "Score", width: 100, sort: "desc", valueFormatter: p => p.value.toLocaleString() },
                    { field: "kills", headerName: "Kills", width: 80, valueFormatter: p => p.value.toLocaleString() },
                    { field: "deaths", headerName: "Deaths", width: 80 },
                    { field: "kd", headerName: "K/D", width: 80, cellStyle: p => ({ color: p.value >= 1 ? "#10b981" : "#ef4444", fontWeight: "bold" }) },
                    { field: "headshots", headerName: "HS", width: 80 }
                ],
                defaultColDef: { sortable: true, filter: true, resizable: true },
                pagination: true,
                paginationPageSize: 20
            };
            new agGrid.Grid(document.querySelector("#grid-scoreboard"), scoreboardOptions);

            // Weapons Grid
            const weaponsData = ', json_encode($weapons), ';
            const weaponsOptions = {
                rowData: weaponsData,
                columnDefs: [
                    { field: "name", headerName: "Weapon", flex: 1, pinned: "left" },
                    { field: "kills", headerName: "Kills", width: 100, sort: "desc" },
                    { field: "headshots", headerName: "HS", width: 100 },
                    { headerName: "HS %", width: 100, valueGetter: p => p.data.kills > 0 ? Math.round((p.data.headshots / p.data.kills) * 100) + "%" : "0%" }
                ],
                defaultColDef: { sortable: true, resizable: true },
                domLayout: "autoHeight"
            };
            new agGrid.Grid(document.querySelector("#grid-weapons"), weaponsOptions);

            // Weapons Chart
            if (weaponsData.length > 0) {
                const chartOptions = {
                    series: [{ name: "Kills", data: weaponsData.map(w => w.kills) }],
                    chart: { type: "bar", height: 350, toolbar: {show:false} },
                    colors: ["#3498db"],
                    plotOptions: { bar: { borderRadius: 4, horizontal: true } },
                    dataLabels: { enabled: false },
                    xaxis: { categories: weaponsData.map(w => w.name) },
                    title: { text: "Top Weapons", align: "center", style: { color: "#2c3e50" } }
                };
                new ApexCharts(document.querySelector("#matchWeaponChart"), chartOptions).render();
            }

            // Versus Heatmap
            const versusData = ', json_encode($versusData), ';
            if(versusData.length > 0) {
                 const vOptions = {
                    series: versusData,
                    chart: { type: "heatmap", height: 500, toolbar: {show:false} },
                    dataLabels: { enabled: true, style: { colors: ["#2c3e50"] } },
                    colors: ["#3498db"],
                    xaxis: { type: "category" },
                    title: { text: "Killer (Y) vs Victim (X)", align: "center" },
                    plotOptions: { heatmap: { shadeIntensity: 0.5, colorScale: { ranges: [{from:0, to:0, color:"#f8f9fa"}] } } }
                };
                new ApexCharts(document.querySelector("#chart-versus"), vOptions).render();
            }
        });
    </script>';
}

/**
 * Match timeline template
 */
function template_match_timeline($events)
{
    global $txt;

    if (empty($events)) {
        echo '<p class="centertext">', $txt['mohaa_no_data'], '</p>';
        return;
    }

    echo '
    <div class="mohaa-timeline">';

    foreach ($events as $event) {
        $type = $event['type'] ?? 'default';
        $actor = htmlspecialchars($event['actor'] ?? 'Unknown');
        $target = htmlspecialchars($event['target'] ?? '');
        $detail = htmlspecialchars($event['detail'] ?? '');
        
        // Construct Description
        $desc = match($type) {
            'player_kill' => "<strong>$actor</strong> killed <strong>$target</strong>" . ($detail ? " with $detail" : ""),
            'headshot' => "<strong>$actor</strong> headshotted <strong>$target</strong>" . ($detail ? " with $detail" : ""),
            'flag_capture' => "<strong>$actor</strong> captured the flag!",
            'match_start' => "Match Started on " . ($detail ?: "Map"),
            'match_end' => "Match Ended",
            default => "$actor - $type"
        };
        
        $icon = match($type) {
            'player_kill' => '💀',
            'headshot' => '🎯',
            'flag_capture' => '🚩',
            'match_start' => '🏁',
            'match_end' => '🏆',
            default => '•'
        };
        
        echo '
        <div class="timeline-event">
            <span class="event-time">', isset($event['timestamp']) ? gmdate('i:s', (int)$event['timestamp']) : '--:--', '</span>
            <span class="event-icon">', $icon, '</span>
            <span class="event-text">', $desc, '</span>
        </div>';
    }

    echo '
    </div>
    
    <style>
        .mohaa-timeline { max-height: 400px; overflow-y: auto; }
        .timeline-event { display: flex; align-items: center; gap: 10px; padding: 8px 0; border-bottom: 1px solid rgba(0,0,0,0.05); }
        .event-time { color: #666; font-family: monospace; min-width: 50px; }
        .event-icon { font-size: 1.2em; }
    </style>';
}

/**
 * Format duration helper
 */
function format_duration($seconds)
{
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = $seconds % 60;
    
    if ($hours > 0) {
        return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
    }
    return sprintf('%d:%02d', $minutes, $secs);
}
