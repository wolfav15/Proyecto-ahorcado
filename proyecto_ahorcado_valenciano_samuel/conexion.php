<?php

$host = "localhost";
$bd = "ahorcaditosBD";
$usuario_bd = "root";
$clave_bd = "";

$conexion = mysqli_connect($host, $usuario_bd, $clave_bd, $bd);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8mb4");

return $conexion;
?>
