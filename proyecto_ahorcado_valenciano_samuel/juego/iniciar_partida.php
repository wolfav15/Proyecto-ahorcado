<?php
session_start();
require_once __DIR__ . "/../logica_juego/dispositivo.php";
$conexion = require_once __DIR__ . "/../conexion.php";
require_once __DIR__ . "/../logica_juego/partida.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../inicioSesion/login.php");
    exit();
}

$dificultad = strtolower($_POST['dificultad'] ?? '');

if (!in_array($dificultad, ['baja', 'media', 'alta'], true)) {
    header("Location: nuevo_juego.php");
    exit();
}

$identificador = Dispositivo::obtenerIdentificador();

$partida = new partida($conexion);

if ($partida->nuevaPartida($_SESSION['usuario_id'], strtoupper($dificultad))) {
    $dispositivoClase = new Dispositivo($conexion);
    $dispositivoClase->registrarPartida($_SESSION['usuario_id'], $identificador, $partida->getId());

    header("Location: jugar.php?partida_id=" . $partida->getId());
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

<p>No hay palabras cargadas para la dificultad seleccionada.</p>
<a href="nuevo_juego.php">Volver</a>

</body>
</html>
