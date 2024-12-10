<?php 
    //iniciar sesion
    session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timergy - Acerca de</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Enlazar el archivo CSS externo -->
    <link href="CSS/acerca.css" rel="stylesheet">
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

<!-- Sección existente -->
<section class="py-5">
    <div class="container text-center">
        <h2 class="fw-bold" style="color: #2f4f2f; font-family: 'Georgia', serif;">¿Qué es nuestra agenda?</h2>
        <p class="mt-4" style="color: #4f4f4f; font-size: 1.1rem; font-family: 'Georgia', serif; line-height: 1.6;">
            Nuestra agenda es una herramienta diseñada para ayudarte a organizar tu tiempo de manera eficiente y evitar la saturación de actividades. 
            Su objetivo es proporcionarte claridad sobre tus tareas diarias, mientras cuida de tu bienestar personal.
        </p>
    </div>
</section>

<!-- Nueva sección basada en la imagen -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Imagen -->
            <div class="col-md-6 image-section">
                <img src="img/4.jpg" alt="Descripción de la imagen"> <!-- Cambia 'tu-imagen.jpg' por el archivo correspondiente -->
            </div>
            <!-- Características principales -->
            <div class="col-md-6">
                <h3 class="section-title">Características principales</h3>
                <ul class="feature-list mt-3 section-subtitle">
                    <li>Organización de tareas por prioridad y tipo.</li>
                    <li>Gráficos que te muestran información detallada sobre tus actividades y cómo afectan tu energía.</li>
                    <li>Sugerencias para distribuir tus descansos de manera óptima.</li>
                </ul>
            </div>
        </div>
        <div class="row mt-5">
            <!-- Tipos de energía -->
            <div class="col-md-6">
                <h3 class="section-title">Tipos de energías que monitoreamos</h3>
                <ul class="energy-types mt-3 section-subtitle">
                    <li><strong>Física:</strong> Relacionada con tus actividades corporales.</li>
                    <li><strong>Emocional:</strong> Conexión con tus emociones y relaciones.</li>
                    <li><strong>Mental:</strong> Procesos de pensamiento, concentración, aprendizaje.</li>
                    <li><strong>Espiritual:</strong> Sentido de propósito y conexión interior.</li>
                </ul>
            </div>
            <!-- Imagen secundaria -->
            <div class="col-md-6 image-section">
                <img src="img/4.jpg" alt="Descripción de la segunda imagen"> <!-- Cambia 'tu-imagen-2.jpg' -->
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-3">
    <div class="container">
        <p>&copy; Timergy © 2024 - Web Wizards</p>
        <a href="#" class="text-white">Política de privacidad</a> |
        <a href="#" class="text-white">Términos y condiciones</a>
    </div>
</footer>

<!-- Scripts de Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
