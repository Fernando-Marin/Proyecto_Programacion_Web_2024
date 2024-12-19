<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Conexión</title>
    <link href="./css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        #welcome-text {
            min-height: 100px;
            font-size: 1.5rem;
        }

        .cursor::after {
            content: '|';
            animation: blink 0.7s infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0;
            }
        }
    </style>
</head>

<body>
    <!-- Encabezado con botones -->
    <header class="bg-primary text-white text-center py-5">
        <h1 class="text-white">Bienvenido al Sistema de Tutorías</h1>
        <div class="d-flex justify-content-center mt-3">
            <a href="./views/register" class="btn btn-success mx-2">Regístrate</a>
            <a href="./views/login" class="btn btn-primary mx-2">Inicia Sesión</a>
        </div>
    </header>

    <!-- Contenido principal -->
    <main class="container mt-5 text-center">
        <div id="welcome-text" class="cursor"></div>
    </main>

    <script>
        $(document).ready(function() {
            const welcomeMessages = [
                "Bienvenido a nuestro Sistema de Tutorías",
                "Aquí podrás conectar con tutores expertos",
                "Mejora tu aprendizaje de manera personalizada",
                "Explora, aprende y crece con nosotros"
            ];

            function typeWriter(element, message, speed = 50) {
                $(element).empty();
                let i = 0;

                function type() {
                    if (i < message.length) {
                        $(element).append(message.charAt(i));
                        i++;
                        setTimeout(type, speed);
                    }
                }
                type();
            }

            function cycleMessages() {
                let currentIndex = 0;

                function displayNextMessage() {
                    typeWriter('#welcome-text', welcomeMessages[currentIndex]);
                    currentIndex = (currentIndex + 1) % welcomeMessages.length;
                }

                // Initial message
                displayNextMessage();

                // Change message every 3 seconds
                setInterval(displayNextMessage, 3000);
            }

            cycleMessages();
        });
    </script>
</body>

</html>