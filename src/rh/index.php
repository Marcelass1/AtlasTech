<?php
    // Enable Error Reporting for Debugging
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    session_start();
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header("Location: login.php");
        exit;
    }
    include 'db.php';

    // Calculate Stats with Error Handling
    $total_emp = 0;
    $total_depts = 0;
    $latest_hire = 'N/A';

    // Total Employees
    if ($res = $conn->query("SELECT count(*) as c FROM employees")) {
        $total_emp = $res->fetch_assoc()['c'] ?? 0;
    }

    // Total Departments
    if ($res = $conn->query("SELECT count(distinct department) as c FROM employees")) {
        $total_depts = $res->fetch_assoc()['c'] ?? 0;
    }

    // Latest Hire
    if ($res = $conn->query("SELECT hired_date FROM employees ORDER BY hired_date DESC LIMIT 1")) {
        if ($row = $res->fetch_assoc()) {
            $latest_hire = $row['hired_date'];
        }
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>AtlasHR | Tableau de Bord</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-body: #0f172a;
            --bg-card: #1e293b;
            --bg-glass: rgba(30, 41, 59, 0.7);
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            --secondary-gradient: linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%);
            --accent-gradient: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
            --sidebar-width: 280px;
            --border-color: rgba(255, 255, 255, 0.08);
            --shadow-card: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2);
            --shadow-glow: 0 0 20px rgba(99, 102, 241, 0.15);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-body);
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.08) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(139, 92, 246, 0.08) 0%, transparent 20%);
            margin: 0;
            display: flex;
            min-height: 100vh;
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: rgba(15, 23, 42, 0.95);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            padding: 2rem 1.5rem;
            z-index: 100;
            backdrop-filter: blur(10px);
            overflow-y: auto;
            box-sizing: border-box;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.6rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 3.5rem;
            letter-spacing: -0.02em;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 12px;
            font-weight: 500;
            margin-bottom: 0.8rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.03);
            color: var(--text-main);
            transform: translateX(4px);
        }

        .nav-link.active {
            background: rgba(99, 102, 241, 0.1);
            color: #8b5cf6;
            font-weight: 600;
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 24px;
            width: 3px;
            background: var(--primary-gradient);
            border-radius: 0 4px 4px 0;
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
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--primary-gradient);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            box-shadow: var(--shadow-glow);
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 3rem 4rem;
            max-width: 1600px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 3rem;
            animation: fadeInDown 0.6s ease-out;
        }

        h1 { 
            margin: 0; 
            font-size: 2.2rem; 
            font-weight: 700; 
            color: var(--text-main); 
            letter-spacing: -1px; 
        }
        p.subtitle { margin: 8px 0 0; color: var(--text-muted); font-size: 1.1rem; font-weight: 300; }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
            padding: 14px 28px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: var(--shadow-glow);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }
        .btn-primary:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 0 30px rgba(99, 102, 241, 0.4); 
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-main);
            border: 1px solid var(--border-color);
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
        }
        .btn-secondary:hover { background: rgba(255, 255, 255, 0.1); border-color: rgba(255, 255, 255, 0.2); }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
            animation: fadeInUp 0.6s ease-out 0.2s backwards;
        }

        .stat-card {
            background: var(--bg-card);
            padding: 2rem;
            border-radius: 20px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-card);
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover { transform: translateY(-5px); }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--secondary-gradient);
            opacity: 0.5;
        }

        .stat-icon {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 3rem;
            opacity: 0.05;
            color: white;
            transform: rotate(-15deg);
        }

        .stat-label { 
            color: var(--text-muted); 
            font-size: 0.85rem; 
            font-weight: 600; 
            text-transform: uppercase; 
            letter-spacing: 1px;
            margin-bottom: 1rem;
        }
        .stat-value { 
            font-size: 3rem; 
            font-weight: 700; 
            color: var(--text-main); 
            margin: 0; 
            letter-spacing: -1.5px;
            line-height: 1;
        }

        /* Toolkit Bar */
        .toolkit-bar {
            background: var(--bg-card);
            padding: 1.2rem;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            margin-bottom: 2rem;
            display: flex;
            gap: 1.5rem;
            align-items: center;
            box-shadow: var(--shadow-card);
            animation: fadeInUp 0.6s ease-out 0.3s backwards;
        }

        .search-box {
            position: relative;
            flex: 1;
        }
        .search-box i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }
        .search-input {
            width: 100%;
            padding: 14px 14px 14px 48px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 0.95rem;
            background: rgba(15, 23, 42, 0.5);
            color: var(--text-main);
            outline: none;
            transition: all 0.2s;
            box-sizing: border-box;
        }
        .search-input:focus { border-color: #6366f1; background: rgba(15, 23, 42, 0.8); }

        .filter-select {
            padding: 14px 20px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 0.95rem;
            outline: none;
            background: rgba(15, 23, 42, 0.5);
            color: var(--text-main);
            width: 220px;
            cursor: pointer;
        }

        /* Table */
        .table-container {
            background: var(--bg-card);
            border-radius: 20px;
            box-shadow: var(--shadow-card);
            border: 1px solid var(--border-color);
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out 0.4s backwards;
        }

        table { width: 100%; border-collapse: collapse; }
        
        th {
            background: rgba(15, 23, 42, 0.5);
            text-align: left;
            padding: 1.5rem 2rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--text-muted);
            letter-spacing: 1px;
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-main);
            font-size: 1rem;
            vertical-align: middle;
        }

        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(255, 255, 255, 0.02); }

        .employee-cell {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .avatar {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #475569 0%, #334155 100%);
            color: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .emp-info div { font-weight: 600; color: var(--text-main); letter-spacing: 0.3px; }
        .emp-info span { font-size: 0.85rem; color: var(--text-muted); }

        .badge {
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .badge-it { background: rgba(6, 182, 212, 0.15); color: #22d3ee; border: 1px solid rgba(34, 211, 238, 0.2); }
        .badge-rh { background: rgba(139, 92, 246, 0.15); color: #c084fc; border: 1px solid rgba(192, 132, 252, 0.2); }
        .badge-sales { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(52, 211, 153, 0.2); }
        .badge-default { background: rgba(148, 163, 184, 0.15); color: #cbd5e1; }

        .action-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
        }
        .action-btn.delete:hover { background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.3); color: #f87171; }

        /* Animations */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--bg-body); }
        ::-webkit-scrollbar-thumb { background: #475569; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #64748b; }

        /* Toast */
        .toast {
            visibility: hidden;
            min-width: 280px;
            background: #1e293b;
            color: #fff;
            text-align: center;
            border-radius: 12px;
            padding: 16px 20px;
            position: fixed;
            z-index: 1000;
            right: 40px;
            bottom: 40px;
            font-size: 0.95rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            border: 1px solid var(--border-color);
            transform: translateY(100px);
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), visibility 0.4s;
        }
        .toast.show { visibility: visible; transform: translateY(0); }
        .toast.success { border-left: 4px solid #10b981; }
        .toast.danger { border-left: 4px solid #ef4444; }

        .no-records td { text-align: center; padding: 4rem; color: var(--text-muted); font-style: italic; }

    </style>
</head>
<body>



    <nav class="sidebar">
        <div class="brand">
            <i class="fas fa-layer-group" style="font-size: 1.4rem;"></i> AtlasHR
        </div>

        <a href="index.php" class="nav-link active">
            <i class="fas fa-users"></i>
            Collaborateurs
        </a>
        <a href="analytics.php" class="nav-link">
            <i class="fas fa-chart-pie"></i>
            Analytiques
        </a>
        <a href="reports.php" class="nav-link">
            <i class="fas fa-file-alt"></i>
            Rapports
        </a>
        
        <div style="flex: 1;"></div>
        
        <a href="#" class="nav-link">
            <i class="fas fa-sliders-h"></i>
            Paramètres
        </a>

        <div class="user-block">
            <div style="display:flex; align-items:center; gap:12px;">
                <div class="user-avatar-sm">
                   <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                </div>
                <div>
                    <div style="font-weight: 600; font-size: 0.95rem; color: var(--text-main);"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">Administrateur</div>
                </div>
            </div>
            <a href="logout.php" class="action-btn delete" style="border:1px solid rgba(255,255,255,0.1);" title="Déconnexion"><i class="fas fa-power-off"></i></a>
        </div>
    </nav>

    <main class="main-content">
        <div class="header">
            <div>
                <h1>Tableau de Bord</h1>
                <p class="subtitle">Vue d'ensemble et gestion des effectifs.</p>
            </div>
            <div style="display:flex; gap:16px;">
                <a href="create.php" class="btn-primary">
                    <i class="fas fa-plus-circle"></i> Nouveau Collaborateur
                </a>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-users stat-icon"></i>
                <div class="stat-label">Effectif Total</div>
                <div class="stat-value"><?php echo $total_emp; ?></div>
            </div>
            <div class="stat-card">
                <i class="fas fa-building stat-icon"></i>
                <div class="stat-label">Départements</div>
                <div class="stat-value"><?php echo $total_depts; ?></div>
            </div>
            <div class="stat-card">
                <i class="fas fa-user-clock stat-icon"></i>
                <div class="stat-label">Dernier Recrutement</div>
                <div class="stat-value" style="font-size: 2rem; margin-top:0.5rem;">
                    <?php echo ($latest_hire !== 'N/A') ? date("d M", strtotime($latest_hire)) : 'Aucun'; ?>
                </div>
                <div style="font-size:0.9rem; color:var(--text-muted); margin-top:5px;">
                    <?php echo ($latest_hire !== 'N/A') ? date("Y", strtotime($latest_hire)) : ''; ?>
                </div>
            </div>
        </div>

        <div class="toolkit-bar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="Rechercher par nom, email ou poste...">
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
                <i class="fas fa-cloud-download-alt"></i> Exporter
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
                            if ($dept == 'IT' || $dept == 'Informatique') $badgeClass = 'badge-it';
                            if ($dept == 'RH' || $dept == 'HR') $badgeClass = 'badge-rh';
                            if ($dept == 'Commercial' || $dept == 'Sales' || $dept == 'Marketing') $badgeClass = 'badge-sales';
                            
                            // Initials
                            $initials = strtoupper(substr($row['first_name'],0,1) . substr($row['last_name'],0,1));

                            echo "<tr>
                                <td style='color:var(--text-muted); font-family:monospace;'>#" . sprintf('%03d', $row["id"]) . "</td>
                                <td>
                                    <div class='employee-cell'>
                                        <div class='avatar'>$initials</div>
                                        <div class='emp-info'>
                                            <div>" . htmlspecialchars($row["first_name"] . " " . $row["last_name"]) . "</div>
                                        </div>
                                    </div>
                                </td>
                                <td style='color:var(--text-muted);'>" . htmlspecialchars($row["email"]) . "</td>
                                <td>" . htmlspecialchars($row["position"]) . "</td>
                                <td><span class='badge $badgeClass'>" . htmlspecialchars($row["department"]) . "</span></td>
                                <td>
                                    <a href='delete.php?id=" . $row["id"] . "' class='action-btn delete' onclick='return confirmDelete(event, this.href)' title='Supprimer'>
                                        <i class='fas fa-trash-alt'></i>
                                    </a>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr class='no-records'><td colspan='6'>Aucun collaborateur trouvé dans la base de données.</td></tr>";
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
                let tdPoste = trs[i].getElementsByTagName('td')[3]; // Position
                let tdDept = trs[i].getElementsByTagName('td')[4]; // Dept

                if (tdName && tdDept) {
                    const txtName = tdName.textContent || tdName.innerText;
                    const txtEmail = tdEmail.textContent || tdEmail.innerText;
                    const txtPoste = tdPoste.textContent || tdPoste.innerText;
                    const txtDept = tdDept.textContent || tdDept.innerText;
                    
                    const matchesSearch = txtName.toLowerCase().indexOf(filterValue) > -1 || 
                                        txtEmail.toLowerCase().indexOf(filterValue) > -1 ||
                                        txtPoste.toLowerCase().indexOf(filterValue) > -1;
                                        
                    const matchesDept = deptValue === 'all' || txtDept.toLowerCase().includes(deptValue);

                    if (matchesSearch && matchesDept) {
                        trs[i].style.display = "";
                        // Add entrance animation when filtering
                        trs[i].style.animation = "fadeInUp 0.3s ease-out";
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
            // Use custom modal or styled confirm in real app, but native confirm is reliable
            if(confirm("Confirmer la suppression du collaborateur ?")) {
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
                showToast("Profil créé avec succès !", "success");
            }
            
            // Clean URL
            if(msg) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }
    </script>
</body>
</html>
