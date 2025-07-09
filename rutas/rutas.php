<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Authorization, Content-Type, Accept, Origin");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 3600");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Content-Length: 0");
    header("Content-Type: text/plain");
    http_response_code(200);
    exit(0);
}

function verificarCredenciales() {
    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
        http_response_code(401);
        echo json_encode(["status" => 401, "detalle" => "Credenciales no proporcionadas"]);
        exit;
    }

    $email = $_SERVER['PHP_AUTH_USER'];
    $password = $_SERVER['PHP_AUTH_PW'];

    $cliente = ModeloClientes::buscarPorEmail("clientes", $email);

    if (!$cliente || !password_verify($password, $cliente['llave_secreta'])) {
        http_response_code(401);
        echo json_encode(["status" => 401, "detalle" => "Credenciales inválidas"]);
        exit;
    }

    return $cliente;
}

$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);
$arrayRutas = explode('/', trim($path, '/'));

if (is_array($arrayRutas)) {
    $arrayRutas = array_values(array_filter($arrayRutas));
} else {
    $arrayRutas = [];
}

// Login endpoint (sin autenticación)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($arrayRutas[1]) && $arrayRutas[1] === 'login') {
    $datos = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($datos['email']) || !isset($datos['llave_secreta'])) {
        echo json_encode([
            "status" => 400,
            "detalle" => "Email y contraseña requeridos"
        ]);
        return;
    }

    $controlador = new ControladorClientes();
    $controlador->login($datos['email'], $datos['llave_secreta']);
    return;
}

// Creación de cliente (sin autenticación)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($arrayRutas[1]) && $arrayRutas[1] === 'clientes') {
    $datos = json_decode(file_get_contents('php://input'), true);
    $controladorClientes = new ControladorClientes();
    $controladorClientes->create($datos);
    return;
}

// Para todas las demás rutas, requerir autenticación
$cliente = verificarCredenciales();

// Rutas protegidas
if (isset($_GET["pagina"]) && is_numeric($_GET['pagina'])) {
    $cursos = new ControladorCursos();
    $cursos->index($_GET["pagina"]);
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($arrayRutas[1]) && $arrayRutas[1] === 'cursos') {
    $datos = json_decode(file_get_contents('php://input'), true);
    $controladorCursos = new ControladorCursos();
    $controladorCursos->create($datos, $cliente);
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($arrayRutas[1]) && $arrayRutas[1] === 'cursos' && isset($arrayRutas[2])) {
    $id = intval($arrayRutas[2]);
    $datos = json_decode(file_get_contents('php://input'), true);
    $controladorCursos = new ControladorCursos();
    $controladorCursos->update($id, $datos, $cliente);
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($arrayRutas[1]) && $arrayRutas[1] === 'cursos' && isset($arrayRutas[2])) {
    $id = intval($arrayRutas[2]);
    $controladorCursos = new ControladorCursos();
    $controladorCursos->delete($id, $cliente);
    return;
}

if (isset($arrayRutas[1]) && $arrayRutas[1] == "cursos" && isset($arrayRutas[2])) {
    $id = intval($arrayRutas[2]);
    $cursos = new ControladorCursos();
    $cursos->show($id);
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($arrayRutas[1]) && $arrayRutas[1] === 'ordenes' && !isset($arrayRutas[2])) {
    $controlador = new ControladorOrdenes();
    $controlador->index($cliente['id']);
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($arrayRutas[1]) && $arrayRutas[1] === 'ordenes') {
    $datos = json_decode(file_get_contents('php://input'), true);
    $controladorOrdenes = new ControladorOrdenes();
    $controladorOrdenes->create($datos, $cliente);
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($arrayRutas[1]) && $arrayRutas[1] === 'ordenes' && isset($arrayRutas[2])) {
    $id = intval($arrayRutas[2]);
    $controlador = new ControladorOrdenes();
    $controlador->show($id, $cliente['id']);
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($arrayRutas[1]) && $arrayRutas[1] === 'ordenes' && isset($arrayRutas[2])) {
    $id = intval($arrayRutas[2]);
    $datos = json_decode(file_get_contents('php://input'), true);
    $controlador = new ControladorOrdenes();
    $controlador->update($id, $datos, $cliente['id']);
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($arrayRutas[1]) && $arrayRutas[1] === 'ordenes' && isset($arrayRutas[2])) {
    $id = intval($arrayRutas[2]);
    $controlador = new ControladorOrdenes();
    $controlador->delete($id, $cliente['id']);
    return;
}

////////////////////////////// YOOUTUBE ////////////////

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($arrayRutas[1]) && $arrayRutas[1] === 'youtube' && isset($arrayRutas[2])) {
    $index = intval($arrayRutas[2]);
    $controlador = new YoutubeControlador();
    $controlador->GetVideoByIndex($index);
    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($arrayRutas[1]) && $arrayRutas[1] === 'youtube') {
    $controlador = new YoutubeControlador();
    $controlador->GetVideo();
    return;
}

http_response_code(404);
echo json_encode([
    "status" => 404,
    "detalle" => "Ruta no encontrada"
]);
?>