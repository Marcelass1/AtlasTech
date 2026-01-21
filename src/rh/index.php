<?php
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
    <title>AtlasHR | Tableau de Bord</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-light: #e0e7ff;
            --secondary: #64748b;
            --bg-body: #f1f5f9;
            --bg-card: #ffffff;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --sidebar-width: 280px;
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
            border-right: 1px solid #e2e8f0;
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
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 3rem;
        }

        .brand i { font-size: 1.8rem; }

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
            border-top: 1px solid #e2e8f0;
            padding-top: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: 3rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 2.5rem;
        }

        h1 { margin: 0; font-size: 1.8rem; font-weight: 700; color: #0f172a; }
        p.subtitle { margin: 5px 0 0; color: var(--text-light); font-size: 0.95rem; }

        .btn-add {
            background: var(--primary);
            color: white;
            padding: 12px 24px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
            transition: transform 0.2s;
        }
        .btn-add:hover { transform: translateY(-2px); }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .stat-meta { color: var(--text-light); font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-value { font-size: 2.2rem; font-weight: 700; color: #0f172a; margin: 0.5rem 0 0; }
        
        /* Table */
        .table-wrapper {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        table { width: 100%; border-collapse: collapse; }
        
        th {
            background: #f8fafc;
            text-align: left;
            padding: 1.25rem 1.5rem;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: 0.95rem;
            font-weight: 500;
        }

        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f8fafc; }

        .badge {
            background: #e0e7ff;
            color: #4338ca;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fee2e2;
            color: #ef4444;
            text-decoration: none;
            transition: all 0.2s;
        }
        .action-btn:hover { background: #ef4444; color: white; }

    </style>
</head>
<body>

    <nav class="sidebar">
        <div class="brand">
            <i class="fas fa-cube"></i> AtlasHR
        </div>

        <a href="#" class="nav-link active">
            <i class="fas fa-users"></i>
            Collaborateurs
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-chart-line"></i>
            Analytiques
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-file-invoice"></i>
            Rapports
        </a>
        <a href="#" class="nav-link" style="margin-top:auto;">
            <i class="fas fa-cog"></i>
            Paramètres
        </a>

        <div class="user-block">
            <div class="user-info">
                <div class="user-avatar">
                   <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                </div>
                <div style="font-size: 0.9rem; font-weight: 600;">
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                    <div style="font-size: 0.75rem; color: #94a3b8; font-weight:400;">Administrateur</div>
                </div>
            </div>
            <a href="logout.php" style="color: #ef4444; font-size:1.1rem;"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </nav>

    <main class="main-content">
        <div class="header">
            <div>
                <h1>Tableau de Bord</h1>
                <p class="subtitle">Aperçu global de l'effectif et des départements.</p>
            </div>
            <a href="create.php" class="btn-add">
                <i class="fas fa-plus"></i> Nouveau Collaborateur
            </a>
        </div>

        <?php
            // Calculate Stats
            $total_emp = $conn->query("SELECT count(*) as c FROM employees")->fetch_assoc()['c'];
            $total_depts = $conn->query("SELECT count(distinct department) as c FROM employees")->fetch_assoc()['c'];
            $latest_hire = $conn->query("SELECT hired_date FROM employees ORDER BY hired_date DESC LIMIT 1")->fetch_assoc()['hired_date'] ?? 'N/A';
        ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-meta">Effectif Total</div>
                <div class="stat-value"><?php echo $total_emp; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-meta">Départements</div>
                <div class="stat-value"><?php echo $total_depts; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-meta">Dernier Recrutement</div>
                <div class="stat-value" style="font-size:1.5rem; margin-top:1.2rem;"><?php echo $latest_hire; ?></div>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th width="50">#ID</th>
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
                            echo "<tr>
                                <td style='color:#94a3b8;'>#" . $row["id"] . "</td>
                                <td>
                                    <div style='font-weight:700;'>" . $row["first_name"] . " " . $row["last_name"] . "</div>
                                </td>
                                <td>" . $row["email"] . "</td>
                                <td>" . $row["position"] . "</td>
                                <td><span class='badge'>" . $row["department"] . "</span></td>
                                <td>
                                    <a href='delete.php?id=" . $row["id"] . "' class='action-btn' onclick='return confirm(\"Supprimer cet employé ?\")'>
                                        <i class='fas fa-trash'></i>
                                    </a>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center; padding:2rem; color:#94a3b8;'>Aucun collaborateur trouvé.</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
