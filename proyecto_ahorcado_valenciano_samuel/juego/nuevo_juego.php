<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../inicioSesion/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo juego</title>
    <link rel="stylesheet" href="juego.css">
</head>
<body>

<h2>Elegí la dificultad</h2>

<form method="POST" action="iniciar_partida.php">
    <label>
        <input type="radio" name="dificultad" value="baja" required> Baja (palabras de hasta 5 letras)
    </label><br><br>
    <label>
        <input type="radio" name="dificultad" value="media"> Media (palabras de 6 a 8 letras)
    </label><br><br>
    <label>
        <input type="radio" name="dificultad" value="alta"> Alta (palabras de más de 8 letras)
    </label><br><br>

    <input type="submit" value="Comenzar">
</form>

<a href="../inicioSesion/bienvenida.php" class="btn-accion">Volver al inicio</a>

</body>
</html>
