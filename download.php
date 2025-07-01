<?php
require_once __DIR__ . '/includes/auth_middleware.php';
require_once __DIR__ . '/includes/db.php';

// Verificar parámetros
if (!isset($_GET['type']) || !isset($_GET['id'])) {
    header('HTTP/1.1 400 Bad Request');
    exit('Parámetros inválidos');
}

$type = $_GET['type'];
$reportId = (int)$_GET['id'];

// Validar tipo
if (!in_array($type, ['pdf', 'docx'])) {
    header('HTTP/1.1 400 Bad Request');
    exit('Tipo de archivo inválido');
}

try {
    // Obtener información del informe
    $stmt = $pdo->prepare("SELECT * FROM reports WHERE id = ? AND therapist_id = (SELECT id FROM therapist WHERE user_id = ?)");
    $stmt->execute([$reportId, $_SESSION['user_id']]);
    $report = $stmt->fetch();
    
    if (!$report) {
        header('HTTP/1.1 404 Not Found');
        exit('Informe no encontrado');
    }
    
    // Determinar ruta del archivo
    $filePath = $type === 'pdf' ? $report['pdf_path'] : $report['docx_path'];
    
    if (empty($filePath)) {
        header('HTTP/1.1 404 Not Found');
        exit('Archivo no disponible');
    }
    
    $fullPath = BASE_PATH . $filePath;
    
    if (!file_exists($fullPath)) {
        header('HTTP/1.1 404 Not Found');
        exit('Archivo no encontrado en el servidor');
    }
    
    // Configurar headers para descarga
    $filename = basename($filePath);
    $mimeType = $type === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    
    header('Content-Type: ' . $mimeType);
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($fullPath));
    header('Cache-Control: private');
    
    // Enviar archivo
    readfile($fullPath);
    
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    exit('Error al descargar el archivo');
}