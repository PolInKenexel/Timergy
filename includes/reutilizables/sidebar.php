<?php 
include("includes/informes.php");
include("includes/registro.php");
include_once ('includes/modificacion.php');
include_once ('includes/eliminacion.php');

if (isset($_SESSION['id_usuario'])) {   
    
    
    echo "<script> messageTEST = null; </script>"; 
    $usuario = getSeparatedUser($_SESSION['id_usuario']);
    $agendas = getAgendasFromUser($_SESSION['id_usuario']);  

    // Obtener el ID de la agenda desde la URL
    $agenda_id_actual = isset($_GET['agenda_id']) ? (int) $_GET['agenda_id'] : 0;
    
    // Inicializar variables
    $agenda_act = null;

    if ($agenda_id_actual > 0) {
        // Consultar la base de datos para obtener los detalles del producto
        $agenda_act = getSeparatedAgenda($agenda_id_actual);
    }

    if (isset($_POST['crearAgen'])) {
        $nombre = $_POST['agendaNombre'];

        $agenda_act = addAgenda($nombre, $_SESSION['id_usuario']);

        header("Location: edicionAgen.php?agenda_id=$agenda_act");
    }

    if (isset($_POST['action']) && $_POST['action'] === 'change_password') {
        $currentPassword = $_POST['currentPassword'] ?? '';
        $newPassword = $_POST['newPassword'] ?? '';
        $confirmPassword = $_POST['confirmPassword'] ?? '';

        // Validar nueva contraseña
        if ($newPassword !== $confirmPassword) {
            $message = 'La nueva contraseña y su confirmación no coinciden.';
        } else {
            $currentPasswordJS = json_encode($currentPassword);
            $storedPasswordJS = json_encode($usuario['contrasenia'] ?? '');

            echo "<script> console.log('Contraseña actual ingresada: ', $currentPasswordJS); </script>";
            echo "<script> console.log('Contraseña almacenada en base de datos: ', $storedPasswordJS); </script>";
            if ($currentPassword === $usuario['contrasenia']) {
                $actualizado = updateUsuarioPassword($_SESSION['id_usuario'], $newPassword);
                if ($actualizado) {
                    $message = 'Contraseña actualizada exitosamente.';
                } else {
                    $message = 'No se pudo actualizar la contraseña. Inténtalo de nuevo.';
                }
            } else {
                $message = 'La contraseña actual es incorrecta.';
            }
        }
        echo "<script> messageTEST = '$message'; </script>";
    }

    if (isset($_POST['action']) && $_POST['action'] === 'delete_account') {
        $eliminado = deleteUsuarioById($_SESSION['id_usuario']);
        if ($eliminado) {
            session_destroy();
            header('Location: index.php'); // Redirige al inicio tras borrar la cuenta
            exit;
        } else {
            $message = 'No se pudo eliminar la cuenta. Inténtalo de nuevo.';
        }
        echo "<script> messageTEST = '$message'; </script>";
    }

    // Procesar el cambio de nombre de la agenda
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevoNombreAgenda'])) {
        $nuevoNombre = trim($_POST['nuevoNombreAgenda']);
        if (!empty($nuevoNombre)) {
            $nombreActualizado = updateAgendaName($agenda_id_actual, $nuevoNombre);
            if ($nombreActualizado) {
                // Refrescar la página para mostrar el nuevo nombre
                header("Location: edicionAgen.php?agenda_id=$agenda_id_actual");
                exit;
            } else {
                $errorActualizarNombre = "No se pudo actualizar el nombre de la agenda. Inténtalo nuevamente.";
            }
        } else {
            $errorActualizarNombre = "El nombre de la agenda no puede estar vacío.";
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrarGraficas'])) {
        // Llama a la función para eliminar las gráficas de la agenda
        $resultado = deleteGraphsByAgenda($agenda_id_actual);
    
        if ($resultado) {
            // Mostrar un mensaje temporal y recargar la página
            echo "<script>alert('Gráficas borradas exitosamente.');</script>";
            header("Location: edicionAgen.php?agenda_id=$agenda_id_actual");
            exit;
        } else {
            $errorBorradoGraficas = "No se pudieron borrar las gráficas. Intenta nuevamente.";
        }
    }
?>

<!-- Sidebar -->
<div class="w3-sidebar w3-bar-block w3-border-right" style="display:none; z-index: 9999;" id="mySidebar">
    <button onclick="w3_close()" class="w3-bar-item w3-large">Cerrar &times;</button>
    <!-- Mostrar agendas traídas de la base de datos -->
    <?php 
    if ($agendas) {
        foreach ($agendas as $agenda) {
            echo '<a href="agenda.php?agenda_id=' . $agenda['ID_Agenda'] . '" class="w3-bar-item w3-button">' . htmlspecialchars($agenda['nombre']) . '</a>';
        }
    } else {
        echo '<p class="w3-bar-item">No tienes agendas disponibles.</p>';
    }
    ?>
    <!-- Botón para añadir una nueva agenda -->
    <a class="w3-bar-item w3-button w3-hover-light-gray" style="text-align: center; font-size: 1.5em;" onclick="openSidebarModal()">+</a>
</div>

<!-- Header con Tabs FALSO -->
<div class="w3-bar w3-light-grey w3-border-bottom w3-flex">
    <div style="display: flex; flex-grow: 1; align-items: center;">
        <!-- Botón del sidebar -->
        <button class="w3-bar-item w3-button w3-hover-teal w3-xlarge" onclick="w3_open()">☰</button>
        
        <!-- Logo -->
        <img src="img/logo_sin-fondo.png" alt="Logo" style="height:40px; margin: 5px; cursor: pointer;" onclick="location.href='index.php';">
    </div>
</div>

<!-- Header con Tabs -->
<div class="w3-bar w3-light-grey w3-border-bottom w3-flex fixed-header">
    <div style="display: flex; flex-grow: 1; align-items: center;">
        <!-- Botón del sidebar -->
        <button class="w3-bar-item w3-button w3-hover-teal w3-xlarge" id="nav-tab" onclick="w3_open()">☰</button>
        
        <!-- Logo -->
        <img src="img/logo_sin-fondo.png" alt="Logo" style="height:40px; margin: 5px; cursor: pointer;" onclick="location.href='index.php';">
        
        <!-- Tabs del header -->
        <a href="agenda.php<?php echo $agenda_id_actual > 0 ? '?agenda_id=' . $agenda_id_actual : ''; ?>" 
        class="w3-bar-item w3-button w3-hover-teal nav-tab <?php echo basename($_SERVER['PHP_SELF']) == 'agenda.php' ? 'active-tab' : ''; ?>" id="nav-tab">
            Seguimiento
        </a>
        <a href="edicionAgen.php<?php echo $agenda_id_actual > 0 ? '?agenda_id=' . $agenda_id_actual : ''; ?>" 
        class="w3-bar-item w3-button w3-hover-teal nav-tab <?php echo basename($_SERVER['PHP_SELF']) == 'edicionAgen.php' ? 'active-tab' : ''; ?>" id="nav-tab">
            Editar
        </a>
        <a href="graficas.php<?php echo $agenda_id_actual > 0 ? '?agenda_id=' . $agenda_id_actual : ''; ?>" 
        class="w3-bar-item w3-button w3-hover-teal nav-tab <?php echo basename($_SERVER['PHP_SELF']) == 'graficas.php' ? 'active-tab' : ''; ?>" id="nav-tab">
            Gráficas
        </a>
        <a href="listaActividades.php<?php echo $agenda_id_actual > 0 ? '?agenda_id=' . $agenda_id_actual : ''; ?>" 
        class="w3-bar-item w3-button w3-hover-teal nav-tab <?php echo basename($_SERVER['PHP_SELF']) == 'listaActividades.php' ? 'active-tab' : ''; ?>" id="nav-tab">
            Actividades
        </a>

        <!-- Espaciador para empujar el dropdown hacia la derecha -->
        <div style="margin-left: auto; position: relative; display: flex; align-items: center; z-index: 1000;">
            <!-- Dropdown -->
            <span class="w3-bar-item w3-button" style="cursor: pointer;" onclick="toggleDropdown()"> 
                <?php echo htmlspecialchars($usuario['email']); ?> ▼
            </span>
            <div id="dropdownMenu" 
                class="w3-dropdown-content w3-bar-block w3-card-4" 
                style="display: none; position: fixed; right: 20px; top: 60px; z-index: 9998; background: white; border: 1px solid #ccc; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
                <a href="javascript:void(0)" onclick="openConfigModal()" class="w3-bar-item w3-button">Configuración</a>
                <a href="includes/procesar_sesion.php?accion=logout" class="w3-bar-item w3-button">Cerrar sesión</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="agendaModal" class="modal">
    <div class="modal-content">
        <h2>Crear Nueva Agenda</h2>
        <form action="agenda.php" method="post">
            <label for="agendaNombre">Nombre de la Agenda</label>
            <input type="text" id="agendaNombre" maxlength="30" name="agendaNombre" required placeholder="Escribe el nombre...">
            <div class="modal-buttons">
                <button type="button" class="btn cancelar" onclick="closeSidebarModal()">Cancelar</button>
                <button type="submit" class="btn confirmar" name="crearAgen">Crear</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Configuración -->
<div id="configModal" class="modal">
    <div class="modal-content">
        <h2>Opciones de Usuario</h2>

        <!-- Cambiar Contraseña -->
        <div>
            <h3>Cambiar Contraseña</h3>
            <form action="" method="POST">
                <input type="hidden" name="action" value="change_password">
                <label for="currentPassword">Contraseña Actual</label>
                <input type="password" id="currentPassword" name="currentPassword" required placeholder="Escribe tu contraseña actual">
                
                <label for="newPassword">Nueva Contraseña</label>
                <input type="password" id="newPassword" name="newPassword" required placeholder="Escribe tu nueva contraseña">
                
                <label for="confirmPassword">Confirmar Nueva Contraseña</label>
                <input type="password" id="confirmPassword" name="confirmPassword" required placeholder="Confirma tu nueva contraseña">
                
                <button type="submit" class="btn confirmar">Actualizar Contraseña</button>
            </form>
        </div>
        
        <!-- Borrar Cuenta -->
        <div style="margin-top: 20px;">
            <h3>Borrar Cuenta</h3>
            <p><strong>Advertencia:</strong> Esta acción es irreversible. Se eliminarán todos tus datos.</p>
            <form action="" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer.')">
                <input type="hidden" name="action" value="delete_account">
                <button type="submit" class="btn cancelar">Eliminar Cuenta</button>
            </form>
        </div>

        <!-- Botón de cierre -->
        <div class="modal-buttons">
            <button class="btn cancelar" onclick="closeConfigModal()">Cerrar</button>
        </div>
    </div>
</div>

<script>
    if(messageTEST){
        showTemporaryMessage(messageTEST, 3000);
    }

    function w3_open() {
        document.getElementById("mySidebar").style.display = "block";
    }

    function w3_close() {
        document.getElementById("mySidebar").style.display = "none";
    }

    // Función para mostrar/ocultar el menú desplegable
    function toggleDropdown() {
        const dropdown = document.getElementById('dropdownMenu');
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }

    // Cerrar el dropdown si se hace clic fuera de él
    window.onclick = function(event) {
        if (!event.target.matches('.w3-bar-item.w3-button')) {
            const dropdown = document.getElementById('dropdownMenu');
            if (dropdown.style.display === 'block') {
                dropdown.style.display = 'none';
            }
        }
    };

    // Función para abrir el modal
    function openSidebarModal() {
        document.getElementById("agendaModal").style.display = "flex";
    }

    // Función para cerrar el modal
    function closeSidebarModal() {
        document.getElementById("agendaModal").style.display = "none";
    }

    // Cerrar el modal si se hace clic fuera de él
    window.onclick = function(event) {
        const modal = document.getElementById("agendaModal");
        if (event.target === modal) {
            closeSidebarModal();
        }
    };

        // Función para mostrar el mensaje
    function showTemporaryMessage(message, duration) {
        // Crear el contenedor del mensaje
        const messageBox = document.createElement('div');
        messageBox.textContent = message;
        messageBox.style.position = 'fixed';
        messageBox.style.bottom = '10px';
        messageBox.style.right = '10px';
        messageBox.style.backgroundColor = '#333';
        messageBox.style.color = '#fff';
        messageBox.style.padding = '10px 20px';
        messageBox.style.borderRadius = '5px';
        messageBox.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.3)';
        messageBox.style.zIndex = '1000';
        document.body.appendChild(messageBox);

        // Eliminar el mensaje después del tiempo indicado
        setTimeout(() => {
            messageBox.remove();
        }, duration);
    }

    // Abrir el modal de Configuración
    function openConfigModal() {
        document.getElementById("configModal").style.display = "flex";
    }

    // Cerrar el modal de Configuración
    function closeConfigModal() {
        document.getElementById("configModal").style.display = "none";
    }

    // Cerrar el modal si se hace clic fuera del contenido
    window.onclick = function(event) {
        const modal = document.getElementById("configModal");
        if (event.target === modal) {
            closeConfigModal();
        }
    };
</script>

<?php
} else {
    header("Location: index.php");
}
?>