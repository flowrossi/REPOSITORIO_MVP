<?php
require_once __DIR__ . '/includes/auth_middleware.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

// Verificar autenticación
AuthMiddleware::requireAuth();
AuthMiddleware::checkSessionExpiration();

$current_page = 'dashboard';
$page_title = 'Dashboard - Gestor TO';

// Obtener estadísticas
try {
    // Total de pacientes
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM patients WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $total_patients = $stmt->fetchColumn();
    
    // Total de informes
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reports WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $total_reports = $stmt->fetchColumn();
    
    // Informes recientes
    $stmt = $pdo->prepare("
        SELECT r.*, p.name as patient_name 
        FROM reports r 
        JOIN patients p ON r.patient_id = p.id 
        WHERE r.user_id = ? 
        ORDER BY r.created_at DESC 
        LIMIT 5
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $recent_reports = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $total_patients = 0;
    $total_reports = 0;
    $recent_reports = [];
}

include __DIR__ . '/templates/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= $total_patients ?></h4>
                        <p class="card-text">Pacientes</p>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-people" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="<?= url('patients') ?>" class="text-white text-decoration-none">
                    Ver todos <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><?= $total_reports ?></h4>
                        <p class="card-text">Informes</p>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-file-text" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="<?= url('reports') ?>" class="text-white text-decoration-none">
                    Ver todos <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><i class="bi bi-plus-circle"></i></h4>
                        <p class="card-text">Nuevo Paciente</p>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="<?= url('patients/new') ?>" class="btn btn-primary">
                    <i class="bi bi-person-plus"></i> Nuevo Paciente
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4><i class="bi bi-file-plus"></i></h4>
                        <p class="card-text">Nuevo Informe</p>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="<?= url('reports/new.php') ?>" class="text-white text-decoration-none">
                    Crear <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($recent_reports)): ?>
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informes Recientes</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Paciente</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_reports as $report): ?>
                                <tr>
                                    <td><?= htmlspecialchars($report['title']) ?></td>
                                    <td><?= htmlspecialchars($report['patient_name']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($report['created_at'])) ?></td>
                                    <td>
                                        <a href="<?= url('reports/view.php', ['id' => $report['id']]) ?>" class="btn btn-sm btn-outline-primary">
                                            Ver
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/templates/footer.php'; ?>