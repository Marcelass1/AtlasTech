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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            color: #0f172a;
        }

        .card {
            background: white;
            padding: 2.5rem;
            border-radius: 6px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 550px;
            border: 1px solid #cbd5e1;
        }

        .header { text-align: center; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid #e2e8f0; }
        .header h2 { margin: 0; font-size: 1.5rem; font-weight: 700; color: #0f172a; }
        .header p { color: #64748b; margin-top: 5px; font-size: 0.95rem; }

        .form-group { margin-bottom: 1.25rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.85rem; color: #334155; text-transform: uppercase; letter-spacing: 0.02em; }
        
        input, select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            font-size: 0.95rem;
            font-family: inherit;
            box-sizing: border-box;
            background: #f8fafc;
            color: #1e293b;
        }
        input:focus, select:focus { outline: 2px solid #2563eb; border-color: #2563eb; background: white; }

        .btn-primary {
            width: 100%;
            background: #0f172a;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 1rem;
            font-size: 1rem;
        }
        .btn-primary:hover { background: #1e293b; }

        .btn-back {
            display: block;
            text-align: center;
            margin-top: 1.2rem;
            color: #64748b;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
        }
        .btn-back:hover { color: #0f172a; text-decoration: underline; }

        .row { display: flex; gap: 20px; }
        .col { flex: 1; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h2>Nouveau Collaborateur</h2>
            <p>Saisissez les informations contractuelles</p>
        </div>

        <?php if(isset($error)) echo "<div style='color:#ef4444; background:#fef2f2; padding:10px; border-radius:4px; margin-bottom:1rem; border:1px solid #fecaca; text-align:center;'>$error</div>"; ?>

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
                    <label>Salaire Mensuel (€)</label>
                    <input type="number" step="0.01" name="salary" required placeholder="3500">
                </div>
            </div>

            <button type="submit" class="btn-primary">
                <i class="fas fa-save" style="margin-right:8px;"></i> Enregistrer le profil
            </button>
            <a href="index.php" class="btn-back">Annuler et retourner au tableau de bord</a>
        </form>
    </div>
</body>
</html>
