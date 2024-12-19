<?php
session_start(); // Iniciar sesión
// Verificar si el usuario está autenticado y es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Administrador') {
    header("Location: login.php"); // Redirigir si no está autenticado o no es admin
    exit();
}
