<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM employees WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // In a real app we would use password_verify($password, $row['password'])
        // For this demo we use direct comparison as seeded in init.sql
        if ($password === $row['password']) {
            // Allow both RH and IT departments
            if ($row['department'] === 'RH' || $row['department'] === 'IT') {
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $row['username'];
                $_SESSION['department'] = $row['department']; // Store dept for UI customization if needed
                header("Location: index.php");
                exit;
            } else {
                $error = "Accès Refusé. Réservé aux départements RH et IT.";
            }
        } else {
            $error = "Mot de passe incorrect.";
        }
    } else {
        $error = "Utilisateur inconnu.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>AtlasHR | Connexion Personnel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #0f172a;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: white;
            padding: 3rem;
            border-radius: 4px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .brand {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 1.5rem;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .input-group { margin-bottom: 1.5rem; }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: #334155;
            text-transform: uppercase;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            font-size: 0.95rem;
            box-sizing: border-box;
            background: #f8fafc;
        }
        input:focus { outline: 2px solid #2563eb; border-color: #2563eb; background: white; }

        button {
            width: 100%;
            padding: 12px;
            background: #0f172a;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
        }
        button:hover { background: #1e293b; }

        .error {
            background: #fef2f2;
            color: #ef4444;
            padding: 10px;
            border: 1px solid #fecaca;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            text-align: center;
        }

        .footer { text-align: center; margin-top: 2rem; color: #64748b; font-size: 0.8rem; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand">
            <i class="fas fa-layer-group" style="color:#2563eb;"></i> AtlasTech
        </div>
        
        <?php if($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>Identifiant</label>
                <input type="text" name="username" required placeholder="admin">
            </div>

            <div class="input-group">
                <label>Mot de Passe</label>
                <input type="password" name="password" required placeholder="••••••••">
            </div>

            <button type="submit">Se Connecter</button>
        </form>

        <div class="footer">
            &copy; 2026 AtlasTech Enterprise Solutions
        </div>
    </div>
</body>
</html>
