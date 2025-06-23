# 🧠 Gestor de Informes TO (Terapia Ocupacional)

Aplicación web desarrollada en PHP y Bootstrap para gestionar informes clínicos de pacientes en el área de Terapia Ocupacional. Permite registrar usuarios, administrar pacientes, generar informes en formato DOCX y configurar información del terapeuta.

---

## 🚀 Funcionalidades

- 🔐 Registro e inicio de sesión de terapeutas
- 👤 Registro de pacientes con edad, diagnóstico y más
- 📄 Generación de informes en formato Microsoft Word (.docx)
- 🧑‍⚕️ Configuración de información del terapeuta
- 📁 Subida y descarga segura de archivos

---

## 🛠️ Tecnologías usadas

- PHP (8.x)
- MySQL/MariaDB
- Bootstrap 5
- JavaScript (Vanilla)
- PhpWord (para generar archivos .docx)
- HTML5/CSS3

---

## 🗂️ Estructura del proyecto
/
├── index.php # Punto de entrada: login + vista principal
├── /api/ # Lógica backend (login, pacientes, generación DOCX)
├── /includes/ # Configuración y conexión a base de datos
├── /uploads/ # Archivos generados (.docx)
├── /assets/ # CSS y JS del frontend
└── README.md # Este archivo´

---

## 🔐 Seguridad básica

Contraseñas cifradas con password_hash().

Protección contra ejecución de archivos maliciosos en /uploads/.

Autenticación por sesión PHP ($_SESSION).

#### faltas xxxx

Los usuarios se guardan en el localStorage del navegador a través del archivo `login.js` :

- Ubicación : localStorage con la clave "users"
- Formato : JSON con pares usuario/contraseña


#####

## Base de Datos
La base de datos MySQL configurada ( gestor_informes ) se usa para:

- Pacientes (tabla patients )
- Información del terapeuta (tabla therapist )
Pero NO para usuarios del sistema - estos están únicamente en el localStorage del navegador.