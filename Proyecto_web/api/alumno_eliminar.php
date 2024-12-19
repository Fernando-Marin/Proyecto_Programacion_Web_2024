<?php

include('../conexionBD/Database.php');
include('../models/Alumno.php');

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    if (!isset($_GET['numero_de_control'])) {
        echo json_encode(array("exito" => false, "mensaje" => "Falta el número de control"));
    } else {
        $numero_de_control = $_GET['numero_de_control'];
        $alumno = new Alumno(Database::getInstance()->getConnection());

        // Comprobar si el alumno existe
        $alumnoExistente = $alumno->read($numero_de_control);
        if (!$alumnoExistente) {
            echo json_encode(array("exito" => false, "mensaje" => "El alumno no existe"));
        } else {
            try {
                $alumno->delete($numero_de_control);
                echo json_encode(array("exito" => true, "mensaje" => "Alumno eliminado correctamente"));
            } catch (Exception $e) {
                echo json_encode(array("exito" => false, "mensaje" => "Error al eliminar el alumno: " . $e->getMessage()));
            }
        }
    }
}

