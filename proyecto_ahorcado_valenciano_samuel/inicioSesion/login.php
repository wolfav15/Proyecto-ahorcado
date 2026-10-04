<?php
session_start();
require_once __DIR__ . "/../logica_juego/dispositivo.php";
$conexion = require __DIR__ . "/../conexion.php";
require __DIR__ . "/Usuario.php";

$error = "";

if (isset($_POST["ingresar"])) {

    $usuario = trim($_POST["usuario"] ?? '');
    $password = $_POST["password"] ?? '';

    $usuarioClase = new Usuario($conexion);
    $datos = $usuarioClase->autenticar($usuario, $password);

    if ($datos) {

        $_SESSION["id"] = $datos["id_usuario"];
        $_SESSION["usuario_id"] = $datos["id_usuario"];
        $_SESSION["usuario"] = $datos["username"];

        Dispositivo::obtenerIdentificador(); // asegura la cookie de dispositivo

        header("Location: bienvenida.php");
        exit();

    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="sesion.css">
</head>
<body>

<div class="contenedor-login">
    <h2>Iniciar sesión</h2>

    <?php if (!empty($error)): ?>
        <p class="mensaje-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form method="POST">

        <div class="campo-form">
            <label for="usuario">Usuario</label>
            <input
                type="text"
                id="usuario"
                name="usuario"
                value="<?php echo isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>"
                required>
        </div>

        <div class="campo-form">
            <label for="password">Contraseña</label>
            <input
                type="password"
                id="password"
                name="password"
                required>
        </div>

        <input type="submit" name="ingresar" value="Ingresar">

    </form>

    <a href="registro.php" class="enlace-login">¿No tienes cuenta? Regístrate aquí</a>
</div>

</body>
</html>
