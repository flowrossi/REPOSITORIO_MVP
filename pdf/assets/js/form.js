// ------------------------- Definiciones ------------------------------
const PATIENT_FIELDS = {
  "I. Identificación": [
    ["nombre_completo","Nombre completo","text"],
    ["nacimiento","Nacimiento","date"],
    ["edad","Edad","number"], // Aquí se quitó "readonly"
    ["escolaridad","Escolaridad","text"],
    ["diagnostico","Diagnóstico","text"],
    ["fecha_informe","Fecha informe","date"]
  ],
  "II. Motivo de consulta":[
    ["persona_responsable","Responsable","text"],
    ["dificultades_principales_detectadas","Dificultades principales","textarea"],
    ["profesional_derivante","Profesional derivante","text"],
    ["temas_clave_impactan_desempeño_ocupacion","Temas impacto desempeño","textarea"]
  ],
  "III. Instrumentos":[
    ["anamnesis_detallada_perfil_ocupacional_r","Anamnesis / perfil","textarea"],
    ["observacion_participante","Observación participante","textarea"],
    ["hitos_del_desarrollo","Hitos desarrollo","textarea"],
    ["SPM_observacion_clinica_perfil_sensorial","Perfil sensorial","textarea"],
    ["complementarios","Complementarios","textarea"]
  ],
  "IV. Antecedentes":[
    ["descripcion_entorno_familiar","Entorno familiar","textarea"],
    ["lugar_nacimiento","Lugar nacimiento","text"],
    ["cuidador","Cuidador principal","text"],
    ["calidad_vinculo_descripcion","Calidad vínculo","text"],
    ["curso_embarazo","Curso embarazo","text"],
    ["tipo_parto","Tipo de parto","select",["Eutócico","Cesárea","Instrumentado"]],
    ["complicaciones","Complicaciones","text"],
    ["desarrollo_psicomotor_hitos_demorados","Desarrollo psicomotor","select",["Típico","Demorado"]],
    ["descripcion_hitos_demorados","Descripción demoras","textarea"],
    ["tipo_alimentacion","Tipo de alimentación","select",["LME","Mixta","Fórmula"]],
    ["dificultad_alimentacion","Dificultades alimentación","textarea"],
    ["incorporacion_alimentos_solidos","Incorp. sólidos","text"],
    ["preferencias_aversiones_particularidades","Preferencias / aversiones","textarea"],
    ["ttos_previos","Tratamientos previos","textarea"],
    ["terapias","Terapias actuales","textarea"],
    ["antecedentes_medicos_previos","Antecedentes médicos","textarea"],
    ["antecedentes_familiares","Antecedentes familiares","textarea"]
  ],
  "V.a Hallazgos":[
    ["area_motriz_fina","Motricidad fina","textarea"],
    ["motricidad_gruesa","Motricidad gruesa","textarea"],
    ["nivel_cognitivo","Nivel cognitivo","textarea"],
    ["funciones_socioemocionales","Funciones socioemocionales","textarea"],
    ["visual","Visual","textarea"],
    ["auditivo","Auditivo","textarea"],
    ["tactil","Táctil","textarea"],
    ["gustativo_olfativo","Gustativo-olfativo","textarea"],
    ["vestibular","Vestibular","textarea"],
    ["propioceptivo","Propioceptivo","textarea"],
    ["proceso_alimentario","Proceso alimentario","textarea"],
    ["aceptacion_rechazo_nvos_alimentos","Aceptación/rechazo","textarea"],
    ["manejo_texturas","Manejo texturas","textarea"],
    ["uso_utensilios","Uso utensilios","textarea"]
  ],
  "V.b Síntesis":[
    ["paciente","Nombre en síntesis","text","readonly"],
    ["descripcion_positiva","Descripción positiva","textarea"],
    ["areas_dificultad","Áreas de dificultad","textarea"]
  ],
  "VI. Sugerencias":[
    ["enfoque_terapeutico","Enfoque terapeutico","text"],
    ["frecuencia_sesiones","Frecuencia sesiones","text"],
    ["periodo_estimado","Período estimado","text"],
    ...Array.from({length:10}, (_,i)=>[`recomendacion_${i+1}`,`Recomendación ${i+1}`,"textarea"])
  ]
};

const THERAPIST_FIELDS = [
  ["firma_nombre","Nombre profesional","text"],
  ["titulo","Título","text"],
  ["titulo_documento","Título del documento","text"],
  ["logo_empresa","Logo empresa","file"],
  ["logo_size","Tamaño logo","range"],
  ["firma_imagen","Firma digital","file"],
  ["Diplomado_1","Diplomado 1","text"],
  ["Diplomado_2","Diplomado 2","text"],
  ["Diplomado_3","Diplomado 3","text"],
  ["Diplomado_4","Diplomado 4","text"],
  ["rut","RUT","text"],
  ["nro_registro","Nº Registro","text"]
];

// ------------------------- Estado ------------------------------
let editId = null;
let therapistData = {};

// ------------------------- Funciones UI ------------------------
function createInput(name, type, extra) {
  let input;
  switch(type) {
    case "textarea":
      input = document.createElement("textarea");
      input.className="form-control";
      input.rows=3;
      break;
    case "select":
      input = document.createElement("select");
      input.className="form-select";
      (Array.isArray(extra) ? extra : []).forEach(opt => {
        const o = document.createElement("option");
        o.text = opt;
        o.value = opt;
        input.appendChild(o);
      });
      break;
    case "file":
      input = document.createElement("input");
      input.type = "file";
      input.className = "form-control";
      break;
    case "range":
      input = document.createElement("input");
      input.type = "range";
      input.min = "0.5";
      input.max = "3";
      input.step = "0.1";
      input.className = "form-range";
      break;
    case "date":
      input = document.createElement("input");
      input.type = "date";
      input.className = "form-control";
      break;
    case "number":
      input = document.createElement("input");
      input.type = "number";
      input.className = "form-control";
      break;
    default:
      input = document.createElement("input");
      input.type = "text";
      input.className = "form-control";
  }
  if (extra === "readonly") input.readOnly = true;
  input.name = name;
  input.id = name;
  return input;
}

function buildForm() {
  const form = document.getElementById("patientForm");
  form.innerHTML = "";
  
  // Añadir campo oculto para ID
  const idField = document.createElement("input");
  idField.type = "hidden";
  idField.name = "id";
  idField.id = "patient_id";
  form.appendChild(idField);
  
  Object.entries(PATIENT_FIELDS).forEach(([section, fields]) => {
    const secDiv = document.createElement("div");
    secDiv.className = "form-section";
    
    const header = document.createElement("h4");
    header.textContent = section;
    secDiv.appendChild(header);
    
    const row = document.createElement("div");
    row.className = "row p-3";
    
    fields.forEach(([k, label, type, ...rest]) => {
      const col = document.createElement("div");
      col.className = "col-md-4 form-group";
      
      const l = document.createElement("label");
      l.textContent = label;
      l.htmlFor = k;
      
      const input = createInput(k, type, rest[0]);
      
      col.appendChild(l);
      col.appendChild(input);
      row.appendChild(col);
    });
    
    secDiv.appendChild(row);
    form.appendChild(secDiv);
  });

  // Sincronizar nombre síntesis
  document.getElementById("nombre_completo").addEventListener("input", e => {
    const synth = document.getElementById("paciente");
    if (synth) synth.value = e.target.value;
  });
}

function buildTherapistForm() {
  const container = document.getElementById("therapistFormContainer");
  if (!container) return;
  
  const form = document.createElement("form");
  form.id = "therapistForm";
  form.className = "row g-3";
  
  THERAPIST_FIELDS.forEach(([k, label, type, ...rest]) => {
    const col = document.createElement("div");
    col.className = type === "textarea" ? "col-md-6" : "col-md-4";
    
    const l = document.createElement("label");
    l.textContent = label;
    l.htmlFor = "therapist_" + k;
    l.className = "form-label";
    
    const input = createInput(k, type, rest[0]);
    input.id = "therapist_" + k;
    
    // Añadir previsualización para archivos
    if (type === "file") {
      const previewDiv = document.createElement("div");
      const previewImg = document.createElement("img");
      previewImg.id = "preview_" + k;
      previewImg.className = "preview-image d-none";
      previewDiv.appendChild(previewImg);
      
      input.addEventListener("change", function() {
        if (this.files && this.files[0]) {
          const reader = new FileReader();
          reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewImg.classList.remove("d-none");
          };
          reader.readAsDataURL(this.files[0]);
        }
      });
      
      col.appendChild(l);
      col.appendChild(input);
      col.appendChild(previewDiv);
    } else {
      col.appendChild(l);
      col.appendChild(input);
    }
    
    form.appendChild(col);
  });
  
  const submitBtn = document.createElement("div");
  submitBtn.className = "col-12";
  submitBtn.innerHTML = `<button type="button" class="btn btn-primary" id="btnSaveTherapist">Guardar configuración</button>`;
  form.appendChild(submitBtn);
  
  container.appendChild(form);
  
  // Evento para guardar datos del terapeuta
  document.getElementById("btnSaveTherapist").addEventListener("click", saveTherapistData);
}

function populateForm(data) {
  // Establecer ID
  document.getElementById("patient_id").value = data.id || "";
  editId = data.id || null;
  
  // Rellenar campos
  Object.keys(PATIENT_FIELDS).forEach(section => {
    PATIENT_FIELDS[section].forEach(([k]) => {
      const el = document.getElementById(k);
      if (!el || el.type === "file") return;
      el.value = data[k] || "";
    });
  });
}

function populateTherapistForm(data) {
  therapistData = data;
  
  THERAPIST_FIELDS.forEach(([k]) => {
    const el = document.getElementById("therapist_" + k);
    if (!el || el.type === "file") return;
    el.value = data[k] || "";
  });
  
  // Mostrar previsualizaciones de imágenes
  if (data.logo_path) {
    const logoPreview = document.getElementById("preview_logo_empresa");
    logoPreview.src = data.logo_path;
    logoPreview.classList.remove("d-none");
  }
  
  if (data.firma_path) {
    const firmaPreview = document.getElementById("preview_firma_imagen");
    firmaPreview.src = data.firma_path;
    firmaPreview.classList.remove("d-none");
  }
}

function clearForm() {
  document.getElementById("patientForm").reset();
  document.getElementById("patient_id").value = "";
  editId = null;
}

function showAlert(message, type = "success") {
  const alertDiv = document.createElement("div");
  alertDiv.className = `alert alert-${type} alert-dismissible fade show alert-float`;
  alertDiv.innerHTML = `
    ${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  `;
  document.body.appendChild(alertDiv);
  
  setTimeout(() => {
    alertDiv.remove();
  }, 3000);
}

// ------------------------- API Calls ------------------------
async function saveRecord() {
  const form = document.getElementById("patientForm");
  const formData = new FormData(form);
  const data = {};
  
  for (let [key, value] of formData.entries()) {
    data[key] = value;
  }
  
  if (!data.nombre_completo) {
    showAlert("Debe ingresar al menos el nombre del paciente", "danger");
    return;
  }
  
  try {
    const response = await fetch("api/patients.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(data)
    });
    
    const result = await response.json();
    
    if (result.success) {
      showAlert(result.message);
      clearForm();
      refreshList();
    } else {
      showAlert(result.message, "danger");
    }
  } catch (error) {
    showAlert("Error al guardar: " + error.message, "danger");
  }
}

async function saveTherapistData() {
  const form = document.getElementById("therapistForm");
  const formData = new FormData(form);
  
  try {
    const response = await fetch("api/therapist.php", {
      method: "POST",
      body: formData
    });
    
    const result = await response.json();
    
    if (result.success) {
      showAlert(result.message);
      loadTherapistData();
    } else {
      showAlert(result.message, "danger");
    }
  } catch (error) {
    showAlert("Error al guardar: " + error.message, "danger");
  }
}

async function loadTherapistData() {
  try {
    const response = await fetch("api/therapist.php");
    const result = await response.json();
    
    if (result.success) {
      populateTherapistForm(result.data);
    }
  } catch (error) {
    console.error("Error al cargar datos del terapeuta:", error);
  }
}

async function generateDocx() {
  if (!editId) {
    showAlert("Debe seleccionar un paciente para generar el informe", "warning");
    return;
  }
  
  try {
    const response = await fetch("api/generate_docx.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ id: editId })
    });
    
    const result = await response.json();
    
    if (result.success) {
      showAlert("Documento generado correctamente");
      // Crear enlace de descarga
      const a = document.createElement("a");
      a.href = result.download_url;
      a.download = result.filename;
      a.click();
    } else {
      showAlert(result.message, "danger");
    }
  } catch (error) {
    showAlert("Error al generar documento: " + error.message, "danger");
  }
}

// ------------------------- Eventos ------------------------
document.addEventListener("DOMContentLoaded", function() {
  buildForm();
  buildTherapistForm();
  loadTherapistData();
  
  document.getElementById("btnGuardar").addEventListener("click", saveRecord);
  document.getElementById("btnLimpiar").addEventListener("click", clearForm);
  document.getElementById("btnDocx").addEventListener("click", generateDocx);
});