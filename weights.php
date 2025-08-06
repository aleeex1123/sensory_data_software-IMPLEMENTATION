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
            <span style="font-size: 0.75rem; color: #646464">Logged in as:</span>
            <span>User123</span>
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
            $conn = new mysqli("localhost", "root", "", "sensory_data");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

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
                <h2>Gross/Net Weight Data</h2>

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

                <script>
                    function fetchTableData() {
                        const showEntries = document.getElementById('show-entries').value;
                        const filterMonth = document.getElementById('filter-month').value;
                        const xhr = new XMLHttpRequest();
                        xhr.open('GET', `fetch/fetch_weights_table.php?show=${showEntries}&month=${filterMonth}`, true);
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
                    // 🔁 Auto-refresh every 30 seconds
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