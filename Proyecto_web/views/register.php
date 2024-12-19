<?php
require_once '../conexionBD/Database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger los datos del formulario
    $email = $_POST['email'];
    $password = $_POST['password'];
    $rol = $_POST['rol'];
    $recaptchaResponse = $_POST['g-recaptcha-response']; // Capturar el token de reCAPTCHA

    // Verificar el CAPTCHA con la API de Google
    $secretKey = "6LcluZAqAAAAAMKuN83bGHIzgi32D3EEI90Is0xs"; // Clave secreta obtenida de Google reCAPTCHA
    $recaptchaUrl = "https://www.google.com/recaptcha/api/siteverify";
    $recaptchaData = [
        'secret' => $secretKey,
        'response' => $recaptchaResponse
    ];

    $recaptchaOptions = [
        'http' => [
            'method'  => 'POST',
            'content' => http_build_query($recaptchaData),
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\n"
        ]
    ];

    $recaptchaContext = stream_context_create($recaptchaOptions);
    $recaptchaVerify = file_get_contents($recaptchaUrl, false, $recaptchaContext);
    $recaptchaSuccess = json_decode($recaptchaVerify)->success;

    if (!$recaptchaSuccess) {
        $error = "Por favor, confirma que no eres un robot.";
    } else {
        // Cifrar la contraseña
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $db = Database::getInstance()->getConnection();

            // Insertar en la tabla 'usuario'
            $stmt = $db->prepare("INSERT INTO usuario (Correo, Contraseña, Rol) VALUES (:email, :password, :rol)");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword); // Usa $hashedPassword aquí
            $stmt->bindParam(':rol', $rol);
            $stmt->execute();

            // Redirigir a la página de login después de registrar
            header("Location: login");
            exit();
        } catch (PDOException $exception) {
            $error = "Error al registrar usuario: " . htmlspecialchars($exception->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">

    <!-- Incluir el script de Google reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body class="d-flex justify-content-center align-items-center" style="height: 100vh; background-color: #f7f7f7;">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Registro de Usuario</h4>
                    </div>
                    <div class="card-body">
                        <!-- Mostrar error si hay -->
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Formulario de registro -->
                        <form method="POST" action="register">
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <script>
                                document.getElementById('email').addEventListener('input', function(e) {
                                    const regex = /^[a-zA-Z0-9@.]*$/; // Permite letras, números, @ y punto
                                    if (!regex.test(e.target.value)) {
                                        e.target.value = e.target.value.replace(/[^a-zA-Z0-9@.]/g, ''); // Reemplaza caracteres no permitidos
                                    }
                                });
                            </script>


                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <script>
                                document.getElementById('password').addEventListener('input', function(e) {
                                    const regex = /^[a-zA-Z0-9]*$/; // Permite letras, números
                                    if (!regex.test(e.target.value)) {
                                        e.target.value = e.target.value.replace(/[^a-zA-Z0-9]/g, ''); // Reemplaza caracteres no permitidos
                                    }
                                });
                            </script>

                            <div class="mb-3">
                                <label for="rol" class="form-label">Rol</label>
                                <select class="form-control" id="rol" name="rol" required>
                                    <option value="Administrador">Administrador</option>
                                    <option value="Docente">Docente</option>
                                    <option value="Alumno">Alumno</option>
                                </select>
                            </div>

                            <!-- Agregar el widget de reCAPTCHA -->
                            <div class="mb-3">
                                <div class="g-recaptcha" data-sitekey="6LcluZAqAAAAANUF7-BLesasasSj4NXKaDvIBeYA"></div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Registrar</button>
                        </form>
                    </div>

                    <div class="card-footer text-center">
                        <p>Ya tienes cuenta? <a href="./login">Inicia sesión</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

</body>

</html>