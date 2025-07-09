# Documentation

### Autenticación
Método: Basic Auth
Credenciales: email + llave_secreta
Cabeceras requeridas:
    pAuthorization: Basic {credenciales_base64}

### Endpoints de Clientes
1. Registro de Cliente (Público)
Crear nuevo cliente
    Método: POST

    Ruta: /clientes

    Body (JSON):
    {
        "nombre": "Juan",
        "apellido": "Pérez",
        "email": "juan@ejemplo.com",
        "llave_secreta": "password123"
    }
    
    Respuesta exitosa (201):
    {
        "status": 201,
        "detalle": "Cliente registrado exitosamente",
        "cliente": {
            "id": 15,
            "nombre": "Juan",
            "apellido": "Pérez",
            "email": "juan@ejemplo.com",
            "id_cliente": "a1b2c3d4e5f6"
        }
    }


2. Login (Público)
    Obtener credenciales de acceso

    Método: POST

    Ruta: /login

    Body (JSON):
    {
    "email": "juan@ejemplo.com",
    "llave_secreta": "password123"
    }
    Respuesta exitosa (200):
    {
    "status": 200,
    "detalle": "Login exitoso",
    "cliente": {
            "id": 15,
            "nombre": "Juan",
            "email": "juan@ejemplo.com",
            "id_cliente": "a1b2c3d4e5f6"
        }
    }

3. Gestión de Cursos (Protegido)
    Listar cursos (paginado)
    Método: GET

    Ruta: /?pagina={número}

    Ejemplo: /?pagina=2

    Respuesta exitosa (200):
    {
    "status": 200,
    "total_registros": 45,
    "detalle": [
        { "id": 1, "titulo": "Curso PHP", ... },
        { "id": 2, "titulo": "Curso JS", ... }
    ]
    }

    Crear curso
    Método: POST

    Ruta: /cursos

    Body (JSON):
    {
    "titulo": "Nuevo curso",
    "descripcion": "Descripción del curso",
    "precio": 49.99
    }

    Ver detalle de curso
    Método: GET

    Ruta: /cursos/{id}

    Ejemplo: /cursos/5

    Actualizar curso
    Método: PUT

    Ruta: /cursos/{id}

    Ejemplo: /cursos/5

    Body (JSON):
    {
    "titulo": "Título actualizado",
    "precio": 59.99
    }

    Eliminar curso
    Método: DELETE

    Ruta: /cursos/{id}

    Ejemplo: /cursos/5

4. Gestión de Órdenes (Protegido)
    Listar órdenes
    Método: GET

    Ruta: /ordenes

    Respuesta exitosa (200):
    {
    "status": 200,
    "detalle": [
        { "id": 101, "total": 99.98, ... },
        { "id": 102, "total": 49.99, ... }
    ]
    }
    Crear orden
    Método: POST

    Ruta: /ordenes

    Body (JSON):
    {
    "id_curso": 5,
    "cantidad": 2,
    "total": 99.98
    }

    Ver detalle de orden
    Método: GET

    Ruta: /ordenes/{id}

    Ejemplo: /ordenes/101

    Actualizar orden
    Método: PUT

    Ruta: /ordenes/{id}

    Ejemplo: /ordenes/101

    Body (JSON):
    {
    "cantidad": 3,
    "total": 149.97
    }

    Eliminar orden
    Método: DELETE

    Ruta: /ordenes/{id}

    Ejemplo: /ordenes/101

5. Obtener videos
   /youtube/

    
   Obtener por index
    /youtube/{index}
    ejemplo : /youtube/1
    Respuesta exitosa:
        {
        "status": 200,
        "curso": "https://youtu.be/zNmDOXbTugE?si=K8Ni7pBaCPk0NSJB"
        }

    

## Respuestas de Error Comunes
    400 Bad Request: Faltan campos obligatorios

    401 Unauthorized: Credenciales inválidas/no proporcionadas

    404 Not Found: Recurso no encontrado

    500 Internal Server Error: Error del servidor

### Ejemplo de error:
    {
    "status": 401,
    "detalle": "Credenciales inválidas"
    }


## Configuración de CORS
La API permite solicitudes desde:
http://localhost:5173

Cabeceras permitidas:
Authorization, Content-Type, Accept, Origin