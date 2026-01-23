<?php 
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
include 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $position = $conn->real_escape_string($_POST['position']);
    $department = $conn->real_escape_string($_POST['department']);
    $salary = floatval($_POST['salary']);

    // Generate Username (firstname.lastname)
    $username = strtolower($first_name . "." . $last_name);
    // Ensure unique username (simple append if exists logic could be added here, but keeping it simple for now)
    
    // Default Password
    $password = "password123"; // In production, hash this!

    $sql = "INSERT INTO employees (username, password, first_name, last_name, email, position, department, salary)
            VALUES ('$username', '$password', '$first_name', '$last_name', '$email', '$position', '$department', '$salary')";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?msg=created");
        exit();
    } else {
        $error = "Erreur SQL: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>AtlasHR | Nouveau Collaborateur</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-body: #0f172a;
            --bg-card: #1e293b;
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            --border-color: rgba(255, 255, 255, 0.08);
            --shadow-glow: 0 0 20px rgba(99, 102, 241, 0.15);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-body);
            background-image: 
                radial-gradient(circle at 80% 10%, rgba(99, 102, 241, 0.08) 0%, transparent 20%),
                radial-gradient(circle at 20% 90%, rgba(139, 92, 246, 0.08) 0%, transparent 20%);
            display: flex;
            min-height: 100vh;
            margin: 0;
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* Main Content Logic */
        .main-content {
            margin-left: 280px; 
            flex: 1; 
            padding: 3rem 4rem;
            display: flex;
            justify-content: center;
        }

        .main-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 600px;
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--primary-gradient);
        }

        .header { text-align: center; margin-bottom: 2.5rem; padding-bottom: 0; }
        .header h2 { 
            margin: 0; 
            font-size: 1.8rem; 
            font-weight: 700; 
            color: var(--text-main); 
            letter-spacing: -0.5px;
        }
        .header p { color: var(--text-muted); margin-top: 8px; font-size: 1rem; font-weight: 300; }

        .form-group { margin-bottom: 1.5rem; }
        label { 
            display: block; 
            margin-bottom: 0.6rem; 
            font-weight: 600; 
            font-size: 0.85rem; 
            color: var(--text-muted); 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
        }
        
        input, select {
            width: 100%;
            padding: 14px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 0.95rem;
            font-family: inherit;
            box-sizing: border-box;
            background: rgba(15, 23, 42, 0.5);
            color: var(--text-main);
            outline: none;
            transition: all 0.2s;
        }
        input:focus, select:focus { 
            border-color: #6366f1; 
            background: rgba(15, 23, 42, 0.8);
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2); 
        }

        /* Custom Select Style workaround */
        select option {
            background: #1e293b;
            color: #fff;
            padding: 10px;
        }

        .btn-primary {
            width: 100%;
            background: var(--primary-gradient);
            color: white;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 1.5rem;
            font-size: 1rem;
            box-shadow: var(--shadow-glow);
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn-primary:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 0 25px rgba(99, 102, 241, 0.4); 
        }

        .btn-back {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.2s;
        }
        .btn-back:hover { color: #fff; }

        .row { display: flex; gap: 24px; }
        .col { flex: 1; }
    </style>
</head>
<body>
    <nav class="sidebar" style="width: 280px; background: rgba(15, 23, 42, 0.95); border-right: 1px solid rgba(255,255,255,0.08); display: flex; flex-direction: column; position: fixed; top: 0; left: 0; bottom: 0; padding: 2rem 1.5rem; backdrop-filter: blur(10px); box-sizing: border-box; z-index:100;">
        <div class="brand" style="margin-bottom: 3.5rem; font-size: 1.6rem; font-weight: 800; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display:flex; align-items:center; gap:12px;">
            <i class="fas fa-layer-group"></i> AtlasHR
        </div>

        <a href="index.php" style="display: flex; align-items: center; gap: 16px; padding: 16px; color: #94a3b8; text-decoration: none; border-radius: 12px; font-weight: 500; margin-bottom: 0.8rem; transition: all 0.3s ease;">
            <i class="fas fa-users"></i> Collaborateurs
        </a>
        <a href="analytics.php" style="display: flex; align-items: center; gap: 16px; padding: 16px; color: #94a3b8; text-decoration: none; border-radius: 12px; font-weight: 500; margin-bottom: 0.8rem; transition: all 0.3s ease;">
            <i class="fas fa-chart-pie"></i> Analytiques
        </a>
        <a href="reports.php" style="display: flex; align-items: center; gap: 16px; padding: 16px; color: #94a3b8; text-decoration: none; border-radius: 12px; font-weight: 500; margin-bottom: 0.8rem; transition: all 0.3s ease;">
            <i class="fas fa-file-alt"></i> Rapports
        </a>

        <div style="flex: 1;"></div>
        
        <div style="border-top: 1px solid rgba(255,255,255,0.08); padding-top: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                   <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                </div>
                <div>
                    <div style="font-weight: 600; font-size: 0.95rem; color: #f1f5f9;"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                    <div style="font-size: 0.8rem; color: #94a3b8;">Admin</div>
                </div>
            </div>
            <a href="logout.php" style="width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255,255,255,0.08); color: #94a3b8; text-decoration: none;"><i class="fas fa-power-off"></i></a>
        </div>
    </nav>

    <main class="main-content">
        <div class="main-container">
            <div class="card" style="margin: 0 auto;">
                <div class="header">
                    <h2>Nouveau Collaborateur</h2>
                    <p>Saisissez les informations du profil.</p>
                </div>

                <?php if(isset($error)) echo "<div style='color:#f87171; background:rgba(239, 68, 68, 0.1); padding:12px; border-radius:10px; margin-bottom:1.5rem; border:1px solid rgba(239, 68, 68, 0.2); text-align:center;'>$error</div>"; ?>

                <form method="post">
                    <div class="row">
                        <div class="col form-group">
                            <label>Prénom</label>
                            <input type="text" name="first_name" required placeholder="Ex: Jean">
                        </div>
                        <div class="col form-group">
                            <label>Nom</label>
                            <input type="text" name="last_name" required placeholder="Ex: Dupont">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email Professionnel</label>
                        <input type="email" name="email" required placeholder="jean.dupont@atlastech.com">
                    </div>

                    <div class="form-group">
                        <label>Poste Occupé</label>
                        <input type="text" name="position" required placeholder="Ex: Développeur Senior">
                    </div>

                    <div class="row">
                        <div class="col form-group">
                            <label>Département</label>
                            <select name="department">
                                <option value="IT">IT / Technique</option>
                                <option value="RH">Ressources Humaines</option>
                                <option value="Commercial">Commercial / Vente</option>
                                <option value="Finance">Finance / Compta</option>
                            </select>
                        </div>
                        <div class="col form-group">
                            <label>Salaire Mensuel (MAD)</label>
                            <input type="number" step="0.01" name="salary" required placeholder="8000">
                        </div>
                    </div>

                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Enregistrer le profil
                    </button>
                    <a href="index.php" class="btn-back">Annuler et retourner au tableau de bord</a>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
