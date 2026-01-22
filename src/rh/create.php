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

    $sql = "INSERT INTO employees (first_name, last_name, email, position, department, salary)
            VALUES ('$first_name', '$last_name', '$email', '$position', '$department', '$salary')";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php?msg=created");
        exit();
    } else {
        $error = "Erreur: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>AtlasHR | Nouveau Collaborateur</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #eef2ff;
            --secondary: #64748b;
            --bg-body: #f8fafc;
            --text-dark: #0f172a;
            --border-color: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            color: var(--text-dark);
        }

        .card {
            background: white;
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 500px;
            border: 1px solid var(--border-color);
        }

        .header { text-align: center; margin-bottom: 2rem; }
        .header h2 { margin: 0; font-size: 1.5rem; font-weight: 700; color: var(--text-dark); }
        .header p { color: var(--secondary); margin-top: 5px; font-size: 0.95rem; }

        .form-group { margin-bottom: 1.25rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem; color: #334155; }
        
        input, select {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 0.95rem;
            font-family: inherit;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }
        input:focus, select:focus { outline: none; border-color: var(--primary); }

        .btn-primary {
            width: 100%;
            background: var(--primary);
            color: white;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.2s;
            margin-top: 1rem;
        }
        .btn-primary:hover { transform: translateY(-1px); }

        .btn-back {
            display: block;
            text-align: center;
            margin-top: 1.5rem;
            color: var(--secondary);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .btn-back:hover { color: var(--text-dark); }

        .row { display: flex; gap: 15px; }
        .col { flex: 1; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <div style="width:50px; height:50px; background:var(--primary-light); color:var(--primary); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1rem; font-size:1.2rem;">
                <i class="fas fa-user-plus"></i>
            </div>
            <h2>Nouveau Collaborateur</h2>
            <p>Remplissez les informations ci-dessous</p>
        </div>

        <?php if(isset($error)) echo "<div style='color:red; margin-bottom:1rem; text-align:center;'>$error</div>"; ?>

        <form method="post">
            <div class="row">
                <div class="col form-group">
                    <label>Prénom</label>
                    <input type="text" name="first_name" required placeholder="Jean">
                </div>
                <div class="col form-group">
                    <label>Nom</label>
                    <input type="text" name="last_name" required placeholder="Dupont">
                </div>
            </div>

            <div class="form-group">
                <label>Email Professionnel</label>
                <input type="email" name="email" required placeholder="jean.dupont@atlastech.com">
            </div>

            <div class="form-group">
                <label>Poste</label>
                <input type="text" name="position" required placeholder="Développeur Fullstack">
            </div>

            <div class="row">
                <div class="col form-group">
                    <label>Département</label>
                    <select name="department">
                        <option value="IT">IT</option>
                        <option value="RH">RH</option>
                        <option value="Commercial">Commercial</option>
                        <option value="Finance">Finance</option>
                        <option value="Marketing">Marketing</option>
                    </select>
                </div>
                <div class="col form-group">
                    <label>Salaire</label>
                    <input type="number" step="0.01" name="salary" required placeholder="45000">
                </div>
            </div>

            <button type="submit" class="btn-primary">Enregistrer</button>
            <a href="index.php" class="btn-back">Annuler</a>
        </form>
    </div>
</body>
</html>
