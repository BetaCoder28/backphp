<?php

require_once "conexion.php";

class ModeloClientes {

    // Listar clientes 
    static public function index($tabla, $cantidad = null, $desde = 0) {
        if ($cantidad != null) {
            $stmt = Conexion::conectar()->prepare("SELECT id, nombre, apellido, email, id_cliente, llave_secreta, created_at, updated_at FROM $tabla LIMIT :desde, :cantidad");
            $stmt->bindParam(":desde", $desde, PDO::PARAM_INT);
            $stmt->bindParam(":cantidad", $cantidad, PDO::PARAM_INT);
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT id, nombre, apellido, email, id_cliente, llave_secreta, created_at, updated_at FROM $tabla");
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS);
        $stmt->close();
        $stmt = null;
    }

    // LOGIN
    static public function buscarPorEmail($tabla, $email) {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE email = :email");
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Crear un nuevo cliente
    static public function create($tabla, $datos) {

        // Hashear la llave secreta antes de guardarla
        $llave_secreta_hash = password_hash($datos["llave_secreta"], PASSWORD_DEFAULT);

        $stmt = Conexion::conectar()->prepare("
            INSERT INTO $tabla 
            (nombre, apellido, email, id_cliente, llave_secreta, created_at, updated_at) 
            VALUES 
            (:nombre, :apellido, :email, :id_cliente, :llave_secreta, :created_at, :updated_at)
        ");

        $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(":apellido", $datos["apellido"], PDO::PARAM_STR);
        $stmt->bindParam(":email", $datos["email"], PDO::PARAM_STR);
        $stmt->bindParam(":id_cliente", $datos["id_cliente"], PDO::PARAM_STR);
        $stmt->bindParam(":llave_secreta", $llave_secreta_hash, PDO::PARAM_STR);
        $stmt->bindParam(":created_at", $datos["created_at"], PDO::PARAM_STR);
        $stmt->bindParam(":updated_at", $datos["updated_at"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } else {
            print_r(Conexion::conectar()->errorInfo());
        }

        $stmt->close();
        $stmt = null;
    }
    // Actualizar un cliente
    static public function update($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombre = :nombre, apellido = :apellido, email = :email, id_cliente = :id_cliente, llave_secreta = :llave_secreta, updated_at = :updated_at WHERE id = :id");

        $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
        $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
        $stmt->bindParam(":apellido", $datos["apellido"], PDO::PARAM_STR);
        $stmt->bindParam(":email", $datos["email"], PDO::PARAM_STR);
        $stmt->bindParam(":id_cliente", $datos["id_cliente"], PDO::PARAM_STR);
        $stmt->bindParam(":llave_secreta", $datos["llave_secreta"], PDO::PARAM_STR);
        $stmt->bindParam(":updated_at", $datos["updated_at"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } else {
            print_r(Conexion::conectar()->errorInfo());
        }

        $stmt->close();
        $stmt = null;
    }

    // Eliminar un cliente
    static public function delete($tabla, $id) {
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "ok";
        } else {
            print_r(Conexion::conectar()->errorInfo());
        }

        $stmt->close();
        $stmt = null;
    }
}
?>
