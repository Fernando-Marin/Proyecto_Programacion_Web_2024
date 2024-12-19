<?php
session_start();  // Iniciar sesión al principio

// Tiempo máximo de inactividad en segundos (2 días)
define('MAX_INACTIVITY_TIME', 2 * 24 * 60 * 60);

// Comprobar si la última actividad está registrada en la sesión
if (isset($_SESSION['last_activity'])) {
    // Calcular el tiempo de inactividad
    $inactivity_time = time() - $_SESSION['last_activity'];

    // Si el tiempo de inactividad excede el tiempo máximo permitido, cerrar la sesión
    if ($inactivity_time > MAX_INACTIVITY_TIME) {
        session_unset();  // Eliminar todas las variables de sesión
        session_destroy();  // Destruir la sesión

        // Redirigir al usuario al login
        header("Location: login.php");  // Cambia a la página que desees
        exit();
    }
}

// Registrar la marca de tiempo de la última actividad
$_SESSION['last_activity'] = time();

// Incluir el archivo de conexión a la base de datos
require_once '../conexionBD/Database.php';

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger los datos del formulario
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Obtener la conexión a la base de datos
        $db = Database::getInstance()->getConnection();

        // Consultar el usuario por el correo
        $stmt = $db->prepare("SELECT * FROM usuario WHERE Correo = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si el usuario existe y la contraseña coincide utilizando password_verify
        if ($user && password_verify($password, $user['Contraseña'])) {
            // Login exitoso: iniciar sesión
            $_SESSION['user_id'] = $user['ID_usuario'];  // Guardar ID de usuario en sesión
            $_SESSION['user_role'] = $user['Rol'];       // Guardar rol de usuario en sesión
            header("Location: dashboard"); // Redirigir a la página deseada
            exit();
        } else {
            $error = "Correo o contraseña incorrectos";
        }
    } catch (PDOException $exception) {
        $error = "Error al conectar o consultar la base de datos: " . htmlspecialchars($exception->getMessage());
    }
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex justify-content-center align-items-center" style="height: 100vh; background-color: #f7f7f7;">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Iniciar Sesión</h4>
                    </div>
                    <div class="card-body">
                        <!-- Mostrar error si hay -->
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Formulario de login -->
                        <form method="POST" action="login.php">
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="text" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <p>No tienes cuenta? <a href="./register">Regístrate</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>

</html>