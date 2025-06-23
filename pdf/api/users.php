<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../includes/db.php';
session_start();

$conn = getConnection();

// Login de usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'login') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['username']) || !isset($data['password'])) {
        echo json_encode(['success' => false, 'message' => 'Usuario y contraseña son obligatorios']);
        exit;
    }
    
    $username = $conn->real_escape_string($data['username']);
    $password = $data['password'];
    
    $sql = "SELECT id, username, password_hash, full_name, role, is_active FROM users WHERE username = '$username' AND is_active = 1";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verificar contraseña
        if (password_verify($password, $user['password_hash'])) {
            // Crear sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            echo json_encode([
                'success' => true, 
                'message' => 'Login exitoso',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'full_name' => $user['full_name'],
                    'role' => $user['role']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Usuario o contraseña incorrectos']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Usuario o contraseña incorrectos']);
    }
}

// Registro de usuario
else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'register') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['username']) || !isset($data['password'])) {
        echo json_encode(['success' => false, 'message' => 'Usuario y contraseña son obligatorios']);
        exit;
    }
    
    $username = $conn->real_escape_string($data['username']);
    $password = $data['password'];
    $full_name = isset($data['full_name']) ? $conn->real_escape_string($data['full_name']) : '';
    $email = isset($data['email']) ? $conn->real_escape_string($data['email']) : '';
    
    // Verificar si el usuario ya existe
    $checkSql = "SELECT id FROM users WHERE username = '$username'";
    $checkResult = $conn->query($checkSql);
    
    if ($checkResult->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'El nombre de usuario ya existe']);
        exit;
    }
    
    // Hash de la contraseña
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (username, password_hash, full_name, email) VALUES ('$username', '$passwordHash', '$full_name', '$email')";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Usuario registrado correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al registrar usuario: ' . $conn->error]);
    }
}

// Logout
else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    echo json_encode(['success' => true, 'message' => 'Sesión cerrada correctamente']);
}

// Verificar sesión
else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'check') {
    if (isset($_SESSION['user_id'])) {
        echo json_encode([
            'success' => true, 
            'logged_in' => true,
            'user' => [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role' => $_SESSION['role']
            ]
        ]);
    } else {
        echo json_encode(['success' => true, 'logged_in' => false]);
    }
}

else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}

closeConnection($conn);
?>