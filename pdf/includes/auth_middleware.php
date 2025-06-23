<?php
require_once __DIR__ . '/config.php';
session_start();

function requireAuth() {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'No autorizado. Inicia sesión.']);
        exit;
    }
    return $_SESSION;
}

function requireAdmin() {
    $session = requireAuth();
    if ($_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acceso denegado. Se requieren permisos de administrador.']);
        exit;
    }
    return $session;
}
?>