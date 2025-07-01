<?php
require_once __DIR__ . '/includes/auth_middleware.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

// Verificar autenticación
AuthMiddleware::requireAuth();
AuthMiddleware::checkSessionExpiration();

$current_page = 'profile';
$page_title = 'Mi Perfil';

$success = '';
$error = '';

// Obtener datos del usuario y terapeuta
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    $stmt = $pdo->prepare("SELECT * FROM therapist WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $therapist = $stmt->fetch();
} catch (PDOException $e) {
    $error = 'Error al cargar los datos del perfil.';
}

if ($_POST) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $therapist_name = trim($_POST['therapist_name'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $license_number = trim($_POST['license_number'] ?? '');
    
    if (empty($name) || empty($email)) {
        $error = 'Por favor, complete todos los campos obligatorios.';
    } else {
        try {
            $pdo->beginTransaction();
            
            // Actualizar usuario
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $email, $_SESSION['user_id']]);
            
            // Actualizar o insertar datos del terapeuta
            if ($therapist) {
                $stmt = $pdo->prepare("
                    UPDATE therapist SET name = ?, title = ?, license_number = ? WHERE user_id = ?
                ");
                $stmt->execute([$therapist_name, $title, $license_number, $_SESSION['user_id']]);
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO therapist (user_id, name, title, license_number) VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$_SESSION['user_id'], $therapist_name, $title, $license_number]);
            }
            
            $pdo->commit();
            $_SESSION['user_name'] = $name;
            $success = 'Perfil actualizado exitosamente.';
            
            // Recargar datos
            $user['full_name'] = $name;
            $user['email'] = $email;
            if ($therapist) {
                $therapist['name'] = $therapist_name;
                $therapist['title'] = $title;
                $therapist['license_number'] = $license_number;
            }
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = 'Error al actualizar el perfil.';
        }
    }
}

include __DIR__ . '/templates/header.php';
?>

<?= breadcrumb([
    ['title' => 'Dashboard', 'url' => url('index.php')],
    ['title' => 'Mi Perfil']
]) ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Mi Perfil</h1>
</div>

<?php if ($success): ?>
    <div class="alert alert-success">
        <i class="bi bi-check-circle"></i> <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Información Personal</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nombre Completo *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    <h6>Información Profesional</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="therapist_name" class="form-label">Nombre Profesional</label>
                                <input type="text" class="form-control" id="therapist_name" name="therapist_name" 
                                       value="<?= htmlspecialchars($therapist['name'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Título Profesional</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?= htmlspecialchars($therapist['title'] ?? '') ?>" 
                                       placeholder="Ej: Terapeuta Ocupacional">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="license_number" class="form-label">Número de Licencia</label>
                        <input type="text" class="form-control" id="license_number" name="license_number" 
                               value="<?= htmlspecialchars($therapist['license_number'] ?? '') ?>">
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Estadísticas</h5>
            </div>
            <div class="card-body">
                <?php
                try {
                    $stmt = $pdo->prepare("SELECT COUNT(*) as total_patients FROM patients WHERE therapist_id = (SELECT id FROM therapist WHERE user_id = ?)");
                    $stmt->execute([$_SESSION['user_id']]);
                    $total_patients = $stmt->fetchColumn();
                    
                    $stmt = $pdo->prepare("SELECT COUNT(*) as total_reports FROM reports WHERE therapist_id = (SELECT id FROM therapist WHERE user_id = ?)");
                    $stmt->execute([$_SESSION['user_id']]);
                    $total_reports = $stmt->fetchColumn();
                } catch (PDOException $e) {
                    $total_patients = 0;
                    $total_reports = 0;
                }
                ?>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Total de Pacientes:</span>
                    <span class="badge bg-primary"><?= $total_patients ?></span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Total de Informes:</span>
                    <span class="badge bg-success"><?= $total_reports ?></span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <span>Miembro desde:</span>
                    <span class="text-muted"><?= date('M Y', strtotime($user['created_at'])) ?></span>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Acciones Rápidas</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= url('patients/new.php') ?>" class="btn btn-outline-primary">
                        <i class="bi bi-person-plus"></i> Nuevo Paciente
                    </a>
                    <a href="<?= url('reports/new.php') ?>" class="btn btn-outline-success">
                        <i class="bi bi-file-plus"></i> Nuevo Informe
                    </a>
                    <a href="<?= url('reports/index.php') ?>" class="btn btn-outline-warning">
                        <i class="bi bi-file-text"></i> Ver Informes
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/templates/footer.php'; ?>