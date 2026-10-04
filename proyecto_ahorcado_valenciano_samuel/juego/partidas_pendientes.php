<?php
session_start();
$conexion = require_once __DIR__ . "/../conexion.php";
require_once __DIR__ . "/../logica_juego/partida.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../inicioSesion/login.php");
    exit();
}

$pendientes = partida::obtenerPendientes($conexion, $_SESSION['usuario_id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Partidas pendientes</title>
    <link rel="stylesheet" href="juego.css">
</head>
<body>

<h2>Partidas pendientes</h2>

<?php if (empty($pendientes)): ?>
    <p>No tenés partidas interrumpidas.</p>
<?php else: ?>
    <table border="1" cellpadding="8" style="margin: 0 auto;">
        <tr>
            <th>Dificultad</th>
            <th>Largo de palabra</th>
            <th>Intentos restantes</th>
            <th>Tiempo jugado</th>
            <th>Iniciada</th>
            <th></th>
        </tr>
        <?php foreach ($pendientes as $p): ?>
        <tr>
            <td><?php echo htmlspecialchars(ucfirst(strtolower($p['dificultad']))); ?></td>
            <td><?php echo (int)$p['largo']; ?></td>
            <td><?php echo (int)$p['intentos_restantes']; ?></td>
            <td><?php echo (int)$p['tiempo_segundos']; ?> seg</td>
            <td><?php echo htmlspecialchars($p['fecha_inicio']); ?></td>
            <td><a href="jugar.php?partida_id=<?php echo (int)$p['id']; ?>"><button type="button">Continuar</button></a></td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<br>
<a href="../inicioSesion/bienvenida.php" class="btn-accion">Volver al inicio</a>

</body>
</html>
