<?php 


class ControladorCursos{

    public function index($pagina){

      /*=============================================
		Validar credenciales del cliente
		=============================================*/

    $clientes = ModeloClientes::index("clientes");


    if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){

      foreach ($clientes as $key => $value) {


        if(base64_encode($_SERVER['PHP_AUTH_USER'].":".$_SERVER['PHP_AUTH_PW']) == 
           base64_encode($value->id_cliente .":". $value->llave_secreta)){

            if($pagina !=null){

              $cantidad=15;

              $desde=($pagina-1)*$cantidad;

              $cursos=ModeloCursos::index("cursos","clientes",$cantidad ,$desde);

            }else{

                $cursos=ModeloCursos::index("cursos","clientes",null, null);

            }
                  $json=array(

                        "status"=>200,
                        "total_registros"=>count($cursos),
                        "detalle"=>$cursos

                                    );

                        echo json_encode($json,true);

                    return;
        }
      }
    }

  }


    public function create($datos){

		/*=============================================
		Validar credenciales del cliente
		=============================================*/
    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])){

      $json = array(
        "status" => 401,
        "detalle" => "No autorizado : credenciales faltantes"
      );
      echo json_encode($json, true);
      return;
    }

    $camposRequeridos = ["titulo", "descripcion", "instructor", "imagen", "precio"];
    
    foreach ($camposRequeridos as $campo){
      if(!isset($datos[$campo])){
        $json = array(
          "status" => 400,
          "detalle" => "Campo '$campo' es requerido"
        );
        echo json_encode($json, true);
        return;
      }

      if (!preg_match('/^[(\\)\\=\\&\\$\\;\\-\\_\\*\\"\\<\\>\\?\\¿\\!\\¡\\:\\,\\.\\0-9a-zA-ZñÑáéíóúÁÉÍÓÚ ]+$/', $datos[$campo])){
        $json = array(
          "status" => 400,
          "detalle" => "Error en el campo '$campo'"
        );
        echo json_decode($json, true);
        return;
      }

    }

     /*=============================================
    Autenticar cliente
    =============================================*/
    $clientes = ModeloClientes::index("clientes");

    foreach ($clientes as $cliente ){
      if (base64_encode($_SERVER['PHP_AUTH_USER'] . ":" . $_SERVER['PHP_AUTH_PW']) ==
        base64_encode($cliente->id_cliente . ":" . $cliente->llave_secreta)){
          break;
        }
    }

     /*=============================================
    Validar duplicados
    =============================================*/
    $cursos = ModeloCursos::index("cursos", "clientes", null, null);

    foreach ($cursos as $curso) {
        if ($curso->titulo == $datos["titulo"]) {
            $json = array(
                "status" => 400,
                "detalle" => "El título ya existe"
            );
            echo json_encode($json, true);
            return;
        }

        if ($curso->descripcion == $datos["descripcion"]) {
            $json = array(
                "status" => 400,
                "detalle" => "La descripción ya existe"
            );
            echo json_encode($json, true);
            return;
        }
    }
    /*=============================================
    Preparar y crear el curso
    =============================================*/
    $datosCurso = array(
      "titulo" => $datos["titulo"],
      "descripcion" => $datos["descripcion"],
      "instructor" => $datos["instructor"],
      "imagen" => $datos["imagen"],
      "precio" => $datos["precio"],
      "id_creador" => $datos["id_creador"],
      "created_at" => date('Y-m-d H:i:s'),
      "updated_at" => date('Y-m-d H:i:s')
    );
    $create = ModeloCursos::create("cursos", $datosCurso);  

    if ($create == 'ok'){
      $json = array(
        "status" => 201,
        "detalle" => "curso creado exitosamente",
        "detalle2" => "Cruso '$create' creado??"
      );
    } else {
      $json = array(
        "status" => 500,
        "detalle" => "Error al guardar el curso"
      );
    }
    echo json_encode($json, true);
    return;


  }


    public function show($id){
        // echo("entro : " . $id);
        $tabla1 = "cursos";
        $tabla2 = "clientes";
      /*=============================================
        Validar credenciales del cliente
        =============================================*/
        $clientes = ModeloClientes::index("clientes");

        if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){

          foreach ($clientes as $key => $valueCliente) {

            if(base64_encode($_SERVER['PHP_AUTH_USER'].":".$_SERVER['PHP_AUTH_PW']) == 
              base64_encode($valueCliente->id_cliente .":". $valueCliente->llave_secreta)){


                /*=============================================
              Mostrar todos los cursos
              =============================================*/

              $curso = ModeloCursos::show($tabla1 ,$tabla2, $id);

              if(!empty($curso)){

                $json=array(
                  "status"=>200,
                  "detalle"=>$curso);

                  echo json_encode($json,true);

                      return;

              }else{

                $json = array(

                    "status"=>200,
                    "total_registros"=>0,
                    "detalles"=>"No hay ningún curso registrado"
                    
                  );

                echo json_encode($json, true);	

                return;
              }

            }

          }
          $json = array(
                "status" => 401,
                "detalle" => "Credenciales incorrectas"
            );
            echo json_encode($json, true);
            return;
        }

    }


    public function update($id,$datos){

      /*=============================================
		Validar credenciales del cliente
		=============================================*/
     if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
          $json = array(
              "status" => 401,
              "detalle" => "Credenciales de autenticación no proporcionadas"
          );
          echo json_encode($json, true);
          return;
      }
      
    $camposRequeridos = ["titulo", "descripcion", "instructor", "imagen", "precio"];

    foreach ($camposRequeridos as $campo) {
      if (!isset($datos[$campo])) {
          $json = array(
              "status" => 400,
              "detalle" => "El campo '$campo' es requerido"
          );
          echo json_encode($json, true);
          return;
      }

      if (!preg_match('/^[(\\)\\=\\&\\$\\;\\-\\_\\*\\"\\<\\>\\?\\¿\\!\\¡\\:\\,\\.\\0-9a-zA-ZñÑáéíóúÁÉÍÓÚ ]+$/', $datos[$campo])) {
          $json = array(
              "status" => 400,
              "detalle" => "Error en el campo '$campo'"
          );
          echo json_encode($json, true);
          return;
        }
    }

    $clientes = ModeloClientes::index("clientes");
    foreach ($clientes as $cliente) {
        if (base64_encode($_SERVER['PHP_AUTH_USER'] . ":" . $_SERVER['PHP_AUTH_PW']) == 
            base64_encode($cliente->id_cliente . ":" . $cliente->llave_secreta)) {
      
      
        $curso = ModeloCursos::show("cursos", "clientes", $id);
          if(!$curso || empty($curso)){
            $json = array(
              "status" => 404,
              "detalle" => "Curso no encontrado"
            );
            echo json_encode($json, true);
            return;
          }
          $curso = $curso[0];
          if($curso->id_creador != $cliente->id){
            $json = array(
              "status" => 403,
              "detalle" => "No autorizado para modificar este curso"
            );
            echo json_encode($json, true);
            return;
          }
          /*=============================================
            Validar duplicados (excluyendo el curso actual)
            =============================================*/
            $cursos = ModeloCursos::index("cursos", "clientes", null, null);
            
            foreach ($cursos as $c) {
                if ($c->id == $id) continue; // Saltar el curso actual
                
                if ($c->titulo == $datos["titulo"]) {
                    $json = array(
                        "status" => 400,
                        "detalle" => "El título ya existe en otro curso"
                    );
                    echo json_encode($json, true);
                    return;
                }

                if ($c->descripcion == $datos["descripcion"]) {
                    $json = array(
                        "status" => 400,
                        "detalle" => "La descripción ya existe en otro curso"
                    );
                    echo json_encode($json, true);
                    return;
                }
            }

            $datosActualizar = array(
              "id" => $id,
              "titulo" => $datos["titulo"],
              "descripcion" => $datos["descripcion"],
              "instructor" => $datos["instructor"],
              "imagen" => $datos["imagen"],
              "precio" => $datos["precio"],
              "updated_at" => date('Y-m-d H:i:s'),
            );
            $update = ModeloCursos::update("cursos", $datosActualizar);

            if ($update == "ok") {
                  $json = array(
                      "status" => 200,
                      "detalle" => "Curso actualizado exitosamente"
                  );
              } else {
                  $json = array(
                      "status" => 500,
                      "detalle" => "Error al actualizar el curso"
                  );
              }

              echo json_encode($json, true);
              return;

            }
        }       
      }   


    public function delete($id){
      
       if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
        $json = array(
            "status" => 401,
            "detalle" => "Credenciales de autenticación no proporcionadas"
        );
        echo json_encode($json, true);
        return;
    }
     $clientes = ModeloClientes::index("clientes");
     foreach ($clientes as $cliente) {
       if (base64_encode($_SERVER['PHP_AUTH_USER'] . ":" . $_SERVER['PHP_AUTH_PW']) == 
                    base64_encode($cliente->id_cliente . ":" . $cliente->llave_secreta)) {

                      
                      $curso = ModeloCursos::show("cursos", "clientes", $id);
                      
                      if (!$curso || empty($curso)) {
                        $json = array(
                          "status" => 404,
                          "detalle" => "Curso no encontrado"
                        );
                        echo json_encode($json, true);
                        return;
                      }
                      
                      $curso = $curso[0];
                      
                      if ($curso->id_creador != $cliente->id) {
                        $json = array(
                          "status" => 403,
                          "detalle" => "No autorizado para eliminar este curso"
                        );
                        echo json_encode($json, true);
                        return;
                      }
                      
                      
                      $delete = ModeloCursos::delete("cursos", $id);
                      
                      /*=============================================
                      Respuesta del modelo
                      =============================================*/
                      if ($delete == "ok") {
                        $json = array(
                          "status" => 200,
                          "detalle" => "Curso eliminado exitosamente"
                        );
                      } else {
                        $json = array(
                          "status" => 500,
                          "detalle" => "Error al eliminar el curso"
                        );
                      }
                      
                      echo json_encode($json, true);
                      return;
                      
                    }
                  } 
                  }
                    
                    
                    
                  }
                  
                  
                  
                  ?>



