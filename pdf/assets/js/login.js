// --------- LOGIN Y REGISTRO ----------
const loginContainer = document.getElementById('loginContainer');
const appContainer = document.getElementById('appContainer');
const loginForm = document.getElementById('loginForm');
const loginError = document.getElementById('loginError');
const registerSuccess = document.getElementById('registerSuccess');
const registerError = document.getElementById('registerError');
const btnLogout = document.getElementById('btnLogout');
const toggleRegister = document.getElementById('toggleRegister');
const toggleLogin = document.getElementById('toggleLogin');
const formTitle = document.getElementById('formTitle');
const btnSubmit = document.getElementById('btnSubmit');

// Estado actual: login o registro
let isRegisterMode = false;
let currentUser = null;

// Funciones de API
async function apiCall(url, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json',
        }
    };
    
    if (data) {
        options.body = JSON.stringify(data);
    }
    
    try {
        const response = await fetch(url, options);
        return await response.json();
    } catch (error) {
        console.error('Error en API:', error);
        return { success: false, message: 'Error de conexión' };
    }
}

// Verificar sesión al cargar la página
async function checkSession() {
    const result = await apiCall('api/users.php?action=check');
    if (result.success && result.logged_in) {
        currentUser = result.user;
        showApp();
    } else {
        showLogin();
    }
}

function showApp() {
    loginContainer.style.display = 'none';
    appContainer.style.display = 'block';
}

function showLogin() {
    loginContainer.style.display = 'block';
    appContainer.style.display = 'none';
    currentUser = null;
}

function switchToRegister() {
    isRegisterMode = true;
    formTitle.textContent = "Registrar nuevo usuario";
    btnSubmit.textContent = "Registrar";
    toggleRegister.style.display = "none";
    toggleLogin.style.display = "inline";
    hideMessages();
}

function switchToLogin() {
    isRegisterMode = false;
    formTitle.textContent = "Iniciar sesión";
    btnSubmit.textContent = "Ingresar";
    toggleRegister.style.display = "inline";
    toggleLogin.style.display = "none";
    hideMessages();
}

function hideMessages() {
    loginError.style.display = "none";
    registerError.style.display = "none";
    registerSuccess.style.display = "none";
}

function showError(message, isRegister = false) {
    hideMessages();
    if (isRegister) {
        registerError.textContent = message;
        registerError.style.display = "block";
    } else {
        loginError.textContent = message;
        loginError.style.display = "block";
    }
}

function showSuccess(message) {
    hideMessages();
    registerSuccess.textContent = message;
    registerSuccess.style.display = "block";
}

// Event listeners
toggleRegister.addEventListener("click", e => {
    e.preventDefault();
    switchToRegister();
});

toggleLogin.addEventListener("click", e => {
    e.preventDefault();
    switchToLogin();
});

// Manejo del formulario de login/registro
loginForm.addEventListener("submit", async e => {
    e.preventDefault();
    
    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value.trim();
    
    if (!username || !password) {
        showError("Todos los campos son obligatorios", isRegisterMode);
        return;
    }
    
    if (isRegisterMode) {
        // Registro de nuevo usuario
        const result = await apiCall('api/users.php?action=register', 'POST', {
            username: username,
            password: password
        });
        
        if (result.success) {
            showSuccess("Registro exitoso, ya puedes iniciar sesión.");
            switchToLogin();
            loginForm.reset();
        } else {
            showError(result.message, true);
        }
    } else {
        // Login
        const result = await apiCall('api/users.php?action=login', 'POST', {
            username: username,
            password: password
        });
        
        if (result.success) {
            currentUser = result.user;
            showApp();
            loginForm.reset();
        } else {
            showError(result.message || "Usuario o contraseña incorrectos");
        }
    }
});

// Cerrar sesión
if (btnLogout) {
    btnLogout.addEventListener("click", async () => {
        const result = await apiCall('api/users.php?action=logout', 'POST');
        showLogin();
    });
}

// Verificar sesión al cargar la página
document.addEventListener('DOMContentLoaded', checkSession);