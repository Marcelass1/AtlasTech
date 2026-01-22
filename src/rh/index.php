<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>AtlasHR | Tableau de Bord</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #eef2ff;
            --secondary: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --text-dark: #0f172a;
            --text-light: #64748b;
            --sidebar-width: 280px;
            --border-color: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            margin: 0;
            display: flex;
            min-height: 100vh;
            color: var(--text-dark);
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: #ffffff;
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            padding: 2rem 1.5rem;
            z-index: 100;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 3rem;
            letter-spacing: -0.5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px;
            color: var(--secondary);
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            margin-bottom: 0.5rem;
            transition: all 0.2s;
        }

        .nav-link:hover, .nav-link.active {
            background-color: var(--primary-light);
            color: var(--primary);
        }

        .user-block {
            margin-top: auto;
            border-top: 1px solid var(--border-color);
            padding-top: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .user-avatar-sm {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 2.5rem 3rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 2.5rem;
        }

        h1 { margin: 0; font-size: 2rem; font-weight: 700; color: var(--text-dark); letter-spacing: -1px; }
        p.subtitle { margin: 8px 0 0; color: var(--text-light); font-size: 1rem; }

        .btn-primary {
            background: var(--primary);
            color: white;
            padding: 12px 24px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(79, 70, 229, 0.3); }

        .btn-secondary {
            background: white;
            color: var(--secondary);
            border: 1px solid var(--border-color);
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
        }
        .btn-secondary:hover { background: #f8fafc; border-color: #cbd5e1; }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--primary);
        }

        .stat-label { color: var(--text-light); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-value { font-size: 2.5rem; font-weight: 800; color: var(--text-dark); margin: 0.5rem 0 0; letter-spacing: -1px; }
        
        /* Toolkit Bar */
        .toolkit-bar {
            background: white;
            padding: 1rem;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            margin-bottom: 1.5rem;
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .search-box {
            position: relative;
            flex: 1;
        }
        .search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        .search-input {
            width: 100%;
            padding: 10px 10px 10px 40px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
            box-sizing: border-box;
        }
        .search-input:focus { border-color: var(--primary); }

        .filter-select {
            padding: 10px 16px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.95rem;
            outline: none;
            color: var(--text-dark);
            width: 200px;
            cursor: pointer;
        }

        /* Table */
        .table-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        table { width: 100%; border-collapse: collapse; }
        
        th {
            background: #f8fafc;
            text-align: left;
            padding: 1rem 1.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--secondary);
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            color: #334155;
            font-size: 0.95rem;
            vertical-align: middle;
        }

        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f8fafc; }

        .employee-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #e0e7ff;
            color: #4338ca;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .emp-info div { font-weight: 600; color: var(--text-dark); }
        .emp-info span { font-size: 0.85rem; color: var(--text-light); }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-it { background: #e0f2fe; color: #0369a1; }
        .badge-rh { background: #f3e8ff; color: #7e22ce; }
        .badge-sales { background: #dcfce7; color: #15803d; }
        .badge-default { background: #f1f5f9; color: #475569; }

        .action-btn {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 1px solid var(--border-color);
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
        }
        .action-btn.delete:hover { background: #fee2e2; border-color: #fca5a5; color: #ef4444; }

        /* Toast Notification */
        .toast {
            visibility: hidden;
            min-width: 250px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 8px;
            padding: 12px 16px;
            position: fixed;
            z-index: 1000;
            right: 30px;
            bottom: 30px;
            font-size: 0.9rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateY(100px);
            transition: transform 0.3s, visibility 0.3s;
        }
        .toast.show { visibility: visible; transform: translateY(0); }
        .toast.success { background-color: #10b981; }
        .toast.danger { background-color: #ef4444; }

    </style>
</head>
<body>

    <?php
    session_start();
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("Location: login.php");
        exit;
    }
    include 'db.php';

    // Calculate Stats
    $total_emp = $conn->query("SELECT count(*) as c FROM employees")->fetch_assoc()['c'];
    $total_depts = $conn->query("SELECT count(distinct department) as c FROM employees")->fetch_assoc()['c'];
    $latest_hire = $conn->query("SELECT hired_date FROM employees ORDER BY hired_date DESC LIMIT 1")->fetch_assoc()['hired_date'] ?? 'N/A';
    ?>

    <nav class="sidebar">
        <div class="brand">
            <i class="fas fa-cube"></i> AtlasHR
        </div>

        <a href="#" class="nav-link active">
            <i class="fas fa-users"></i>
            Collaborateurs
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-chart-pie"></i>
            Analytiques
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-file-contract"></i>
            Documents
        </a>
        <a href="#" class="nav-link" style="margin-top:auto;">
            <i class="fas fa-sliders-h"></i>
            Paramètres
        </a>

        <div class="user-block">
            <div style="display:flex; align-items:center; gap:10px;">
                <div class="user-avatar-sm">
                   <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                </div>
                <div>
                    <div style="font-weight: 700; font-size: 0.9rem;"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                    <div style="font-size: 0.75rem; color: #94a3b8;">Administrateur</div>
                </div>
            </div>
            <a href="logout.php" class="action-btn delete" style="border:none;" title="Déconnexion"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </nav>

    <main class="main-content">
        <div class="header">
            <div>
                <h1>Gestions des Ressources</h1>
                <p class="subtitle">Supervisez votre équipe, analysez les performances et gérez les recrutements.</p>
            </div>
            <div style="display:flex; gap:10px;">
                <a href="create.php" class="btn-primary">
                    <i class="fas fa-plus"></i> Nouveau
                </a>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Effectif Total</div>
                <div class="stat-value"><?php echo $total_emp; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Directions Actives</div>
                <div class="stat-value"><?php echo $total_depts; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Dernier Recrutement</div>
                <div class="stat-value" style="font-size: 1.8rem; margin-top:0.8rem;"><?php echo date("d M Y", strtotime($latest_hire)); ?></div>
            </div>
        </div>

        <div class="toolkit-bar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="Rechercher un employé par nom, email...">
            </div>
            <select id="deptFilter" class="filter-select">
                <option value="all">Tous les Départements</option>
                <?php
                $depts_sql = "SELECT DISTINCT department FROM employees ORDER BY department";
                $depts = $conn->query($depts_sql);
                while($d = $depts->fetch_assoc()) {
                    echo "<option value='".$d['department']."'>".$d['department']."</option>";
                }
                ?>
            </select>
            <button onclick="exportTableToCSV('employes_atlashr.csv')" class="btn-secondary">
                <i class="fas fa-download"></i> Exporter
            </button>
        </div>

        <div class="table-container">
            <table id="empTable">
                <thead>
                    <tr>
                        <th width="80">#ID</th>
                        <th>Employé</th>
                        <th>Contact</th>
                        <th>Poste</th>
                        <th>Département</th>
                        <th width="80" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM employees ORDER BY id DESC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            // Badge logic
                            $dept = strtoupper($row['department']);
                            $badgeClass = 'badge-default';
                            if ($dept == 'IT') $badgeClass = 'badge-it';
                            if ($dept == 'RH' || $dept == 'HR') $badgeClass = 'badge-rh';
                            if ($dept == 'SALES' || $dept == 'COMMERCIAL') $badgeClass = 'badge-sales';
                            
                            // Initials
                            $initials = strtoupper(substr($row['first_name'],0,1) . substr($row['last_name'],0,1));

                            echo "<tr>
                                <td style='color:#94a3b8;'>#" . sprintf('%03d', $row["id"]) . "</td>
                                <td>
                                    <div class='employee-cell'>
                                        <div class='avatar'>$initials</div>
                                        <div class='emp-info'>
                                            <div>" . htmlspecialchars($row["first_name"] . " " . $row["last_name"]) . "</div>
                                        </div>
                                    </div>
                                </td>
                                <td>" . htmlspecialchars($row["email"]) . "</td>
                                <td>" . htmlspecialchars($row["position"]) . "</td>
                                <td><span class='badge $badgeClass'>" . htmlspecialchars($row["department"]) . "</span></td>
                                <td>
                                    <a href='delete.php?id=" . $row["id"] . "' class='action-btn delete' onclick='return confirmDelete(event, this.href)'>
                                        <i class='fas fa-trash-alt'></i>
                                    </a>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr class='no-records'><td colspan='6' style='text-align:center; padding:3rem; color:#94a3b8;'>Aucun collaborateur trouvé.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="toast" class="toast">Notification</div>

    <script>
        // Search & Filter Logic
        const searchInput = document.getElementById('searchInput');
        const deptFilter = document.getElementById('deptFilter');
        const table = document.getElementById('empTable');
        const trs = table.getElementsByTagName('tr');

        function filterTable() {
            const filterValue = searchInput.value.toLowerCase();
            const deptValue = deptFilter.value.toLowerCase();

            for (let i = 1; i < trs.length; i++) {
                let tdName = trs[i].getElementsByTagName('td')[1]; // Employee Name
                let tdEmail = trs[i].getElementsByTagName('td')[2]; // Email
                let tdDept = trs[i].getElementsByTagName('td')[4]; // Dept

                if (tdName && tdDept) {
                    const txtName = tdName.textContent || tdName.innerText;
                    const txtEmail = tdEmail.textContent || tdEmail.innerText;
                    const txtDept = tdDept.textContent || tdDept.innerText;
                    
                    const matchesSearch = txtName.toLowerCase().indexOf(filterValue) > -1 || txtEmail.toLowerCase().indexOf(filterValue) > -1;
                    const matchesDept = deptValue === 'all' || txtDept.toLowerCase() === deptValue;

                    if (matchesSearch && matchesDept) {
                        trs[i].style.display = "";
                    } else {
                        trs[i].style.display = "none";
                    }
                }
            }
        }

        searchInput.addEventListener('keyup', filterTable);
        deptFilter.addEventListener('change', filterTable);

        // CSV Export Logic
        function downloadCSV(csv, filename) {
            let csvFile;
            let downloadLink;
            csvFile = new Blob([csv], {type: "text/csv"});
            downloadLink = document.createElement("a");
            downloadLink.download = filename;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
            downloadLink.click();
        }

        function exportTableToCSV(filename) {
            let csv = [];
            let rows = document.querySelectorAll("table tr");
            
            for (let i = 0; i < rows.length; i++) {
                let row = [], cols = rows[i].querySelectorAll("td, th");
                
                // Skip invisible rows (filtered out)
                if (rows[i].style.display === 'none') continue;

                for (let j = 0; j < cols.length - 1; j++) { // Skip Action column
                   // Clean up text (remove excessive spaces/newlines)
                   let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, "").trim();
                   row.push('"' + data + '"'); // Quote fields
                }
                csv.push(row.join(","));
            }
            downloadCSV(csv.join("\n"), filename);
            showToast("Exportation réussie !", "success");
        }

        // Toast Notification Logic
        function showToast(message, type = 'success') {
            const toast = document.getElementById("toast");
            toast.className = "toast show " + type;
            toast.innerText = message;
            setTimeout(function(){ toast.className = toast.className.replace("show", ""); }, 3000);
        }

        function confirmDelete(e, url) {
            e.preventDefault();
            if(confirm("Êtes-vous sûr de vouloir supprimer ce collaborateur ? Cette action est irréversible.")) {
                window.location.href = url;
            }
        }

        // Check for URL parameters for notifications
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const msg = urlParams.get('msg');
            if(msg === 'deleted') {
                showToast("Collaborateur supprimé avec succès.", "danger");
            } else if (msg === 'created') {
                showToast("Nouveau collaborateur ajouté !", "success");
            }
            
            // Clean URL
            if(msg) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }
    </script>
</body>
</html>
