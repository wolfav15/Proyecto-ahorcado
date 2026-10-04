document.addEventListener("DOMContentLoaded", () => {
    const password = document.getElementById("password");
    const form = document.getElementById("formRegistro");
    const btnRegistrar = document.getElementById("btnRegistrar");
    const usuario = document.getElementById("usuario");

    const largo = document.getElementById("largo");
    const mayuscula = document.getElementById("mayuscula");
    const minuscula = document.getElementById("minuscula");
    const numero = document.getElementById("numero");

    function validarPassword(valor) {
        return valor.length >= 8 && /[A-Z]/.test(valor) && /[a-z]/.test(valor) && /[0-9]/.test(valor);
    }

    function validarUsuarioFormato(valor) {
        return valor.length >= 8 && valor.length <= 20 && /^[A-Za-z0-9]+$/.test(valor);
    }

    function actualizarValidacionPassword(valor) {
        if (!password) {
            return;
        }

        // Se agrega la clase 'icono' a las etiquetas creadas dinámicamente por JS
        const imagenOk = "<img src='../assets/ok.jpg' alt='ok' class='icono'>";
        const imagenNo = "<img src='../assets/no.jpg' alt='no' class='icono'>";

        if (largo) {
            const esValido = valor.length >= 8;
            largo.innerHTML = `${esValido ? imagenOk : imagenNo} Mínimo 8 caracteres`;
            largo.className = esValido ? 'valido' : 'invalido';
        }

        if (mayuscula) {
            const esValido = /[A-Z]/.test(valor);
            mayuscula.innerHTML = `${esValido ? imagenOk : imagenNo} Una letra mayúscula`;
            mayuscula.className = esValido ? 'valido' : 'invalido';
        }

        if (minuscula) {
            const esValido = /[a-z]/.test(valor);
            minuscula.innerHTML = `${esValido ? imagenOk : imagenNo} Una letra minúscula`;
            minuscula.className = esValido ? 'valido' : 'invalido';
        }

        if (numero) {
            const esValido = /[0-9]/.test(valor);
            numero.innerHTML = `${esValido ? imagenOk : imagenNo} Un número`;
            numero.className = esValido ? 'valido' : 'invalido';
        }

        if (btnRegistrar) {
            btnRegistrar.disabled = !validarPassword(valor);
        }
    }

    if (password) {
        password.addEventListener("input", () => {
            actualizarValidacionPassword(password.value);
        });

        actualizarValidacionPassword(password.value);
    }

    if (form && password) {
        form.addEventListener("submit", (event) => {
            if (!validarPassword(password.value)) {
                event.preventDefault();
                alert("La contraseña debe tener mínimo 8 caracteres, una mayúscula, una minúscula y un número.");
                return;
            }

            if (usuario && !validarUsuarioFormato(usuario.value.trim())) {
                event.preventDefault();
                alert("El nombre de usuario debe tener entre 8 y 20 caracteres alfanuméricos, sin símbolos.");
            }
        });
    }
});
