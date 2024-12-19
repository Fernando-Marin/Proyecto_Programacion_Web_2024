<?php
// Procesar la baja de un alumno
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM alumno WHERE Numero_de_control = :delete_id");
        $stmt->bindParam(':delete_id', $delete_id);

        if ($stmt->execute()) {
            $success_message = "Alumno eliminado exitosamente.";
        } else {
            $error_message = "Error al eliminar el alumno.";
        }
    } catch (PDOException $exception) {
        $error_message = "Error en la base de datos: " . htmlspecialchars($exception->getMessage());
    }
}
