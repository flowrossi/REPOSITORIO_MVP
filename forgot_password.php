<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

$success = '';
$error = '';

if ($_POST) {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Por favor, ingrese su email.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Por favor, ingrese un email válido.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND active = 1");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                // Aquí implementarías el envío de email
                // Por ahora solo mostramos un mensaje
                $success = 'Si el email existe en nuestro sistema, recibirá instrucciones para restablecer su contraseña.';
            } else {
                // Por seguridad, mostramos el mismo mensaje
                $success = 'Si el email existe en nuestro sistema, recibirá instrucciones para restablecer su contraseña.';
            }
        } catch (PDOException $e) {
            $error = 'Error del sistema. Intente nuevamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Gestor de Informes TO</title>
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
                            <i class="bi bi-key text-warning" style="font-size: 3rem;"></i>
                            <h3 class="mt-2">Recuperar Contraseña</h3>
                            <p class="text-muted">Ingrese su email para recibir instrucciones</p>
                        </div>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle"></i> <?= htmlspecialchars($success) ?>
                                <div class="mt-2">
                                    <a href="/login.php" class="btn btn-sm btn-primary">Volver al Login</a>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!$success): ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                            </div>
                            
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="bi bi-envelope"></i> Enviar Instrucciones
                            </button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <a href="/login.php" class="text-decoration-none">Volver al Login</a> |
                                <a href="/register.php" class="text-decoration-none">Crear Cuenta</a>
                            </small>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>