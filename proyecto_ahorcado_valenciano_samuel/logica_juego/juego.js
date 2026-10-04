// juego/juego.js
document.addEventListener("DOMContentLoaded", () => {
    const partidaId = document.getElementById("partida_id").value;
    const mascaraContenedor = document.getElementById("palabra-mascara");
    const intentosContenedor = document.getElementById("intentos-conteo");
    const letrasFalladasContenedor = document.getElementById("letras-falladas");
    const timerContenedor = document.getElementById("timer");
    const rankingContenedor = document.getElementById("ranking-contenedor");

    let tiempoTurno = 0;

    setInterval(() => {
        tiempoTurno++;
        const tiempoAcumulado = parseInt(timerContenedor.dataset.acumulado) + tiempoTurno;
        timerContenedor.textContent = `Tiempo jugado: ${tiempoAcumulado} seg`;
    }, 1000);

    window.enviarLetra = (letraBoton, letra) => {
        letraBoton.disabled = true;

        realizarJugada({
            partida_id: partidaId,
            accion: 'letra',
            letra: letra,
            tiempo_turno: tiempoTurno
        });

        tiempoTurno = 0;
    };

    const btnArriesgar = document.getElementById("btn-arriesgar");
    if (btnArriesgar) {
        btnArriesgar.addEventListener("click", () => {
            const palabra = prompt("Escribe la palabra completa que crees que es:");
            if (palabra && palabra.trim() !== "") {
                realizarJugada({
                    partida_id: partidaId,
                    accion: 'arriesgar',
                    palabra: palabra.trim(),
                    tiempo_turno: tiempoTurno
                });
                tiempoTurno = 0;
            }
        });
    }

    const btnRendirse = document.getElementById("btn-rendirse");
    if (btnRendirse) {
        btnRendirse.addEventListener("click", () => {
            if (confirm("¿Estás seguro de que deseas darte por vencido?")) {
                realizarJugada({
                    partida_id: partidaId,
                    accion: 'rendirse',
                    tiempo_turno: tiempoTurno
                });
                tiempoTurno = 0;
            }
        });
    }

    const btnPausar = document.getElementById("btn-pausar");
    if (btnPausar) {
        btnPausar.addEventListener("click", () => {
            realizarJugada({
                partida_id: partidaId,
                accion: 'pausar',
                tiempo_turno: tiempoTurno
            }, () => {
                window.location.href = "../inicioSesion/bienvenida.php";
            });
            tiempoTurno = 0;
        });
    }

    function realizarJugada(datos, callback) {
        const params = new URLSearchParams();
        for (const clave in datos) {
            params.append(clave, datos[clave]);
        }

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "../logica_juego/procesar_juego.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = () => {
            if (xhr.readyState !== 4) {
                return;
            }

            if (xhr.status !== 200) {
                alert("No se pudo comunicar con el servidor.");
                return;
            }

            const data = JSON.parse(xhr.responseText);

            if (data.error) {
                alert(data.error);
                return;
            }

            mascaraContenedor.textContent = data.mascara;
            intentosContenedor.textContent = data.intentos_restantes;
            letrasFalladasContenedor.textContent = data.letras_falladas.join(", ");
            timerContenedor.dataset.acumulado = data.tiempo_total;

            if (data.estado !== 'EN_CURSO') {
                finalizarPartidaPantalla(data.estado, data.palabra_secreta, data.ranking);
            }

            if (callback) {
                callback();
            }
        };

        xhr.send(params.toString());
    }

    function finalizarPartidaPantalla(estado, palabraSecreta, ranking) {
        document.querySelectorAll(".tecla-ahorcado").forEach(b => { b.disabled = true; });
        document.getElementById("btn-arriesgar").style.display = "none";
        document.getElementById("btn-rendirse").style.display = "none";
        document.getElementById("btn-pausar").style.display = "none";

        const mensajeFinal = document.getElementById("mensaje-final");
        mensajeFinal.style.display = "block";

        if (estado === 'GANADA') {
            mensajeFinal.innerHTML = `<h3>¡Felicitaciones! Has ganado.</h3>`;
        } else {
            mensajeFinal.innerHTML = `<h3> No pudiste acertar. La palabra era: <strong>${palabraSecreta}</strong></h3>`;
        }

        if (rankingContenedor && ranking && ranking.length > 0) {
            const filas = ranking.map((r, i) => `
                <tr>
                    <td>${i + 1}</td>
                    <td>${r.usuario}</td>
                    <td>${r.tiempo_segundos} seg</td>
                </tr>
            `).join("");

            rankingContenedor.innerHTML = `
                <h3>Ranking (misma dificultad)</h3>
                <table border="1" cellpadding="6" style="margin: 10px auto;">
                    <tr><th>#</th><th>Usuario</th><th>Tiempo</th></tr>
                    ${filas}
                </table>
            `;
        }

        document.getElementById("opciones-finales").style.display = "block";
    }
});
