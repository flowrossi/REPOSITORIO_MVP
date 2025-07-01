-- =====================================================
-- BASE DE DATOS COMPLETA Y FINAL
-- Sistema de Gestión de Informes Clínicos
-- =====================================================

-- Crear y usar la base de datos
CREATE DATABASE IF NOT EXISTS gestor_informes_to;
USE gestor_informes_to;

-- =====================================================
-- TABLA DE USUARIOS
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'therapist') NOT NULL DEFAULT 'therapist',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =====================================================
-- TABLA DE TERAPEUTAS
-- =====================================================
CREATE TABLE IF NOT EXISTS therapist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    license_number VARCHAR(50),
    specialty VARCHAR(100),
    signature_path VARCHAR(255),
    logo_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =====================================================
-- TABLA DE PACIENTES (ESTRUCTURA ACTUALIZADA)
-- =====================================================
CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    age INT NOT NULL,
    diagnosis TEXT,
    medical_history TEXT,
    treatment_goals TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_email (email)
);

-- =====================================================
-- TABLA DE INFORMES
-- =====================================================
CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    patient_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    report_date DATE NOT NULL,
    session_date DATE,
    session_duration INT, -- en minutos
    observations TEXT,
    recommendations TEXT,
    next_session_date DATE,
    docx_path VARCHAR(255),
    pdf_path VARCHAR(255),
    status ENUM('draft', 'completed', 'archived') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_patient_id (patient_id),
    INDEX idx_report_date (report_date)
);

-- =====================================================
-- DATOS INICIALES
-- =====================================================

-- Usuario administrador por defecto
INSERT INTO users (username, password, email, full_name, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com', 'Administrador del Sistema', 'admin');

-- Usuario terapeuta de ejemplo
INSERT INTO users (username, password, email, full_name, role) VALUES
('terapeuta1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'terapeuta@example.com', 'Dr. Juan Pérez', 'therapist');

-- Datos del terapeuta
INSERT INTO therapist (user_id, title, license_number, specialty) VALUES
(2, 'Dr. en Psicología Clínica', 'PSI-12345', 'Terapia Cognitivo-Conductual');

-- Pacientes de ejemplo
INSERT INTO patients (user_id, name, email, phone, age, diagnosis, medical_history, treatment_goals) VALUES
(2, 'María García López', 'maria.garcia@email.com', '+34 600 123 456', 28, 'Trastorno de Ansiedad Generalizada', 'Sin antecedentes médicos relevantes', 'Reducir niveles de ansiedad y mejorar calidad de vida'),
(2, 'Carlos Rodríguez Martín', 'carlos.rodriguez@email.com', '+34 600 789 012', 35, 'Depresión Mayor', 'Episodio depresivo previo hace 3 años', 'Estabilización del estado de ánimo y prevención de recaídas');

-- Informes de ejemplo
INSERT INTO reports (user_id, patient_id, title, content, report_date, session_date, session_duration, observations, recommendations, status) VALUES
(2, 1, 'Evaluación Inicial - María García', 'Paciente de 28 años que acude a consulta por sintomatología ansiosa...', CURDATE(), CURDATE(), 60, 'Paciente colaboradora, buen insight', 'Continuar con terapia cognitivo-conductual', 'completed'),
(2, 2, 'Primera Sesión - Carlos Rodríguez', 'Paciente de 35 años con diagnóstico de depresión mayor...', CURDATE(), CURDATE(), 50, 'Estado de ánimo bajo, motivación limitada', 'Establecer rutinas diarias y objetivos pequeños', 'completed');

-- =====================================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- =====================================================

-- Índices compuestos para consultas frecuentes
CREATE INDEX idx_patients_user_name ON patients(user_id, name);
CREATE INDEX idx_reports_user_date ON reports(user_id, report_date DESC);
CREATE INDEX idx_reports_patient_date ON reports(patient_id, report_date DESC);

-- =====================================================
-- VISTAS ÚTILES
-- =====================================================

-- Vista para informes con datos del paciente
CREATE VIEW reports_with_patient AS
SELECT 
    r.*,
    p.name as patient_name,
    p.email as patient_email,
    p.age as patient_age
FROM reports r
JOIN patients p ON r.patient_id = p.id;

-- Vista para estadísticas de pacientes por usuario
CREATE VIEW patient_stats AS
SELECT 
    user_id,
    COUNT(*) as total_patients,
    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as new_this_month,
    COUNT(CASE WHEN diagnosis IS NOT NULL AND diagnosis != '' THEN 1 END) as with_diagnosis,
    AVG(age) as average_age
FROM patients 
GROUP BY user_id;

-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS ÚTILES
-- =====================================================

DELIMITER //

-- Procedimiento para obtener estadísticas de un usuario
CRETE PROCEDURE GetUserStats(IN userId INT)
BEGIN
    SELECT 
        (SELECT COUNT(*) FROM patients WHERE user_id = userId) as total_patients,
        (SELECT COUNT(*) FROM reports WHERE user_id = userId) as total_reports,
        (SELECT COUNT(*) FROM reports WHERE user_id = userId AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as reports_this_month;
END //

DELIMITER ;

-- =====================================================
-- CONFIGURACIÓN FINAL
-- =====================================================

-- Configurar charset y collation
ALTER DATABASE gestor_informes_to CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

SELECT 'Base de datos creada exitosamente!' as mensaje;