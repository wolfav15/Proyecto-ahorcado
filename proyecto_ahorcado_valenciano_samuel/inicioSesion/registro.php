<?php
$conexion = require __DIR__ . "/../conexion.php";
require __DIR__ . "/Usuario.php";

$mensaje = "";

if(isset($_POST["registrar"])){

    $usuario = trim($_POST["usuario"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';
    $fecha = $_POST["fecha"] ?? '';
    $pais = trim($_POST["pais"] ?? '');

    $usuarioClase = new Usuario($conexion);
    $resultado = $usuarioClase->registrar($usuario, $email, $password, $fecha, $pais);

    if($resultado === true){
        header("Location: login.php?registro=ok");
        exit();
    }else{
        $mensaje = $resultado;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="sesion.css">
</head>
<body>

<div class="contenedor-registro">

    <h2>REGISTRO DE USUARIO</h2>

    <?php if($mensaje != ""): ?>
        <p class="mensaje-error"><?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form method="POST" id="formRegistro">

        <div class="campo-form">
            <label for="usuario">Usuario</label>
            <input
                type="text"
                id="usuario"
                name="usuario"
                pattern="[A-Za-z0-9]{8,20}"
                maxlength="20"
                title="Entre 8 y 20 caracteres alfanuméricos, sin símbolos."
                value="<?php echo isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>"
                required>
        </div>

        <div class="campo-form">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                required>
        </div>

        <div class="campo-form">
            <label for="password">Contraseña</label>
            <input
                type="password"
                id="password"
                name="password"
                required>

            <div id="validacion">
                <p id="largo"><img src="../assets/no.jpg" alt="icono" class="icono"> Mínimo 8 caracteres</p>
                <p id="mayuscula"><img src="../assets/no.jpg" alt="icono" class="icono"> Una letra mayúscula</p>
                <p id="minuscula"><img src="../assets/no.jpg" alt="icono" class="icono"> Una letra minúscula</p>
                <p id="numero"><img src="../assets/no.jpg" alt="icono" class="icono"> Un número</p>
            </div>
        </div>

        <div class="campo-form">
            <label for="fecha">Fecha de nacimiento</label>
            <input
                type="date"
                id="fecha"
                name="fecha"
                value="<?php echo isset($_POST['fecha']) ? htmlspecialchars($_POST['fecha']) : ''; ?>"
                required>
        </div>

        <div class="campo-form">
            <label for="pais">País</label>
            <input
                type="text"
                id="pais"
                name="pais"
                value="<?php echo isset($_POST['pais']) ? htmlspecialchars($_POST['pais']) : ''; ?>"
                required>
        </div>

        <input type="submit" id="btnRegistrar" name="registrar" value="REGISTRARSE">

    </form>

    <a href="login.php" class="enlace-login">Volver al inicio de sesión</a>

</div>

<script src="registro.js"></script>
</body>
</html>
