<?php
session_start();

// Hardcoded credentials for the prototype
$VALID_USER = "admin";
$VALID_PASS = "admin123";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === $VALID_USER && $password === $VALID_PASS) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid credentials. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AtlasHR - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #F3F4F6;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }
        .login-card {
            background: white;
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            width: 100%;
            max-width: 400px;
        }
        .brand {
            text-align: center;
            margin-bottom: 2rem;
            color: #4F46E5;
            font-size: 1.5rem;
            font-weight: 700;
        }
        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid #D1D5DB;
        }
        .form-control:focus {
            border-color: #4F46E5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        .btn-login {
            background-color: #4F46E5;
            color: white;
            font-weight: 600;
            padding: 0.75rem;
            border-radius: 8px;
            width: 100%;
            margin-top: 1rem;
            border: none;
            transition: background 0.2s;
        }
        .btn-login:hover {
            background-color: #4338CA;
        }
        .error-msg {
            color: #EF4444;
            font-size: 0.875rem;
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="brand">
            <i class="fas fa-layer-group"></i> AtlasHR
        </div>
        
        <?php if($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">USERNAME</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-user text-muted"></i></span>
                    <input type="text" name="username" class="form-control border-start-0" placeholder="Enter username" required>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label text-muted small fw-bold">PASSWORD</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-lock text-muted"></i></span>
                    <input type="password" name="password" class="form-control border-start-0" placeholder="Enter password" required>
                </div>
            </div>

            <button type="submit" class="btn-login">Sign In</button>
            
            <div class="text-center mt-4">
                <small class="text-muted">Restricted System &copy; 2026 AtlasTech</small>
            </div>
        </form>
    </div>

</body>
</html>
