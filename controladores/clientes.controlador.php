<?php 

class ControladorClientes {

    // Listar clientes con paginación opcional (puedes adaptarlo a tu uso)
    public function index($pagina = null) {

        /*=============================================
        Validar credenciales del cliente
        =============================================*/

        $clientes = ModeloClientes::index("clientes");

        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {

            foreach ($clientes as $cliente) {

                if (base64_encode($_SERVER['PHP_AUTH_USER'] . ":" . $_SERVER['PHP_AUTH_PW']) == 
                    base64_encode($cliente->id_cliente . ":" . $cliente->llave_secreta)) {

                    if ($pagina != null) {

                        $cantidad = 10;
                        $desde = ($pagina - 1) * $cantidad;

                        // Aquí puedes hacer un método específico para paginar clientes si quieres
                        $clientesPaginados = ModeloClientes::index("clientes", $cantidad, $desde);

                    } else {
                        $clientesPaginados = ModeloClientes::index("clientes");
                    }

                    $json = array(
                        "status" => 200,
                        "total_registros" => count($clientesPaginados),
                        "detalle" => $clientesPaginados
                    );

                    echo json_encode($json, true);
                    return;
                }
            }
        }

        // Si no hay autenticación válida
        http_response_code(401);
        echo json_encode(array("status" => 401, "detalle" => "No autorizado"));
    }

    // LOGIN
    public function login($email, $llave_secreta) {
        $cliente = ModeloClientes::buscarPorEmail("clientes", $email);

        if ($cliente && password_verify($llave_secreta, $cliente['llave_secreta'])) {
            $payload = $cliente['id_cliente'] . ":" . time();
            $token = base64_encode($payload);

            echo json_encode(array(
                "status" => 200,
                "detalle" => "Login exitoso",
                "token" => $token,
                "cliente" => [
                    "id" => $cliente['id'],
                    "nombre" => $cliente['nombre'],
                    "email" => $cliente['email']
                ]
            ));
        } else {
            echo json_encode(array(
                "status" => 401,
                "detalle" => "Credenciales no encontradas"
            ));
        }
    }

    // Crear un cliente
    public function create($datos) {

        /*=============================================
        Validar credenciales del cliente
        =============================================*/

        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
            if (isset($datos['nombre']) && !preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚ]+$/', $datos['nombre'])){
            $json = array(
                "status" => 400,
                "detalle" => "Error en el campo del nombre permitido solo letras"
            );
            echo json_encode($json, true);
            return; 
        }
        if (isset($datos['apellido']) && !preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚ]+$/', $datos['apellido'])){
            $json = array(
                "status" => 400,
                "detalle" => "Error en el campo del apellido permitido solo letras"
            );
            echo json_encode($json, true);
            return; 
        }
        if (isset($datos['email']) && !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $datos['email'])) {
            $json = array(
                "status" => 400,
                "detalle" => "Error en el campo del email"
            );
            echo json_encode($json, true);
            return; 
        }
        
        $clientes = ModeloClientes::index("clientes");

            foreach ($clientes as $cliente) {

                if (base64_encode($_SERVER['PHP_AUTH_USER'] . ":" . $_SERVER['PHP_AUTH_PW']) == 
                    base64_encode($cliente->id_cliente . ":" . $cliente->llave_secreta)) {

                    // Preparar datos para crear
                    $datosCliente = array(
                        "nombre" => $datos["nombre"],
                        "apellido" => $datos["apellido"],
                        "email" => $datos["email"],
                        "id_cliente" => $cliente->id_cliente,
                        "llave_secreta" => $datos['llave_secreta'],
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s')
                    );

                    $create = ModeloClientes::create("clientes", $datosCliente);

                    if ($create == "ok") {
                        echo json_encode(array(
                            "status" => 200,
                            "detalle" => "Registro exitoso, el cliente ha sido guardado"
                        ), true);
                        return;
                    }
                }
            }
        }

        echo json_encode(array(
            "status" => 401,
            "detalle" => "No autorizado"
        ), true);
    }

    // Mostrar un cliente por id
    public function show($id) {

        /*=============================================
        Validar credenciales del cliente
        =============================================*/

        $clientes = ModeloClientes::index("clientes");

        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {

            foreach ($clientes as $cliente) {

                if (base64_encode($_SERVER['PHP_AUTH_USER'] . ":" . $_SERVER['PHP_AUTH_PW']) == 
                    base64_encode($cliente->id_cliente . ":" . $cliente->llave_secreta)) {

                    $clienteBuscado = ModeloClientes::show("clientes", $id);

                    if (!empty($clienteBuscado)) {
                        echo json_encode(array(
                            "status" => 200,
                            "detalle" => $clienteBuscado
                        ), true);
                        return;
                    } else {
                        echo json_encode(array(
                            "status" => 200,
                            "total_registros" => 0,
                            "detalle" => "No hay ningún cliente registrado con ese ID"
                        ), true);
                        return;
                    }
                }
            }
        }

        echo json_encode(array(
            "status" => 401,
            "detalle" => "No autorizado"
        ), true);
    }

    // Actualizar cliente
    public function update($id, $datos) {

        /*=============================================
        Validar credenciales del cliente
        =============================================*/

        $clientes = ModeloClientes::index("clientes");

        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {

            foreach ($clientes as $cliente) {

                if (base64_encode($_SERVER['PHP_AUTH_USER'] . ":" . $_SERVER['PHP_AUTH_PW']) == 
                    base64_encode($cliente->id_cliente . ":" . $cliente->llave_secreta)) {

                    // Validar datos
                    foreach ($datos as $key => $value) {
                        if (isset($value) && !preg_match('/^[(\\)\\=\\&\\$\\;\\-\\_\\*\\"\\<\\>\\?\\¿\\!\\¡\\:\\,\\.\\0-9a-zA-ZñÑáéíóúÁÉÍÓÚ ]+$/', $value)) {
                            echo json_encode(array(
                                "status" => 404,
                                "detalle" => "Error en el campo " . $key
                            ), true);
                            return;
                        }
                    }

                    // Verificar que el id corresponde al cliente autenticado, si quieres hacer esa validación:
                    if ($cliente->id == $id) {
                        $datosUpdate = array(
                            "id" => $id,
                            "nombre" => $datos["nombre"],
                            "apellido" => $datos["apellido"],
                            "email" => $datos["email"],
                            "id_cliente" => $datos["id_cliente"],
                            "llave_secreta" => $datos["llave_secreta"],
                            "updated_at" => date('Y-m-d H:i:s')
                        );

                        $update = ModeloClientes::update("clientes", $datosUpdate);

                        if ($update == "ok") {
                            echo json_encode(array(
                                "status" => 200,
                                "detalle" => "Registro actualizado exitosamente"
                            ), true);
                            return;
                        } else {
                            echo json_encode(array(
                                "status" => 404,
                                "detalle" => "No se pudo actualizar el cliente"
                            ), true);
                            return;
                        }
                    } else {
                        echo json_encode(array(
                            "status" => 403,
                            "detalle" => "No autorizado para modificar este cliente"
                        ), true);
                        return;
                    }
                }
            }
        }

        echo json_encode(array(
            "status" => 401,
            "detalle" => "No autorizado"
        ), true);
    }

    // Eliminar cliente
    public function delete($id) {

        /*=============================================
        Validar credenciales del cliente
        =============================================*/

        $clientes = ModeloClientes::index("clientes");

        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {

            foreach ($clientes as $cliente) {

                if (base64_encode($_SERVER['PHP_AUTH_USER'] . ":" . $_SERVER['PHP_AUTH_PW']) == 
                    base64_encode($cliente->id_cliente . ":" . $cliente->llave_secreta)) {

                    // Verificar que el id corresponde al cliente autenticado, si quieres hacer esa validación:
                    if ($cliente->id == $id) {

                        $delete = ModeloClientes::delete("clientes", $id);

                        if ($delete == "ok") {
                            echo json_encode(array(
                                "status" => 200,
                                "detalle" => "Cliente eliminado correctamente"
                            ), true);
                            return;
                        } else {
                            echo json_encode(array(
                                "status" => 404,
                                "detalle" => "No se pudo eliminar el cliente"
                            ), true);
                            return;
                        }
                    } else {
                        echo json_encode(array(
                            "status" => 403,
                            "detalle" => "No autorizado para eliminar este cliente"
                        ), true);
                        return;
                    }
                }
            }
        }

        echo json_encode(array(
            "status" => 401,
            "detalle" => "No autorizado"
        ), true);
    }
}
?>
