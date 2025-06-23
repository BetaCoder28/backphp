<?php
// Configuración CORS mejorada
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Authorization, Content-Type, Accept, Origin");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 3600");

// Manejar solicitud OPTIONS inmediatamente
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Content-Length: 0");
    header("Content-Type: text/plain");
    http_response_code(200);
    exit(0);
}

// Obtener la URL completa y parsear las rutas
$requestUri = $_SERVER['REQUEST_URI']; // Obtén la URL completa
$path = parse_url($requestUri, PHP_URL_PATH); // Extrae solo el path
$arrayRutas = explode('/', trim($path, '/')); // Divide la URL en partes

// Eliminar elementos vacíos y reindexar
if (is_array($arrayRutas)) {
    $arrayRutas = array_values(array_filter($arrayRutas));
} else {
    $arrayRutas = [];
}
// echo "<pre>"; print_r($arrayRutas); echo "</pre>";

if(isset($_GET["pagina"])&& is_numeric(($_GET['pagina']))){
    $arrayRutas = array_values(array_filter($arrayRutas));
    
    $cursos= new ControladorCursos();
    $cursos->index($_GET["pagina"]);
    
}

else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($arrayRutas[1]) && $arrayRutas[1] === 'clientes') {
    
    $datos = json_decode(file_get_contents('php://input'), true);
    
    $controladorClientes = new ControladorClientes();
    $controladorClientes->create($datos);
    
    return;
}
#Cursos CRUD
else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($arrayRutas[1]) && $arrayRutas[1] === 'cursos') {
    
    $datos = json_decode(file_get_contents('php://input'), true);
    
    $controladorClientes = new ControladorCursos();
    $controladorClientes->create($datos);
    
    return;
}

else if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($arrayRutas[1]) && $arrayRutas[1] === 'cursos' && isset($arrayRutas[2])) {
    
    $id = intval($arrayRutas[2]);
    $datos = json_decode(file_get_contents('php://input'), true);
    
    if(empty($datos)) {
        echo json_encode(array(
            "status" => 400,
            "detalle" => "Datos de actualización no recibidos"
        ), true);
        return;
    }
    
    $controladorCursos = new ControladorCursos();
    $controladorCursos->update($id, $datos);
    
    return;
}

else if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($arrayRutas[1]) && $arrayRutas[1] === 'cursos' && isset($arrayRutas[2])) {
    $id = intval($arrayRutas[2]);
    $controladorCursos = new ControladorCursos();
    $controladorCursos->delete($id);
    return;
}

else if (isset($arrayRutas[1]) && $arrayRutas[1] == "cursos" && isset($arrayRutas[2]) ) {
    
    $id = intval($arrayRutas[2]);
    
    $cursos = new ControladorCursos();
    $cursos->show($id);
    return;
}
############# ORDENES ############################
// GET /ordenes
else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($arrayRutas[1]) && $arrayRutas[1] === 'ordenes' && !isset($arrayRutas[2])) {
    $controlador = new ControladorOrdenes();
    $controlador->index();
    return;
}

else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($arrayRutas[1]) && $arrayRutas[1] === 'ordenes') {
    
    $datos = json_decode(file_get_contents('php://input'), true);
    
    $controladorClientes = new ControladorOrdenes();
    $controladorClientes->create($datos);
    
    return;
}

// GET /ordenes/{id}
else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($arrayRutas[1]) && $arrayRutas[1] === 'ordenes' && isset($arrayRutas[2])) {
    $id = intval($arrayRutas[2]);
    $controlador = new ControladorOrdenes();
    $controlador->show($id);
    return;
}

// PUT /ordenes/{id}
else if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($arrayRutas[1]) && $arrayRutas[1] === 'ordenes' && isset($arrayRutas[2])) {
    $id = intval($arrayRutas[2]);
    $datos = json_decode(file_get_contents('php://input'), true);
    $controlador = new ControladorOrdenes();
    $controlador->update($id, $datos);
    return;
}

// DELETE /ordenes/{id}
else if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($arrayRutas[1]) && $arrayRutas[1] === 'ordenes' && isset($arrayRutas[2])) {
    $id = intval($arrayRutas[2]);
    $controlador = new ControladorOrdenes();
    $controlador->delete($id);
    return;
}

// ############################## LOGIN
else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $arrayRutas[1] === 'login') {
    $datos = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($datos['email']) || !isset($datos['llave_secreta'])) {
        echo json_encode(array(
            "status" => 400,
            "detalle" => "Email y contraseña requeridos"
        ));
        return;
    }

    $controlador = new ControladorClientes();
    $controlador->login($datos['email'], $datos['llave_secreta']);
    return;
}
#########################################
else{
    if(count(array_filter($arrayRutas))==2) {
    }
}
