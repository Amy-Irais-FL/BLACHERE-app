function mostrarRegistro(){
    document.getElementById("loginBox").style.display = "none";
    document.getElementById("registroBox").style.display = "block";
    document.getElementById("titulo").innerText = "Crear cuenta";
    document.getElementById("mensajeLogin").innerText = "";
    document.getElementById("mensajeRegistro").innerText = "";
}

function mostrarLogin(){
    document.getElementById("loginBox").style.display = "block";
    document.getElementById("registroBox").style.display = "none";
    document.getElementById("titulo").innerText = "Iniciar sesión";
    document.getElementById("mensajeLogin").innerText = "";
    document.getElementById("mensajeRegistro").innerText = "";
}

function login(){
    let usuario = document.getElementById("usuario").value.trim();
    let password = document.getElementById("password").value.trim();
    let mensaje = document.getElementById("mensajeLogin");
    mensaje.innerText = "";
    if(usuario === "" || password === ""){
        mensaje.innerText = "❌ Completa todos los campos";
        return;
    }
    fetch("https://blacheres-app.onrender.com/usuarios/login.php", {
        method: "POST",
        headers:{
            "Content-Type":"application/json"
        },
        body: JSON.stringify({
            accion: "login",
            usuario,
            password
        })
    })
    .then(res => res.json())
    .then(data => {
        if(data.estado === "OK"){
            localStorage.setItem("usuario", data.usuario);
            localStorage.setItem("tipo", data.tipo);
            if(data.tipo === "usuario2"){
                window.location.href = "sistemas.html";
            }
            if(data.tipo === "admin"){
                window.location.href = "Solicitante1.html";
            }
        }else{
            mensaje.innerText = "❌ Datos incorrectos";
        }
    })
    .catch(()=>{
        mensaje.innerText = "❌ Error de conexión";
    });
}

function registrar(){
    let usuario = document.getElementById("newUsuario").value.trim();
    let pass = document.getElementById("newPassword").value.trim();
    let confirm = document.getElementById("confirmPassword").value.trim();
    let mensaje = document.getElementById("mensajeRegistro");
    mensaje.innerText = "";
    if(usuario === "" || pass === "" || confirm === ""){
        mensaje.innerText = "❌ Completa todos los campos";
        return;
    }
    if(pass !== confirm){
        mensaje.innerText = "❌ Las contraseñas no coinciden";
        return;
    }
    fetch("https://blacheres-app.onrender.com/usuarios/usuarios2.php", {
        method: "POST",
        headers:{"Content-Type":"application/json"},
        body: JSON.stringify({
            accion: "registro",
            usuario,
            password: pass
        })
    })
    .then(res => res.json())
    .then(data => {
        if(data.estado === "OK"){
            document.getElementById("mensajeRegistro").innerText =
            "✅ Cuenta creada correctamente";
            setTimeout(() => {
                mostrarLogin();
                document.getElementById("usuario").value = usuario;
                document.getElementById("password").focus();
            }, 1000);
        }else{
            mensaje.innerText =
            data.error || "❌ Error al crear cuenta";
        }
    })
    .catch(()=>{
        mensaje.innerText = "❌ Error de conexión";
    });
}

function togglePass(id, icon){
    let input = document.getElementById(id);
    if(input.type === "password"){
        input.type = "text";
        icon.innerText = "👀";
    }else{
        input.type = "password";
        icon.innerText = "🙈";
    }
}

document.getElementById("usuario").addEventListener("keypress", function(e){
    if(e.key === "Enter"){
        login();
    }
});
document.getElementById("password").addEventListener("keypress", function(e){
    if(e.key === "Enter"){
        login();
    }
});

document.getElementById("newUsuario").addEventListener("keypress", function(e){
    if(e.key === "Enter"){
        registrar();
    }
});

document.getElementById("newPassword").addEventListener("keypress", function(e){
    if(e.key === "Enter"){
        registrar();
    }
});

document.getElementById("confirmPassword").addEventListener("keypress", function(e){
    if(e.key === "Enter"){
        registrar();
    }
});