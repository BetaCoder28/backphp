<?php 
class ControladorClientes {
    public function index() {
        $clientes = ModeloClientes::index("clientes");
        $json = [
            "status" => 200,
            "total_registros" => count($clientes),
            "detalle" => $clientes
        ];
        echo json_encode($json, true);
    }

    public function login($email, $llave_secreta) {
        $cliente = ModeloClientes::buscarPorEmail("clientes", $email);

        if ($cliente && password_verify($llave_secreta, $cliente['llave_secreta'])) {
            echo json_encode([
                "status" => 200,
                "detalle" => "Login exitoso",
                "cliente" => [
                    "id" => $cliente['id'],
                    "nombre" => $cliente['nombre'],
                    "email" => $cliente['email'],
                    "id_cliente" => $cliente['id_cliente']
                ]
            ]);
        } else {
            echo json_encode([
                "status" => 401,
                "detalle" => "Credenciales inválidas"
            ]);
        }
    }

    public function create($datos) {
        if (!isset($datos["nombre"]) || 
            !isset($datos["apellido"]) || 
            !isset($datos["email"]) || 
            !isset($datos["llave_secreta"])) {
            echo json_encode([
                "status" => 400,
                "detalle" => "Todos los campos son requeridos"
            ]);
            return;
        }

        $clienteExistente = ModeloClientes::buscarPorEmail("clientes", $datos["email"]);
        if ($clienteExistente) {
            echo json_encode([
                "status" => 400,
                "detalle" => "El email ya está registrado"
            ]);
            return;
        }

        $hashedPassword = password_hash($datos["llave_secreta"], PASSWORD_DEFAULT);
        $id_cliente = md5(uniqid(rand(), true));

        $stmt = Conexion::conectar()->prepare("INSERT INTO clientes (nombre, apellido, email, llave_secreta, id_cliente, created_at, updated_at) 
                                              VALUES (:nombre, :apellido, :email, :llave_secreta, :id_cliente, NOW(), NOW())");

        $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(":apellido", $datos["apellido"], PDO::PARAM_STR);
        $stmt->bindParam(":email", $datos["email"], PDO::PARAM_STR);
        $stmt->bindParam(":llave_secreta", $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(":id_cliente", $id_cliente, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo json_encode([
                "status" => 201,
                "detalle" => "Cliente registrado exitosamente",
                "cliente" => [
                    "id" => Conexion::conectar()->lastInsertId(),
                    "nombre" => $datos["nombre"],
                    "apellido" => $datos["apellido"],
                    "email" => $datos["email"],
                    "id_cliente" => $id_cliente
                ]
            ]);
        } else {
            echo json_encode([
                "status" => 500,
                "detalle" => "Error al registrar el cliente"
            ]);
        }
    }
}
?>