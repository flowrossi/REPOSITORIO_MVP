// ------------------------- Funciones para la lista de pacientes ------------------------
async function refreshList() {
  const tbody = document.querySelector("#tblPacientes tbody");
  tbody.innerHTML = "";
  
  const searchQuery = document.getElementById("txtBuscar").value;
  
  try {
    const response = await fetch(`api/patients.php${searchQuery ? `?search=${encodeURIComponent(searchQuery)}` : ''}`);
    const result = await response.json();
    
    if (result.success && result.data) {
      result.data.forEach(p => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${p.nombre_completo}</td>
          <td>${p.edad || ""}</td>
          <td>${p.diagnostico || ""}</td>
          <td>
            <button class="btn btn-sm btn-primary btn-edit" data-id="${p.id}">Editar</button>
            <button class="btn btn-sm btn-danger btn-delete" data-id="${p.id}">Eliminar</button>
          </td>
        `;
        tbody.appendChild(tr);
      });
      
      // Añadir eventos a los botones
      document.querySelectorAll(".btn-edit").forEach(btn => {
        btn.addEventListener("click", () => loadPatient(btn.dataset.id));
      });
      
      document.querySelectorAll(".btn-delete").forEach(btn => {
        btn.addEventListener("click", () => deletePatient(btn.dataset.id));
      });
    }
  } catch (error) {
    console.error("Error al cargar la lista:", error);
  }
}

async function loadPatient(id) {
  try {
    const response = await fetch(`api/patients.php?id=${id}`);
    const result = await response.json();
    
    if (result.success && result.data) {
      populateForm(result.data);
      // Cambiar a la pestaña del formulario
      const formTab = document.querySelector('#form-tab');
      const tab = new bootstrap.Tab(formTab);
      tab.show();
    } else {
      showAlert("Error al cargar paciente", "danger");
    }
  } catch (error) {
    showAlert("Error al cargar paciente: " + error.message, "danger");
  }
}

async function deletePatient(id) {
  if (!confirm("¿Está seguro de eliminar este paciente?")) return;
  
  try {
    const response = await fetch("api/patients.php", {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ id })
    });
    
    const result = await response.json();
    
    if (result.success) {
      showAlert("Paciente eliminado correctamente");
      refreshList();
    } else {
      showAlert(result.message, "danger");
    }
  } catch (error) {
    showAlert("Error al eliminar: " + error.message, "danger");
  }
}

// ------------------------- Eventos ------------------------
document.addEventListener("DOMContentLoaded", function() {
  refreshList();
  document.getElementById("txtBuscar").addEventListener("input", refreshList);
});