<?php
date_default_timezone_set('Asia/Manila');

// Include DB config
require_once __DIR__ . '/fetch/db_config.php';

// Get $machine from URL (GET parameter)
$machine = isset($_GET['machine']) ? $_GET['machine'] : null;
$durationText = 'Loading...';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Production Cycle | Sensory Data </title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/logo-2.png">

    <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="css/webpage_defaults.css">
    <link rel="stylesheet" href="css/production_cycle.css">
    <link rel="stylesheet" href="css/table.css">
    
</head>
<body>
    <script src="script/navbar-sidebar.js"></script>

    <!-- Navbar -->
    <div class="navbar">
        <!-- Sidebar Toggle (Logo) -->
        <div id="sidebarToggle">
            <i class="fa fa-bars" style="color: #417630; font-size: 2rem; cursor: pointer;"></i> 
            <a href="#"><img src="images/logo-1.png" style="height: 36px"></a>
        </div>
        

        <!-- Right Icon with Logout Dropdown -->
        <div class="navbar-right" style="position: relative;">
            <i class="fa fa-user-circle" style="font-size: 2rem; color:#417630; cursor:pointer;" id="userIcon"></i>
            <div id="userDropdown">
                <a href="#">Settings</a>
                <a href="#">Logout</a>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div id="sidebar">
        <div class="tabs">
            <p>CORE</p>
            <div class="sidebar-link-group">
                <a href="index.php" class="sidebar-link"><i class='bx  bx-dashboard-alt'></i> Dashboard</a>
            </div>
            <p>SYSTEMS</p>
            <div class="sidebar-link-group">
                <a href="#" class="sidebar-link sidebar-active">
                    <i class='bx  bx-timer'></i> Production Cycle
                    <span class="fa fa-caret-down" style="margin-left:8px;"></span>
                </a>
                <div class="sidebar-submenu">
                    <a href="production_cycle.php?machine=CLF750A" onclick="setMachineSession('CLF750A')">CLF 750A</a>
                    <a href="production_cycle.php?machine=CLF750B" onclick="setMachineSession('CLF750B')">CLF 750B</a>
                    <a href="production_cycle.php?machine=CLF750C" onclick="setMachineSession('CLF750C')">CLF 750C</a>
                </div>
            </div>
            <div class="sidebar-link-group">
                <a href="#" class="sidebar-link">
                    <i class='bx  bx-chart-network'></i>  Real-time Parameters
                    <span class="fa fa-caret-down" style="margin-left:8px;"></span>
                </a>
                <div class="sidebar-submenu" style="display:none;">
                    <a href="motor_temperatures.php">Motor Temperatures</a>
                    <a href="#">Coolant Flow Rates</a>
                </div>
            </div>
            <div class="sidebar-link-group">
                <a href="#" class="sidebar-link">
                    <i class='bx  bx-dumbbell'></i> Weight Data
                    <span class="fa fa-caret-down" style="margin-left:8px;"></span>
                </a>
                <div class="sidebar-submenu" style="display:none;">
                    <a href="weights.php">Gross/Net Weights</a>
                </div>
            </div>
        </div>
        <div id="sidebar-footer" class="sidebar-footer">
            <span style="font-size: 0.75rem; color: #646464">Logged in as:</span>
            <span>User123</span>
        </div>
    </div>

    <!-- Side Table -->
    <div class="side-table" id="sideTable">
        <span class="side-table-toggle" id="sideTableToggle">&#x25C0;</span>

        <div class="content-header">
            <h2 style="margin: 0;">Product Molds</h2>
            <input type="text" id="searchMold" placeholder="Search Molds..." onkeyup="filterMolds()">
        </div>

        <div class="table-container">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mold Name</th>
                        <th>Mold Number</th>
                        <th>Thickness</th>
                    </tr>
                </thead>
                <tbody id="moldTableBody">
                    <?php
                    $mold_sql = "SELECT * FROM mold_thickness ORDER BY id";
                    $mold_result = $conn->query($mold_sql);
                    if ($mold_result && $mold_result->num_rows > 0) {
                        while ($row = $mold_result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['mold_name']}</td>
                                    <td>{$row['mold_number']}</td>
                                    <td>{$row['thickness']} mm</td>
                                </tr>";
                        }
                    }
                    ?>
                    <tr class="no-results" style="display: none;">
                        <td colspan="4" style="text-align: center; color: #aaa;">No molds found</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <script>
            // Toggle side table visibility
            document.addEventListener("DOMContentLoaded", function () {
                const sideTable = document.getElementById('sideTable');
                const sideTableToggle = document.getElementById('sideTableToggle');
                sideTableToggle.addEventListener('click', function () {
                    sideTable.classList.toggle('collapsed');
                });
            });

            // Filter molds based on search input
            function filterMolds() {
                const input = document.getElementById("searchMold");
                const filter = input.value.toLowerCase();
                const rows = document.querySelectorAll("#moldTableBody tr:not(.no-results)");
                const noResultsRow = document.querySelector(".no-results");

                let visibleCount = 0;

                rows.forEach(row => {
                    const cells = row.getElementsByTagName("td");
                    const match = Array.from(cells).some(cell =>
                        cell.textContent.toLowerCase().includes(filter)
                    );
                    if (match) {
                        row.style.display = "";
                        visibleCount++;
                    } else {
                        row.style.display = "none";
                    }
                });

                // Show or hide "no results" row
                if (visibleCount === 0) {
                    noResultsRow.style.display = "";
                } else {
                    noResultsRow.style.display = "none";
                }
            }
        </script>
        <style>
            .side-table.collapsed {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            .side-table {
                transition: transform 0.3s;
            }
            .no-results td {
                background-color: var(--gray);
            }
        </style>
    </div>

    <!-- Main -->
    <div class="main-content">

        <div class="header">
            <div class="header-left">
                <h3>Production Cycle</h3>
                <span>Production Department - Sensory Data</span>
            </div>
            <div class="header-right">
                <h2><?php echo htmlspecialchars($machine ? $machine : 'No Machine Selected'); ?></h2>
                <span id="machine-status-duration" style="color: #adadad; text-align: right;">
                    <?php echo $durationText; ?>
                </span>
            </div>

            <script>
                function refreshDuration() {
                    const machineName = <?php echo json_encode($machine); ?>;
                    if (!machineName) return;

                    fetch(`fetch/fetch_production_cycle_machine_status.php?machine=${encodeURIComponent(machineName)}`)
                        .then(res => res.text())
                        .then(text => {
                            document.getElementById('machine-status-duration').innerText = text;
                        })
                        .catch(err => {
                            console.error("Failed to update machine status duration", err);
                        });
                }

                // Initial call and repeat every 60 seconds
                refreshDuration();
                setInterval(refreshDuration, 60000);
            </script>
        </div>

        <!-- Production Status -->
        <div class="section">
            <div class="content-header">
                <h2 style="margin: 0;">Production Status</h2>
            </div>

            <?php
            // Fetch the latest row from the database for the cards
            $machine_safe = preg_replace('/[^a-z0-9_]/', '', strtolower($machine));
            $latest = [
                'cycle_status' => 0,
                'tempC_01' => 0,
                'tempC_02' => 0,
                'product' => 'N/A'
            ];
            if ($machine_safe) {
                $latest_sql = "SELECT * FROM production_cycle_" . $machine_safe . " ORDER BY timestamp DESC LIMIT 1";
                $latest_result = $conn->query($latest_sql);
                if ($latest_result && $latest_result->num_rows > 0) {
                    $row = $latest_result->fetch_assoc();
                    $latest = array_merge($latest, $row);
                }
            }
            ?>

            <!-- Production Cards -->
            <div class="card-container">
                <!-- Status Card -->
                <?php
                $borderClass = 'inactive-border';
                if ($latest['cycle_status'] == 1) $borderClass = 'active-border';
                elseif ($latest['cycle_status'] == 2) $borderClass = 'inactive-gray-border';
                ?>
                <div id="status-card" class="card machine-card <?php echo $borderClass; ?>">
                    <div class="status-container">
                        <?php
                        $dotClass = 'inactive';
                        if ($latest['cycle_status'] == 1) $dotClass = 'active';
                        elseif ($latest['cycle_status'] == 2) $dotClass = 'inactive-gray';
                        ?>
                        <div id="status-indicator" class="status-indicator <?php echo $dotClass; ?>"></div>
                        <?php
                        $statusText = "Mold Open";
                        if ($latest['cycle_status'] == 1) $statusText = "Mold Closed";
                        elseif ($latest['cycle_status'] == 2) $statusText = "Machine Inactive";
                        ?>
                        <h2 id="machine-status"><?php echo $statusText; ?></h2>
                    </div>
                    <p style="font-size: 0.75rem">Injection Status</p>
                </div>

                <!-- Temperature 1 Card -->
                <div class="card temperature1-card">
                    <h2 id="temp1-value"><?php echo htmlspecialchars($latest['tempC_01']); ?>°C</h2>
                    <p style="font-size: 0.75rem">Motor Temperature 1</p>
                    <div class="chart-container">
                        <canvas id="chartTemp1"></canvas>
                    </div>
                </div>

                <!-- Temperature 2 Card -->
                <div class="card temperature2-card">
                    <h2 id="temp2-value"><?php echo htmlspecialchars($latest['tempC_02']); ?>°C</h2>
                    <p style="font-size: 0.75rem">Motor Temperature 2</p>
                    <div class="chart-container">
                        <canvas id="chartTemp2"></canvas>
                    </div>
                </div>

                <!-- Product Card -->
                <div class="card product-card">
                    <h2 id="product-status"><?php echo htmlspecialchars($latest['product']); ?></h2>
                    <p style="font-size: 0.75rem">Current Product</p>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                let temp1Chart, temp2Chart;

                function updateCharts(temp1, temp2) {
                    temp1Chart.data.datasets[0].data = [temp1, 100 - temp1];
                    temp2Chart.data.datasets[0].data = [temp2, 100 - temp2];
                    temp1Chart.update();
                    temp2Chart.update();
                }

                function fetchData() {
                    fetch("fetch/fetch_production_status.php?machine=<?php echo urlencode($machine); ?>")
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById("temp1-value").textContent = data.tempC_01 + "°C";
                            document.getElementById("temp2-value").textContent = data.tempC_02 + "°C";
                            document.getElementById("product-status").textContent = data.product.split('|')[0].trim();

                            // Update status
                            let statusText = "Mold Open";
                            let dotClass = "inactive";
                            let borderClass = "inactive-border";

                            if (data.cycle_status == 1) {
                                statusText = "Mold Closed";
                                dotClass = "active";
                                borderClass = "active-border";
                            } else if (data.cycle_status == 2) {
                                statusText = "Machine Inactive";
                                dotClass = "inactive-gray";
                                borderClass = "inactive-gray-border";
                            }

                            document.getElementById("machine-status").textContent = statusText;
                            document.getElementById("status-indicator").className = "status-indicator " + dotClass;
                            document.getElementById("status-card").className = "card machine-card " + borderClass;

                            // Update charts
                            updateCharts(data.tempC_01, data.tempC_02);
                        });
                }

                document.addEventListener("DOMContentLoaded", function () {
                    function createChart(ctx, value, maxValue, color) {
                        return new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                datasets: [{
                                    data: [value, maxValue - value],
                                    backgroundColor: [color, '#222'],
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '60%',
                                plugins: {
                                    tooltip: { enabled: false },
                                    legend: { display: false },
                                }
                            }
                        });
                    }

                    // Initialize Charts with PHP values
                    temp1Chart = createChart(document.getElementById("chartTemp1"), <?php echo (int)$latest['tempC_01']; ?>, 100, "#FFB347");
                    temp2Chart = createChart(document.getElementById("chartTemp2"), <?php echo (int)$latest['tempC_02']; ?>, 100, "#FF6347");

                    // Fetch Data Every 8 Seconds
                    setInterval(fetchData, 8000);
                });
            </script>
            
            <!-- Production Cycle Span -->
            <div class="content-header" style="margin-top: 18px;">
                <h2>
                    Production Cycle Span
                    <button id="refreshCycleSpan" style="background: none; border: none; cursor: pointer;">
                        <i class='bxr  bx-refresh-cw refresh'></i> 
                    </button>
                </h2>
            </div>

            <div class="bar-container">
                <div id="bar-wrapper" class="bar-wrapper"></div>
            </div>

            <div class="legend">
                <div class="legend-item"><div class="legend-color mold-open_hourly"></div>Mold Open</div>
                <div class="legend-item"><div class="legend-color mold-closed_hourly"></div>Mold Closed</div>
            </div>

            <script>
                const machineName = "<?php echo $machine ?>";

                // Tooltip element
                const spanTooltip = document.createElement('div');
                spanTooltip.classList.add('spanTooltip');
                document.body.appendChild(spanTooltip);

                function renderBar(data) {
                    const barWrapper = document.getElementById('bar-wrapper');
                    barWrapper.innerHTML = '';

                    const hasSegments = Array.isArray(data) && data.length > 0;
                    const total = hasSegments ? data.reduce((sum, seg) => sum + (Number(seg.size) || 0), 0) : 0;

                    // Fallback if no segments or total size is zero
                    if (!hasSegments || total <= 0) {
                        const div = document.createElement('div');
                        div.classList.add('bar-segment');
                        div.style.flex = 1;
                        div.style.backgroundColor = '#555';  // gray fallback

                        // Tooltip for "no data"
                        div.addEventListener('mouseenter', () => {
                            spanTooltip.innerHTML = `
                                <span style="font-weight: bold; color: #bbb;">No production data available</span><br>
                                Product: <span style="color: #adadad; font-weight: bold;">—</span><br>
                                Motor 1 Temp: <span style="color: #bbb; font-weight: bold;">—</span><br>
                                Motor 2 Temp: <span style="color: #bbb; font-weight: bold;">—</span>
                            `;
                            spanTooltip.style.opacity = 1;
                        });

                        div.addEventListener('mousemove', (e) => {
                            const rect = spanTooltip.getBoundingClientRect();
                            const padding = 12;
                            let left = e.pageX + padding;
                            let top = e.pageY + padding;

                            if (left + rect.width > window.innerWidth) left = e.pageX - rect.width - padding;
                            if (top + rect.height > window.innerHeight) top = e.pageY - rect.height - padding;

                            spanTooltip.style.left = left + 'px';
                            spanTooltip.style.top = top + 'px';
                        });

                        div.addEventListener('mouseleave', () => {
                            spanTooltip.style.opacity = 0;
                        });

                        barWrapper.appendChild(div);
                        return;
                    }

                    // Normal render
                    data.forEach(segment => {
                        const size = Number(segment.size) || 0;
                        const flex = size / total;

                        const div = document.createElement('div');
                        div.classList.add('bar-segment', segment.type);
                        div.style.flex = flex;

                        div.addEventListener('mouseenter', () => {
                            let labelColor = '';
                            if ((segment.label || '').toLowerCase().startsWith("mold open")) {
                                labelColor = 'rgb(174, 21, 21)';
                            } else if ((segment.label || '').toLowerCase().startsWith("mold closed")) {
                                labelColor = '#417630';
                            } else {
                                labelColor = '#417630';
                            }

                            spanTooltip.innerHTML = `
                                <span style="font-weight: bold; color: ${labelColor};">${segment.label || ''}</span><br>
                                Product: <span style="color: #adadad; font-weight: bold;">${segment.product || ''}</span><br>
                                Motor 1 Temp: <span style="color: rgb(255,179,71); font-weight: bold;">${segment.temp1 ?? ''}°C</span><br>
                                Motor 2 Temp: <span style="color: rgb(255,99,71); font-weight: bold;">${segment.temp2 ?? ''}°C</span>
                            `;
                            spanTooltip.style.opacity = 1;
                        });

                        div.addEventListener('mousemove', (e) => {
                            const rect = spanTooltip.getBoundingClientRect();
                            const padding = 12;
                            let left = e.pageX + padding;
                            let top = e.pageY + padding;

                            if (left + rect.width > window.innerWidth) left = e.pageX - rect.width - padding;
                            if (top + rect.height > window.innerHeight) top = e.pageY - rect.height - padding;

                            spanTooltip.style.left = left + 'px';
                            spanTooltip.style.top = top + 'px';
                        });

                        div.addEventListener('mouseleave', () => {
                            spanTooltip.style.opacity = 0;
                        });

                        barWrapper.appendChild(div);
                    });
                }

                function loadBarSegments() {
                    fetch(`fetch/fetch_production_cycle_stack.php?machine=${encodeURIComponent(machineName)}`)
                        .then(res => res.json())
                        .then(data => {
                            const segments = Array.isArray(data?.segments) ? data.segments : [];
                            renderBar(segments);
                        })
                        .catch(err => {
                            console.error("Error fetching bar segments:", err);
                            // Show gray fallback on error, too
                            renderBar([]);
                        });
                }

                // Load on page load
                loadBarSegments();

                // Refresh button click
                document.getElementById('refreshCycleSpan').addEventListener('click', loadBarSegments);
            </script>
        </div>

        <!-- Time and Temperature -->
        <div class="section">
            <div class="content-header">
                <h2 style="margin: 0;">
                    Time and Temperature
                    <button id="refreshCycleTemp" style="background: none; border: none; cursor: pointer;">
                        <i class='bxr  bx-refresh-cw refresh'></i> 
                    </button>
                </h2>

                <div class="section-controls">
                    <button id="prevButton" class="triangle-button prev">&#9665</button>
                    <button id="nextButton" class="triangle-button next">&#9655</button>
            </div>
            </div>
            
            <div class="chart-section">
                <div class="chart-fullwidth">
                    <canvas id="cycleTempChart"></canvas>
                </div>
                
                <div class="chartInfo">
                    <div class="legends">
                    <h2>Legends</h2>
                        <div class="legend-item">
                            <span class="legend-box cycle-bar"></span>
                            Cycle time averages at...
                        </div>
                        <div class="legend-item">
                            <span class="legend-line motor1"></span>
                            Motor Temperature averages at...
                        </div>
                        <div class="legend-item">
                            <span class="legend-line motor2"></span>
                            Motor Temperature averages at...
                        </div>
                    </div>

                    <div class="remarks">
                        <h2>Remarks</h2>
                        <span>Stable</span>
                        <p>Maintain current operation parameters</p>
                        <h2>Recommendations</h2>
                        <p>None</p>
                    </div>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    let currentPage = 1;
                    let cycleTempChart = null;

                    async function loadChartData() {
                        const machine = "<?php echo $_GET['machine']; ?>";
                        const response = await fetch(`fetch/fetch_production_cycle_temp.php?machine=${machine}&page=${currentPage}`);
                        const data = await response.json();

                        const labels = data.map(d => d.time);
                        const cycleTime = data.map(d => d.cycle_time);
                        const motor1 = data.map(d => d.temp1);
                        const motor2 = data.map(d => d.temp2);

                        // Disable/enable controls
                        document.getElementById('prevButton').disabled = (currentPage === 1);
                        document.getElementById('nextButton').disabled = (data.length === 0 || data.length < 10);

                        const ctx = document.getElementById('cycleTempChart').getContext('2d');

                        if (cycleTempChart) {
                            cycleTempChart.data.labels = labels;
                            cycleTempChart.data.datasets[0].data = cycleTime;
                            cycleTempChart.data.datasets[1].data = motor1;
                            cycleTempChart.data.datasets[2].data = motor2;
                            cycleTempChart.update();
                        } else {
                            cycleTempChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: labels,
                                    datasets: [
                                        { label: 'Cycle Time', type: 'bar', data: cycleTime, backgroundColor: 'rgba(65,118,48,0.7)', borderRadius: 6, yAxisID: 'y', order: 0 },
                                        { label: 'Motor 1 Temp', type: 'line', data: motor1, borderColor: 'rgb(174,21,21)', backgroundColor: 'rgba(174,21,21,0.2)', borderWidth: 2, tension: 0.4, pointRadius: 2, pointHoverRadius: 4, pointBackgroundColor: 'rgb(174,21,21)', yAxisID: 'y1', order: 2 },
                                        { label: 'Motor 2 Temp', type: 'line', data: motor2, borderColor: '#f59c2f', backgroundColor: 'rgba(245,156,47,0.2)', borderWidth: 2, tension: 0.4, pointRadius: 2, pointHoverRadius: 4, pointBackgroundColor: '#f59c2f', yAxisID: 'y1', order: 3 }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    interaction: { mode: 'index', intersect: false },
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: {
                                            enabled: false,
                                            external: function(context) {
                                                const tooltipModel = context.tooltip;
                                                let tooltipEl = document.getElementById('chartjs-tooltip');

                                                // create tooltip element if not exists
                                                if (!tooltipEl) {
                                                    tooltipEl = document.createElement('div');
                                                    tooltipEl.id = 'chartjs-tooltip';
                                                    // base styles - adjust as needed
                                                    Object.assign(tooltipEl.style, {
                                                        position: 'absolute',
                                                        zIndex: 9999,
                                                        background: 'rgb(16,16,16)',
                                                        border: '1px solid #417630',
                                                        borderRadius: '6px',
                                                        color: 'rgb(216,216,216)',
                                                        padding: '6px 10px',
                                                        pointerEvents: 'none',
                                                        transition: 'all .08s ease',
                                                        boxSizing: 'border-box',
                                                        maxWidth: '40vw',
                                                        minWidth: '90px',
                                                        whiteSpace: 'normal',
                                                        wordBreak: 'break-word',
                                                    });
                                                    document.body.appendChild(tooltipEl);
                                                }

                                                // hide
                                                if (tooltipModel.opacity === 0) {
                                                    tooltipEl.style.opacity = 0;
                                                    return;
                                                }

                                                // build content
                                                let time = (tooltipModel.title && tooltipModel.title[0]) ? tooltipModel.title[0] : '';
                                                let innerHtml = `<div style="font-weight:bold; font-size:0.75rem; margin-bottom:6px;">${time}</div>`;

                                                if (tooltipModel.dataPoints) {
                                                    tooltipModel.dataPoints.forEach((item) => {
                                                        const color = item.dataset && (item.dataset.borderColor || item.dataset.backgroundColor) ? (item.dataset.borderColor || item.dataset.backgroundColor) : '#fff';
                                                        const label = item.dataset ? item.dataset.label : '';
                                                        let value = item.formattedValue !== undefined ? item.formattedValue : (item.raw ?? '');
                                                        if (label && label.toLowerCase().includes('cycle')) value = `${value}s`;
                                                        else value = `${value}°C`;

                                                        innerHtml += `
                                                            <div style="font-size:0.75rem; display:flex; text-align: left; align-items: left; gap:8px; margin:2px 0;">
                                                                <span style="display:inline-block; width:10px; height:10px; background:${color}; border-radius:50%;"></span>
                                                                <div style="flex:1;">${label}: <span style="margin-left:4px; font-weight:600;">${value}</span></div>
                                                            </div>`;
                                                    });
                                                }

                                                tooltipEl.innerHTML = innerHtml;

                                                // ensure element is in DOM so measurements work
                                                if (!document.body.contains(tooltipEl)) document.body.appendChild(tooltipEl);

                                                // measure
                                                const canvasRect = context.chart.canvas.getBoundingClientRect();
                                                const ttRect = tooltipEl.getBoundingClientRect();

                                                // Get caret coordinates (fall back to first data point element if caret not available)
                                                let caretX = tooltipModel.caretX;
                                                let caretY = tooltipModel.caretY;
                                                if ((caretX === undefined || caretY === undefined) && tooltipModel.dataPoints && tooltipModel.dataPoints.length > 0) {
                                                    const el = tooltipModel.dataPoints[0].element;
                                                    if (el) {
                                                        if (caretX === undefined && el.x !== undefined) caretX = el.x;
                                                        if (caretY === undefined && el.y !== undefined) caretY = el.y;
                                                    }
                                                }

                                                // Default positions if still undefined
                                                if (caretX === undefined) caretX = canvasRect.width / 2;
                                                if (caretY === undefined) caretY = 10;

                                                // compute absolute coords
                                                let x = window.scrollX + canvasRect.left + caretX;
                                                let y = window.scrollY + canvasRect.top + caretY;

                                                const margin = 8;

                                                // If tooltip would overflow right edge, shift it left
                                                if (x + ttRect.width + margin > window.scrollX + window.innerWidth) {
                                                    x = window.scrollX + canvasRect.left + canvasRect.width - ttRect.width - margin;
                                                }
                                                // prevent going off left edge
                                                if (x < window.scrollX + margin) {
                                                    x = window.scrollX + margin;
                                                }

                                                // If tooltip would overflow bottom edge, shift it up
                                                if (y + ttRect.height + margin > window.scrollY + window.innerHeight) {
                                                    y = window.scrollY + canvasRect.top + canvasRect.height - ttRect.height - margin;
                                                }
                                                // prevent going off top edge
                                                if (y < window.scrollY + margin) {
                                                    y = window.scrollY + margin;
                                                }

                                                tooltipEl.style.left = `${Math.round(x)}px`;
                                                tooltipEl.style.top  = `${Math.round(y)}px`;
                                                tooltipEl.style.opacity = 1;
                                            }
                                        }
                                    },
                                    layout: { padding: { top: 5, bottom: 5, left: 5, right: 5 } },
                                    scales: {
                                        x: { ticks: { color: '#adadad', font: { size: 11 } }, grid: { color: '#272727' } },
                                        y: { type: 'linear', position: 'left', ticks: { color: '#adadad', font: { size: 11 } }, title: { display: true, text: 'Cycle Time (sec)', color: '#d8d8d8', font: { size: 12 } }, grid: { color: '#272727' } },
                                        y1: { type: 'linear', position: 'right', grid: { drawOnChartArea: false }, ticks: { color: '#adadad', font: { size: 11 } }, title: { display: true, text: 'Temperature (°C)', color: '#d8d8d8', font: { size: 12 } } }
                                    }
                                }
                            });
                        }
                    }

                    // Controls
                    document.getElementById('prevButton').addEventListener('click', function () {
                        if (currentPage > 1) {
                            currentPage--;
                            loadChartData();
                        }
                    });
                    document.getElementById('nextButton').addEventListener('click', function () {
                        currentPage++;
                        loadChartData();
                    });
                    document.getElementById('refreshCycleTemp').addEventListener('click', loadChartData);
                    document.addEventListener("DOMContentLoaded", loadChartData);
                </script>
            </div>
        </div>

        <!-- Production Cycle History -->
        <div class="section">
            <div class="content-header">
                <h2>
                    Production Cycle History
                    <button id="refreshCycleHistory" style="background: none; border: none; cursor: pointer;">
                        <i class='bxr  bx-refresh-cw refresh'></i> 
                    </button>
                </h2>

                <div class="section-controls">
                    <div class="by_product">
                        <label for="show-product">Product</label>
                        <select id="show-product">
                            <option value="" selected>All</option>
                            <!-- Options will be populated by JS -->
                        </select>
                    </div>
                    <div class="by_number">
                        <label for="show-entries">Show</label>
                        <select id="show-entries">
                            <option value="all" selected>All</option>
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                    <div class="by_month">
                        <label for="filter-month">Filter by month</label>
                        <select id="filter-month">
                            <option value="0">All</option>
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                    </div>

                    <script>
                        const urlParams = new URLSearchParams(window.location.search);
                        const machine = urlParams.get('machine');

                        fetch('fetch/fetch_products.php?machine=' + machine)
                            .then(res => res.json())
                            .then(products => {
                                const select = document.getElementById("show-product");
                                select.innerHTML = '<option value="" selected>All</option>';
                                products.forEach(product => {
                                    const option = document.createElement("option");
                                    option.value = product.name;
                                    option.textContent = product.name.split('|')[0].trim(); // Show product name
                                    select.appendChild(option);
                                });
                            })
                            .catch(err => {
                                console.error("Failed to load products:", err);
                            });
                    </script>
                </div>
            </div>

            <div class="time-cards">
                <?php
                // Example: Fetch stats from DB (replace with your actual queries)
                $stats = [
                    'standard' => ['cycle' => 120, 'processing' => 60, 'recycle' => 60],
                    'average' => ['cycle' => 0, 'processing' => 0, 'recycle' => 0],
                    'minimum' => ['cycle' => 0, 'processing' => 0, 'recycle' => 0],
                    'maximum' => ['cycle' => 0, 'processing' => 0, 'recycle' => 0],
                ];
                $maxValue = max(
                    $stats['average']['cycle'], $stats['standard']['cycle'],
                    $stats['minimum']['cycle'], $stats['maximum']['cycle'],
                    $stats['average']['processing'], $stats['standard']['processing'],
                    $stats['minimum']['processing'], $stats['maximum']['processing'],
                    $stats['average']['recycle'], $stats['standard']['recycle'],
                    $stats['minimum']['recycle'], $stats['maximum']['recycle']
                );
                foreach ($stats as $type => $values):
                ?>

                <div class="time-card <?php echo $type; ?>">
                    <h2><?php echo ucfirst($type); ?></h2>
                    <span class="info-icon" data-type="<?php echo $type; ?>">ⓘ
                        <div class="tooltip" id="tooltip-<?php echo $type; ?>">
                            <p>Loading...</p>
                        </div>
                    </span>
                    
                    <h3>
                        Cycle Time (seconds)
                        <span class="diff-indicator" data-type="<?php echo $type; ?>" data-metric="cycle"></span>
                    </h3>
                    <div class="bar-container">
                        <div class="bar-wrapper">
                            <div class="bar" style="width:<?php echo ($values['cycle']/$maxValue)*100; ?>%;background:#417630;">
                                <span class="bar-label"><?php echo $values['cycle']; ?></span>
                            </div>
                            <div class="bar-tooltip">Cycle Time: <?php echo $values['cycle']; ?>s</div>
                        </div>
                    </div>

                    <h3>
                        Processing Time (seconds)
                        <span class="diff-indicator" data-type="<?php echo $type; ?>" data-metric="processing"></span>
                    </h3>
                    <div class="bar-container">
                        <div class="bar-wrapper">
                            <div class="bar" style="width:<?php echo ($values['processing']/$maxValue)*100; ?>%;background:#f59c2f;">
                                <span class="bar-label"><?php echo $values['processing']; ?></span>
                            </div>
                            <div class="bar-tooltip">Processing Time: <?php echo $values['processing']; ?>s</div>
                        </div>
                    </div>
                    
                    <h3>
                        Recycle Time (seconds)
                        <span class="diff-indicator" data-type="<?php echo $type; ?>" data-metric="recycle"></span>
                    </h3>
                    <div class="bar-container">
                        <div class="bar-wrapper">
                            <div class="bar" style="width:<?php echo ($values['recycle']/$maxValue)*100; ?>%;background:#2a656f;">
                                <span class="bar-label"><?php echo $values['recycle']; ?></span>
                            </div>
                            <div class="bar-tooltip">Recycle Time: <?php echo $values['recycle']; ?>s</div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="table-responsive">
                <table class="styled-table" id="sensorTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cycle Time (seconds)</th>
                            <th>Processing Time (seconds)</th>
                            <th>Recycle Time (seconds)</th>
                            <th>Motor Temperature 1 (°C)</th>
                            <th>Motor Temperature 2 (°C)</th>
                            <th>Product</th>
                            <th>Mold Number</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        <!-- Table rows will be loaded here via AJAX -->
                    </tbody>
                </table>
            </div>

            <script>
                // Pass PHP $machine_safe to JS
                const machineSafe = "<?php echo $machine_safe; ?>";

                function fetchTableData() {
                    let showEntries = document.getElementById('show-entries').value;
                    const filterMonth = document.getElementById('filter-month').value;
                    const selectedProduct = document.getElementById('show-product').value;

                    // Convert 'all' to a large number like 9999
                    const showLimit = (showEntries === 'all') ? 9999 : showEntries;

                    const xhr = new XMLHttpRequest();
                    xhr.open('GET', `fetch/fetch_production_cycle_table.php?machine=${encodeURIComponent(machineSafe)}&show=${showLimit}&month=${filterMonth}&product=${encodeURIComponent(selectedProduct)}`, true);
                    xhr.onload = function () {
                        if (xhr.status === 200) {
                            document.getElementById('table-body').innerHTML = xhr.responseText;
                        }
                    };
                    xhr.send();
                }

                function fetchStandardTimecard(product) {
                    const url = `fetch/fetch_production_cycle_standard_timecard.php?machine=${encodeURIComponent(machineSafe)}&product=${encodeURIComponent(product)}`;

                    fetch(url)
                        .then(res => res.json())
                        .then(data => {
                            const card = document.querySelector(`.time-card.standard`);
                            if (!card) return;

                            const bars = card.querySelectorAll('.bar');
                            const tooltips = card.querySelectorAll('.bar-tooltip');

                            const max = Math.max(data.cycle, data.processing, data.recycle, 1);

                            const values = [data.cycle, data.processing, data.recycle];
                            const labels = ['Cycle Time', 'Processing Time', 'Recycle Time'];

                            values.forEach((val, i) => {
                                bars[i].style.width = (val / max * 100) + '%';
                                bars[i].innerText = val;
                                tooltips[i].innerText = `${labels[i]}: ${val}s`;
                            });
                        });
                }

                function updateTimeCards(stats) {
                    const standard = stats.standard;

                    const allZero = ['average', 'minimum', 'maximum'].every(type =>
                        ['cycle', 'processing', 'recycle'].every(metric => stats[type][metric] === 0)
                    );

                    ['average', 'minimum', 'maximum'].forEach(type => {
                        const card = document.querySelector(`.time-card.${type}`);
                        const val = stats[type];

                        ['cycle', 'processing', 'recycle'].forEach((key, i) => {
                            const bar = card.querySelectorAll('.bar')[i];
                            const tooltip = card.querySelectorAll('.bar-tooltip')[i];
                            const indicator = card.querySelector(`.diff-indicator[data-type="${type}"][data-metric="${key}"]`);
                            const value = val[key];
                            const standardValue = standard[key];

                            const colors = ['#417630', '#f59c2f', '#2a656f'];

                            if (allZero || value === 0 || standardValue === 0) {
                                bar.style.width = '0%';
                                bar.innerText = '0';
                                bar.style.backgroundColor = '#646464'; // Gray
                                tooltip.textContent = 'No data available';
                                if (indicator) indicator.innerHTML = '';
                                return;
                            }

                            const widthPercent = Math.min((value / standardValue) * 100, 100);
                            bar.style.width = widthPercent + '%';
                            bar.innerText = value;
                            bar.style.backgroundColor = colors[i]; // ✅ Restore original color here

                            if (indicator) {
                                const percentDiff = ((value - standardValue) / standardValue) * 100;
                                const rounded = Math.abs(percentDiff).toFixed(2);
                                const direction = percentDiff > 0 ? '▲' : percentDiff < 0 ? '▼' : '';
                                const color = percentDiff > 0 ? '#d32f2f' : percentDiff < 0 ? '#4caf50' : '#aaa';

                                indicator.innerHTML = `<span style="color:${color}; font-weight: bold; font-size: 0.8em;"><strong>${direction}</strong> ${rounded}%</span>`;
                            }

                            const rawDiff = value - standardValue;
                            const absDiff = Math.abs(rawDiff);
                            const percentDiff = Math.abs((rawDiff / standardValue) * 100);

                            const timeThreshold = 0.1;
                            const percentThreshold = 1.0;

                            if (absDiff < timeThreshold || percentDiff < percentThreshold) {
                                tooltip.innerHTML = `<span style="color:#cccccc;">${capitalizeFirstLetter(key)} time matches the standard exactly.</span>`;
                            } else {
                                const diff = absDiff.toFixed(2);
                                const percent = percentDiff.toFixed(2);
                                const isHigher = rawDiff > 0;

                                const coloredWord = `<span style="color:${isHigher ? '#ff5252' : '#4caf50'};">${isHigher ? 'slower' : 'faster'}</span>`;

                                tooltip.innerHTML = `${capitalizeFirstLetter(key)} time is ${percent}% ${coloredWord} than the standard by ${diff} seconds`;
                            }
                        });
                    });
                }

                // Utility to capitalize time types
                function capitalizeFirstLetter(str) {
                    return str.charAt(0).toUpperCase() + str.slice(1);
                }


                function updateTooltips(product, standardCycle = 120) {
                    const cards = ['standard', 'average', 'minimum', 'maximum'];

                    // Fallback check
                    const isInvalid = (
                        typeof standardCycle !== 'number' ||
                        isNaN(standardCycle)
                    );

                    cards.forEach(type => {
                        const tooltip = document.getElementById(`tooltip-${type}`);
                        let content = "";

                        if (type === 'standard') {
                            const trimmedProduct = product ? product.split('|')[0].trim() : '';
                            content = trimmedProduct
                                ? `<p>${trimmedProduct} selected</p>`
                                : '<p>No product selected</p>';
                        } else {
                            if (isInvalid) {
                                content = `<p style="color:gray;">No parameters available</p>
                                    <p><span style="color:#417630; font-weight: bold;">Cycle time</span> limit set to 120 seconds</p>
                                    <p><span style="color:#f59c2f; font-weight: bold;">Processing time</span> limit set to 60 seconds</p>
                                    <p><span style="color:#2a656f; font-weight: bold;">Recycle time</span> limit set to 60 seconds</p>`;
                            } else {
                                content = ` <p><span style="color:#417630; font-weight: bold;">Cycle time</span> limit set to ${standardCycle} seconds</p>
                                            <p><span style="color:#f59c2f; font-weight: bold;">Processing time</span> limit set to ${standardCycle / 2} seconds</p>
                                            <p><span style="color:#2a656f; font-weight: bold;">Recycle time</span> limit set to ${standardCycle / 2} seconds</p>`;
                            }
                        }

                        if (tooltip) tooltip.innerHTML = content;
                    });
                }

                function fetchAll() {
                    fetchTableData(); // update table

                    const machine = "<?php echo $machine_safe; ?>";
                    let show = document.getElementById('show-entries').value;
                    const month = document.getElementById('filter-month').value;
                    const product = document.getElementById('show-product').value;

                    const showLimit = (show === 'all') ? 9999 : show;

                    const url = `fetch/fetch_production_cycle_timecards.php?machine=${encodeURIComponent(machine)}&show=${showLimit}&month=${month}&product=${encodeURIComponent(product)}`;
                    
                    fetch(url)
                        .then(res => res.json())
                        .then(data => {
                            if (data.error) return;

                            const standardCycle = data.standard?.cycle || data.standard || 0;

                            const stats = {
                                standard: {
                                    cycle: data.standard?.cycle || 120,
                                    processing: data.standard?.processing || 60,
                                    recycle: data.standard?.recycle || 60
                                },
                                average: {
                                    cycle: data.average?.cycle || 0,
                                    processing: data.average?.processing || 0,
                                    recycle: data.average?.recycle || 0
                                },
                                minimum: {
                                    cycle: data.minimum?.cycle || 0,
                                    processing: data.minimum?.processing || 0,
                                    recycle: data.minimum?.recycle || 0
                                },
                                maximum: {
                                    cycle: data.maximum?.cycle || 0,
                                    processing: data.maximum?.processing || 0,
                                    recycle: data.maximum?.recycle || 0
                                }
                            };
                            updateTimeCards(stats);
                            updateTooltips(product, standardCycle);
                        });

                    if (product) {
                        fetchStandardTimecard(product);
                    } else {
                        const card = document.querySelector(".time-card.standard");
                        ['cycle', 'processing', 'recycle'].forEach((_, i) => {
                            card.querySelectorAll('.bar')[i].style.width = '0%';
                            card.querySelectorAll('.bar')[i].innerText = '0';
                        });
                    }
                }

                // Initial load
                document.addEventListener("DOMContentLoaded", function () {
                    let currentMonth = new Date().getMonth() + 1;
                    document.getElementById("filter-month").value = currentMonth;
                    fetchAll();
                });

                // Trigger fetch on control changes
                ['show-entries', 'filter-month', 'show-product'].forEach(id => {
                    document.getElementById(id).addEventListener('change', fetchAll);
                });

                // Update table when controls change
                document.getElementById('show-entries').addEventListener('change', fetchTableData);
                document.getElementById('filter-month').addEventListener('change', fetchTableData);
                document.getElementById('show-product').addEventListener('change', fetchTableData);

                // Set default month to current month
                document.addEventListener("DOMContentLoaded", function () {
                    let currentMonth = new Date().getMonth() + 1;
                    document.getElementById("filter-month").value = currentMonth;
                    fetchTableData();
                });

                let isInteracting = false;

                ['show-entries', 'filter-month', 'show-product'].forEach(id => {
                    const el = document.getElementById(id);
                    el.addEventListener('focus', () => isInteracting = true);
                    el.addEventListener('blur', () => isInteracting = false);
                    el.addEventListener('change', fetchAll);
                });

                // Refresh button click
                document.getElementById('refreshCycleHistory').addEventListener('click', fetchAll);
            </script>
            
            <div class="table-download">
                <a href="#" class="btn-download">Download PDF</a>
                <a href="#" class="btn-download">Download Excel</a>
            </div>
        </div>
    </div>
</body>
</html>