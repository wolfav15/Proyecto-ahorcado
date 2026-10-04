<?php

class Partida {
    private $conexion;

    private $id;
    private $usuario_id;
    private $palabra_id;
    private $palabra_texto;
    private $dificultad;
    private $estado;
    private $intentos_restantes;
    private $tiempo_segundos;
    private $fecha_inicio;
    private $fecha_fin;

    function __construct($conexion) {
        $this->conexion = $conexion;
    }

    function nuevaPartida($usuario_id, $dificultad) {
        $stmt = $this->conexion->prepare("SELECT id_palabra, palabra FROM palabras WHERE dificultad = ? ORDER BY RAND() LIMIT 1");
        $stmt->bind_param("s", $dificultad);
        $stmt->execute();
        $palabra = $stmt->get_result()->fetch_assoc();

        if (!$palabra) {
            return false;
        }

        $stmt = $this->conexion->prepare("INSERT INTO partidas (id_usuario, id_palabra, fecha_inicio, estado, oportunidades_restantes, tiempo_segundos) VALUES (?, ?, NOW(), 'EN_CURSO', 10, 0)");
        $stmt->bind_param("ii", $usuario_id, $palabra['id_palabra']);
        $stmt->execute();

        $this->id = $this->conexion->insert_id;
        $this->usuario_id = $usuario_id;
        $this->palabra_id = $palabra['id_palabra'];
        $this->palabra_texto = mb_strtoupper($palabra['palabra'], 'UTF-8');
        $this->dificultad = $dificultad;
        $this->estado = 'EN_CURSO';
        $this->intentos_restantes = 10;
        $this->letras_descubiertas = [];
        $this->letras_falladas = [];
        $this->tiempo_segundos = 0;

        return true;
    }

    function cargarPartida($partida_id) {
        $sql = "SELECT p.*, pal.palabra, pal.dificultad
                FROM partidas p
                JOIN palabras pal ON p.id_palabra = pal.id_palabra
                WHERE p.id_partida = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $partida_id);
        $stmt->execute();
        $datos = $stmt->get_result()->fetch_assoc();

        if (!$datos) {
            return false;
        }

        $this->id = $datos['id_partida'];
        $this->usuario_id = $datos['id_usuario'];
        $this->palabra_id = $datos['id_palabra'];
        $this->palabra_texto = mb_strtoupper($datos['palabra'], 'UTF-8');
        $this->dificultad = $datos['dificultad'];
        $this->estado = $datos['estado'];
        $this->intentos_restantes = (int)$datos['oportunidades_restantes'];
        $this->tiempo_segundos = (int)$datos['tiempo_segundos'];
        $this->fecha_inicio = $datos['fecha_inicio'];
        $this->fecha_fin = $datos['fecha_fin'];

        $stmtLetras = $this->conexion->prepare("SELECT letra, acierto FROM letras_intentadas WHERE id_partida = ?");
        $stmtLetras->bind_param("i", $this->id);
        $stmtLetras->execute();
        $filas = $stmtLetras->get_result();

        $this->letras_descubiertas = [];
        $this->letras_falladas = [];
        while ($fila = $filas->fetch_assoc()) {
            if ($fila['acierto']) {
                $this->letras_descubiertas[] = $fila['letra'];
            } else {
                $this->letras_falladas[] = $fila['letra'];
            }
        }

        return true;
    }

    function obtenerMascara() {
        $mascara = [];
        $caracteres = preg_split('//u', $this->palabra_texto, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($caracteres as $letra) {
            if (in_array($letra, $this->letras_descubiertas)) {
                $mascara[] = $letra;
            } else {
                $mascara[] = "_";
            }
        }
        return implode(" ", $mascara);
    }

    private function registrarLetraIntentada($letra, $acierto) {
        $valorAcierto = $acierto ? 1 : 0;
        $stmt = $this->conexion->prepare("INSERT INTO letras_intentadas (id_partida, letra, acierto) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $this->id, $letra, $valorAcierto);
        $stmt->execute();
    }

    function ingresarLetra($letra, $tiempo_transcurrido_turno) {
        if ($this->estado !== 'EN_CURSO') return;

        $letra = mb_strtoupper(trim($letra), 'UTF-8');
        $this->tiempo_segundos += (int)$tiempo_transcurrido_turno;

        if (in_array($letra, $this->letras_descubiertas) || in_array($letra, $this->letras_falladas)) {
            return;
        }

        if (mb_strpos($this->palabra_texto, $letra, 0, 'UTF-8') !== false) {
            $this->letras_descubiertas[] = $letra;
            $this->registrarLetraIntentada($letra, true);

            $gano = true;
            $caracteres = preg_split('//u', $this->palabra_texto, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($caracteres as $char) {
                if (!in_array($char, $this->letras_descubiertas)) {
                    $gano = false;
                    break;
                }
            }
            if ($gano) {
                $this->estado = 'GANADA';
            }
        } else {
            $this->letras_falladas[] = $letra;
            $this->registrarLetraIntentada($letra, false);
            $this->intentos_restantes--;

            if ($this->intentos_restantes <= 0) {
                $this->estado = 'PERDIDA';
            }
        }

        $this->guardarEstado();
    }

    function arriesgarPalabra($palabra_arriesgada, $tiempo_transcurrido_turno) {
        if ($this->estado !== 'EN_CURSO') return;

        $palabra_arriesgada = mb_strtoupper(trim($palabra_arriesgada), 'UTF-8');
        $this->tiempo_segundos += (int)$tiempo_transcurrido_turno;

        if ($this->palabra_texto === $palabra_arriesgada) {
            $this->estado = 'GANADA';
            $this->letras_descubiertas = preg_split('//u', $this->palabra_texto, -1, PREG_SPLIT_NO_EMPTY);
        } else {
            $this->estado = 'PERDIDA';
            $this->intentos_restantes = 0;
        }

        $this->guardarEstado();
    }

    function darsePorVencido($tiempo_transcurrido_turno) {
        if ($this->estado !== 'EN_CURSO') return;

        $this->tiempo_segundos += (int)$tiempo_transcurrido_turno;
        $this->estado = 'ABANDONADA';
        $this->guardarEstado();
    }
    function guardarProgreso($tiempo_transcurrido_turno) {
        $this->tiempo_segundos += (int)$tiempo_transcurrido_turno;
        $this->guardarEstado();
    }

    function guardarEstado() {
        $fecha_fin = ($this->estado !== 'EN_CURSO') ? date('Y-m-d H:i:s') : null;

        $stmt = $this->conexion->prepare("UPDATE partidas SET estado = ?, oportunidades_restantes = ?, tiempo_segundos = ?, fecha_fin = ? WHERE id_partida = ?");
        $stmt->bind_param("siisi", $this->estado, $this->intentos_restantes, $this->tiempo_segundos, $fecha_fin, $this->id);
        $stmt->execute();
    }

    function getId() { return $this->id; }
    function getUsuarioId() { return $this->usuario_id; }
    function getEstado() { return $this->estado; }
    function getPalabraTexto() { return $this->palabra_texto; }
    function getDificultad() { return $this->dificultad; }
    function getIntentosRestantes() { return $this->intentos_restantes; }
    function getLetrasDescubiertas() { return $this->letras_descubiertas; }
    function getLetrasFalladas() { return $this->letras_falladas; }
    function getTiempoSegundos() { return $this->tiempo_segundos; }
    function getFechaInicio() { return $this->fecha_inicio; }
    function getFechaFin() { return $this->fecha_fin; }


    static function obtenerPendientes($conexion, $usuario_id) {
        $sql = "SELECT p.id_partida AS id, p.oportunidades_restantes, p.tiempo_segundos, p.fecha_inicio, pal.dificultad,
                       CHAR_LENGTH(pal.palabra) AS largo
                FROM partidas p
                JOIN palabras pal ON p.id_palabra = pal.id_palabra
                WHERE p.id_usuario = ? AND p.estado = 'EN_CURSO'
                ORDER BY p.fecha_inicio DESC";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $lista = [];
        while ($fila = $resultado->fetch_assoc()) {
            $lista[] = $fila;
        }
        return $lista;
    }


    static function obtenerEstadisticas($conexion, $usuario_id) {
        $sql = "SELECT pal.dificultad,
                       COUNT(*) AS total_adivinadas,
                       MIN(p.tiempo_segundos) AS tiempo_min,
                       MAX(p.tiempo_segundos) AS tiempo_max
                FROM partidas p
                JOIN palabras pal ON p.id_palabra = pal.id_palabra
                WHERE p.id_usuario = ? AND p.estado = 'GANADA'
                GROUP BY pal.dificultad";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $estadisticas = [
            'baja'  => ['total_adivinadas' => 0, 'tiempo_min' => null, 'tiempo_max' => null],
            'media' => ['total_adivinadas' => 0, 'tiempo_min' => null, 'tiempo_max' => null],
            'alta'  => ['total_adivinadas' => 0, 'tiempo_min' => null, 'tiempo_max' => null],
        ];

        while ($fila = $resultado->fetch_assoc()) {
            $clave = strtolower($fila['dificultad']);
            $estadisticas[$clave] = [
                'total_adivinadas' => (int)$fila['total_adivinadas'],
                'tiempo_min' => $fila['tiempo_min'],
                'tiempo_max' => $fila['tiempo_max'],
            ];
        }

        return $estadisticas;
    }


    static function obtenerRanking($conexion, $dificultad, $limite = 10) {
        $sql = "SELECT u.username AS usuario, p.tiempo_segundos, p.fecha_fin
                FROM partidas p
                JOIN usuarios u ON p.id_usuario = u.id_usuario
                JOIN palabras pal ON p.id_palabra = pal.id_palabra
                WHERE p.estado = 'GANADA' AND pal.dificultad = ?
                ORDER BY p.tiempo_segundos ASC
                LIMIT ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("si", $dificultad, $limite);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $lista = [];
        while ($fila = $resultado->fetch_assoc()) {
            $lista[] = $fila;
        }
        return $lista;
    }
}
