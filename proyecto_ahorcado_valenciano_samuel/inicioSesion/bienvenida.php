<?php
session_start();
require_once __DIR__ . "/../logica_juego/dispositivo.php";
$conexion = require __DIR__ . "/../conexion.php";
require_once __DIR__ . "/../logica_juego/partida.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}

$identificador = Dispositivo::obtenerIdentificador();
$dispositivoClase = new Dispositivo($conexion);
$ultimaPartida = $dispositivoClase->obtenerUltimaPartida($_SESSION["usuario_id"], $identificador);
$pendientes = partida::obtenerPendientes($conexion, $_SESSION["usuario_id"]);

$resultadosTexto = [
    'GANADA' => 'ganada',
    'PERDIDA' => 'perdida',
    'ABANDONADA' => 'abandonada (te diste por vencido)',
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="sesion.css">
</head>
<body>

<div class="contenedor-menu">
    <h2>¡Bienvenido, <?php echo htmlspecialchars($_SESSION["usuario"]); ?>!</h2>

    <?php if ($ultimaPartida): ?>
        <?php if ($ultimaPartida["estado"] === "EN_CURSO"): ?>
            <p>
                Tu último juego desde este dispositivo, iniciado el
                <strong><?php echo htmlspecialchars($ultimaPartida["fecha_inicio"]); ?></strong>,
                todavía está en curso.
            </p>
        <?php else: ?>
            <p>
                Tu última partida en este dispositivo fue el
                <strong><?php echo htmlspecialchars($ultimaPartida["fecha_fin"]); ?></strong>
                y el resultado fue:
                <strong><?php echo htmlspecialchars($resultadosTexto[$ultimaPartida["estado"]] ?? $ultimaPartida["estado"]); ?></strong>.
            </p>
        <?php endif; ?>
    <?php else: ?>
        <p>Todavía no jugaste ninguna partida desde este dispositivo.</p>
    <?php endif; ?>

    <?php if (!empty($pendientes)): ?>
        <p class="alerta-pendientes">Tenés <?php echo count($pendientes); ?> partida(s) interrumpida(s) esperando que las continúes.</p>
    <?php endif; ?>

    <div class="menu-acciones">
        <a href="../juego/nuevo_juego.php"><button type="button">Iniciar nuevo juego</button></a>
        <a href="../juego/partidas_pendientes.php"><button type="button">Retomar partidas pendientes</button></a>
        <a href="../juego/estadisticas.php"><button type="button">Ver mis estadísticas</button></a>
        <a href="../juego/ranking.php"><button type="button">Ver ranking de jugadores</button></a>
        <a href="logout.php"><button type="button" class="btn-salir">Cerrar sesión</button></a>
    </div>
</div>

</body>
</html>
