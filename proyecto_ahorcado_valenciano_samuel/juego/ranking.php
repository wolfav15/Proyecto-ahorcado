<?php
session_start();
$conexion = require_once __DIR__ . "/../conexion.php";
require_once __DIR__ . "/../logica_juego/partida.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../inicioSesion/login.php");
    exit();
}

$rankingBaja = partida::obtenerRanking($conexion, 'BAJA', 10);
$rankingMedia = partida::obtenerRanking($conexion, 'MEDIA', 10);
$rankingAlta = partida::obtenerRanking($conexion, 'ALTA', 10);

function mostrarTabla($titulo, $ranking) {
    echo "<h3>$titulo</h3>";

    if (empty($ranking)) {
        echo "<p>Todavía no hay partidas ganadas en esta dificultad.</p>";
        return;
    }

    echo "<table border='1' cellpadding='8' style='margin: 0 auto 30px auto;'>";
    echo "<tr><th>#</th><th>Usuario</th><th>Tiempo</th><th>Fecha</th></tr>";

    $puesto = 1;
    foreach ($ranking as $fila) {
        echo "<tr>";
        echo "<td>" . $puesto . "</td>";
        echo "<td>" . htmlspecialchars($fila['usuario']) . "</td>";
        echo "<td>" . (int)$fila['tiempo_segundos'] . " seg</td>";
        echo "<td>" . htmlspecialchars($fila['fecha_fin']) . "</td>";
        echo "</tr>";
        $puesto++;
    }

    echo "</table>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ranking de jugadores</title>
    <link rel="stylesheet" href="juego.css">
</head>
<body>

<h2>Ranking de jugadores</h2>
<p>Los mejores tiempos para acertar una palabra, por dificultad.</p>

<?php
mostrarTabla("Dificultad baja", $rankingBaja);
mostrarTabla("Dificultad media", $rankingMedia);
mostrarTabla("Dificultad alta", $rankingAlta);
?>

<a href="../inicioSesion/bienvenida.php" class="btn-accion">Volver al inicio</a>

</body>
</html>
