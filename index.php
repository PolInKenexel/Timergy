<?php 
    // Iniciar sesión
    session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Web de productividad con gestión de tiempo y energía">
    <meta name="author" content="Web Wizards">
    <title>Timergy - Inicio</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        .custom-header {
            background-color: #7a8c55;
            color: white; /* Asegúrate de que el texto sea legible */
        }
        /* General */
        body {
            font-family: Arial, sans-serif;
            background-color: #f6f6f6;
        }

        /* Hero Section */
        .hero {
            background: url('img/hero-image.png') center/cover no-repeat; /* Ruta de tu imagen */
            height: 70vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            position: relative;
        }

        .hero-overlay {
            background-color: rgba(0, 0, 0, 0.5); /* Oscurece la imagen para resaltar el texto */
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-content h1 {
            font-size: 3rem;
        }

        .hero-content p {
            font-size: 1.5rem;
        }

        .cta-button {
            padding: 0.5rem 2rem;
            background-color: #5e6b40;
            color: white;
            border: none;
            border-radius: 0.25rem;
            text-decoration: none;
            font-size: 1rem;
        }
        .hero {
            background: url('img/5.jpg') center/contain no-repeat; /* Imagen ajustada a tamaño similar al botón */
            aspect-ratio: 3 / 1; /* Relación de aspecto ajustada al botón (ejemplo 3:1) */
            width: 100%; /* Ocupa todo el ancho del contenedor */
            max-height: 70vh; /* Mantén una altura máxima */
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            position: relative;
        }


        .cta-button:hover {
            background-color: #7a8c55;
        }

        /* Nueva sección */
        .feature-section {
            background-color: #d9dfd4;
            padding: 3rem 0;
        }

        .feature-section h2 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 1rem;
            color: #4b5320;
        }

        .feature-section p {
            font-size: 1rem;
            color: #4b5320;
            margin-bottom: 1rem;
        }

        .feature-section .btn {
            background-color: #4b5320;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 0.6rem 1.5rem;
        }

        .feature-section .btn:hover {
            background-color: #7a8c55;
        }

        .feature-section img {
            max-width: 70%;
            height: auto;
            border-radius: 10px;
        }

        /* Subtítulo */
        .section-subtitle {
            text-align: center;
            margin: 2rem 0;
            font-size: 1.5rem;
            font-weight: bold;
            color: #4b5320;
            text-decoration: underline;
        }

        /* Footer */
        footer {
            margin-top: 2rem;
        }
    </style>
</head>

<body>
    <!-- Header con navegación integrada -->
    <header class="custom-header text-white py-3">
        <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between">
            <div>
                <a href="index.php"><img src="img/logo_sin-fondo-blanco.png" style="max-width: 50%; height: auto;"></a>
                <p class="mb-0 d-none d-md-block">Equilibra tu tiempo y energía, equilibra tu vida</p>
            </div>
            <nav class="mt-3 mt-md-0">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a href="ayuda.php" class="nav-link text-white">Ayuda</a>
                    </li>
                    <li class="nav-item">
                        <a href="acerca.php" class="nav-link text-white">Acerca de</a>
                    </li>

                    <?php 
                        if(isset($_SESSION['id_usuario'])){                        
                    ?>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle text-white" id="dashboardDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Cuenta
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dashboardDropdown">
                            <li><a href="agenda.php" class="dropdown-item">Dashboard</a></li>
                            <li><a href="includes/procesar_sesion.php?accion=logout" class="dropdown-item">Cerrar sesión</a></li>
                        </ul>
                    </li>
                    <?php
                        }else{
                        ?>
                            <li class="nav-item">
                                <a href="usuario_Ini.php" class="nav-link text-white">Log in</a>
                            </li>
                            <li class="nav-item">
                                <a href="usuario_Reg.php" class="nav-link text-white">Sign in</a>
                            </li>
                        <?php 
                        }
                    ?>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <div class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>Equilibra tu tiempo y tu energía</h1>
            <p>Organiza tu vida con Timergy</p>
            <a href="usuario_Reg.php" class="cta-button">Comienza ahora</a>
        </div>
    </div>

    <!-- Nueva Sección -->
    <div class="feature-section">
        <div class="container">
            <div class="row align-items-center">
                <!-- Columna de imagen -->
                <div class="col-md-6">
                    <img src="img/agenda-clock.jpg" alt="Gestión de tiempo e imagen decorativa">
                </div>
                <!-- Columna de texto -->
                <div class="col-md-6">
                    <h2>Otro INCREÍBLE uso de convencimiento</h2>
                    <p>Aquí puedes administrar tu agenda semanal, analizar tus descansos y mucho más.</p>
                    <a href="acerca.php" class="btn">Leer más</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Subtítulo de transición -->
    <div class="section-subtitle">¿Por qué TIMERGY?</div>

    <!-- Main Content -->
    <main class="container py-5">
        <div class="row align-items-center mb-5">
            <div class="col-md-6">
                <h2>¿Por qué Timergy?</h2>
                <p>¡Es hora de que descubras Timergy, la herramienta que cambiará la forma en que gestionas tu tiempo y organizas tus días! Timergy te permite crear un horario claro y bien estructurado en cuestión de minutos. Organizar tu tiempo no solo mejora tu eficiencia, también reduce el estrés y te ayuda a equilibrar tu vida para que puedas disfrutar cada momento.</p>
            </div>
            <div class="col-md-6">
                <img src="img/tiempo-y-energía-gtd.webp" alt="Timergy Benefits" class="feature-image" style="max-width: 100%; height: auto; border-radius: 5px;">
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3">
        <div class="container">
            <p>&copy; Timergy © 2024 - Web Wizards</p>
            <a href="#" class="text-white">Política de privacidad</a> |
            <a href="#" class="text-white">Términos y condiciones</a>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
