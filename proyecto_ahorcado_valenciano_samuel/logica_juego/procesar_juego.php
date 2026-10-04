<?php
session_start();

$conexion = require_once __DIR__ . "/../conexion.php";
require_once __DIR__ . "/partida.php";

if (!isset($_SESSION['usuario_id']) || !isset($_POST['partida_id'])) {
    echo json_encode(['error' => 'Sesión o partida no válida.']);
    exit();
}

$partida_id = (int)$_POST['partida_id'];
$accion = $_POST['accion']; // 'letra', 'arriesgar', 'rendirse', 'pausar'
$tiempo_turno = isset($_POST['tiempo_turno']) ? (int)$_POST['tiempo_turno'] : 0;

$partida = new Partida($conexion);

if (!$partida->cargarPartida($partida_id) || $partida->getUsuarioId() != $_SESSION['usuario_id']) {
    echo json_encode(['error' => 'Partida no encontrada.']);
    exit();
}

if ($partida->getEstado() !== 'EN_CURSO' && $accion !== 'pausar') {
    echo json_encode(['error' => 'Esta partida ya ha finalizado.']);
    exit();
}

switch ($accion) {
    case 'letra':
        if (isset($_POST['letra'])) {
            $partida->ingresarLetra($_POST['letra'], $tiempo_turno);
        }
        break;

    case 'arriesgar':
        if (isset($_POST['palabra'])) {
            $partida->arriesgarPalabra($_POST['palabra'], $tiempo_turno);
        }
        break;

    case 'rendirse':
        $partida->darsePorVencido($tiempo_turno);
        break;

    case 'pausar':
        $partida->guardarProgreso($tiempo_turno);
        break;
}
$ranking = [];
if ($partida->getEstado() !== 'EN_CURSO') {
    $ranking = partida::obtenerRanking($conexion, $partida->getDificultad(), 10);
}

echo json_encode([
    'estado' => $partida->getEstado(),
    'mascara' => $partida->obtenerMascara(),
    'intentos_restantes' => $partida->getIntentosRestantes(),
    'letras_falladas' => $partida->getLetrasFalladas(),
    'tiempo_total' => $partida->getTiempoSegundos(),
    'palabra_secreta' => ($partida->getEstado() !== 'EN_CURSO') ? $partida->getPalabraTexto() : null,
    'ranking' => $ranking
]);
