<?php
$conn = new mysqli("localhost", "root", "", "sensory_data");

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM production_cycle ORDER BY timestamp DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Production Cycle | TS - Sensory Data </title>
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
                <a href="dashboard.html" class="sidebar-link"><i class='bx  bx-dashboard-alt'></i> Dashboard</a>
            </div>
            <p>SYSTEMS</p>
            <div class="sidebar-link-group">
                <a href="#" class="sidebar-link sidebar-active">
                    <i class='bx  bx-timer'></i> Production Cycle
                    <span class="fa fa-caret-down" style="margin-left:8px;"></span>
                </a>
                <div class="sidebar-submenu">
                    <a href="#">CLF 750A</a>
                    <a href="#">CLF 750B</a>
                    <a href="#">CLF 750C</a>
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

    <!-- Main -->
    <div class="main-content">

        <div class="header">
            <div class="header-left">
                <h3>Production Cycle</h3>
                <span>Technical Service Department - Sensory Data</span>
            </div>
            <div class="header-right">
                <h2>CLF 750A</h2>
            </div>
        </div>
        
        <!-- Production Status -->
        <div class="section">
            <div class="content-header">
                <h2 style="margin: 0;">Production Status</h2>
            </div>

            <?php
            // Fetch the latest row from the database for the cards
            $latest_sql = "SELECT * FROM production_cycle ORDER BY timestamp DESC LIMIT 1";
            $latest_result = $conn->query($latest_sql);
            $latest = $latest_result->fetch_assoc();
            ?>

            <!-- Production Cards -->
            <div class="card-container">
                <!-- Status Card -->
                <div id="status-card" class="card machine-card <?php echo ($latest['cycle_status'] == 1) ? 'active-border' : 'inactive-border'; ?>">
                    <div class="status-container">
                        <div id="status-indicator" class="status-indicator <?php echo ($latest['cycle_status'] == 1) ? 'active' : 'inactive'; ?>"></div>
                        <h2 id="machine-status"><?php echo ($latest['cycle_status'] == 1) ? 'Mold Closed' : 'Mold Open'; ?></h2>
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
                    fetch("fetch/fetch_production_status.php")
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById("temp1-value").textContent = data.tempC_01 + "°C";
                            document.getElementById("temp2-value").textContent = data.tempC_02 + "°C";
                            document.getElementById("product-status").textContent = data.product;

                            // Update status
                            document.getElementById("machine-status").textContent = data.cycle_status == 1 ? "Mold Closed" : "Mold Open";
                            document.getElementById("status-indicator").className = "status-indicator " + (data.cycle_status == 1 ? "active" : "inactive");
                            document.getElementById("status-card").className = "card machine-card " + (data.cycle_status == 1 ? "active-border" : "inactive-border");

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

                    // Fetch Data Every 1 Second
                    setInterval(fetchData, 1000);
                });
            </script>
        </div>

        <!-- Status History -->
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
                                <th>Cycle Time (seconds)</th>
                                <th>Recycle Time (seconds)</th>
                                <th>Motor Temperature 1 (°C)</th>
                                <th>Motor Temperature 2 (°C)</th>
                                <th>Machine</th>
                                <th>Product</th>
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
                    xhr.open('GET', `fetch/fetch_production_cycle_table.php?show=${showEntries}&month=${filterMonth}`, true);
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