<?php 
    // Iniciar sesión
    session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timergy - Ayuda</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

<section>
    <h2>¿como usar mi agenda?</h2>
    <p>al tener tu secion iniciada, debes hacer click sobre el tetxo "cuenta" y se desplegaran dos opciones, debes hacer click sobre dashboard; te redirigira a una nueva pestaña donde tendras en la parte superior diferentes acciones "seguimiento", "editar", "graficas", "actividades" y a la parte de la izquierda un boton que despliega una sidebar donde se muestran las diferentes agendas</p>
    	<li><strong>boton desplegable:</strong> aqui puedes crear nuevas agendas dependiendo de la necesidad del usuario, por ejemplo "semana de clases", "vacaciones de invierno", "jornada de trabajo semanal", etc.</li>
    	<li><strong>seguimiento:</strong> sigvue pendiente esta herramienta que te ayudara a mejorar tu desempeño como civili de la sociedad.</li>
    	</ul>
    	<li><strong>editar:</strong>primero debes dfe seleccionar una de las agendas que ya creaste y te mostrara algo similar a una hoja de excell, donde muestra los dias de lunes a domingo, diviendo cada dia en bloques de una hora desde las 00:00 hasta las 24:00.
    		la pagina web te mostrara 4 botones a la izquierda de este horario semanalas: "nuevo", "editar", "eliminar" y "guardar".<br>
    		<p><b>nuevo: </b>aqui podras agregar un nuevo bloque de tipo generico, prioritario o wildblock,. le agregas un titulo, añades una nota acerca de la actividad, el tipo de actividad y seleccionas el color a tu gusto</p><br><p><b>editar: </b>aqui podras editar los atributos que anteriuormente le otorgaste al bloque que selecciones</p><br><p><b>eliminar: </b>con este puedes eliminar por completo el bloque que hayas seleccionado</p><br><p><b>guardar: </b>con este boton podras guardar los cambios realizados anteriormente en tu agenda</p></li>
    	<li><strong>graficas: </strong>aqui se muestran tus graficas con las que podras obervar tu rendimiento semanal.</li>
    	<li><strong>actividades:</strong>aqui podras agregar nuevas actividades, como "meditar", "trabajar", "hacer tarea", etc. y lo podras guardar dentro de distintas categorias: "fisico", "emocional", "mental" y "espiritual" principalmente pues estas son los 4 tipos de energias que se nos dan a conocer en el libro “The Power of Full Engagement: Managing Energy, Not Time, Is the Key to High Performance and Personal Renewal” (2005) por James E. Loehr y Tony Schwartz (especializados en el área de emprendimiento y psicología respectivamente).</li>
		</ul>


    <h3>¿como crear una cuenta?</h3>
    <p>pones tu correo, contraseña y verificacion de contraseña, despues haces click al boton de "registrarse"</p>
    	
    <h3>¿como iniciar sesion?</h3>
    <p>pones tu correo y tu contraseña, despues haces click al boton de iniciar secion</p>

    <h2>¿mi informacion esta segura?</h2>
    <p>claro, en web wizards nos importa mucho la integridad de los datos de nuestros usuarios</p>
    <ul>
        <li><strong>¿seguro?:</strong> asi es.</li>
        <li><strong>¿deberia confiar?:</strong> totalmente, puedes ver las "Política de privacidad" y "Términos y condiciones" en la parte baja de la pagina web.</li>
    </ul>

    <h3>cerrar sesion</h3>
    <p>para esto debes tener tu sesion iniciada en el dispositivo</p>
    <ul>
        <p>en el area de arriba se muestra la palabra "cuenta", debes hacer click sobre ella y despues se desplegaran varias opciones, ahi es donde se mostrara "cerrar sesion, haces click sobre ella y habras cerrado tu sesion"</p>
    </ul>
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