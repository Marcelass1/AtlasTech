<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
include 'db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>AtlasHR | Rapports</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        :root {
            --bg-body: #0f172a;
            --bg-card: #1e293b;
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            --sidebar-width: 280px;
            --border-color: rgba(255, 255, 255, 0.08);
            --shadow-card: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2);
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

        /* Sidebar Partial */
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
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.15);
        }
        
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
        .action-btn:hover { background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.3); color: #f87171; }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 3rem 4rem;
            max-width: 1600px;
        }

        .header {
            margin-bottom: 3rem;
            animation: fadeInDown 0.6s ease-out;
        }
        
        h1 { margin: 0; font-size: 2.2rem; font-weight: 700; color: var(--text-main); letter-spacing: -1px; }
        p.subtitle { margin: 8px 0 0; color: var(--text-muted); font-size: 1.1rem; font-weight: 300; }

        /* Reports Grid */
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
            animation: fadeInUp 0.6s ease-out 0.2s backwards;
        }

        .report-card {
            background: var(--bg-card);
            padding: 2rem;
            border-radius: 20px;
            border: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            gap: 1rem;
            transition: transform 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .report-card:hover { transform: translateY(-5px); border-color: #6366f1; }

        .report-icon {
            font-size: 2.5rem;
            color: #8b5cf6;
            background: rgba(139, 92, 246, 0.1);
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .report-title { font-size: 1.1rem; font-weight: 700; margin: 0; }
        .report-desc { color: var(--text-muted); font-size: 0.9rem; line-height: 1.5; flex: 1; }

        .btn-download {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            padding: 12px;
            border-radius: 10px;
            text-align: center;
            text-decoration: none;
            font-weight: 600;
            margin-top: 1rem;
            transition: opacity 0.2s;
            border: none;
            cursor: pointer;
            width: 100%;
        }
        .btn-download:hover { opacity: 0.9; }

        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* Recent Activity Table (Mock) */
        .activity-section { margin-top: 4rem; animation: fadeInUp 0.6s ease-out 0.4s backwards; }
        .section-title { font-size: 1.2rem; font-weight: 700; margin-bottom: 1.5rem; display:flex; align-items:center; gap:10px; }
        
        .table-container {
            background: var(--bg-card);
            border-radius: 20px;
            border: 1px solid var(--border-color);
            overflow: hidden;
        }
        
         table { width: 100%; border-collapse: collapse; }
         th { background: rgba(15, 23, 42, 0.5); padding: 1.2rem; text-align: left; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; }
         td { padding: 1.2rem; border-bottom: 1px solid var(--border-color); color: var(--text-main); font-size: 0.95rem; }
    </style>
</head>
<body>

    <nav class="sidebar">
        <div class="brand">
            <i class="fas fa-layer-group" style="font-size: 1.4rem;"></i> AtlasHR
        </div>

        <a href="index.php" class="nav-link">
            <i class="fas fa-users"></i>
            Collaborateurs
        </a>
        <a href="analytics.php" class="nav-link">
            <i class="fas fa-chart-pie"></i>
            Analytiques
        </a>
        <a href="reports.php" class="nav-link active">
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
            <a href="logout.php" class="action-btn" title="Déconnexion"><i class="fas fa-power-off"></i></a>
        </div>
    </nav>

    <main class="main-content">
        <div class="header">
            <h1>Centre de Rapports</h1>
            <p class="subtitle">Générez et téléchargez des rapports officiels.</p>
        </div>

        <div class="reports-grid">
            <!-- Report 1 -->
            <div class="report-card">
                <div class="report-icon"><i class="fas fa-table"></i></div>
                <div>
                    <h3 class="report-title">Liste des Employés</h3>
                    <p class="report-desc">Export complet de tous les collaborateurs (Nom, Poste, Email, Département) au format CSV.</p>
                </div>
                <button onclick="exportCSV()" class="btn-download">
                    <i class="fas fa-download"></i> Télécharger CSV
                </button>
            </div>

            <!-- Report 2 -->
            <div class="report-card">
                <div class="report-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                <div>
                    <h3 class="report-title">Revue de Masse Salariale</h3>
                    <p class="report-desc">Aperçu financier par département et grille des salaires.</p>
                </div>
                <button onclick="printReport('pay')" class="btn-download" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="fas fa-print"></i> Imprimer PDF
                </button>
            </div>
            
             <!-- Report 3 -->
            <div class="report-card">
                <div class="report-icon"><i class="fas fa-user-check"></i></div>
                <div>
                    <h3 class="report-title">Audit des Effectifs</h3>
                    <p class="report-desc">Rapport détaillé sur la répartition des postes et l'ancienneté.</p>
                </div>
                <button class="btn-download" style="background: rgba(255,255,255,0.05); border:1px solid var(--border-color);" disabled>
                    <i class="fas fa-lock"></i> Bientôt disponible
                </button>
            </div>
        </div>

        <div class="activity-section">
            <div class="section-title"><i class="fas fa-history" style="color:#94a3b8;"></i> Historique Récent</div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Rapport</th>
                            <th>Date de génération</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><i class="fas fa-file-csv" style="color:#10b981; margin-right:8px;"></i> Export Global_2026.csv</td>
                            <td>Aujourd'hui, 10:42</td>
                            <td><span style="color:#10b981; font-size:0.8rem; font-weight:600;">COMPLÉTÉ</span></td>
                            <td><a href="#" style="color:#6366f1;">Re-télécharger</a></td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-file-pdf" style="color:#ef4444; margin-right:8px;"></i> Paie_Janvier.pdf</td>
                            <td>Hier, 16:30</td>
                            <td><span style="color:#10b981; font-size:0.8rem; font-weight:600;">COMPLÉTÉ</span></td>
                            <td><a href="#" style="color:#6366f1;">Re-télécharger</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Hidden Table for Export Logic -->
    <table id="hiddenTable" style="display:none;">
        <?php
        echo "<thead><tr><th>ID</th><th>Nom</th><th>Email</th><th>Poste</th><th>Dept</th><th>Salaire</th></tr></thead><tbody>";
        $sql = "SELECT id, first_name, last_name, email, position, department, salary FROM employees";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['first_name'] . " " . $row['last_name'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>" . $row['position'] . "</td>";
            echo "<td>" . $row['department'] . "</td>";
            echo "<td>" . $row['salary'] . "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        ?>
    </table>

    <script>
        function exportCSV() {
            let csv = [];
            let rows = document.querySelectorAll("#hiddenTable tr");
            
            for (let i = 0; i < rows.length; i++) {
                let row = [], cols = rows[i].querySelectorAll("td, th");
                for (let j = 0; j < cols.length; j++) 
                    row.push('"' + cols[j].innerText + '"');
                csv.push(row.join(","));
            }

            let csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
            let downloadLink = document.createElement("a");
            downloadLink.download = "Rapport_AtlasHR_" + new Date().toISOString().slice(0,10) + ".csv";
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
            downloadLink.click();
        }

        function printReport(type) {
           window.print();
        }
    </script>
</body>
</html>
