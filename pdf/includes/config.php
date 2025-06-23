<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Usuario por defecto de XAMPP
define('DB_PASS', '');     // Contraseña por defecto (vacía)
define('DB_NAME', 'gestor_informes');

// Rutas de la aplicación
define('BASE_PATH', dirname(__DIR__));
define('UPLOADS_PATH', BASE_PATH . '/uploads');

// Configuración de sesiones
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 en HTTPS

// Configuración de errores (desactivar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Zona horaria
date_default_timezone_set('America/Santiago'); // Ajusta según tu ubicación
?>