<?php
date_default_timezone_set('Asia/Manila');

$conn = new mysqli("localhost", "root", "", "sensory_data");

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <a href="dashboard.php" class="sidebar-link"><i class='bx  bx-dashboard-alt'></i> Dashboard</a>
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
            <span style="font-size: 0.75rem; color: #646464">Logged in as:</span>
            <span>User123</span>
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
                        <p>Motor 01</p>
                        <div class="chart-container">
                            <!-- You can set width/height here via attributes or CSS -->
                            <canvas id="chartTemp01"></canvas>
                        </div>
                    </div>
                    <!-- Remarks -->
                    <div class="remarks">
                        <h2>Remarks</h2>
                        <span>Normal</span>
                        <p>Motor temperatures are monitored in real-time to ensure optimal performance and prevent overheating. The data is updated every 5 seconds.</p>
                        <h2>Recommendations</h2>
                        <p>None</p>
                    </div>
                </div>

                <div class="card-column">
                    <div class="card temperature2-card">
                        <h2 id="temp02-value">--°C</h2>
                        <p>Motor 02</p>
                        <div class="chart-container">
                            <canvas id="chartTemp02"></canvas>
                        </div>
                     </div>
                    <!-- Remarks -->
                    <div class="remarks">
                        <h2>Remarks</h2>
                        <span>Normal</span>
                        <p>Motor temperatures are monitored in real-time to ensure optimal performance and prevent overheating. The data is updated every 5 seconds.</p>
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
                                remarks.querySelector("p").innerText = "No motor temperature data available for the selected machine.";
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
                    msg.innerText = `Motor 0${motorIndex + 1} temperature is too high: ${currentTemp}°C.`;
                    recommendation.innerText = "Check cooling system and machine load.";
                } else if (currentTemp < 40) {
                    status.innerText = "Abnormally Low";
                    status.style.color = "#17a2b8"; // blue
                    msg.innerText = `Motor 0${motorIndex + 1} temperature is unusually low: ${currentTemp}°C.`;
                    recommendation.innerText = "Verify if machine is running or sensor is connected.";
                } else {
                    status.innerText = "Normal";
                    status.style.color = "";
                    msg.innerText = "Motor temperature is within optimal range.";
                    recommendation.innerText = "None";
                }
                }

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
                                        color: '#bbb'
                                    },
                                    ticks: { color: '#ccc' },
                                    grid: { color: 'rgba(255,255,255,0.1)' }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'Temperature (°C)',
                                        font: { size: 12, weight: 'bold' },
                                        color: '#bbb'
                                    },
                                    ticks: { color: '#ccc', beginAtZero: true },
                                    grid: { color: 'rgba(255,255,255,0.1)' }
                                }
                            },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: ctx => `${ctx.parsed.y} °C`
                                    }
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
                    }, 5000);
                });
            </script>
        </div>

        <!-- Temperature History -->
        <div class="section">
            <div class="content-header">
                <h2>Temperature History</h2>

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
                function fetchTableData() {
                    const showEntries = document.getElementById('show-entries').value;
                    const filterMonth = document.getElementById('filter-month').value;
                    const selectedMachine = document.querySelector('#machine-tab-list-history .machine-tab.active').getAttribute('data-machine');
                    
                    const xhr = new XMLHttpRequest();
                    xhr.open('GET', `fetch/fetch_motor_temp_table.php?show=${showEntries}&month=${filterMonth}&machine=${encodeURIComponent(selectedMachine)}`, true);
                    xhr.onload = function() {
                    if (xhr.status === 200) {
                        document.getElementById('table-body').innerHTML = xhr.responseText;
                    }
                    };
                    xhr.send();
                }

                // Update table when controls change
                document.getElementById('show-entries').addEventListener('change', fetchTableData);
                document.getElementById('filter-month').addEventListener('change', fetchTableData);

                // Handle machine tab switching for history section
                function selectMachineTabHistory(tab) {
                    document.querySelectorAll('#machine-tab-list-history .machine-tab').forEach(btn => btn.classList.remove('active'));
                    tab.classList.add('active');
                    fetchTableData(); // refresh table when machine changes
                }

                // Set default month to current month
                document.addEventListener("DOMContentLoaded", function () {
                    // Set default month to current
                    let currentMonth = new Date().getMonth() + 1;
                    document.getElementById("filter-month").value = currentMonth;

                    // Explicitly activate CLF 750A tab for history
                    const defaultMachineTab = document.querySelector('#machine-tab-list-history .machine-tab[data-machine="CLF 750A"]');
                    if (defaultMachineTab) {
                    selectMachineTabHistory(defaultMachineTab); // this calls fetchTableData()
                    } else {
                    fetchTableData(); // fallback if something goes wrong
                    }
                });
                // 🔁 Auto-refresh the temperature table every 30 seconds
                setInterval(fetchTableData, 15000);
            </script>

            
            <div class="table-download">
                <a href="#" class="btn-download">Download PDF</a>
                <a href="#" class="btn-download">Download Excel</a>
            </div>
        </div>
    </div>
</body>
</html>
