<?php
declare(strict_types=1);
/**
 * AI Performance Predictions Template
 * 
 * Advanced visualizations for:
 * - Next match K/D & Kill predictions
 * - Performance trend forecasting (ApexCharts)
 * - Rival analysis (Win Probability)
 * - AI recommendations
 *
 * @package MohaaStats
 * @version 2.1.1
 */

function template_mohaa_predictions()
{
    global $context, $scripturl, $txt;
    
    echo '
    <div class="cat_bar">
        <h3 class="catbg">
            <span class="main_icons stats floatleft"></span>
            ', $context['page_title'], '
        </h3>
    </div>
    
    <div class="windowbg">
        <div class="content">';
    
    if (isset($context['error'])) {
        echo '
            <div class="errorbox">', $context['error'], '</div>';
        return;
    }

    $predictions = $context['predictions'];
    if (!$predictions || isset($predictions['error'])) {
        echo '
            <div class="errorbox">', $predictions['error'] ?? 'No prediction data available', '</div>';
        return;
    }
    
    // Prediction Dashboard
    echo '
            <div class="prediction_dashboard">
                <div class="prediction_grid">';
    
    // Performance Forecast Card
    template_ai_performance_forecast($predictions);
    
    // Next Match Prediction Card
    template_ai_next_match_prediction($predictions);
    
    // Rival Analysis Card
    template_ai_rival_analysis($predictions);
    
    echo '
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .prediction_dashboard { padding: 20px; background: #0f172a; border-radius: 0 0 8px 8px; }
        .prediction_grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px; }
        .prediction_card { background: #1e293b; border: 1px solid #334155; border-radius: 12px; padding: 24px; color: #f8fafc; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .prediction_card h4 { margin: 0 0 20px 0; font-size: 18px; color: #38bdf8; display: flex; align-items: center; gap: 10px; }
        .metric_big { font-size: 42px; font-weight: 800; text-align: center; color: #fff; margin: 15px 0; }
        .trend_label { display: block; text-align: center; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 20px; }
        .trend_improving { color: #4ade80; }
        .trend_declining { color: #f87171; }
        .trend_stable { color: #94a3b8; }
        
        .confidence_container { margin-bottom: 25px; }
        .confidence_bar { background: #334155; border-radius: 10px; height: 10px; overflow: hidden; margin: 10px 0; }
        .confidence_fill { height: 100%; border-radius: 10px; background: linear-gradient(90deg, #38bdf8, #818cf8); transition: width 1.5s ease-out; }
        
        .rival_list { list-style: none; padding: 0; margin: 0; }
        .rival_item { display: flex; justify-content: space-between; align-items: center; padding: 12px; border-bottom: 1px solid #334155; }
        .rival_item:last-child { border-bottom: none; }
        .rival_info { display: flex; flex-direction: column; }
        .rival_name { font-weight: 600; color: #f1f5f9; }
        .rival_desc { font-size: 12px; color: #94a3b8; }
        .win_prob { font-weight: 800; font-size: 16px; color: #4ade80; }
        
        .recommendations { margin-top: 20px; padding: 15px; background: #0f172a; border-radius: 8px; border-left: 4px solid #38bdf8; }
        .rec_item { margin-bottom: 8px; font-size: 14px; line-height: 1.5; color: #cbd5e1; }
        .rec_item:last-child { margin-bottom: 0; }
        
        .chart_wrap { background: #1e293b; border-radius: 8px; padding: 10px; margin: 15px 0; }
    </style>';
}

function template_ai_performance_forecast($predictions)
{
    $kd = round($predictions['expected_kd'], 2);
    $trend = $predictions['trend'];
    $history = $predictions['performance_history'] ?? [0.5, 0.8, 1.2, 1.1, 1.4]; // Fallback small set
    $chartId = 'forecast_chart_' . mt_rand();
    
    echo '
    <div class="prediction_card">
        <h4><span class="main_icons stats"></span> Performance Forecast</h4>
        <div class="metric_big">', $kd, ' <span style="font-size: 16px; color: #94a3b8;">E-KD</span></div>
        <span class="trend_label trend_', $trend, '">Trend: ', strtoupper($trend), '</span>
        
        <div class="chart_wrap">
            <div id="', $chartId, '"></div>
        </div>
        
        <div class="recommendations">';
    foreach ($predictions['recommendations'] as $rec) {
        echo '
            <div class="rec_item"><strong>[', strtoupper($rec['type']), ']</strong> ', $rec['description'], '</div>';
    }
    echo '
        </div>
    </div>
    
    <script>
    (function() {
        const options = {
            series: [{ name: "Recent K/D", data: ', json_encode($history), ' }],
            chart: { type: "area", height: 180, toolbar: { show: false }, background: "transparent" },
            theme: { mode: "dark" },
            stroke: { curve: "smooth", width: 3, colors: ["#38bdf8"] },
            fill: { type: "gradient", gradient: { shadeIntensity: 1, opacityFrom: 0.5, opacityTo: 0.1 } },
            dataLabels: { enabled: false },
            xaxis: { labels: { show: false }, axisBorder: { show: false } },
            yaxis: { labels: { style: { colors: "#94a3b8" } } },
            grid: { borderColor: "#334155", strokeDashArray: 4 },
            tooltip: { theme: "dark" }
        };
        new ApexCharts(document.querySelector("#', $chartId, '"), options).render();
    })();
    </script>';
}

function template_ai_next_match_prediction($predictions)
{
    $kills = $predictions['predicted_kills'];
    $deaths = $predictions['predicted_deaths'];
    $conf = round($predictions['confidence']);
    
    echo '
    <div class="prediction_card">
        <h4><span class="main_icons history"></span> Upcoming Match Analysis</h4>
        <div style="display: flex; justify-content: space-around; margin: 30px 0;">
            <div style="text-align: center;">
                <div style="font-size: 14px; color: #94a3b8; text-transform: uppercase;">Predicted Kills</div>
                <div style="font-size: 36px; font-weight: 800; color: #4ade80;">', $kills, '</div>
            </div>
            <div style="width: 1px; background: #334155;"></div>
            <div style="text-align: center;">
                <div style="font-size: 14px; color: #94a3b8; text-transform: uppercase;">Predicted Deaths</div>
                <div style="font-size: 36px; font-weight: 800; color: #f87171;">', $deaths, '</div>
            </div>
        </div>
        
        <div class="confidence_container">
            <div style="display: flex; justify-content: space-between; font-size: 13px; color: #94a3b8; margin-bottom: 8px;">
                <span>AI Confidence Rating</span>
                <span>', $conf, '%</span>
            </div>
            <div class="confidence_bar">
                <div class="confidence_fill" style="width: ', $conf, '%"></div>
            </div>
        </div>
        
        <div style="background: rgba(56, 189, 248, 0.1); border: 1px dashed #38bdf8; padding: 15px; border-radius: 8px; text-align: center;">
            <span style="display: block; font-weight: 700; color: #38bdf8; margin-bottom: 5px;">STRATEGIC OUTLOOK</span>
            <span style="font-size: 14px; color: #f1f5f9;">', $predictions['outlook'], '</span>
        </div>
    </div>';
}

function template_ai_rival_analysis($predictions)
{
    $rivals = $predictions['rival_analysis'];
    
    echo '
    <div class="prediction_card">
        <h4><span class="main_icons membergroups"></span> Encounter Rival Analysis</h4>
        <ul class="rival_list">';
    
    if (empty($rivals)) {
        echo '
            <li class="rival_item" style="justify-content: center; color: #94a3b8;">No historical rival data available.</li>';
    } else {
        foreach ($rivals as $rival) {
            $winProb = round($rival['win_prob'] * 100);
            $nemesis = $rival['nemesis'] ? ' <span style="color: #fca5a5; font-size: 10px;">[NEMESIS]</span>' : '';
            
            echo '
                <li class="rival_item">
                    <div class="rival_info">
                        <span class="rival_name">', $rival['opponent_name'], $nemesis, '</span>
                        <span class="rival_desc">Historical encounter analysis</span>
                    </div>
                    <div class="win_prob">', $winProb, '% <span style="font-size: 10px; color: #94a3b8; font-weight: 400;">WIN PROB</span></div>
                </li>';
        }
    }
    
    echo '
        </ul>
        <div style="margin-top: 25px; font-size: 12px; color: #94a3b8; font-style: italic; text-align: center;">
            Win probabilities are calculated using weighted historical outcome variance against specific opponent playstyles.
        </div>
    </div>';
}
?>
