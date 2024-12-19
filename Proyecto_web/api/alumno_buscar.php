<?php

include('../conexionBD/Database.php');
include('../models/Alumno.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $termino = isset($_GET['termino']) ? $_GET['termino'] : '';

    // Crear conexión
    $pdo = Database::getInstance()->getConnection();

    // Construir consulta SQL
    if ($termino) {
        $sql = "SELECT * FROM alumno WHERE Numero_de_control LIKE :termino OR Nombre LIKE :termino OR Primer_Apellido LIKE :termino OR Segundo_Apellido LIKE :termino OR Semestre LIKE :termino OR ID_usuario LIKE :termino OR ID_carrera LIKE :termino";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':termino' => "%$termino%"]);
    } else {
        $sql = "SELECT * FROM alumno";
        $stmt = $pdo->query($sql);
    }

    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mapeamos las claves para que coincidan con las del modelo
    $resultados_formateados = array_map(function ($alumno) {
        return [
            'numero_de_control' => $alumno['Numero_de_control'],
            'nombre' => $alumno['Nombre'],
            'primer_apellido' => $alumno['Primer_Apellido'],
            'segundo_apellido' => $alumno['Segundo_Apellido'],
            'semestre' => $alumno['Semestre'],
            'id_usuario' => $alumno['ID_usuario'],
            'id_carrera' => $alumno['ID_carrera']
        ];
    }, $resultados);

    // Devolvemos los datos en formato JSON con las claves correctas
    echo json_encode($resultados_formateados);
}

?>
