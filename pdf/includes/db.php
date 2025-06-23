<?php
require_once __DIR__ . '/config.php';

function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die(json_encode(['error' => 'Error de conexión: ' . $conn->connect_error]));
    }
    
    $conn->set_charset("utf8");
    return $conn;
}

function closeConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}

// Función para sanitizar entradas
function sanitizeInput($data) {
    $conn = getConnection();
    return $conn->real_escape_string($data);
}
?>