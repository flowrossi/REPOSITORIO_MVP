# 🧠 Gestor de Informes para Terapeutas Ocupacionales
Sistema web completo desarrollado en PHP 8 con MySQL, diseñado específicamente para terapeutas ocupacionales. Permite registrar pacientes y generar informes clínicos profesionales en formatos .docx (editables) y .pdf (estáticos) de forma segura y eficiente.

## 🚀 Características Principales
- ✅ Gestión completa de pacientes y terapeutas
- ✅ Autenticación segura con password_hash() y sesiones PHP
- ✅ Generación de informes clínicos en múltiples formatos:
  - Documentos editables (.docx)
  - Documentos estáticos (.pdf)
- ✅ Arquitectura modular y escalable
- ✅ Sistema desacoplado de servicios para formatos de informes
- ✅ Testing automatizado con PHPUnit integrado
- ✅ Interfaz responsive desarrollada con Bootstrap 5
## 📂 Estructura del Proyecto
/
├── api/                              # Endpoints para generación de informes
│   ├── generate_docx.php            # Generador de documentos Word
│   └── generate_pdf.php             # Generador de documentos PDF
│
├── includes/                         # Núcleo de la aplicación
│   ├── config.php                   # Configuración global
│   ├── db.php                       # Conexión a base de datos
│   ├── auth_middleware.php          # Middleware de autenticación
│   └── services/                    # Servicios de la aplicación
│       ├── ReportGenerator.php      # Interfaz base para generadores
│       ├── WordReportGenerator.php  # Implementación con PhpWord
│       └── PdfReportGenerator.php   # Implementación con Dompdf
│
├── uploads/                          # Almacenamiento seguro de archivos
│   └── .htaccess                    # Prevención de ejecución de scripts
│
├── tests/                            # Pruebas automatizadas
│   └── PdfReportGeneratorTest.php   # Pruebas unitarias con PHPUnit
│
├── vendor/                           # Dependencias gestionadas por Composer
├── composer.json                     # Definición de dependencias
├── database_setup.sql               # Script de creación de base de datos
└── index.php                         # Punto de entrada principal
## 🛠️ Instalación
1. Clonar el repositorio en tu entorno local:
   pdf2.0
   
2. Crear base de datos MySQL :
   - Crea una base de datos llamada gestor_informes_to
   - Ejecuta el script database_setup.sql para crear las tablas necesarias

3. Configurar conexión :
   - Edita los parámetros de conexión en includes/db.php

4. Instalar dependencias :
   composer install

5. Configurar permisos :
   - Asegúrate que la carpeta /uploads tenga permisos de escritura
   - Verifica que el archivo .htaccess esté presente en /uploads para garantizar la seguridad
## 🧪 Testing
El proyecto utiliza PHPUnit para pruebas unitarias. Para ejecutarlas:

```
.\vendor\bin\phpunit
```
### Pruebas implementadas:
- ✔️ PdfReportGeneratorTest.php : Verifica la correcta generación de archivos PDF.
## 📦 Dependencias
```
{
  "require": {
    "phpoffice/phpword": "^1.0",
    "dompdf/dompdf": "^2.0"
  },
  "require-dev": {
    "phpunit/phpunit": "^9.0"
  }
}
```
## 🧱 Base de Datos
Tablas principales incluidas en database_setup.sql :

- users : Usuarios del sistema con credenciales seguras
- patients : Datos clínicos completos de pacientes
- therapist : Información profesional del terapeuta (nombre, título, firma digital, logo)
## 🔒 Seguridad
- Autenticación robusta mediante sesiones ( $_SESSION )
- Hashing seguro de contraseñas con password_hash()
- Middleware de protección para rutas privadas ( auth_middleware.php )
- Sistema seguro de archivos en /uploads con bloqueo de ejecución de scripts
## 📱 Diseño y Accesibilidad
- Diseño responsivo implementado con Bootstrap 5
- Interfaz accesible optimizada para terapeutas ocupacionales
- Navegación intuitiva con flujos de trabajo eficientes
- Compatibilidad con múltiples dispositivos y navegadores
## 📈 Escalabilidad
- Arquitectura basada en servicios completamente desacoplados
- Interfaz ReportGenerator permite agregar nuevos formatos fácilmente (ej: HTML, ODT)
- Código modular y documentado para facilitar mantenimiento
- Diseño extensible que facilita la expansión futura
## 👥 Colaboración
Este proyecto está diseñado para equipos de desarrollo que buscan una solución clínica profesional y extensible. Se acepta colaboración con enfoque profesional mediante pull requests y sugerencias de mejora.

## 📄 Licencia
Este proyecto es de uso privado exclusivamente para instituciones o profesionales de Terapia Ocupacional. No está permitida su distribución sin autorización expresa.

© 2023 Gestor de Informes TO | Desarrollado para profesionales de Terapia Ocupacional