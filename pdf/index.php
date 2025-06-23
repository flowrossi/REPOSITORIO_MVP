<?php
require_once __DIR__ . '/includes/config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestor de Informes TO (Web) - Login y Registro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">

  <!-- LOGIN Y REGISTRO -->
  <div id="loginContainer" class="mx-auto" style="max-width:400px; margin-top:80px;">
    <h2 class="mb-4 text-center" id="formTitle">Iniciar sesión</h2>

    <form id="loginForm">
      <div class="mb-3">
        <label for="username" class="form-label">Usuario</label>
        <input type="text" id="username" class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" id="password" class="form-control" required />
      </div>
      <button type="submit" class="btn btn-primary w-100" id="btnSubmit">Ingresar</button>
    </form>

    <div class="text-center mt-3">
      <button id="toggleRegister" class="btn btn-link p-0">¿No tienes cuenta? Regístrate</button>
      <button id="toggleLogin" class="btn btn-link p-0" style="display:none;">¿Ya tienes cuenta? Inicia sesión</button>
    </div>

    <div id="loginError" class="text-danger mt-2" style="display:none;">Usuario o contraseña incorrectos.</div>
    <div id="registerSuccess" class="text-success mt-2" style="display:none;">Registro exitoso, ya puedes iniciar sesión.</div>
    <div id="registerError" class="text-danger mt-2" style="display:none;"></div>
  </div>

  <!-- APP PRINCIPAL -->
  <div id="appContainer" style="display:none;">
    <h1 class="mb-4">Gestor de Informes TO (Web)</h1>

    <ul class="nav nav-tabs" id="mainTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="form-tab" data-bs-toggle="tab" data-bs-target="#formTab" type="button" role="tab">Formulario</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="pacientes-tab" data-bs-toggle="tab" data-bs-target="#pacientesTab" type="button" role="tab">Pacientes</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="config-tab" data-bs-toggle="tab" data-bs-target="#configTab" type="button" role="tab">Configuración</button>
      </li>
    </ul>

    <div class="tab-content pt-3">
      <div class="tab-pane fade show active" id="formTab" role="tabpanel">
        <form id="patientForm">
          <!-- Secciones del formulario generadas por JS -->
        </form>
        <div class="mt-3">
          <button type="button" class="btn btn-success" id="btnGuardar">Guardar/Actualizar</button>
          <button type="button" class="btn btn-secondary" id="btnLimpiar">Limpiar</button>
          <button type="button" class="btn btn-primary" id="btnDocx">Generar DOCX</button>
          <button type="button" class="btn btn-danger float-end" id="btnLogout">Cerrar sesión</button>
        </div>
      </div>

      <div class="tab-pane fade" id="pacientesTab" role="tabpanel">
        <div class="mb-2">
          <input class="form-control" type="search" id="txtBuscar" placeholder="Buscar por nombre…">
        </div>
        <table class="table table-striped" id="tblPacientes">
          <thead>
            <tr><th>Nombre</th><th>Edad</th><th>Diagnóstico</th><th>Acciones</th></tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
      
      <div class="tab-pane fade" id="configTab" role="tabpanel">
        <div class="therapist-section">
          <h3>Configuración del Terapeuta</h3>
          <p class="text-muted">Esta información se utilizará en los informes generados.</p>
          <div id="therapistFormContainer">
            <!-- Formulario generado por JS -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/login.js"></script>
<script src="assets/js/form.js"></script>
<script src="assets/js/patients.js"></script>
</body>
</html>