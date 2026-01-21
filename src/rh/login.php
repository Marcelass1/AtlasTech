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
            if ($row['department'] === 'RH') {
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $row['username'];
                header("Location: index.php");
                exit;
            } else {
                $error = "Accès Refusé. Réservé au département RH.";
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
    <title>AtlasHR | Connexion Sécurisée</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4338ca;
            --bg-gradient: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-gradient);
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Animated Background Shapes */
        .shape {
            position: absolute;
            background: rgba(99, 102, 241, 0.4);
            filter: blur(80px);
            border-radius: 50%;
            z-index: 0;
            animation: float 20s infinite alternate;
        }
        .shape-1 { width: 400px; height: 400px; top: -100px; left: -100px; background: rgba(147, 51, 234, 0.3); }
        .shape-2 { width: 300px; height: 300px; bottom: -50px; right: -50px; background: rgba(59, 130, 246, 0.3); }

        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(50px, 50px) rotate(20deg); }
        }

        .login-container {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            padding: 3rem;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 420px;
            z-index: 1;
            text-align: center;
        }

        .brand-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
        }

        h2 { margin: 0 0 0.5rem; color: #1e293b; font-weight: 700; }
        p.subtitle { color: #64748b; font-size: 0.95rem; margin-bottom: 2rem; }

        .input-group {
            position: relative;
            margin-bottom: 1.25rem;
            text-align: left;
        }
        
        .input-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #475569;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .input-wrapper { position: relative; }
        
        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
            transition: color 0.3s;
        }

        input {
            width: 100%;
            padding: 12px 12px 12px 48px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            font-family: inherit;
            color: #1e293b;
            transition: all 0.3s;
            box-sizing: border-box;
            background: #f8fafc;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        input:focus + i { color: var(--primary); }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 1rem;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.4);
        }

        .error-banner {
            background: #fee2e2;
            color: #ef4444;
            padding: 10px;
            border-radius: 8px;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            border: 1px solid #fca5a5;
        }

        .footer { margin-top: 2rem; color: #94a3b8; font-size: 0.85rem; }
    </style>
</head>
<body>
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>

    <div class="login-container">
        <div class="brand-icon">
            <i class="fas fa-fingerprint"></i>
        </div>
        <h2>Bienvenue</h2>
        <p class="subtitle">Connectez-vous au portail RH AtlasTech</p>

        <?php if($error): ?>
            <div class="error-banner">
                <i class="fas fa-exclamation-circle me-1"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>Nom d'utilisateur</label>
                <div class="input-wrapper">
                    <input type="text" name="username" placeholder="ex: admin" required>
                    <i class="far fa-user"></i>
                </div>
            </div>

            <div class="input-group">
                <label>Mot de passe</label>
                <div class="input-wrapper">
                    <input type="password" name="password" placeholder="••••••••" required>
                    <i class="fas fa-lock"></i>
                </div>
            </div>

            <button type="submit">Se Connecter <i class="fas fa-arrow-right" style="margin-left: 8px;"></i></button>
        </form>

        <div class="footer">
            Système Sécurisé • v2.4.0
        </div>
    </div>
</body>
</html>
