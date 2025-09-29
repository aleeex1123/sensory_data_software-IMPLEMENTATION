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
    <title>Weights | Sensory Data</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/logo-2.png">

    <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="css/webpage_defaults.css">
    <link rel="stylesheet" href="css/weights.css">
    <link rel="stylesheet" href="css/table.css">
    <link rel="stylesheet" href="css/histogram.css">
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
                <a href="#" class="sidebar-link sidebar-active">
                    <i class='bx  bx-dumbbell'></i> Weight Data
                    <span class="fa fa-caret-down" style="margin-left:8px;"></span>
                </a>
                <div class="sidebar-submenu" style="display:none;">
                    <a href="#">Gross/Net Weights</a>
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
                <h3>Gross/Net Weights</h3>
                <span>Production Department - Sensory Data</span>
            </div>
            <div class="header-right">
            </div>
        </div>

        <!-- Scale Controls -->
        <div class="section">
            <div class="content-header">
                <h2>Weighing Scale Controls</h2>
            </div>

            <?php
            $availableMachines = [
                                    "ARB 50",
                                    "SUM 260C",
                                    "SUM 650",
                                    "MIT 650D",
                                    "TOS 650A",
                                    "TOS 850A",
                                    "TOS 850B",
                                    "TOS 850C",
                                    "CLF 750A",
                                    "CLF 750B",
                                    "CLF 750C",
                                    "CLF 950A",
                                    "CLF 950B",
                                    "MIT 1050B"
                                ];


            $sql = "SELECT scale_id, scale_status, assigned_machine FROM weighing_scale_controls";
            $result = $conn->query($sql);

            echo '<div class="scale-controls">';
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $scaleId = htmlspecialchars($row['scale_id']);
                    $assignedMachine = trim($row['assigned_machine']);
                    $isActive = $row['scale_status'] == 1;

                    $labelClass = $isActive ? "label-on" : "label-off";
                    $btnText = $isActive ? "Change" : "Assign";
                    $btnClass = $isActive ? "scale-on" : "scale-off";

                    // Status text
                    $statusText = $assignedMachine ?
                        "assigned to " . htmlspecialchars($assignedMachine) :
                        "no assigned machine";

                    echo '
                    <div class="scale" data-scale-id="' . $scaleId . '">
                        <div class="scale-info">
                            <label class="' . $labelClass . '">' . $scaleId . '</label>
                            <p>' . $statusText . '</p>
                        </div>
                        <form method="post" action="to_database/update_scale_control.php" class="scale-form" style="display:inline;">
                            <input type="hidden" name="scale_id" value="' . $scaleId . '">
                            <div class="controls">
                                <select name="assigned_machine" class="input-field">
                                    <option value="">None</option>';
                    
                    foreach ($availableMachines as $machine) {
                        $selected = ($assignedMachine === $machine) ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($machine) . '" ' . $selected . '>' . htmlspecialchars($machine) . '</option>';
                    }

                    echo '      </select>
                                <button type="submit" class="btn btn-primary ' . $btnClass . '">' . $btnText . '</button>
                            </div>
                        </form>
                    </div>';
                }
            } else {
                echo '<p>No scales found.</p>';
            }
            echo '</div>';
            $conn->close();
            ?>

        </div>

        <script>
        // Optional: Confirm before stopping a scale
        document.querySelectorAll('.scale-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
            var action = form.querySelector('input[name="action"]').value;
            var scaleId = form.querySelector('input[name="scale_id"]').value;
            var productInput = form.querySelector('input[name="product"]');
            if (action === 'stop') {
                if (!confirm('Are you sure you want to stop ' + scaleId + '?')) {
                e.preventDefault();
                }
            } else if (action === 'start') {
                if (productInput && !productInput.value.trim()) {
                alert('Please enter a product name.');
                e.preventDefault();
                }
            }
            });
        });
        </script>

        <!-- Weight Data -->
        <div class="section">
            <div class="content-header">
                <h2>
                    Gross/Net Weight Data
                    <button id="refreshWeightData" style="background: none; border: none; cursor: pointer;">
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
                            <span class="legend-box gross"></span>
                            Loading Gross Weight...
                        </div>
                        <div class="legend-item">
                            <span class="legend-box net"></span>
                            Loading Net Weight...
                        </div>
                        <div class="legend-item">
                            <span class="legend-line diff"></span>
                            Loading Difference...
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
                            <th>Machine</th>
                            <th>Product</th>
                            <th>Mold Number</th>
                            <th>Gross Weight (g)</th>
                            <th>Net Weight (g)</th>
                            <th>Difference (g)</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        <!-- Table rows will be loaded here via AJAX -->
                    </tbody>
                </table>
            </div>
            
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                let weightChart = null;
                            
                function fetchWeightHistogram() {
                    const filterMonth = document.getElementById('filter-month').value;
                    const product = document.getElementById('show-product').value;

                    fetch(`fetch/fetch_weights_histogram.php?month=${filterMonth}&product=${encodeURIComponent(product)}`)
                        .then(res => res.json())
                        .then(data => {
                            console.log("Weight Histogram data:", data);

                            const grossFreq = data.grossFreq || [];
                            const netFreq = data.netFreq || [];
                            const diffFreq = data.diffFreq || [];
                            const labels = data.labels || [];

                            const noData = (
                                !labels.length ||
                                (grossFreq.every(v => v === 0) &&
                                netFreq.every(v => v === 0) &&
                                diffFreq.every(v => v === 0))
                            );

                            // Update remarks
                            const remarksStatus = document.querySelector(".histogram-remarks .remarks-status");
                            const remarksText = document.querySelector(".histogram-remarks p");
                            if (noData) {
                                remarksStatus.innerText = "No Data Found";
                                remarksStatus.style.color = "rgb(153, 153, 153)";
                                remarksText.innerText = "No weight records found.";
                            } else {
                                remarksStatus.innerText = "Stable";
                                remarksStatus.style.color = "#417630";
                                remarksText.innerText = "Weights are within expected range.";
                            }

                            // Update legends
                            document.querySelector(".legend-item .gross").nextSibling.textContent =
                                (grossFreq.every(v => v === 0)) ? "No gross weight data" : "Gross weight distribution...";
                            document.querySelector(".legend-item .net").nextSibling.textContent =
                                (netFreq.every(v => v === 0)) ? "No net weight data" : "Net weight distribution...";
                            document.querySelector(".legend-item .diff").nextSibling.textContent =
                                (diffFreq.every(v => v === 0)) ? "No difference data" : "Gross-Net difference trend...";

                            // Render chart
                            const ctx = document.getElementById('histogramChart').getContext('2d');
                            if (weightChart) weightChart.destroy();

                            weightChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: labels,
                                    datasets: [
                                        {
                                            label: 'Gross Weight',
                                            data: grossFreq,
                                            backgroundColor: 'rgb(42, 101, 111)',
                                            borderWidth: 1,
                                            borderRadius: 4,
                                            yAxisID: 'y',
                                            order: 2
                                        },
                                        {
                                            label: 'Net Weight',
                                            data: netFreq,
                                            backgroundColor: 'rgb(174, 21, 21)',
                                            borderWidth: 1,
                                            borderRadius: 4,
                                            yAxisID: 'y',
                                            order: 2
                                        },
                                        {
                                            label: 'Difference',
                                            data: diffFreq,
                                            type: 'line',
                                            borderColor: "<?php echo $labelColor; ?>",
                                            backgroundColor: "<?php echo $labelColor; ?>",
                                            borderWidth: 3,
                                            pointRadius: 4,
                                            pointHoverRadius: 6,
                                            fill: false,
                                            tension: 0.3,
                                            spanGaps: true,
                                            yAxisID: 'y',
                                            order: 1
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    interaction: { mode: 'index', intersect: false },
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: {
                                            backgroundColor: "<?php echo $tooltipBg; ?>",
                                            titleColor: "<?php echo $tooltipText; ?>",
                                            bodyColor: "<?php echo $tooltipText; ?>",
                                            callbacks: {
                                                title: (ctx) => `Weight bin: ${labels[ctx[0].dataIndex]} kg`,
                                                label: (ctx) => `${ctx.dataset.label}: ${ctx.raw}`
                                            }
                                        }
                                    },
                                    scales: {
                                        x: {
                                            ticks: { color: "<?php echo $labelColor; ?>" },
                                            grid: { color: "<?php echo $gridColor; ?>" },
                                            title: { display: true, text: 'Weight (kg)', color: "<?php echo $titleColor; ?>" }
                                        },
                                        y: {
                                            beginAtZero: true,
                                            ticks: { color: "<?php echo $labelColor; ?>" },
                                            grid: { color: "<?php echo $gridColor; ?>" },
                                            title: { display: true, text: 'Frequency', color: "<?php echo $titleColor; ?>" }
                                        }
                                    }
                                }
                            });
                        })
                        .catch(err => console.error("Weight Histogram fetch failed:", err));
                }

                // Product options
                function loadProductOptions() {
                    const xhr = new XMLHttpRequest();
                    xhr.open('GET', 'fetch/fetch_weights_products.php', true);
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            document.getElementById('show-product').innerHTML = xhr.responseText;
                        }
                    };
                    xhr.send();
                }

                // Table data
                function fetchTableData() {
                    const showEntries = document.getElementById('show-entries').value;
                    const filterMonth = document.getElementById('filter-month').value;
                    const product     = document.getElementById('show-product').value;

                    const xhr = new XMLHttpRequest();
                    xhr.open('GET', `fetch/fetch_weights_table.php?show=${showEntries}&month=${filterMonth}&product=${encodeURIComponent(product)}`, true);
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            document.getElementById('table-body').innerHTML = xhr.responseText;
                        }
                    };
                    xhr.send();
                }

                // Init
                document.addEventListener("DOMContentLoaded", () => {
                    let currentMonth = new Date().getMonth() + 1;
                    document.getElementById("filter-month").value = currentMonth;

                    fetchTableData();
                    fetchWeightHistogram();
                    loadProductOptions();

                    // Filters
                    document.getElementById('filter-month').addEventListener('change', () => {
                        fetchWeightHistogram();
                        fetchTableData();
                    });
                    document.getElementById('show-product').addEventListener('change', () => {
                        fetchWeightHistogram();
                        fetchTableData();
                    });
                    document.getElementById('show-entries').addEventListener('change', fetchTableData);

                    // Refresh button
                    document.getElementById('refreshWeightData').addEventListener('click', () => {
                        fetchWeightHistogram();
                        fetchTableData();
                    });
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