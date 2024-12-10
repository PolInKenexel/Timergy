<?php 
    include("includes/procesar_sesion.php");
?>
<?php 
    if (isset($_POST['enviarLog'])) {
        login();
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <style>
        /* Estilo CSS */

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #c5cbb3; /* Color de fondo similar al de la imagen */
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 900px;
        }

        .text-and-images {
            background-color: #dde1d4; /* Fondo de texto e imágenes */
            padding: 20px;
            text-align: center;
        }

        .text-and-images h1 {
            color: #4b5841; /* Color del texto del título */
            font-size: 24px;
            margin-bottom: 20px;
        }

        .text-and-images .images {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: center;
        }

        .text-and-images .images img {
            width: 90%; /* Ajustar tamaño de imágenes */
            border-radius: 5px;
        }

        .login-form {
            background-color: #ffffff;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-form form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .login-form label {
            font-size: 16px;
            color: #4b5841;
        }

        .login-form input {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
        }

        .login-form button {
            padding: 10px;
            font-size: 16px;
            background-color: #4b5841;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login-form button:hover {
            background-color: #6b7c5c;
        }

        .back-link {
            margin-top: 10px;
            font-size: 14px;
            color: #4b5841;
            text-decoration: none;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: #6b7c5c;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-content">
            <div class="text-and-images">
                <h1>¿Listo para empezar?</h1>
                <div class="images">
                    <img src="img/3.jpg" alt="Reloj de organización">
              
                </div>
            </div>
            <div class="login-form">
                <form action="usuario_Ini.php" method="POST">
                    <label for="login_email">Correo electrónico:</label>
                    <input type="email" name="email" id="login_email" required>
                    <label for="login_contrasenia">Contraseña:</label>
                    <input type="password" name="contrasenia" id="login_contrasenia" maxlength="30" required>
                    <button type="submit" name="enviarLog">Iniciar sesión</button>
                </form>
                <a href="index.php" class="back-link">Regresar al inicio</a>
            </div>
        </div>
    </div>
</body>
</html>
