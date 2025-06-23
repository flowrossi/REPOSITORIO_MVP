<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';

$conn = getConnection();

// Obtener datos del terapeuta
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM therapist LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $therapist = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $therapist]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se encontraron datos del terapeuta']);
    }
}

// Actualizar datos del terapeuta
else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Manejar archivos subidos
    $data = $_POST;
    $files = $_FILES;
    $therapistId = 1; // Siempre usamos el ID 1 para el terapeuta
    
    // Procesar logo si se subió
    if (isset($files['logo_empresa']) && $files['logo_empresa']['error'] === 0) {
        $logoName = 'logo_' . time() . '_' . basename($files['logo_empresa']['name']);
        $logoPath = UPLOADS_PATH . '/' . $logoName;
        
        if (move_uploaded_file($files['logo_empresa']['tmp_name'], $logoPath)) {
            $data['logo_path'] = 'uploads/' . $logoName;
        }
    }
    
    // Procesar firma si se subió
    if (isset($files['firma_imagen']) && $files['firma_imagen']['error'] === 0) {
        $firmaName = 'firma_' . time() . '_' . basename($files['firma_imagen']['name']);
        $firmaPath = UPLOADS_PATH . '/' . $firmaName;
        
        if (move_uploaded_file($files['firma_imagen']['tmp_name'], $firmaPath)) {
            $data['firma_path'] = 'uploads/' . $firmaName;
        }
    }
    
    // Construir consulta SQL
    $fields = [];
    foreach ($data as $key => $value) {
        if ($key !== 'id' && $key !== 'logo_empresa' && $key !== 'firma_imagen') {
            $escapedValue = $conn->real_escape_string($value);
            $fields[] = "`$key` = '$escapedValue'";
        }
    }
    
    if (!empty($fields)) {
        $fieldsStr = implode(", ", $fields);
        $sql = "UPDATE therapist SET $fieldsStr WHERE id = $therapistId";
        
        if ($conn->query($sql) === TRUE) {
            echo json_encode(['success' => true, 'message' => 'Datos del terapeuta actualizados correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No hay datos para actualizar']);
    }
}

closeConnection($conn);
?>