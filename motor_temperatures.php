<?php
date_default_timezone_set('Asia/Manila'); // or your correct timezone

require_once __DIR__ . '/fetch/db_config.php';

session_start();
ob_start();

// Always define $isAjax
$isAjax = (isset($_GET['ajax']) && $_GET['ajax'] === '1') ? true : false;

// Default to dark mode (1) if not set yet
if (!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = 1;
}
$theme = $_SESSION['theme']; // 1 = dark, 0 = light

if ($theme == 1) { // dark mode
    $chartBg    = "rgb(16,16,16)";
    $gridColor  = "#272727";
    $labelColor = "#d8d8d8";
    $titleColor = "#d8d8d8";
    $tooltipBg  = "rgb(16,16,16)";
    $tooltipText= "rgb(216,216,216)";
} else { // light mode
    $chartBg    = "#fff";
    $gridColor  = "#ccc";
    $labelColor = "#333";
    $titleColor = "#000";
    $tooltipBg  = "#fff";
    $tooltipText= "#000";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Motor Temperatures | Sensory Data</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/logo-2.png">

    <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="css/webpage_defaults.css">
    <link rel="stylesheet" href="css/motor_temperatures.css">
    <link rel="stylesheet" href="css/table.css">
    <link rel="stylesheet" href="css/histogram.css">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="<?php echo ($theme == 1) ? 'dark-mode' : 'light-mode'; ?>">
    <script src="script/navbar-sidebar.js"></script>

    <!-- Navbar -->
    <div class="navbar">
        <!-- Sidebar Toggle (Logo) -->
        <div id="sidebarToggle">
            <i class="fa fa-bars"></i> 
            <a href="#"><img src="images/logo-1.png" style="height: 36px"></a>
        </div>
        

        <!-- Right Icon with Dropdown -->
        <div class="navbar-right" style="position: relative;">
            <i class="fa fa-user-circle" id="userIcon"></i>
            <div id="userDropdown">
                <a href="#"><i class='bxr  bx-cog'
                style="margin-right: 6px; vertical-align: middle; font-size: smaller;"></i> Settings</a>

                <a href="#" id="darkModeToggle">
                    <i class="bxr <?php echo ($theme == 1) ? 'bx-sun' : 'bx-moon'; ?>" 
                    style="margin-right: 6px; vertical-align: middle; font-size: smaller;"></i>
                    <?php echo ($theme == 1) ? 'To Light Mode' : 'To Dark Mode'; ?>
                </a>

                <a href="#"><i class='bxr  bx-arrow-out-left-square-half'
                style="margin-right: 6px; vertical-align: middle; font-size: smaller;"></i> Logout</a>
            </div>
        </div>

        <script>
            document.getElementById("darkModeToggle").addEventListener("click", function(e) {
                e.preventDefault();

                fetch("fetch/toggle_theme.php")
                .then(res => res.json())
                .then(data => {
                    if (data.theme == 1) {
                        document.body.classList.add("dark-mode");
                        document.body.classList.remove("light-mode");
                        this.innerHTML = "<i class='bxr bx-sun' style='margin-right:6px; font-size: smaller;'></i>To Light Mode";
                    } else {
                        document.body.classList.add("light-mode");
                        document.body.classList.remove("dark-mode");
                        this.innerHTML = "<i class='bxr bx-moon' style='margin-right:6px; font-size: smaller;'></i>To Dark Mode";
                    }
                    location.reload();
                });
            });
        </script>
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
                <a href="#" class="sidebar-link">
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
                <a href="#" class="sidebar-link sidebar-active">
                    <i class='bx  bx-chart-network'></i>  Real-time Parameters
                    <span class="fa fa-caret-down" style="margin-left:8px;"></span>
                </a>
                <div class="sidebar-submenu" style="display:none;">
                    <a href="#">Motor Temperatures</a>
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
            <span class="loggedin_as">Logged in as:</span>
            <span class="username">User123</span>
        </div>
    </div>

    <!-- Main -->
    <div class="main-content">

        <div class="header">
            <div class="header-left">
                <h3>Motor Temperatures</h3>
                <span>Technical Services Department - Sensory Data</span>
            </div>
            <div class="header-right">
            </div>
        </div>
        
        <!-- Real-time Temperatures -->
        <div class="section">
            <div class="content-header">
                <h2 style="margin: 0;">Real-time Temperatures</h2>
            </div>

            <!-- Machine Selection (Realtime) -->
            <div class="machine-tabs">
                <label for="machine-select-realtime" style="display:none;">Select Machine:</label>
                <div id="machine-tab-list-realtime" class="machine-tab-list">
                    <button class="machine-tab active" data-machine="CLF 750A" onclick="selectMachineTabRealtime(this)">CLF 750A</button>
                    <button class="machine-tab" data-machine="CLF 750B" onclick="selectMachineTabRealtime(this)">CLF 750B</button>
                    <button class="machine-tab" data-machine="CLF 750C" onclick="selectMachineTabRealtime(this)">CLF 750C</button>
                </div>
            </div>

            <script>
                // Real-time section machine tab logic
                function selectMachineTabRealtime(tab) {
                    document.querySelectorAll('#machine-tab-list-realtime .machine-tab').forEach(b => b.classList.remove('active'));
                    tab.classList.add('active');
                    // Call your chart update logic here
                    updateChartRealtime(tab.getAttribute('data-machine'));
                }

                function updateChartRealtime(machine) {
                    // You can implement machine-specific chart update logic here if needed
                    // For now, just refetch data (or filter by machine if your backend supports it)
                    fetchRealtimeData(machine);
                }
            </script>
            
            <!-- Cards -->
            <div class="card-container">
                <?php
                // Fetch the latest row from the database
                $sql = "SELECT motor_tempC_01, motor_tempC_02 FROM motor_temperatures ORDER BY timestamp DESC LIMIT 1";
                $result = $conn->query($sql);
                $data = $result->fetch_assoc();
                ?>
                <div class="card-column"> 
                    <div class="card temperature1-card">
                        <h2 id="temp01-value">--°C</h2>
                        <p>Motor 1</p>
                        <div class="chart-container">
                            <!-- You can set width/height here via attributes or CSS -->
                            <canvas id="chartTemp01"></canvas>
                        </div>
                    </div>
                    <!-- Remarks -->
                    <div class="remarks">
                        <h2>Remarks</h2>
                        <span>Loading...</span>
                        <p>Motor Temperatures are monitored in real-time to ensure optimal performance and prevent overheating. The data is updated every 5 seconds.</p>
                        <h2>Recommendations</h2>
                        <p>None</p>
                    </div>
                </div>

                <div class="card-column">
                    <div class="card temperature2-card">
                        <h2 id="temp02-value">--°C</h2>
                        <p>Motor 2</p>
                        <div class="chart-container">
                            <canvas id="chartTemp02"></canvas>
                        </div>
                     </div>
                    <!-- Remarks -->
                    <div class="remarks">
                        <h2>Remarks</h2>
                        <span>Loading...</span>
                        <p>Motor Temperatures are monitored in real-time to ensure optimal performance and prevent overheating. The data is updated every 5 seconds.</p>
                        <h2>Recommendations</h2>
                        <p>None</p>
                    </div>
                </div>
            </div>

            <script>
                let chartTemp01, chartTemp02;

                function fetchRealtimeData(machine) {
                let url = "fetch/fetch_motor_temp.php?type=realtime";
                if (machine) {
                url += "&machine=" + encodeURIComponent(machine);
                }

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        const temp01 = data.motor_tempC_01;
                        const temp02 = data.motor_tempC_02;

                        const hasData = Array.isArray(temp01) && temp01.length > 0 && Array.isArray(temp02) && temp02.length > 0;

                        if (hasData) {
                            const labels = data.timestamps.slice().reverse();
                            const reversedTemp01 = temp01.slice().reverse();
                            const reversedTemp02 = temp02.slice().reverse();

                            const currentTemp01 = reversedTemp01[reversedTemp01.length - 1];
                            const currentTemp02 = reversedTemp02[reversedTemp02.length - 1];

                            // Update displayed temperature values
                            document.getElementById("temp01-value").innerText = currentTemp01 + "°C";
                            document.getElementById("temp02-value").innerText = currentTemp02 + "°C";

                            // Update charts
                            updateChart(chartTemp01, reversedTemp01);
                            updateChart(chartTemp02, reversedTemp02);
                            chartTemp01.data.labels = labels;
                            chartTemp02.data.labels = labels;

                            // Update remarks
                            updateRemarks(0, currentTemp01);
                            updateRemarks(1, currentTemp02);

                        } else {
                            document.getElementById("temp01-value").innerText = "--";
                            document.getElementById("temp02-value").innerText = "--";
                            updateChart(chartTemp01, []);
                            updateChart(chartTemp02, []);

                            document.querySelectorAll(".remarks").forEach(remarks => {
                                remarks.querySelector("span").innerText = "No data found";
                                remarks.querySelector("span").style.color = "#999";
                                remarks.querySelector("p").innerText = "No Motor Temperature data available for the selected machine.";
                                remarks.querySelector("h2 + p").innerText = "Please check if the machine is active.";
                            });
                        }
                    })
                    .catch(error => {
                        console.error("Error fetching real-time data:", error);
                        document.getElementById("temp01-value").innerText = "--";
                        document.getElementById("temp02-value").innerText = "--";
                        updateChart(chartTemp01, []);
                        updateChart(chartTemp02, []);

                        document.querySelectorAll(".remarks").forEach(remarks => {
                            remarks.querySelector("span").innerText = "Error";
                            remarks.querySelector("span").style.color = "#dc3545";
                            remarks.querySelector("p").innerText = "Unable to fetch real-time data.";
                            remarks.querySelector("h2 + p").innerText = "Check network or server issues.";
                        });
                    });
                }

                // Add this helper function once, outside fetchRealtimeData:
                function updateRemarks(motorIndex, currentTemp) {
                const remarks = document.querySelectorAll(".remarks")[motorIndex];
                const status = remarks.querySelector("span");
                const msg = remarks.querySelector("p");
                const recommendation = remarks.querySelector("h2 + p");

                if (currentTemp > 90) {
                    status.innerText = "Overheat";
                    status.style.color = "#dc3545"; // red
                    msg.innerText = `Motor Temperature ${motorIndex + 1}  is too high: ${currentTemp}°C.`;
                    recommendation.innerText = "Check cooling system and machine load.";
                } else if (currentTemp < 40) {
                    status.innerText = "Abnormally Low";
                    status.style.color = "#17a2b8"; // blue
                    msg.innerText = `Motor Temperature ${motorIndex + 1}  is unusually low: ${currentTemp}°C.`;
                    recommendation.innerText = "Verify if machine is running or sensor is connected.";
                } else {
                    status.innerText = "Normal";
                    status.style.color = "";
                    msg.innerText = "Motor Temperature is within optimal range.";
                    recommendation.innerText = "None";
                }
                }

                const isDarkTheme = <?php echo ($theme == 1 ? 'true' : 'false'); ?>;

                function createChart(canvasId, color) {
                    return new Chart(document.getElementById(canvasId), {
                        type: "line",
                        data: {
                            labels: [],  // Set later with timestamps
                            datasets: [{
                                label: "Temperature (°C)",
                                data: [],
                                borderColor: color,
                                borderWidth: 2,
                                pointRadius: 3,
                                fill: false,
                                tension: 0.3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    display: true,
                                    title: {
                                        display: true,
                                        text: 'Timestamp',
                                        font: { size: 12, weight: 'bold' },
                                        color: isDarkTheme ? '#bbb' : '#333'
                                    },
                                    ticks: { color: isDarkTheme ? '#ccc' : '#000' },
                                    grid: { color: isDarkTheme ? 'rgba(255,255,255,0.1)' : '#ccc' }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'Temperature (°C)',
                                        font: { size: 12, weight: 'bold' },
                                        color: isDarkTheme ? '#bbb' : '#333'
                                    },
                                    ticks: { color: isDarkTheme ? '#ccc' : '#000', beginAtZero: true },
                                    grid: { color: isDarkTheme ? 'rgba(255,255,255,0.1)' : '#ccc' }
                                }
                            },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: ctx => `${ctx.parsed.y} °C`
                                    },
                                    backgroundColor: isDarkTheme ? "#101010" : "#fff",
                                    titleColor: isDarkTheme ? "#d8d8d8" : "#000",
                                    bodyColor: isDarkTheme ? "#d8d8d8" : "#000",
                                    borderColor: isDarkTheme ? "#417630" : "#ccc",
                                    borderWidth: 1
                                }
                            }
                        }
                    });
                }

                function updateChart(chart, newData) {
                    if (chart) {
                        chart.data.datasets[0].data = newData;
                        chart.update();
                        
                    }
                }

                document.addEventListener("DOMContentLoaded", function () {
                    chartTemp01 = createChart("chartTemp01", "#FFB347");
                    chartTemp02 = createChart("chartTemp02", "#FF6347");

                    // Fetch initial data for default machine
                    let defaultMachine = document.querySelector('#machine-tab-list-realtime .machine-tab.active').getAttribute('data-machine');
                    fetchRealtimeData(defaultMachine);

                    // Auto-update every 5 seconds for the selected machine
                    setInterval(function () {
                        let machine = document.querySelector('#machine-tab-list-realtime .machine-tab.active').getAttribute('data-machine');
                        fetchRealtimeData(machine);
                    }, 8000);
                });
            </script>
        </div>

        <!-- Temperature History -->
        <div class="section">
            <div class="content-header">
                <h2>
                    Temperature History
                    <button id="refreshTempHistory" style="background: none; border: none; cursor: pointer;">
                        <i class='bxr  bx-refresh-cw refresh'></i> 
                    </button>
                </h2>

                <div class="section-controls">
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
                </div>
            </div>

            <!-- Machine Selection (History) -->
            <div class="machine-tabs" style="margin-bottom: 18px;">
                <label for="machine-select-history" style="display:none;">Select Machine:</label>
                <div id="machine-tab-list-history" class="machine-tab-list">
                    <button class="machine-tab active" data-machine="CLF 750A" onclick="selectMachineTabHistory(this)">CLF 750A</button>
                    <button class="machine-tab" data-machine="CLF 750B" onclick="selectMachineTabHistory(this)">CLF 750B</button>
                    <button class="machine-tab" data-machine="CLF 750C" onclick="selectMachineTabHistory(this)">CLF 750C</button>
                </div>
            </div>

            <!-- Histogram -->
            <div class="chart-section">
                <div class="scroll-wrapper">
                    <div class="chart-container histogram" style="min-width: 80%; height: 360px;"> <!-- Adjust width and height here -->
                        <canvas id="histogramChart"></canvas>
                    </div>
                    <style>
                        @media screen and (max-width: 720px) { .histogram {width: 200%; height: 360px;} }
                    </style>
                </div>

                <div class="chartInfo">
                    <div class="histogram-legends">
                        <h2>Legends</h2>
                        <div class="legend-item">
                            <span class="legend-line motor1"></span>
                            Loading Motor Temperature 1...
                        </div>
                        <div class="legend-item">
                            <span class="legend-line motor2"></span>
                            Loading Motor Temperature 2...
                        </div>
                    </div>

                    <div class="histogram-remarks">
                        <h2>Remarks</h2>
                        <span class="remarks-status">Loading...</span>
                        <p>---</p>
                        <h2>Recommendations</h2>
                        <p>None</p>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="styled-table" id="sensorTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Motor Temperature 1 (°C)</th>
                            <th>Motor Temperature 1 (°F)</th>
                            <th>Remarks</th>
                            <th>Motor Temperature 2 (°C)</th>
                            <th>Motor Temperature 2 (°F)</th>
                            <th>Remarks</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                    <!-- Table rows will be loaded here via AJAX -->
                    </tbody>
                </table>
            </div>

            <script>
                let motorTempChart = null;
                let selectedMachineHistory = "CLF 750A"; // default machine

                function fetchMotorTempHistogram() {
                    const month = document.getElementById("filter-month")?.value || 0;

                    // Fetch chart data
                    fetch(`fetch/fetch_motor_temp_histogram.php?machine=${encodeURIComponent(selectedMachineHistory)}&month=${month}`)
                        .then(res => res.json())
                        .then(data => {
                            console.log("Motor Temp Histogram data:", data);

                            const temp1Freq = data.temp1Freq || [];
                            const temp2Freq = data.temp2Freq || [];
                            const labels = data.labels || [];

                            const noData = (!labels.length || (temp1Freq.every(v => v === 0) && temp2Freq.every(v => v === 0)));

                            // Update Remarks
                            const remarksStatus = document.querySelector(".histogram-remarks .remarks-status");
                            const remarksText = document.querySelector(".histogram-remarks p");
                            if (noData) {
                                remarksStatus.innerText = "No data found";
                                remarksStatus.style.color = "rgb(153, 153, 153)";
                                remarksText.innerText = "No Motor Temperature records for the selected filters.";
                            } else {
                                remarksStatus.innerText = "Stable";
                                remarksStatus.style.color = "#417630";
                                remarksText.innerText = "Motor Temperatures are within expected range.";
                            }

                            // Update Legends
                            const legendMotor1 = document.querySelector(".legend-item .motor1").nextSibling;
                            const legendMotor2 = document.querySelector(".legend-item .motor2").nextSibling;

                            legendMotor1.textContent = (temp1Freq.every(v => v === 0)) 
                                ? "No Motor Temperature 1 data" 
                                : "Motor Temperature 1 is stable at...";

                            legendMotor2.textContent = (temp2Freq.every(v => v === 0)) 
                                ? "No Motor Temperature 2 data" 
                                : "Motor Temperature 2 is stable at...";

                            // Render Chart
                            const ctx = document.getElementById('histogramChart').getContext('2d');
                            if (motorTempChart) motorTempChart.destroy();

                            motorTempChart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: labels, // x-axis: temperature bins
                                    datasets: [
                                        {
                                            label: 'Motor Temp 1 Frequency',
                                            data: temp1Freq,
                                            borderColor: '#f59c2f',
                                            backgroundColor: '#f59c2f',
                                            borderWidth: 2,
                                            fill: false,
                                            tension: 0.3,
                                            spanGaps: true,
                                            yAxisID: 'y'
                                        },
                                        {
                                            label: 'Motor Temp 2 Frequency',
                                            data: temp2Freq,
                                            borderColor: 'rgb(174,21,21)',
                                            backgroundColor: 'rgb(174,21,21)',
                                            borderWidth: 2,
                                            fill: false,
                                            tension: 0.3,
                                            spanGaps: true,
                                            yAxisID: 'y'
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    interaction: {
                                        mode: 'index',
                                        intersect: false
                                    },
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: {
                                            backgroundColor: "<?php echo $tooltipBg; ?>",
                                            titleColor: "<?php echo $tooltipText; ?>",
                                            bodyColor: "<?php echo $tooltipText; ?>",
                                            callbacks: {
                                                title: (context) => `Temperature bin: ${labels[context[0].dataIndex]}°C`,
                                                label: (ctx) => `${ctx.dataset.label}: ${ctx.raw}`
                                            }
                                        }
                                    },
                                    scales: {
                                        x: {
                                            title: { display: true, text: 'Temperature (°C)', color: "<?php echo $titleColor; ?>" },
                                            ticks: { color: "<?php echo $labelColor; ?>" },
                                            grid: { color: "<?php echo $gridColor; ?>" }
                                        },
                                        y: {
                                            beginAtZero: true,
                                            title: { display: true, text: 'Frequency', color: "<?php echo $titleColor; ?>" },
                                            ticks: { color: "<?php echo $labelColor; ?>" },
                                            grid: { color: "<?php echo $gridColor; ?>" }
                                        }
                                    }
                                }
                            });
                        })
                        .catch(err => console.error("Motor Temp Histogram fetch failed:", err));

                    // Fetch table data
                    fetchMotorTempTable();
                }

                // Fetch table
                function fetchMotorTempTable() {
                    const showEntries = document.getElementById('show-entries').value;
                    const month = document.getElementById('filter-month').value;
                    
                    const xhr = new XMLHttpRequest();
                    xhr.open('GET', `fetch/fetch_motor_temp_table.php?show=${showEntries}&month=${month}&machine=${encodeURIComponent(selectedMachineHistory)}`, true);
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            document.getElementById('table-body').innerHTML = xhr.responseText;
                        }
                    };
                    xhr.send();
                }

                // Machine tab selection
                function selectMachineTabHistory(tab) {
                    document.querySelectorAll('#machine-tab-list-history .machine-tab').forEach(btn => btn.classList.remove('active'));
                    tab.classList.add('active');
                    selectedMachineHistory = tab.getAttribute('data-machine');
                    fetchMotorTempHistogram(); // fetch both chart and table
                }

                // Event listeners
                document.getElementById('filter-month').addEventListener('change', fetchMotorTempHistogram);
                document.getElementById('show-entries').addEventListener('change', fetchMotorTempTable);

                // Initial load
                document.addEventListener("DOMContentLoaded", () => {
                    // Activate default machine tab
                    const defaultTab = document.querySelector('#machine-tab-list-history .machine-tab[data-machine="CLF 750A"]');
                    if (defaultTab) selectMachineTabHistory(defaultTab);
                });
            </script>

            <div class="table-download">
                <a href="#" class="btn-download">Download PDF</a>
                <a href="#" class="btn-download">Download Excel</a>
            </div>
        </div>
    </div>
</body>
</html>
