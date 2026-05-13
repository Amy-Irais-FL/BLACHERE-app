let destino = "";

let opcionesOrigen = ["","Soporte","Drive","SAP","Internet","Respaldos","Control de accesos","Camaras","Otros"];
let opcionesUrgencia = ["","Alta","Media","Baja"];
let opcionesEstado = ["","Sin empezar","Terminado","Cancelado","Pendiente"];

//  MAYÚSCULA
function capitalizarOracion(texto){
    texto = texto.trim().toLowerCase();
    return texto.charAt(0).toUpperCase() + texto.slice(1);
}

//  GUARDAR REGISTRO 
let enviando = false;

function guardar(){
    if(enviando) return;
    enviando = true;

    let boton = document.getElementById("btnGuardar");
    let mensaje = document.getElementById("mensaje");

    boton.disabled = true;
    boton.innerText = "Guardando... ⏳";

    let inputObs = document.getElementById("obs_solicitante1");
    let observaciones = capitalizarOracion(inputObs.value.trim());

    let origen = document.getElementById("origen").value.trim();
    let urgencia = document.querySelector('input[name="urgente"]:checked')?.value || "";

    // 🔴 VALIDACIÓN
    if(observaciones === "" || origen === "" || urgencia === ""){
        mensaje.innerText = "❌ Completa todos los campos";
        mensaje.style.color = "red";

        boton.disabled = false;
        boton.innerText = "Guardar";
        enviando = false;
        return;
    }

    inputObs.value = observaciones;

    fetch("https://blacheres-app.onrender.com/sesion.php")
    .then(res => res.json())
    .then(data => {
        if(data === "NO"){
            mensaje.innerText = "❌ Sesión no válida";
            boton.disabled = false;
            boton.innerText = "Guardar";
            enviando = false;
            return;
        }

        let solicitante = data.usuario; 
        enviarDatos(solicitante, urgencia, origen, observaciones, boton, mensaje);
    });
}

function enviarDatos(solicitante, urgencia, origen, observaciones, boton, mensaje){
    fetch("https://blacheres-app.onrender.com/guardar.php", {
        method: "POST",
        headers: {"Content-Type":"application/json"},
        body: JSON.stringify({
            solicitante,
            urgencia,
            origen,
            observaciones
        })
    })
    .then(res => res.text())
    .then(data => {
        if(data === "OK"){
            mensaje.innerText = "✅ Registro guardado";
            mensaje.style.color = "green";
            limpiarFormulario();
        } 
        else if(data === "DUPLICADO"){
            mensaje.innerText = "⚠️ Registro repetido";
            mensaje.style.color = "orange";
        }
        else{
            mensaje.innerText = "❌ Error: " + data;
            mensaje.style.color = "red";
        }
        boton.disabled = false;
        boton.innerText = "Guardar";
        enviando = false;

        setTimeout(()=>{
            mensaje.innerText = "";
        },3000);

    })
    .catch(()=>{
        mensaje.innerText = "❌ Error de conexión";
        mensaje.style.color = "red";

        boton.disabled = false;
        boton.innerText = "Guardar";
        enviando = false;
    });
}

//  VERIFICAR SESIÓN
function verificarSesion(){
    fetch("https://blacheres-app.onrender.com/sesion.php")
    .then(res => res.text())
    .then(data => {
        console.log("RESPUESTA:", data);
        if(data === "NO"){
            abrirLogin("lista");
        }
    })
    .catch(()=>{
        console.log("Error verificando sesión");
    });
}
fetch("https://blacheres-app.onrender.com/sesion.php")
.then(res => res.json())
.then(data => {
    if(data === "NO"){
        window.location.href = "index.html";
        return;
    }
    let tipo = data.tipo;
    let sidebar = document.getElementById("sidebar");
    if(tipo === "usuario2"){
        sidebar.innerHTML = `
            <div class="sidebar-header">
                <span class="close-btn" onclick="toggleMenu()">✖</span>
            </div>
            <a href="#">Registro</a>
            <a href="usuarios/mis_solicitudes.html">Mis solicitudes</a>
           <a href="#" onclick="cerrarSesion()">Cerrar sesión</a>
        `;
    }

    if(tipo === "admin"){
        sidebar.innerHTML = `
            <div class="sidebar-header">
                <span class="close-btn" onclick="toggleMenu()">✖</span>
            </div>
            <a href="#">Registro</a>
            <a href="Solicitante1.html">Lista de registro</a>
            <a href="perfil.html">Perfil</a>
            <a href="usuarios/ver_usuarios.html">Usuarios</a>
            <a href="#" onclick="cerrarSesion()">Cerrar sesión</a>
        `;
    }

});

function cerrarSesion(){
    document.getElementById("logoutModal").style.display = "flex";
}
function cerrarLogout(){
    document.getElementById("logoutModal").style.display = "none";
}
function confirmarLogout(){
    fetch("https://blacheres-app.onrender.com/logout.php")
    .then(()=>{
        localStorage.clear();
        window.location.href = "index.html";
    })
    .catch(()=>{
        alert("Error al cerrar sesión");
    });
}

//  MENÚ 
function toggleMenu(){
    let sidebar = document.getElementById("sidebar");
    if(sidebar.style.left === "0px"){
        sidebar.style.left = "-260px";
    }else{
        sidebar.style.left = "0px";
    }
}

//  LIMPIAR FORMULARIO
function limpiarFormulario(){
    let o = document.getElementById("obs_solicitante1");
    let origen = document.getElementById("origen");
    if(o) o.value = "";
    if(origen) origen.value = "";
    document.querySelectorAll('input[name="urgente"]').forEach(r => r.checked = false);
}

//  AUTOLLENAR USUARIO 
function cargarUsuario(){

    fetch("https://blacheres-app.onrender.com/sesion.php")
   .then(res => res.json())
    .then(data => {
        if(data !== "NO"){
            let input = document.getElementById("solicitante1");
            if(input){
                input.value = data.usuario; 
                input.readOnly = true;
                input.style.background = "#e9ecef";
            }
        }
    });
}

window.onload = function(){
    cargarUsuario();
};