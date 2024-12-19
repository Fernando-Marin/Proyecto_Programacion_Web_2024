<?php
session_start();  // Iniciar sesión
session_unset();  // Eliminar todas las variables de sesión
session_destroy();  // Destruir la sesión

// Redirigir al usuario al login o a la página de inicio
header("Location: login.php");  // Cambia a la página que desees
exit();
