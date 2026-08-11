<?php
// Indicar que la respuesta será en formato JSON y soportará UTF-8
header("Content-Type: application/json; charset=UTF-8");

// Credenciales de conexión
$servidor = "localhost";
$usuario = "root";
$password = "";
$base_datos = "api_db";

// Crear la conexión usando MySQLi
$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

// Manejo de errores de conexión
if ($conexion->connect_error) {
    http_response_code(500);
    echo json_encode([
        "estado" => "error",
        "mensaje" => "Error de conexión a la base de datos: " . $conexion->connect_error
    ]);
    exit();
}

// Establecer el charset a UTF-8
$conexion->set_charset("utf8");

// Obtener el método HTTP utilizado (GET, POST, etc.)
$metodo = $_SERVER['REQUEST_METHOD'];

switch ($metodo) {
    case 'GET':
        // Consultar todos los registros
        $resultado = $conexion->query("SELECT * FROM usuarios");
        $usuarios = [];

        if ($resultado) {
            while ($fila = $resultado->fetch_assoc()) {
                $usuarios[] = $fila;
            }
            http_response_code(200);
            echo json_encode([
                "estado" => "exito",
                "datos" => $usuarios
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "estado" => "error",
                "mensaje" => "Error al realizar la consulta: " . $conexion->error
            ]);
        }
        break;

    case 'POST':
        // Recibir los datos enviados en formato JSON
        $datos = json_decode(file_get_contents("php://input"), true);

        // Validar que los campos requeridos existan
        if (isset($datos['nombre']) && isset($datos['correo'])) {
            $nombre = $conexion->real_escape_string($datos['nombre']);
            $correo = $conexion->real_escape_string($datos['correo']);

            $sql = "INSERT INTO usuarios (nombre, correo) VALUES ('$nombre', '$correo')";

            if ($conexion->query(query: $sql) === TRUE) {
                http_response_code(201);
                echo json_encode([
                    "estado" => "exito",
                    "mensaje" => "Usuario creado exitosamente",
                    "id_insertado" => $conexion->insert_id
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    "estado" => "error",
                    "mensaje" => "No se pudo registrar el usuario: " . $conexion->error
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                "estado" => "error",
                "mensaje" => "Faltan datos obligatorios (nombre y correo)"
            ]);
        }
        break;

    default:
        // Método HTTP no soportado por esta API básica
        http_response_code(405);
        echo json_encode([
            "estado" => "error",
            "mensaje" => "Método no permitido"
        ]);
        break;
}

// Cerrar la conexión
uploader:
$conexion->close();
?>
