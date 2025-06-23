<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Font;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || empty($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de paciente no proporcionado']);
    exit;
}

$conn = getConnection();
$patientId = $conn->real_escape_string($data['id']);

// Obtener datos del paciente
$sql = "SELECT * FROM patients WHERE id = '$patientId'";
$patientResult = $conn->query($sql);

if ($patientResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Paciente no encontrado']);
    exit;
}

$patient = $patientResult->fetch_assoc();

// Obtener datos del terapeuta
$sql = "SELECT * FROM therapist LIMIT 1";
$therapistResult = $conn->query($sql);
$therapist = $therapistResult->num_rows > 0 ? $therapistResult->fetch_assoc() : [];

closeConnection($conn);

// Crear documento DOCX
try {
    $phpWord = new PhpWord();
    
    // Estilos
    $phpWord->setDefaultFontName('Calibri');
    $phpWord->setDefaultFontSize(11);
    
    $sectionStyle = [
        'orientation' => 'portrait',
        'marginTop' => 1000,
        'marginRight' => 1000,
        'marginBottom' => 1000,
        'marginLeft' => 1000,
    ];
    
    $titleStyle = ['bold' => true, 'size' => 14];
    $subtitleStyle = ['bold' => true, 'size' => 12];
    $normalStyle = [];
    
    // Crear sección
    $section = $phpWord->addSection($sectionStyle);
    
    // Encabezado con logo y título
    $table = $section->addTable(['width' => 100 * 50, 'unit' => 'pct']);
    $table->addRow();
    
    // Celda para logo
    $cell1 = $table->addCell(2000);
    if (!empty($therapist['logo_path'])) {
        $logoPath = __DIR__ . '/../' . $therapist['logo_path'];
        if (file_exists($logoPath)) {
            $logoSize = isset($therapist['logo_size']) ? $therapist['logo_size'] * 100 : 100;
            $cell1->addImage($logoPath, ['width' => $logoSize, 'height' => $logoSize]);
        }
    }
    
    // Celda para título
    $cell2 = $table->addCell(8000);
    $titulo = !empty($therapist['titulo_documento']) ? $therapist['titulo_documento'] : 'INFORME DE EVALUACIÓN TERAPIA OCUPACIONAL';
    $cell2->addText($titulo, ['bold' => true, 'size' => 16], ['alignment' => 'center']);
    
    $section->addTextBreak(2);
    
    // I. IDENTIFICACIÓN
    $section->addText('I. IDENTIFICACIÓN', $titleStyle);
    $section->addTextBreak(1);
    
    $infoTable = $section->addTable();
    
    $infoTable->addRow();
    $infoTable->addCell(2000)->addText('Nombre:', ['bold' => true]);
    $infoTable->addCell(6000)->addText($patient['nombre_completo'] ?? '');
    
    $infoTable->addRow();
    $infoTable->addCell(2000)->addText('Fecha de nacimiento:', ['bold' => true]);
    $infoTable->addCell(6000)->addText($patient['nacimiento'] ?? '');
    
    $infoTable->addRow();
    $infoTable->addCell(2000)->addText('Edad:', ['bold' => true]);
    $infoTable->addCell(6000)->addText($patient['edad'] ?? '');
    
    $infoTable->addRow();
    $infoTable->addCell(2000)->addText('Escolaridad:', ['bold' => true]);
    $infoTable->addCell(6000)->addText($patient['escolaridad'] ?? '');
    
    $infoTable->addRow();
    $infoTable->addCell(2000)->addText('Diagnóstico:', ['bold' => true]);
    $infoTable->addCell(6000)->addText($patient['diagnostico'] ?? '');
    
    $infoTable->addRow();
    $infoTable->addCell(2000)->addText('Fecha informe:', ['bold' => true]);
    $infoTable->addCell(6000)->addText($patient['fecha_informe'] ?? '');
    
    $section->addTextBreak(1);
    
    // II. MOTIVO DE CONSULTA
    $section->addText('II. MOTIVO DE CONSULTA', $titleStyle);
    $section->addTextBreak(1);
    
    $section->addText('Responsable:', $subtitleStyle);
    $section->addText($patient['persona_responsable'] ?? '', $normalStyle);
    $section->addTextBreak(1);
    
    $section->addText('Dificultades principales:', $subtitleStyle);
    $section->addText($patient['dificultades_principales_detectadas'] ?? '', $normalStyle);
    $section->addTextBreak(1);
    
    $section->addText('Profesional derivante:', $subtitleStyle);
    $section->addText($patient['profesional_derivante'] ?? '', $normalStyle);
    $section->addTextBreak(1);
    
    $section->addText('Temas que impactan desempeño:', $subtitleStyle);
    $section->addText($patient['temas_clave_impactan_desempeño_ocupacion'] ?? '', $normalStyle);
    $section->addTextBreak(1);
    
    // III. INSTRUMENTOS
    $section->addText('III. INSTRUMENTOS', $titleStyle);
    $section->addTextBreak(1);
    
    $section->addText('Anamnesis / perfil:', $subtitleStyle);
    $section->addText($patient['anamnesis_detallada_perfil_ocupacional_r'] ?? '', $normalStyle);
    $section->addTextBreak(1);
    
    $section->addText('Observación participante:', $subtitleStyle);
    $section->addText($patient['observacion_participante'] ?? '', $normalStyle);
    $section->addTextBreak(1);
    
    $section->addText('Hitos desarrollo:', $subtitleStyle);
    $section->addText($patient['hitos_del_desarrollo'] ?? '', $normalStyle);
    $section->addTextBreak(1);
    
    $section->addText('Perfil sensorial:', $subtitleStyle);
    $section->addText($patient['SPM_observacion_clinica_perfil_sensorial'] ?? '', $normalStyle);
    $section->addTextBreak(1);
    
    $section->addText('Complementarios:', $subtitleStyle);
    $section->addText($patient['complementarios'] ?? '', $normalStyle);
    $section->addTextBreak(1);
    
    // IV. ANTECEDENTES
    $section->addText('IV. ANTECEDENTES', $titleStyle);
    $section->addTextBreak(1);
    
    // Continuar con todas las secciones...
    // V. HALLAZGOS
    $section->addText('V. HALLAZGOS', $titleStyle);
    $section->addTextBreak(1);
    
    // V.a Hallazgos detallados
    $section->addText('a) Hallazgos detallados', $subtitleStyle);
    $section->addTextBreak(1);
    
    // Añadir todos los campos de hallazgos...
    
    // V.b Síntesis
    $section->addText('b) Síntesis', $subtitleStyle);
    $section->addTextBreak(1);
    
    $section->addText('Descripción positiva:', ['bold' => true]);
    $section->addText($patient['descripcion_positiva'] ?? '', $normalStyle);
    $section->addTextBreak(1);
    
    $section->addText('Áreas de dificultad:', ['bold' => true]);
    $section->addText($patient['areas_dificultad'] ?? '', $normalStyle);
    $section->addTextBreak(1);
    
    // VI. SUGERENCIAS
    $section->addText('VI. SUGERENCIAS', $titleStyle);
    $section->addTextBreak(1);
    
    $section->addText('Enfoque terapéutico: ' . ($patient['enfoque_terapeutico'] ?? ''), $normalStyle);
    $section->addText('Frecuencia sesiones: ' . ($patient['frecuencia_sesiones'] ?? ''), $normalStyle);
    $section->addText('Período estimado: ' . ($patient['periodo_estimado'] ?? ''), $normalStyle);
    $section->addTextBreak(1);
    
    // Recomendaciones
    for ($i = 1; $i <= 10; $i++) {
        $recomendacion = $patient["recomendacion_$i"] ?? '';
        if (!empty($recomendacion)) {
            $section->addListItem($recomendacion, 0);
        }
    }
    
    $section->addTextBreak(2);
    
    // Firma del terapeuta
    $section->addText('Atentamente,', ['italic' => true], ['alignment' => 'right']);
    $section->addTextBreak(1);
    
    // Imagen de firma
    if (!empty($therapist['firma_path'])) {
        $firmaPath = __DIR__ . '/../' . $therapist['firma_path'];
        if (file_exists($firmaPath)) {
            $section->addImage($firmaPath, ['width' => 100, 'height' => 50, 'alignment' => 'right']);
        }
    }
    
    $section->addTextBreak(1);
    
    // Datos del terapeuta
    $section->addText($therapist['firma_nombre'] ?? '', ['bold' => true], ['alignment' => 'right']);
    $section->addText($therapist['titulo'] ?? '', [], ['alignment' => 'right']);
    
    // Diplomados
    for ($i = 1; $i <= 4; $i++) {
        $diplomado = $therapist["Diplomado_$i"] ?? '';
        if (!empty($diplomado)) {
            $section->addText($diplomado, ['italic' => true, 'size' => 10], ['alignment' => 'right']);
        }
    }
    
    // RUT y registro
    if (!empty($therapist['rut'])) {
        $section->addText('RUT: ' . $therapist['rut'], ['size' => 10], ['alignment' => 'right']);
    }
    
    if (!empty($therapist['nro_registro'])) {
        $section->addText('Nº Registro: ' . $therapist['nro_registro'], ['size' => 10], ['alignment' => 'right']);
    }
    
    // Guardar documento
    $filename = 'Informe_' . preg_replace('/[^a-zA-Z0-9]/', '_', $patient['nombre_completo']) . '_' . date('Y-m-d') . '.docx';
    $filepath = __DIR__ . '/../uploads/' . $filename;
    $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
    $objWriter->save($filepath);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Documento generado correctamente',
        'filename' => $filename,
        'download_url' => 'uploads/' . $filename
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error al generar documento: ' . $e->getMessage()]);
}