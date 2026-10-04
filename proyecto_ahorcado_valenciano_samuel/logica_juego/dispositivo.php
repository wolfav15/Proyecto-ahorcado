<?php
class Dispositivo {
    private $conexion;

    function __construct($conexion) {
        $this->conexion = $conexion;
    }


    static function obtenerIdentificador() {
        if (!isset($_COOKIE['device_id']) || !preg_match('/^[a-f0-9]{32}$/', $_COOKIE['device_id'])) {
            $device_id = bin2hex(random_bytes(16));
            setcookie('device_id', $device_id, time() + 60 * 60 * 24 * 365, '/');
            $_COOKIE['device_id'] = $device_id;
        }

        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'desconocido';

        return md5($_COOKIE['device_id'] . '|' . $user_agent);
    }


    function registrarPartida($usuario_id, $identificador, $partida_id) {
        $stmt = $this->conexion->prepare("SELECT id FROM dispositivos WHERE id_usuario = ? AND identificador_dispositivo = ?");
        $stmt->bind_param("is", $usuario_id, $identificador);
        $stmt->execute();
        $fila = $stmt->get_result()->fetch_assoc();

        if ($fila) {
            $upd = $this->conexion->prepare("UPDATE dispositivos SET ultima_partida = ? WHERE id = ?");
            $upd->bind_param("ii", $partida_id, $fila['id']);
            $upd->execute();
        } else {
            $ins = $this->conexion->prepare("INSERT INTO dispositivos (id_usuario, identificador_dispositivo, ultima_partida) VALUES (?, ?, ?)");
            $ins->bind_param("isi", $usuario_id, $identificador, $partida_id);
            $ins->execute();
        }
    }


    function obtenerUltimaPartida($usuario_id, $identificador) {
        $sql = "SELECT p.estado, p.fecha_inicio, p.fecha_fin
                FROM dispositivos d
                JOIN partidas p ON d.ultima_partida = p.id_partida
                WHERE d.id_usuario = ? AND d.identificador_dispositivo = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("is", $usuario_id, $identificador);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
