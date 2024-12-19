<?php

include('../conexionBD/Database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cadenaJSON = file_get_contents('php://input');
    $datos = json_decode($cadenaJSON, true);

    if ($datos == null || !isset($datos['correo']) || !isset($datos['contraseña'])) {
        echo json_encode(array("exito" => false, "mensaje" => "Error al decodificar el JSON o faltan datos"));
    } else {
        $correo = $datos['correo'];
        $contraseña = $datos['contraseña'];

        $pdo = Database::getInstance()->getConnection();

        // Buscar el usuario por correo
        $sql = "SELECT * FROM usuario WHERE Correo = :correo";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':correo' => $correo]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($contraseña, $usuario['Contraseña'])) {
            // Si la contraseña coincide, devolver respuesta exitosa
            echo json_encode(array("exito" => true, "mensaje" => "Inicio de sesión exitoso", "usuario" => $usuario));
        } else {
            // Si no coincide
            echo json_encode(array("exito" => false, "mensaje" => "Credenciales incorrectas"));
        }
    }
}
