<?php
    include_once("conectar.php");
    //Iniciar sesión
    function login() {
        $conn = connection();
        session_start();

        $email = trim($_POST['email']);
        $contrasenia = trim($_POST['contrasenia']); 

        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $usuario = $result->fetch_assoc();

            if ($usuario['status'] == 'activo') {

                if ($contrasenia === $usuario['contrasenia']) {
                    $_SESSION['id_usuario'] = $usuario['ID_Usuario'];

                    // Redireccionar al usuario a la página deseada (index.php en este caso)
                    header("Location: index.php");
                    exit();
                } else {
                    echo "Contraseña incorrecta.";
                }
            } else {
                echo "Tu cuenta está inactiva.";
            }
        } else {
            echo "Usuario no encontrado.";
        }

        $stmt->close();
        $conn->close();
    }
    
    //Cerrar sesión
    function logout(){
        session_start();
        session_unset(); // Elimina todas las variables de sesión
        session_destroy(); // Destruye la sesión actual
        header("Location: ../index.php");
    }
    if (isset($_GET['accion']) && $_GET['accion'] == 'logout') {
        logout();
    }
?>