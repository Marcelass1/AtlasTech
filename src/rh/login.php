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
                radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.1) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(139, 92, 246, 0.1) 0%, transparent 20%);
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-main);
        }

        .login-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            padding: 3rem;
            border-radius: 20px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-color);
            transition: transform 0.3s ease;
        }

        .login-card:hover { transform: translateY(-5px); }

        .brand {
            text-align: center;
            margin-bottom: 2.5rem;
            font-size: 1.8rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            letter-spacing: -0.02em;
        }

        .input-group { margin-bottom: 1.5rem; }
        
        label {
            display: block;
            margin-bottom: 0.6rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        input {
            width: 100%;
            padding: 14px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 0.95rem;
            box-sizing: border-box;
            background: rgba(15, 23, 42, 0.5);
            color: var(--text-main);
            outline: none;
            transition: all 0.3s;
        }
        input:focus { 
            border-color: #6366f1; 
            background: rgba(15, 23, 42, 0.8); 
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }

        button {
            width: 100%;
            padding: 14px;
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
            box-shadow: var(--shadow-glow);
        }
        button:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 0 25px rgba(99, 102, 241, 0.4); 
        }

        .error {
            background: rgba(239, 68, 68, 0.1);
            color: #f87171;
            padding: 12px;
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            text-align: center;
        }

        .footer { 
            text-align: center; 
            margin-top: 2rem; 
            color: var(--text-muted); 
            font-size: 0.85rem; 
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand">
            <i class="fas fa-layer-group" style="background: var(--primary-gradient); -webkit-background-clip: tekst; -webkit-text-fill-color: transparent;"></i> AtlasHR
        </div>
        
        <?php if(isset($error) && $error): ?>
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

            <button type="submit">Se Connecter <i class="fas fa-arrow-right" style="margin-left:8px; font-size:0.9em;"></i></button>
        </form>

        <div class="footer">
            &copy; 2026 AtlasTech Enterprise Solutions
        </div>
    </div>
</body>
</html>
