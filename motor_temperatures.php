<?php
$conn = new mysqli("localhost", "root", "", "sensory_data");

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM realtime_parameters ORDER BY timestamp DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Motor Temperatures | TS - Sensory Data</title>
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
    <!-- Add Chart.js CDN -->
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
                <a href="dashboard.html" class="sidebar-link"><i class='bx  bx-dashboard-alt'></i> Dashboard</a>
            </div>
            <p>SYSTEMS</p>
            <div class="sidebar-link-group">
                <a href="#" class="sidebar-link">
                    <i class='bx  bx-timer'></i> Production Cycle
                    <span class="fa fa-caret-down" style="margin-left:8px;"></span>
                </a>
                <div class="sidebar-submenu">
                    <a href="production_cycle.php">CLF 750A</a>
                    <a href="#">CLF 750B</a>
                    <a href="#">CLF 750C</a>
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
                <span>Technical Service Department - Sensory Data</span>
            </div>
            <div class="header-right">
            </div>
        </div>
        
        <!-- Real-time Temperatures -->
        <div class="section">
            <div class="content-header">
                <h2 style="margin: 0;">Real-time Temperatures</h2>
            </div>

            <!-- Machine Selection -->
            <div class="machine-tabs">
                <label for="machine-select" style="display:none;">Select Machine:</label>
                <div id="machine-tab-list" class="machine-tab-list">
                    <button class="machine-tab active" data-machine="CLF 750A" onclick="selectMachineTab(this)">CLF 750A</button>
                    <button class="machine-tab" data-machine="CLF 750B" onclick="selectMachineTab(this)">CLF 750B</button>
                    <button class="machine-tab" data-machine="CLF 750C" onclick="selectMachineTab(this)">CLF 750C</button>
                </div>
            </div>

            <script>
                function selectMachineTab(tab) {
                    document.querySelectorAll('.machine-tab').forEach(b => b.classList.remove('active'));
                    tab.classList.add('active');
                    // Call your chart update logic here
                    updateChart(tab.getAttribute('data-machine'));
                }
                
                function selectMachine(btn) {
                    document.querySelectorAll('.machine-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    // Call your chart update logic here
                    updateChart(btn.getAttribute('data-machine'));
                }
            </script>
            
            <!-- Cards -->
            <div class="card-container">
                <div class="card-row"> 
                    <?php
                        // Fetch the latest row from the database
                        $sql = "SELECT motor_tempC_01, motor_tempC_02 FROM motor_temperatures ORDER BY timestamp DESC LIMIT 1";
                        $result = $conn->query($sql);
                        $data = $result->fetch_assoc();
                    ?>

                    <div class="card temperature1-card">
                        <h2 id="temp01-value">--°C</h2>
                        <p>Motor 01</p>
                        <div class="chart-container">
                            <!-- You can set width/height here via attributes or CSS -->
                            <canvas id="chartTemp01" width="150" height="60"></canvas>
                        </div>
                    </div>

                    <div class="card temperature2-card">
                        <h2 id="temp02-value">--°C</h2>
                        <p>Motor 02</p>
                        <div class="chart-container">
                            <canvas id="chartTemp02" width="150" height="60"></canvas>
                        </div>
                    </div>
                </div>

                <div class="remarks">
                    <h2>Remarks</h2>
                    <span>Normal</span>
                    <p>Motor temperatures are monitored in real-time to ensure optimal performance and prevent overheating. The data is updated every 5 seconds.</p>
                    <h2>Recommendations</h2>
                    <p>None</p>
                </div>
            </div>

            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    let chartTemp01, chartTemp02;

                    function fetchRealtimeData() {
                        fetch("fetch/fetch_motor_temp.php?type=realtime")
                            .then(response => response.json())
                            .then(data => {
                                // Update text values
                                document.getElementById("temp01-value").innerText = data.motor_tempC_01[0] + "°C";
                                document.getElementById("temp02-value").innerText = data.motor_tempC_02[0] + "°C";

                                // Update charts dynamically
                                updateChart(chartTemp01, data.motor_tempC_01.reverse());
                                updateChart(chartTemp02, data.motor_tempC_02.reverse());
                            })
                            .catch(error => console.error("Error fetching real-time data:", error));
                    }

                    function createChart(canvasId, color) {
                        return new Chart(document.getElementById(canvasId), {
                            type: "line",
                            data: {
                                labels: Array.from({length: 10}, (_, i) => i + 1),
                                datasets: [{
                                    data: [],
                                    borderColor: color,
                                    borderWidth: 2,
                                    pointRadius: 2,
                                    fill: false,
                                    tension: 0.3
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    x: { display: false },
                                    y: { display: false }
                                },
                                plugins: { legend: { display: false } }
                            }
                        });
                    }

                    function updateChart(chart, newData) {
                        if (chart) {
                            chart.data.datasets[0].data = newData;
                            chart.update();
                        }
                    }

                    // Initialize charts
                    chartTemp01 = createChart("chartTemp01", "#FFB347");
                    chartTemp02 = createChart("chartTemp02", "#FF6347");

                    // Fetch initial data
                    fetchRealtimeData();

                    // Auto-update every 5 seconds
                    setInterval(fetchRealtimeData, 5000);
                });
            </script>
        </div>

        <!-- Temperature History -->
        <div class="section">
            <div class="content-header">
                <h2>Status History</h2>

                <div class="section-controls">
                    <div class="by_number">
                        <label for="show-entries">Show</label>
                        <select id="show-entries">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
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

                <div class="table-responsive">
                    <table class="styled-table" id="sensorTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Motor Temperature 1 (°C)</th>
                                <th>Motor Temperature 1 (°F)</th>
                                <th>Motor Temperature 2 (°C)</th>
                                <th>Motor Temperature 2 (°F)</th>
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
                    const xhr = new XMLHttpRequest();
                    xhr.open('GET', `fetch/fetch_motor_temp_table.php?show=${showEntries}&month=${filterMonth}`, true);
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

                // Set default month to current month
                document.addEventListener("DOMContentLoaded", function () {
                    let currentMonth = new Date().getMonth() + 1;
                    document.getElementById("filter-month").value = currentMonth;
                    fetchTableData();
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