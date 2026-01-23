<?php
// Enable Error Reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
include 'db.php';

// --- Data Aggregation for Charts ---

// 1. Department Distribution
$dept_data = [];
$dept_labels = [];
$dept_res = $conn->query("SELECT department, COUNT(*) as count FROM employees GROUP BY department");
while($row = $dept_res->fetch_assoc()) {
    $dept_labels[] = $row['department'];
    $dept_data[] = $row['count'];
}

// 2. Average Salary by Department
$salary_data = [];
$salary_labels = [];
$salary_res = $conn->query("SELECT department, AVG(salary) as avg_sal FROM employees GROUP BY department");
while($row = $salary_res->fetch_assoc()) {
    $salary_labels[] = $row['department'];
    $salary_data[] = round($row['avg_sal'], 2);
}

// 3. Key Metrics
$avg_salary_total = 0;
$avg_res = $conn->query("SELECT AVG(salary) as avg_all FROM employees");
if($r = $avg_res->fetch_assoc()) {
    $avg_salary_total = round($r['avg_all'], 2);
}

// Encode for JS
$json_dept_labels = json_encode($dept_labels);
$json_dept_data = json_encode($dept_data);
$json_salary_labels = json_encode($salary_labels);
$json_salary_data = json_encode($salary_data);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>AtlasHR | Analytiques</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --bg-body: #0f172a;
            --bg-card: #1e293b;
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            --secondary-gradient: linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%);
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

        /* Sidebar (Copied from index.php for consistency - effectively a partial) */
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

        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
            animation: fadeInUp 0.6s ease-out 0.2s backwards;
        }

        .chart-card {
            background: var(--bg-card);
            padding: 2rem;
            border-radius: 20px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-card);
            position: relative;
        }

        .card-title {
            margin: 0 0 1.5rem 0;
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .metric-card {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.7) 0%, rgba(15, 23, 42, 0.8) 100%);
            padding: 2rem;
            border-radius: 20px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-card);
            margin-bottom: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        
        .metric-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--secondary-gradient);
            opacity: 0.7;
        }

        .metric-val { font-size: 2.5rem; font-weight: 800; color: var(--text-main); margin-top: 5px; }
        .metric-label { font-size: 0.9rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; }

        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

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
        <a href="analytics.php" class="nav-link active">
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
            <a href="logout.php" class="action-btn" title="Déconnexion"><i class="fas fa-power-off"></i></a>
        </div>
    </nav>

    <main class="main-content">
        <div class="header">
            <h1>Analytiques & Statistiques</h1>
            <p class="subtitle">Analyse en temps réel de votre capital humain.</p>
        </div>

        <div class="dashboard-grid">
            <!-- Left Golumn: Main Charts -->
            <div style="display:flex; flex-direction:column; gap:2rem;">
                <div class="chart-card">
                    <h3 class="card-title">Salaire Moyen par Département</h3>
                    <canvas id="salaryChart" style="max-height: 300px;"></canvas>
                </div>
                
                 <div class="chart-card">
                    <h3 class="card-title">Évolution des Recrutements (Mock)</h3>
                    <canvas id="timelineChart" style="max-height: 250px;"></canvas>
                </div>
            </div>

            <!-- Right Column: Distribution & Metrics -->
            <div style="display:flex; flex-direction:column; gap:2rem;">
                <div class="chart-card">
                    <h3 class="card-title">Répartition par Département</h3>
                    <canvas id="deptChart" style="max-height: 300px;"></canvas>
                </div>

                <div class="metric-card">
                    <div class="metric-label">Masse Salariale Moyenne</div>
                    <div class="metric-val"><?php echo number_format($avg_salary_total, 2, ',', ' '); ?> €</div>
                </div>
                
                 <div class="metric-card">
                    <div class="metric-label">Taux de Rétention (Est.)</div>
                    <div class="metric-val" style="color:#10b981;">98.5%</div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // ChartJS Globals
        Chart.defaults.color = '#94a3b8';
        Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.05)';

        // 1. Department Distribution (Doughnut)
        const ctxDept = document.getElementById('deptChart').getContext('2d');
        new Chart(ctxDept, {
            type: 'doughnut',
            data: {
                labels: <?php echo $json_dept_labels; ?>,
                datasets: [{
                    data: <?php echo $json_dept_data; ?>,
                    backgroundColor: [
                        '#6366f1', '#3b82f6', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6'
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
                },
                cutout: '70%',
                responsive: true
            }
        });

        // 2. Salary Bar Chart
        const ctxSalary = document.getElementById('salaryChart').getContext('2d');
        new Chart(ctxSalary, {
            type: 'bar',
            data: {
                labels: <?php echo $json_salary_labels; ?>,
                datasets: [{
                    label: 'Salaire Moyen (€)',
                    data: <?php echo $json_salary_data; ?>,
                    backgroundColor: '#8b5cf6',
                    borderRadius: 6
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } },
                    x: { grid: { display: false } }
                },
                plugins: { legend: { display: false } },
                responsive: true
            }
        });

        // 3. Timeline (Mock Data)
        const ctxTime = document.getElementById('timelineChart').getContext('2d');
        new Chart(ctxTime, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                datasets: [{
                    label: 'Nouveaux Recrutements',
                    data: [1, 2, 0, 1, 3, 2],
                    borderColor: '#06b6d4',
                    backgroundColor: 'rgba(6, 182, 212, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                scales: {
                    y: { display: false },
                    x: { grid: { display: false } }
                },
                plugins: { legend: { display: false } },
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>
</body>
</html>
