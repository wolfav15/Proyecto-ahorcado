<?php
session_start();
$conexion = require __DIR__ . "/../conexion.php";
require_once __DIR__ . "/../logica_juego/partida.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../inicioSesion/login.php");
    exit();
}

$partida_id = isset($_GET['partida_id']) ? (int)$_GET['partida_id'] : 0;

$partida = new Partida($conexion);

if (!$partida->cargarPartida($partida_id) || $partida->getUsuarioId() != $_SESSION['usuario_id']) {
    header("Location: partidas_pendientes.php");
    exit();
}

$letrasUsadas = array_merge($partida->getLetrasDescubiertas(), $partida->getLetrasFalladas());
$alfabeto = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','Ñ','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
$juegoTerminado = $partida->getEstado() !== 'EN_CURSO';

$ranking = [];
if ($juegoTerminado) {
    $ranking = partida::obtenerRanking($conexion, $partida->getDificultad(), 10);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ahorcado - Jugando</title>
    <link rel="stylesheet" href="juego.css">
</head>
<body>

<input type="hidden" id="partida_id" value="<?php echo (int)$partida->getId(); ?>">

<h2>Ahorcado — Dificultad: <?php echo htmlspecialchars(ucfirst(strtolower($partida->getDificultad()))); ?></h2>

<p id="timer" data-acumulado="<?php echo (int)$partida->getTiempoSegundos(); ?>">
    Tiempo jugado: <?php echo (int)$partida->getTiempoSegundos(); ?> seg
</p>

<h1 id="palabra-mascara"><?php echo htmlspecialchars($partida->obtenerMascara()); ?></h1>

<p>Intentos restantes: <span id="intentos-conteo"><?php echo (int)$partida->getIntentosRestantes(); ?></span></p>

<p>Letras falladas: <span id="letras-falladas"><?php echo htmlspecialchars(implode(", ", $partida->getLetrasFalladas())); ?></span></p>

<div id="teclado">
<?php foreach ($alfabeto as $letra): ?>
    <button
        type="button"
        class="tecla-ahorcado"
        <?php echo (in_array($letra, $letrasUsadas) || $juegoTerminado) ? "disabled" : ""; ?>
        onclick="enviarLetra(this, '<?php echo $letra; ?>')">
        <?php echo $letra; ?>
    </button>
<?php endforeach; ?>
</div>

<div id="acciones">
    <button type="button" id="btn-arriesgar" style="display: <?php echo $juegoTerminado ? 'none' : 'inline-block'; ?>;">Arriesgar palabra</button>
    <button type="button" id="btn-rendirse" style="display: <?php echo $juegoTerminado ? 'none' : 'inline-block'; ?>;">Darme por vencido</button>
    <button type="button" id="btn-pausar" style="display: <?php echo $juegoTerminado ? 'none' : 'inline-block'; ?>;">Pausar y salir</button>
</div>

<div id="mensaje-final" style="display: <?php echo $juegoTerminado ? 'block' : 'none'; ?>;">
<?php if ($juegoTerminado):
    if ($partida->getEstado() === 'GANADA'): ?>
        <h3> ¡Felicitaciones! Has ganado.</h3>
    <?php else: ?>
        <h3> No pudiste acertar. La palabra era: <strong><?php echo htmlspecialchars($partida->getPalabraTexto()); ?></strong></h3>
    <?php endif;
endif; ?>
</div>

<div id="ranking-contenedor">
<?php if ($juegoTerminado && !empty($ranking)): ?>
    <h3>Ranking (misma dificultad)</h3>
    <table border="1" cellpadding="6" style="margin: 10px auto;">
        <tr><th>#</th><th>Usuario</th><th>Tiempo</th></tr>
        <?php $puesto = 1; foreach ($ranking as $fila): ?>
        <tr>
            <td><?php echo $puesto; ?></td>
            <td><?php echo htmlspecialchars($fila['usuario']); ?></td>
            <td><?php echo (int)$fila['tiempo_segundos']; ?> seg</td>
        </tr>
        <?php $puesto++; endforeach; ?>
    </table>
<?php endif; ?>
</div>

<div id="opciones-finales" style="display: <?php echo $juegoTerminado ? 'block' : 'none'; ?>;">
    <a href="nuevo_juego.php"><button type="button">Jugar de nuevo</button></a>
    <a href="../inicioSesion/bienvenida.php"><button type="button">Volver al inicio</button></a>
</div>

<script src="../logica_juego/juego.js?v=<?php echo time(); ?>"></script>
</body>
</html>
