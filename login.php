<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/CsrfProtection.php';

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['user_id'])) {
    redirect('dashboard');
}

$error = '';
$expired = isset($_GET['expired']) ? true : false;

if ($_POST) {
    // Validar token CSRF
    CsrfProtection::requireValidToken();
    
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validar con Validator
    $validator = new Validator($_POST);
    $validator->required('email', 'El email es requerido')
              ->email('email', 'El email no es válido')
              ->required('password', 'La contraseña es requerida');
    
    if ($validator->hasErrors()) {
        $error = implode('<br>', $validator->getErrors());
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, full_name, email, password, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['last_activity'] = time();
                
                redirect('dashboard');
            } else {
                $error = 'Credenciales incorrectas.';
            }
        } catch (PDOException $e) {
            Logger::error('Error de login: ' . $e->getMessage());
            $error = 'Error de conexión. Intente nuevamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Gestor de Informes TO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm mt-5">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <i class="bi bi-clipboard-pulse text-primary" style="font-size: 3rem;"></i>
                            <h3 class="mt-2">Gestor de Informes TO</h3>
                            <p class="text-muted">Iniciar Sesión</p>
                        </div>
                        
                        <?php if ($expired): ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-clock"></i> Su sesión ha expirado. Por favor, inicie sesión nuevamente.
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <?= CsrfProtection::getTokenField() ?>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                            </button>
                        </form>
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                ¿No tienes cuenta? 
                                <a href="register.php" class="text-decoration-none">Crear Cuenta</a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>