<?php

class Usuario {
    private $conexion;

    function __construct($conexion) {
        $this->conexion = $conexion;
    }

    function usuarioValido($usuario) {
        return preg_match('/^[A-Za-z0-9]{8,20}$/', $usuario);
    }

    function passwordValida($password) {
        return strlen($password) >= 8
            && preg_match('/[A-Z]/', $password)
            && preg_match('/[a-z]/', $password)
            && preg_match('/[0-9]/', $password);
    }

    function existe($usuario) {
        $stmt = $this->conexion->prepare("SELECT id_usuario FROM usuarios WHERE username = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    function existeEmail($email) {
        $stmt = $this->conexion->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }


    function registrar($usuario, $email, $password, $fecha, $pais) {
        if (!$this->usuarioValido($usuario)) {
            return "El nombre de usuario debe tener entre 8 y 20 caracteres alfanuméricos, sin símbolos.";
        }

        if (!$this->passwordValida($password)) {
            return "La contraseña debe tener mínimo 8 caracteres, una mayúscula, una minúscula y un número.";
        }

        if ($this->existe($usuario)) {
            return "El nombre de usuario ya existe.";
        }

        if ($this->existeEmail($email)) {
            return "Ya existe una cuenta registrada con ese email.";
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conexion->prepare("INSERT INTO usuarios (username, email, password, fecha_nacimiento, pais) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $usuario, $email, $hash, $fecha, $pais);

        if ($stmt->execute()) {
            return true;
        }

        return "No se pudo registrar.";
    }


    function autenticar($usuario, $password) {
        $stmt = $this->conexion->prepare("SELECT * FROM usuarios WHERE username = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $datos = $stmt->get_result()->fetch_assoc();

        if (!$datos) {
            return null;
        }

        if (!password_verify($password, $datos['password'])) {
            return null;
        }

        return $datos;
    }
}
