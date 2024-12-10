<?php 
// Iniciar sesión
session_start();
if (!isset($_SESSION['bloques'])) {
    $_SESSION['bloques'] = [];
    $_SESSION['bloque_id_counter'] = 0; // Inicializar contador si no existe
}

// Incluir sidebar
include 'includes/reutilizables/sidebar.php';

$URLLegal = false;

if ($agenda_act) {
    foreach ($agendas as $agenda) {
        if ($agenda['ID_Agenda'] == $agenda_id_actual) {
            $URLLegal = true;
        }
    }
}

if ($URLLegal) {
    $actividadesDepleting = getActividadesPorTipo('depleting', $_SESSION['id_usuario']);
    $actividadesRenewing = getActividadesPorTipo('renewing', $_SESSION['id_usuario']);

    $listaActividades = array_merge($actividadesDepleting, $actividadesRenewing);

    // Inicializar lista de IDs existentes desde la base de datos
    initializeExistingBlockIds($agenda_id_actual);

    // Actualizar siempre la sesión con los bloques obtenidos
    $bloquesSesion = getFormattedBlocksForSession($agenda_id_actual);
    $_SESSION['bloques'] = $bloquesSesion;

    // Pasar todos los detalles al frontend
    $bloquesBD = getAllBlocksWithDetails($agenda_id_actual);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Variables META de PHP a JavaScript -->
        <meta name="agenda_id_actual" content="<?php echo htmlspecialchars($agenda_id_actual, ENT_QUOTES, 'UTF-8'); ?>">

        <title>Edición: <?php echo $agenda_act['nombre'] ?></title>
        <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js" defer></script>
        <link rel="stylesheet" href="CSS/estilosEdicionAgen.css">
        <link rel="stylesheet" href="CSS/METAestilosModals.css">
        <link rel="stylesheet" href="CSS/METAestilosDefault.css">

        <!-- w3schools CSS -->
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    </head>
    <body>
        <!-- Titulo de la página -->
        <div class="w3-container" style="background-color: #f0f0f0; box-shadow: 0px 4px 2px -2px rgba(0, 0, 0, 0.1);">
            <h1>Edición de agenda: <?php echo $agenda_act['nombre'] ?></h1>
        </div>

        <!-- Indicador de días -->
        <div class="estructuraBanner">
            <div class="semana">
                <div class="dia">Lunes</div>
                <div class="dia">Martes</div>
                <div class="dia">Miércoles</div>
                <div class="dia">Jueves</div>
                <div class="dia">Viernes</div>
                <div class="dia">Sábado</div>
                <div class="dia">Domingo</div>
            </div>
        </div>

        <div class="estructura">
            <!-- Contenedor de botones -->
            <div class="botones">
                <button class="btn nuevo">Nuevo</button>
                <button class="btn editar">Editar</button>
                <button class="btn eliminar">Eliminar</button>
                <button class="btn guardar">Guardar</button>
            </div>

            <!-- Agenda -->
            <div class="horario">
                <!-- Generar dinámicamente las horas -->
                <?php for ($i = 0; $i < 24; $i++): ?>
                    <div class="hora"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>:00</div>
                <?php endfor; ?>
            </div>
            <div class="container"></div>
        </div>

        <!-- Modal para crear un bloque -->
        <div id="modalNuevoBloque" class="modal">
            <div class="modal-content">
                <h2 id="tituloFormularioModal"></h2>
                <form id="formNuevoBloque">
                    <label for="tipoBloque">Tipo de Bloque:</label>
                    <select id="tipoBloque" name="tipoBloque">
                        <option value="generico" selected>Genérico</option>
                        <option value="prioritario">Prioritario</option>
                        <option value="wildblock">Wildblock</option>
                    </select>

                    <label for="tituloBloque">Título:</label>
                    <input id="tituloBloque" type="text" maxlength="25" placeholder="Máximo 25 caracteres">

                    <label for="notasBloque">Notas:</label>
                    <textarea id="notasBloque" maxlength="500" placeholder="Máximo 500 caracteres"></textarea>

                    <select id="modalInputActivity" name="actividad">
                        <option value="" disabled selected>Seleccionar actividad</option>
                        <?php foreach ($listaActividades as $actividad): ?>
                            <option value="<?php echo $actividad['ID_Actividad']; ?>">
                                <?php echo htmlspecialchars($actividad['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label id="colorBloqueLabel" for="colorBloque">Color:</label>
                    <input id="colorBloque" type="color" value="#ffffff">

                    <div class="modal-buttons">
                        <button type="button" id="cancelarNuevoBloque" class="btn cancelar">Cancelar</button>
                        <button type="button" id="confirmarNuevoBloque" class="btn confirmar">Confirmar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal de Confirmación -->
        <div id="confirmGuardarModal" class="modal">
            <div class="modal-content">
                <h2>Confirmar Guardado</h2>
                <p>¿Estás seguro de que deseas guardar los cambios? Esta acción sobrescribirá la estructura anterior de la agenda.</p>
                <div class="modal-buttons">
                    <button id="cancelarGuardar" class="btn cancelar">Cancelar</button>
                    <button id="confirmarGuardar" class="btn confirmar">Confirmar</button>
                </div>
            </div>
        </div>

        <!-- Formulario para cambiar el nombre de la agenda -->
        <div class="w3-container" style="padding: 20px; background: #f0f0f0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
            <h3>Cambiar Nombre de la Agenda</h3>
            <?php if (!empty($errorActualizarNombre)): ?>
                <div style="color: red;"><?php echo htmlspecialchars($errorActualizarNombre); ?></div>
            <?php endif; ?>
            <form action="edicionAgen.php<?php echo $agenda_id_actual > 0 ? '?agenda_id=' . $agenda_id_actual : ''; ?>" method="post">
                <label for="nuevoNombreAgenda">Nuevo Nombre:</label>
                <input type="text" id="nuevoNombreAgenda" name="nuevoNombreAgenda" maxlength="50" value="<?php echo htmlspecialchars($agenda_act['nombre']); ?>" required style="width: 100%; padding: 8px; margin-top: 5px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px;">
                <button type="submit" class="w3-button w3-teal" style="padding: 10px 15px;">Guardar Cambios</button>
            </form>
        </div>

        <form action="edicionAgen.php<?php echo $agenda_id_actual > 0 ? '?agenda_id=' . $agenda_id_actual : ''; ?>" method="post" style="background-color: #f0f0f0">
            <button type="submit" name="borrarGraficas" style="margin-bottom: 10px;
    padding: 10px 15px;
    border: none;
    margin-left: 15px;
    border-radius: 5px;
    color: #fff;
    font-size: 14px;
    cursor: pointer;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    transition: background-color 0.3s ease;background-color: #f44336; color: white; transition: background-color 0.3s ease;">Borrar Agenda</button>
        </form>

        <script>
            // Pasar el array de PHP como JSON al JavaScript
            const bloquesBD = <?php echo json_encode($bloquesBD); ?>;
            console.log(bloquesBD);
        </script>
        <script type="module" src="JavaScript/GRUPOedicionAgen/edicionAgen.js"></script>
    </body>
    <footer style="text-align: center; font-size: 0.8em; color: #a0a0a0; background-color: #f0f0f0;">
        © 2024 Timergy. All rights reserved.
    </footer>
</html>

<?php }else{ ?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edición</title>
        <link rel="stylesheet" href="CSS/METAestilosDefault.css">
        <link rel="stylesheet" href="CSS/METAestilosModals.css">

        <!-- w3schools CSS -->
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    </head>
    <body>
        <!-- Titulo de la página -->
        <div class="w3-container">
            <div class="agenda-card">
                <h1>Edición de agenda</h1>
                <p>Parece que hubo un error... intente seleccionar una de sus agendas.</p>
            </div>
        </div>
    </body>
    <footer style="text-align: center; font-size: 0.8em; color: #a0a0a0;">
        © 2024 Timergy. All rights reserved.
    </footer>
</html>

<?php } ?>

<?php 
/**
 * Inicializa los IDs de los bloques existentes y ajusta el contador.
 * @param int $id_agenda - ID de la agenda actual.
 */
function initializeExistingBlockIds($id_agenda) {
    // Obtener todos los bloques desde la base de datos
    $bloquesBD = getAllBlocksWithDetails($id_agenda);

    // Crear una lista con los IDs existentes
    $_SESSION['existingBlockIds'] = array_map(fn($bloque) => $bloque['ID_Bloque'], $bloquesBD);

    // Ajustar el contador al mayor ID encontrado
    if (!empty($_SESSION['existingBlockIds'])) {
        $_SESSION['bloque_id_counter'] = max($_SESSION['existingBlockIds']);
    }
}
?>