<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';

$conn = getConnection();

// Obtener todos los pacientes
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    
    $sql = "SELECT id, nombre_completo, edad, diagnostico FROM patients";
    if (!empty($search)) {
        $search = $conn->real_escape_string("%$search%");
        $sql .= " WHERE nombre_completo LIKE '$search'";
    }
    $sql .= " ORDER BY nombre_completo";
    
    $result = $conn->query($sql);
    $patients = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $patients[] = $row;
        }
    }
    
    echo json_encode(['success' => true, 'data' => $patients]);
}

// Obtener un paciente específico
else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $sql = "SELECT * FROM patients WHERE id = '$id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $patient = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $patient]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Paciente no encontrado']);
    }
}

// Crear o actualizar paciente
else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['nombre_completo']) || empty($data['nombre_completo'])) {
        echo json_encode(['success' => false, 'message' => 'El nombre del paciente es obligatorio']);
        exit;
    }
    
    // Actualizar paciente existente
    if (isset($data['id']) && !empty($data['id'])) {
        $id = $conn->real_escape_string($data['id']);
        
        $fields = [];
        foreach ($data as $key => $value) {
            if ($key !== 'id') {
                $escapedValue = $conn->real_escape_string($value);
                $fields[] = "`$key` = '$escapedValue'";
            }
        }
        
        $fieldsStr = implode(", ", $fields);
        $sql = "UPDATE patients SET $fieldsStr WHERE id = '$id'";
        
        if ($conn->query($sql) === TRUE) {
            echo json_encode(['success' => true, 'message' => 'Paciente actualizado correctamente', 'id' => $id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $conn->error]);
        }
    } 
    // Crear nuevo paciente
    else {
        $columns = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            $columns[] = "`$key`";
            $escapedValue = $conn->real_escape_string($value);
            $values[] = "'$escapedValue'";
        }
        
        $columnsStr = implode(", ", $columns);
        $valuesStr = implode(", ", $values);
        
        $sql = "INSERT INTO patients ($columnsStr) VALUES ($valuesStr)";
        
        if ($conn->query($sql) === TRUE) {
            $newId = $conn->insert_id;
            echo json_encode(['success' => true, 'message' => 'Paciente guardado correctamente', 'id' => $newId]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al guardar: ' . $conn->error]);
        }
    }
}

// Eliminar paciente
else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id']) || empty($data['id'])) {
        echo json_encode(['success' => false, 'message' => 'ID de paciente no proporcionado']);
        exit;
    }
    
    $id = $conn->real_escape_string($data['id']);
    $sql = "DELETE FROM patients WHERE id = '$id'";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Paciente eliminado correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . $conn->error]);
    }
}

closeConnection($conn);
?>