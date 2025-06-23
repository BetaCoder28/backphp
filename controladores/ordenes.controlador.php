<?php

class ControladorOrdenes{

    /*=============================================
    LISTAR ÓRDENES
    =============================================*/
    public function index() {
        $ordenes = ModeloOrdenes::index("ordenes_clientes");
        echo json_encode(["status" => 200, "ordenes" => $ordenes]);
    }


    /*=============================================
    CREAR ORDEN
    =============================================*/
    public function create($datos) {

        if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
            echo json_encode(["status" => 401, "detalle" => "No autorizado: credenciales faltantes"]);
            return;
        }

        $camposRequeridos = ["id_cliente", "id_curso", "id_metodo_pago"];
        foreach ($camposRequeridos as $campo) {
            if (!isset($datos[$campo])) {
                echo json_encode(["status" => 400, "detalle" => "Campo '$campo' es requerido"]);
                return;
            }
        }

        $clientes = ModeloClientes::index("clientes");
        $autenticado = false;

        foreach ($clientes as $cliente) {
            if (base64_encode($_SERVER['PHP_AUTH_USER'] . ":" . $_SERVER['PHP_AUTH_PW']) ==
                base64_encode($cliente->id_cliente . ":" . $cliente->llave_secreta)) {
                $autenticado = true;
                break;
            }
        }

        if (!$autenticado) {
            echo json_encode(["status" => 403, "detalle" => "Credenciales inválidas"]);
            return;
        }

        $datosVenta = [
            "id_cliente" => $datos["id_cliente"],
            "id_curso" => $datos["id_curso"],
            "id_metodo_pago" => $datos["id_metodo_pago"],
            "created_at" => date("Y-m-d H:i:s"),
            "updated_at" => date("Y-m-d H:i:s")
        ];

        $respuesta = ModeloOrdenes::registrarVenta("clientes_cursos", "ordenes_clientes", $datosVenta);

        if ($respuesta === "ok") {
            echo json_encode(["status" => 201, "detalle" => "Venta registrada exitosamente"]);
        } else {
            echo json_encode(["status" => 500, "detalle" => "Error al registrar la venta", "error" => $respuesta]);
        }
    }


    /*=============================================
    MOSTRAR ORDEN ESPECÍFICA
    =============================================*/
    public function show($id) {
        $orden = ModeloOrdenes::show("ordenes_clientes", $id);
        if ($orden) {
            echo json_encode(["status" => 200, "orden" => $orden]);
        } else {
            echo json_encode(["status" => 404, "detalle" => "Orden no encontrada"]);
        }
    }

    /*=============================================
    ACTUALIZAR ORDEN
    =============================================*/
     public function update($id, $datos) {
        if (!isset($datos["id_metodo_pago"])) {
            echo json_encode(["status" => 400, "detalle" => "Campo 'id_metodo_pago' es requerido"]);
            return;
        }

        $respuesta = ModeloOrdenes::update("ordenes_clientes", $id, [
            "id_metodo_pago" => $datos["id_metodo_pago"],
            "updated_at" => date("Y-m-d H:i:s")
        ]);

        if ($respuesta === "ok") {
            echo json_encode(["status" => 200, "detalle" => "Orden actualizada correctamente"]);
        } else {
            echo json_encode(["status" => 500, "detalle" => "Error al actualizar"]);
        }
    }

    /*=============================================
    ELIMINAR ORDEN
    =============================================*/
     public function delete($id) {
        $respuesta = ModeloOrdenes::delete("ordenes_clientes", $id);

        if ($respuesta === "ok") {
            echo json_encode(["status" => 200, "detalle" => "Orden eliminada correctamente"]);
        } else {
            echo json_encode(["status" => 500, "detalle" => "Error al eliminar"]);
        }
    }
}