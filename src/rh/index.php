<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>AtlasTech RH | Admin Console</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #4F46E5;
            --bg-body: #F3F4F6;
            --text-dark: #111827;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #FFFFFF;
            border-right: 1px solid #E5E7EB;
            padding: 2rem 1rem;
            display: flex;
            flex-direction: column;
        }

        .brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #4B5563;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .nav-item:hover, .nav-item.active {
            background-color: #EEF2FF;
            color: var(--primary-color);
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem 3rem;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
        }

        .t-title { font-size: 1.8rem; font-weight: 700; color: var(--text-dark); }
        .t-subtitle { color: #6B7280; font-size: 0.9rem; }

        /* Stats Cards */
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            border: 1px solid #E5E7EB;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .stat-val { font-size: 2rem; font-weight: 700; color: var(--text-dark); }
        .stat-label { color: #6B7280; font-size: 0.85rem; font-weight: 500; }

        /* Table */
        .table-container {
            background: white;
            border-radius: 12px;
            border: 1px solid #E5E7EB;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .table thead {
            background-color: #F9FAFB;
        }
        
        .table th {
            font-weight: 600;
            color: #374151;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #E5E7EB;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .table td {
            padding: 1rem 1.5rem;
            vertical-align: middle;
            color: #1F2937;
            font-size: 0.95rem;
        }

        .badge-dept {
            background: #DBEAFE;
            color: #1E40AF;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .btn-add {
            background-color: var(--primary-color);
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
        }
        .btn-add:hover { background-color: #4338CA; color: white; }

        .btn-icon {
            color: #9CA3AF;
            transition: 0.2s;
        }
        .btn-icon:hover { color: #EF4444; }

    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="brand">
            <i class="fas fa-layer-group"></i> AtlasHR
        </div>
        <a href="#" class="nav-item active">
            <i class="fas fa-users"></i> Employees
        </a>
        <a href="#" class="nav-item">
            <i class="fas fa-chart-pie"></i> Analytics
        </a>
        <a href="#" class="nav-item">
            <i class="fas fa-cog"></i> Settings
        </a>
        <div style="margin-top: auto; padding-top: 2rem; border-top: 1px solid #F3F4F6;">
            <div class="d-flex align-items-center gap-2">
                <img src="https://ui-avatars.com/api/?name=Admin+User&background=random" style="width: 32px; border-radius: 50%;">
                <small style="font-weight: 600;">Admin RH</small>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        
        <div class="header">
            <div>
                <div class="t-title">Employee Management</div>
                <div class="t-subtitle">Manage your team members and their roles.</div>
            </div>
            <a href="create.php" class="btn btn-add">
                <i class="fas fa-plus me-2"></i> Add Employee
            </a>
        </div>

        <!-- Dashboard Stats Row -->
        <div class="row mb-5">
            <?php
            // Simple logic to count employees
            $total_sql = "SELECT count(*) as total FROM employees";
            $total_res = $conn->query($total_sql);
            $total_emp = ($total_res->num_rows > 0) ? $total_res->fetch_assoc()['total'] : 0;
            ?>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-val"><?php echo $total_emp; ?></div>
                    <div class="stat-label">Total Employees</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-val">Active</div>
                    <div class="stat-label">System Status</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-val">Secure</div>
                    <div class="stat-label">Department Access</div>
                </div>
            </div>
        </div>

        <!-- Employee Table -->
        <div class="table-container">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Role / Position</th>
                        <th>Department</th>
                        <th>Contact</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM employees ORDER BY id DESC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>#" . $row["id"] . "</td>
                                <td>
                                    <div class='d-flex align-items-center gap-3'>
                                        <div class='fw-bold'>" . $row["first_name"] . " " . $row["last_name"] . "</div>
                                    </div>
                                </td>
                                <td>" . $row["position"] . "</td>
                                <td><span class='badge-dept'>" . $row["department"] . "</span></td>
                                <td>" . $row["email"] . "</td>
                                <td class='text-end'>
                                    <a href='delete.php?id=" . $row["id"] . "' class='btn-icon' onclick='return confirm(\"Permanently delete this employee?\")'>
                                        <i class='fas fa-trash-alt'></i>
                                    </a>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center py-4 text-muted'>No employees found in the database.</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>
