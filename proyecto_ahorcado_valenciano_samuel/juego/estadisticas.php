<?php
session_start();
$conexion = require_once __DIR__ . "/../conexion.php";
require_once __DIR__ . "/../logica_juego/partida.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../inicioSesion/login.php");
    exit();
}

$stats = partida::obtenerEstadisticas($conexion, $_SESSION['usuario_id']);

function formatearTiempo($segundos) {
    if ($segundos === null) return "-";
    return (int)$segundos . " seg";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis estadísticas</title>
    <link rel="stylesheet" href="juego.css">
</head>
<body>

<h2>Mis estadísticas</h2>

<table border="1" cellpadding="8" style="margin: 0 auto;">
    <tr>
        <th>Dificultad</th>
        <th>Palabras adivinadas</th>
        <th>Tiempo mínimo</th>
        <th>Tiempo máximo</th>
    </tr>
    <?php foreach (['baja', 'media', 'alta'] as $dificultad): ?>
    <tr>
        <td><?php echo ucfirst($dificultad); ?></td>
        <td><?php echo (int)$stats[$dificultad]['total_adivinadas']; ?></td>
        <td><?php echo formatearTiempo($stats[$dificultad]['tiempo_min']); ?></td>
        <td><?php echo formatearTiempo($stats[$dificultad]['tiempo_max']); ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<br>
<a href="../inicioSesion/bienvenida.php" class="btn-accion">Volver al inicio</a>

</body>
</html>
