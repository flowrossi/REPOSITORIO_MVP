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

// Obtiene usuarios desde localStorage o crea admin por defecto
function getUsers(){
  const u = localStorage.getItem("users");
  if(u){
    try {
      return JSON.parse(u);
    } catch {
      return {};
    }
  } else {
    // Usuario admin por defecto
    const defaultUsers = { "admin":"1234" };
    localStorage.setItem("users", JSON.stringify(defaultUsers));
    return defaultUsers;
  }
}

function saveUsers(users){
  localStorage.setItem("users", JSON.stringify(users));
}

// Estado actual: login o registro
let isRegisterMode = false;

function switchToRegister(){
  isRegisterMode = true;
  formTitle.textContent = "Registrar nuevo usuario";
  btnSubmit.textContent = "Registrar";
  toggleRegister.style.display = "none";
  toggleLogin.style.display = "inline";
  loginError.style.display = "none";
  registerError.style.display = "none";
  registerSuccess.style.display = "none";
}

function switchToLogin(){
  isRegisterMode = false;
  formTitle.textContent = "Iniciar sesión";
  btnSubmit.textContent = "Ingresar";
  toggleRegister.style.display = "inline";
  toggleLogin.style.display = "none";
  loginError.style.display = "none";
  registerError.style.display = "none";
  registerSuccess.style.display = "none";
}

toggleRegister.addEventListener("click", e=>{
  e.preventDefault();
  switchToRegister();
});

toggleLogin.addEventListener("click", e=>{
  e.preventDefault();
  switchToLogin();
});

// Manejo del formulario de login/registro
// Función simple de hash (para una implementación más segura, considera usar bcrypt en el servidor)
function hashPassword(password) {
  // Esta es una implementación muy básica, no usar en producción
  let hash = 0;
  for (let i = 0; i < password.length; i++) {
    const char = password.charCodeAt(i);
    hash = ((hash << 5) - hash) + char;
    hash = hash & hash; // Convertir a entero de 32 bits
  }
  return hash.toString(16); // Convertir a hexadecimal
}

// Modificar las funciones de registro y login para usar el hash
loginForm.addEventListener("submit", e=>{
  e.preventDefault();
  const username = document.getElementById("username").value.trim();
  const password = document.getElementById("password").value.trim();
  
  if(!username || !password){
    registerError.textContent = "Todos los campos son obligatorios";
    registerError.style.display = "block";
    return;
  }
  
  const users = getUsers();
  
  if(isRegisterMode){
    // Registro de nuevo usuario
    if(users[username]){
      registerError.textContent = "El nombre de usuario ya existe";
      registerError.style.display = "block";
      return;
    }
    
    users[username] = hashPassword(password); // Usar hash en lugar de contraseña en texto plano
    saveUsers(users);
    registerSuccess.style.display = "block";
    switchToLogin();
    loginForm.reset();
  } else {
    // Login
    if(users[username] && users[username] === hashPassword(password)){ // Comparar con hash
      // Login exitoso
      loginContainer.style.display = "none";
      appContainer.style.display = "block";
      loginForm.reset();
    } else {
      loginError.style.display = "block";
    }
  }
});

// Cerrar sesión
if(btnLogout) {
  btnLogout.addEventListener("click", ()=>{
    appContainer.style.display = "none";
    loginContainer.style.display = "block";
  });
}

// Verificar si hay sesión guardada (para una implementación más completa)
// Aquí podrías agregar código para mantener la sesión con cookies o localStorage