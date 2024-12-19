<?php

include('../conexionBD/Database.php');
include('../models/Alumno.php');

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $cadenaJSON = file_get_contents('php://input');

    if ($cadenaJSON == false) {
        echo json_encode(array("exito" => false, "mensaje" => "No hay cadena de petición JSON"));
    } else {
        $datos = json_decode(urldecode($cadenaJSON), true);

        if ($datos == null) {
            echo json_encode(array("exito" => false, "mensaje" => "Error al decodificar el JSON"));
        } else {
            $alumno = new Alumno(Database::getInstance()->getConnection());
            $alumno->numero_de_control = $datos['numero_de_control'];
            $alumno->nombre = $datos['nombre'];
            $alumno->primer_apellido = $datos['primer_apellido'];
            $alumno->segundo_apellido = $datos['segundo_apellido'];
            $alumno->semestre = $datos['semestre'];
            $alumno->id_usuario = $datos['id_usuario'];
            $alumno->id_carrera = $datos['id_carrera'];

            // Comprobar si el alumno existe
            $alumnoExistente = $alumno->read($alumno->numero_de_control);
            if (!$alumnoExistente) {
                echo json_encode(array("exito" => false, "mensaje" => "El alumno no existe"));
            } else {
                try {
                    $alumno->update();
                    echo json_encode(array("exito" => true, "mensaje" => "Alumno actualizado correctamente"));
                } catch (Exception $e) {
                    echo json_encode(array("exito" => false, "mensaje" => "Error al actualizar el alumno: " . $e->getMessage()));
                }
            }
        }
    }
}

?>
