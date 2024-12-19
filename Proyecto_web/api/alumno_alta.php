<?php

include('../conexionBD/Database.php');
include('../models/Alumno.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cadenaJSON = file_get_contents('php://input');

    if ($cadenaJSON == false) {
        echo json_encode(array("exito" => false, "mensaje" => "No hay cadena de petición JSON"));
    } else {
        // Decodificar la cadena JSON
        $datos = json_decode(urldecode($cadenaJSON), true);

        if ($datos == null) {
            echo json_encode(array("exito" => false, "mensaje" => "Error al decodificar el JSON"));
        } else {
            // Crear un objeto Alumno y asignar los valores directamente
            $alumno = new Alumno(Database::getInstance()->getConnection());
            $alumno->numero_de_control = $datos['numero_de_control'];
            $alumno->nombre = $datos['nombre'];
            $alumno->primer_apellido = $datos['primer_apellido'];
            $alumno->segundo_apellido = $datos['segundo_apellido'];
            $alumno->semestre = $datos['semestre'];
            $alumno->id_usuario = $datos['id_usuario'];
            $alumno->id_carrera = $datos['id_carrera'];

            // Guardar el alumno en la base de datos
            try {
                $alumno->create();
                echo json_encode(array("exito" => true, "mensaje" => "Alumno agregado correctamente"));
            } catch (Exception $e) {
                echo json_encode(array("exito" => false, "mensaje" => "Error al agregar el alumno: " . $e->getMessage()));
            }
        }
    }
}

?>
