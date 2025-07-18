<?php
$conn = new mysqli("localhost", "root", "", "sensory_data");

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get $machine from URL (GET parameter)
$machine = isset($_GET['machine']) ? $_GET['machine'] : null;

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
                <a href="dashboard.php" class="sidebar-link"><i class='bx  bx-dashboard-alt'></i> Dashboard</a>
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
        
        <h2>Last Cycle History</h2>

        <div class="table-container">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Machine Name</th>
                        <th>Last Cycle Time (seconds)</th>
                        <th>Last Processing Time (seconds)</th>
                        <th>Last Recycle Time (seconds)</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>CLF750A</td>
                        <td>120</td>
                        <td>60</td>
                        <td>60 </td>
                        <td>2024-06-01 12:00:00</td>
                    </tr>
                    <tr>
                        <td>CLF750B</td>
                        <td>110</td>
                        <td>55 </td>
                        <td>55 </td>
                        <td>2024-06-01 12:05:00</td>
                    </tr>
                    <tr>
                        <td>CLF750C</td>
                        <td>130</td>
                        <td>65</td>
                        <td>65</td>
                        <td>2024-06-01 12:10:00</td>
                    </tr>
                </tbody>
            </table>

            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    fetch('fetch/fetch_production_cycle_last_entries.php')
                        .then(res => res.json())
                        .then(data => {
                            const tbody = document.querySelector("#sideTable .styled-table tbody");
                            tbody.innerHTML = "";

                            if (Array.isArray(data)) {
                                data.forEach(row => {
                                    const tr = document.createElement("tr");
                                    tr.innerHTML = `
                                        <td>${row.machine}</td>
                                        <td>${row.cycle_time}</td>
                                        <td>${row.processing_time}</td>
                                        <td>${row.recycle_time}</td>
                                        <td>${row.timestamp}</td>
                                    `;
                                    tbody.appendChild(tr);
                                });
                            } else {
                                tbody.innerHTML = `<tr><td colspan="5">No data found</td></tr>`;
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching last cycles:', error);
                            const tbody = document.querySelector("#sideTable .styled-table tbody");
                            tbody.innerHTML = `<tr><td colspan="5">Error loading data</td></tr>`;
                        });
                });
            </script>
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
        </script>
        <style>
            .side-table.collapsed {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            .side-table {
                transition: transform 0.3s;
            }
        </style>
    </div>

    <!-- Main -->
    <div class="main-content">

        <div class="header">
            <div class="header-left">
                <h3>Production Cycle</h3>
                <span>Technical Service Department - Sensory Data</span>
            </div>
            <div class="header-right">
                <h2><?php echo htmlspecialchars($machine ? $machine : 'No Machine Selected'); ?></h2>
            </div>
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
                    fetch("fetch/fetch_production_status.php?machine=<?php echo urlencode($machine); ?>")
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

        <!-- Cycle History -->
        <div class="section">
            <div class="content-header">
                <h2>Cycle History</h2>

                <div class="section-controls">
                    <div class="by_product">
                        <label for="show-product">Product</label>
                        <select id="show-product">
                            <option value="" selected>All</option>
                            <option value="Pepsi">Pepsi</option>
                            <option value="Basket">Basket</option>
                            <option value="Chair">Chair</option>
                        </select>
                    </div>
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
                    <h3>Cycle Time (seconds)</h3>
                    <div class="bar-container">
                        <div class="bar" style="width:<?php echo ($values['cycle']/$maxValue)*100; ?>%;background:#417630;">
                            <span class="bar-label"><?php echo $values['cycle']; ?></span>
                        </div>
                    </div>
                    <h3>Processing Time (seconds)</h3>
                    <div class="bar-container">
                        <div class="bar" style="width:<?php echo ($values['processing']/$maxValue)*100; ?>%;background:#9b2b2b;">
                            <span class="bar-label"><?php echo $values['processing']; ?></span>
                        </div>
                    </div>
                    <h3>Recycle Time (seconds)</h3>
                    <div class="bar-container">
                        <div class="bar" style="width:<?php echo ($values['recycle']/$maxValue)*100; ?>%;background:#2a656f;">
                            <span class="bar-label"><?php echo $values['recycle']; ?></span>
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
                    const showEntries = document.getElementById('show-entries').value;
                    const filterMonth = document.getElementById('filter-month').value;
                    const selectedProduct = document.getElementById('show-product').value;

                    const xhr = new XMLHttpRequest();
                    xhr.open('GET', `fetch/fetch_production_cycle_table.php?machine=${encodeURIComponent(machineSafe)}&show=${showEntries}&month=${filterMonth}&product=${encodeURIComponent(selectedProduct)}`, true);
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            document.getElementById('table-body').innerHTML = xhr.responseText;
                        }
                    };
                    xhr.send();
                }

                function updateTimeCards(stats) {
                    const max = Math.max(
                        stats.average.cycle, stats.maximum.cycle,
                        stats.average.processing, stats.maximum.processing,
                        stats.average.recycle, stats.maximum.recycle,
                        1 // avoid division by zero
                    );

                    ['average', 'minimum', 'maximum'].forEach(type => {
                        const card = document.querySelector(`.time-card.${type}`);
                        const val = stats[type];
                        card.querySelectorAll('.bar')[0].style.width = (val.cycle / max * 100) + '%';
                        card.querySelectorAll('.bar')[0].innerText = val.cycle;

                        card.querySelectorAll('.bar')[1].style.width = (val.processing / max * 100) + '%';
                        card.querySelectorAll('.bar')[1].innerText = val.processing;

                        card.querySelectorAll('.bar')[2].style.width = (val.recycle / max * 100) + '%';
                        card.querySelectorAll('.bar')[2].innerText = val.recycle;
                    });
                }

                function fetchAll() {
                    fetchTableData(); // update table

                    const machine = "<?php echo $machine_safe; ?>";
                    const show = document.getElementById('show-entries').value;
                    const month = document.getElementById('filter-month').value;
                    const product = document.getElementById('show-product').value;

                    const url = `fetch/fetch_production_cycle_timecards.php?machine=${encodeURIComponent(machine)}&show=${show}&month=${month}&product=${encodeURIComponent(product)}`;

                    fetch(url)
                        .then(res => res.json())
                        .then(data => {
                            if (data.error) return;
                            updateTimeCards(data);
                        });
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
            </script>
            
            <div class="table-download">
                <a href="#" class="btn-download">Download PDF</a>
                <a href="#" class="btn-download">Download Excel</a>
            </div>
        </div>
    </div>
</body>
</html>