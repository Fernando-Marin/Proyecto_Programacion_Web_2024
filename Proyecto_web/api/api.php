<?php
header("Content-Type: application/json"); // Asegurarse de devolver JSON
require_once '../conexionBD/Database.php'; // Incluir la clase Database

$response = ["success" => false, "message" => "", "data" => null];

try {
    // Obtener la conexión a la base de datos
    $db = Database::getInstance()->getConnection();

    // Procesar solicitudes dependiendo del método
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            // Obtener alumnos o carreras
            if (isset($_GET['resource']) && $_GET['resource'] === 'carreras') {
                $stmt = $db->query("SELECT * FROM carrera");
                $carreras = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $response["success"] = true;
                $response["data"] = $carreras;
            } else {
                $query = $db->query("SELECT a.Numero_de_control, a.Nombre, a.Primer_Apellido, a.Segundo_Apellido, a.Semestre, c.Nombre_carrera
                                     FROM alumno a
                                     LEFT JOIN carrera c ON a.ID_carrera = c.ID_carrera");
                $alumnos = $query->fetchAll(PDO::FETCH_ASSOC);
                $response["success"] = true;
                $response["data"] = $alumnos;
            }
            break;

        case 'POST':
            $input = json_decode(file_get_contents("php://input"), true);

            if (isset($input['action']) && $input['action'] === 'alta_alumno') {
                $stmt = $db->prepare("INSERT INTO alumno (Numero_de_control, Nombre, Primer_Apellido, Segundo_Apellido, Semestre, ID_carrera) 
                                      VALUES (:numero_control, :nombre, :primer_apellido, :segundo_apellido, :semestre, :id_carrera)");

                $stmt->bindParam(':numero_control', $input['numero_control']);
                $stmt->bindParam(':nombre', $input['nombre']);
                $stmt->bindParam(':primer_apellido', $input['primer_apellido']);
                $stmt->bindParam(':segundo_apellido', $input['segundo_apellido']);
                $stmt->bindParam(':semestre', $input['semestre']);
                $stmt->bindParam(':id_carrera', $input['id_carrera']);

                if ($stmt->execute()) {
                    $response["success"] = true;
                    $response["message"] = "Alumno registrado exitosamente.";
                } else {
                    $response["message"] = "Error al registrar el alumno.";
                }
            } elseif (isset($input['action']) && $input['action'] === 'editar_alumno') {
                $stmt = $db->prepare("UPDATE alumno SET Nombre = :nombre, Primer_Apellido = :primer_apellido, Segundo_Apellido = :segundo_apellido, Semestre = :semestre, ID_carrera = :id_carrera WHERE Numero_de_control = :numero_control");

                $stmt->bindParam(':numero_control', $input['numero_control']);
                $stmt->bindParam(':nombre', $input['nombre']);
                $stmt->bindParam(':primer_apellido', $input['primer_apellido']);
                $stmt->bindParam(':segundo_apellido', $input['segundo_apellido']);
                $stmt->bindParam(':semestre', $input['semestre']);
                $stmt->bindParam(':id_carrera', $input['id_carrera']);

                if ($stmt->execute()) {
                    $response["success"] = true;
                    $response["message"] = "Alumno actualizado exitosamente.";
                } else {
                    $response["message"] = "Error al actualizar el alumno.";
                }
            }

            break;


        case 'DELETE':
            $input = json_decode(file_get_contents("php://input"), true); // Obtener datos del cuerpo
            if (isset($input['numero_control'])) {
                $stmt = $db->prepare("DELETE FROM alumno WHERE Numero_de_control = :numero_control");
                $stmt->bindParam(':numero_control', $input['numero_control']);

                if ($stmt->execute()) {
                    $response["success"] = true;
                    $response["message"] = "Alumno eliminado exitosamente.";
                } else {
                    $response["message"] = "Error al eliminar el alumno.";
                }
            } else {
                $response["message"] = "Numero de control no proporcionado.";
                http_response_code(400); // Bad Request
            }
            break;
        default:
            $response["message"] = "Método no permitido.";
            http_response_code(405);
            break;
    }
} catch (PDOException $exception) {
    $response["message"] = "Error en la base de datos: " . $exception->getMessage();
    http_response_code(500);
}

// Enviar respuesta en formato JSON
echo json_encode($response);
