<?php
include "includes/registro.php";
include "includes/procesar_sesion.php";
?>
<?php
if (isset($_POST['enviarReg'])) {
    $email = $_POST['email'];
    $contrasenia = $_POST['contrasenia'];

    $proceso = addUsuario($email, $contrasenia);

    if ($proceso == true) {
        login();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <style>
        /* Estilo CSS */

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #dde1d4; /* Color de fondo */
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .content {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 500px;
            padding: 30px;
        }

        .title {
            color: #4b5841; /* Color del texto del título */
            font-size: 36px; /* Tamaño grande del título */
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        form label {
            font-size: 16px;
            color: #4b5841;
        }

        form input {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
        }

        form button {
            padding: 10px;
            font-size: 16px;
            background-color: #4b5841;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form button:hover {
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
    <div class="container">
        <div class="content">
            <div class="title">Registro</div>
            <form action="usuario_Reg.php" method="POST">
                <label for="email">Correo electrónico:</label>
                <input type="email" name="email" id="email" required>
                <label for="contrasenia">Contraseña:</label>
                <input type="password" name="contrasenia" id="contrasenia" maxlength="30" required>
                <button type="submit" name="enviarReg">Registrarse</button>
            </form>
            <a href="index.php" class="back-link">Regresar al inicio</a>
        </div>
    </div>
</body>
</html>
