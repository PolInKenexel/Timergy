<?php
//iniciar sesion
session_start();

//incluir sidebar
include 'includes/reutilizables/sidebar.php';

include_once 'includes/registro.php';
include_once 'includes/informes.php';
include_once 'includes/eliminacion.php';
include_once 'includes/modificacion.php';

if (isset($_POST['enviarAct'])) {
    $agenda_id = isset($_GET['agenda_id']) ? (int) $_GET['agenda_id'] : 0;
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $tipo = $_POST['tipo'];
    $id_categoria = $_POST['categoria'];
    $id_usuario = $_SESSION['id_usuario'];
    addActividad($titulo, $descripcion, $tipo, $id_categoria, $id_usuario);
}


if (isset($_POST['enviarCat'])) {
    $agenda_id = isset($_GET['agenda_id']) ? (int) $_GET['agenda_id'] : 0;
    $titulo = $_POST['titulo'];
    $color = $_POST['color'];
    $id_usuario = $_SESSION['id_usuario'];
    addCategoria($titulo, $color, $id_usuario);

}

if (isset($_POST['eliminarActividad'])) {
    $agenda_id = isset($_GET['agenda_id']) ? (int) $_GET['agenda_id'] : 0;
    $id_actividad = $_POST['id_actividad'];

    // Primero eliminar las relaciones en la tabla intermedia
    eliminarRelacionesActividad($id_actividad);

    // Luego eliminar la actividad
    eliminarActividad($id_actividad);
}

if (isset($_POST['eliminarCategoria'])) {
    $id_categoria = $_POST['id_categoria'];
    eliminarRelacionesCategoria($id_categoria);
    eliminarCategoria($id_categoria);
}

if (isset($_POST['eliminar_categoria_actividad'])) {
    $id_actividad = $_POST['id_actividad'];
    $id_categoria = $_POST['id_categoria'];

    // Verificar cuántas categorías tiene la actividad
    $categoriasAsignadas = contarCategoriasPorActividad($id_actividad);

    if ($categoriasAsignadas > 1) {
        // Eliminar la relación entre la actividad y la categoría
        eliminarRelacionActividadCategoria($id_actividad, $id_categoria);
        $mensaje = "Categoría eliminada exitosamente.";
    } else {
        $mensaje = "La actividad debe tener al menos una categoría asignada.";
    }
}

if (isset($_POST['agregarCategoriaActividad'])) {
    $agenda_id = isset($_GET['agenda_id']) ? (int) $_GET['agenda_id'] : 0;
    $id_actividad = $_POST['id_actividad'];
    $id_categoria = $_POST['id_categoria'];

    agregarCategoriaAActividad($id_actividad, $id_categoria);
}

if (isset($_POST['editarAct'])) {
    $id_actividad = $_POST['id_actividad'];
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];

    modificarActividad($id_actividad, $titulo, $descripcion);
}

if (isset($_POST['editarCat'])) {
    $id_categoria = $_POST['id_categoria'];
    $titulo = $_POST['titulo'];
    $color = $_POST['color'];

    modificarCategoria($id_categoria, $titulo, $color);
}

$id_usuario = $_SESSION['id_usuario'];
$actividadesDepleting = getActividadesPorTipo('depleting', $id_usuario);
$actividadesRenewing = getActividadesPorTipo('renewing', $id_usuario);

$categoriasUsuario = getCategoriasPorUsuario($id_usuario);

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividades</title>
    <link rel="stylesheet" href="CSS/estilosListaActividades.css">
    <link rel="stylesheet" href="CSS/METAestilosDefault.css">
    <link rel="stylesheet" href="CSS/METAestilosModals.css">
    <!-- w3schools CSS -->
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>

<body>
    <!-- Título de la página -->
    <div class="w3-container">
        <h1>Actividades y Categorías</h1>
    </div>


    <div class="container">
        <!-- Section for Cards -->
        <div class="section">
            <h3>Actividades</h3>
            <div class="column" id="cardColumn">
                <!-- Actividades generales -->
            </div>

            <button class="add-card-button" onclick="openActivityModal()">Agregar Nueva Actividad</button>

            <!-- Activity Columns -->
            <div class="activity-columns">
                <div class="activity-column" id="depletingColumn">
                    <h4>Depleting</h4>
                    <?php if (!empty($actividadesDepleting)): ?>
                        <?php foreach ($actividadesDepleting as $actividad): ?>
                            <div class="activity-item">
                                <h4><?php echo htmlspecialchars($actividad['nombre']); ?></h4>
                                <p><?php echo htmlspecialchars($actividad['descripcion']); ?></p>
                                <div class="activity-categories">
                                    <?php
                                    // Aquí obtendrás las categorías relacionadas con la actividad
                                    $categorias = getCategoriasPorActividad($actividad['ID_Actividad']);
                                    foreach ($categorias as $categoria): ?>
                                        <div class="category-badge"
                                            style="background-color: <?php echo htmlspecialchars($categoria['color']); ?>;">
                                            <form method="POST"
                                                action="listaActividades.php<?php echo $agenda_id_actual > 0 ? '?agenda_id=' . $agenda_id_actual : ''; ?>"
                                                style="display: inline;">
                                                <input type="hidden" name="id_actividad"
                                                    value="<?php echo htmlspecialchars($actividad['ID_Actividad']); ?>">
                                                <input type="hidden" name="id_categoria"
                                                    value="<?php echo htmlspecialchars($categoria['ID_Categoria']); ?>">
                                                <input type="hidden" name="eliminar_categoria_actividad" value="1">
                                                <button type="submit" style="all: unset; cursor: pointer;"
                                                    class="delete-category-activity">
                                                    <?php echo htmlspecialchars($categoria['nombre']); ?>
                                                </button>
                                            </form>
                                        </div>
                                    <?php endforeach; ?>
                                </div>



                                <div class="activity-actions">
                                    <!-- Botón para agregar categoría -->
                                    <button class="add-category-to-activity"
                                        onclick="openAddCategoryModal(<?php echo $actividad['ID_Actividad']; ?>)">➕</button>
                                    <button class="edit-activity" onclick="openEditActivityModal(
    <?php echo $actividad['ID_Actividad']; ?>, 
    '<?php echo htmlspecialchars($actividad['nombre']); ?>', 
    '<?php echo htmlspecialchars($actividad['descripcion']); ?>')">✏️</button>
                                    <!-- Formulario para eliminar actividad -->
                                    <form method="POST" style="display: inline;"
                                        action="listaActividades.php<?php echo $agenda_id_actual > 0 ? '?agenda_id=' . $agenda_id_actual : ''; ?>">
                                        <input type="hidden" name="eliminarActividad" value="1">
                                        <input type="hidden" name="id_actividad"
                                            value="<?php echo $actividad['ID_Actividad']; ?>">
                                        <button type="submit" class="delete-activity">🗑️</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No hay actividades de tipo <strong>Depleting</strong></p>
                    <?php endif; ?>
                </div>

                <div class="activity-column" id="renewingColumn">
                    <h4>Renewing</h4>
                    <?php if (!empty($actividadesRenewing)): ?>
                        <?php foreach ($actividadesRenewing as $actividad): ?>
                            <div class="activity-item">
                                <h4><?php echo htmlspecialchars($actividad['nombre']); ?></h4>
                                <p><?php echo htmlspecialchars($actividad['descripcion']); ?></p>
                                <div class="activity-categories">
                                    <?php
                                    // Aquí obtendrás las categorías relacionadas con la actividad
                                    $categorias = getCategoriasPorActividad($actividad['ID_Actividad']);
                                    foreach ($categorias as $categoria): ?>
                                        <div class="category-badge"
                                            style="background-color: <?php echo htmlspecialchars($categoria['color']); ?>;">
                                            <form method="POST"
                                                action="listaActividades.php<?php echo $agenda_id_actual > 0 ? '?agenda_id=' . $agenda_id_actual : ''; ?>"
                                                style="display: inline;">
                                                <input type="hidden" name="id_actividad"
                                                    value="<?php echo htmlspecialchars($actividad['ID_Actividad']); ?>">
                                                <input type="hidden" name="id_categoria"
                                                    value="<?php echo htmlspecialchars($categoria['ID_Categoria']); ?>">
                                                <input type="hidden" name="eliminar_categoria_actividad" value="1">
                                                <button type="submit" style="all: unset; cursor: pointer;" class="delete-category-activity">
                                                    <?php echo htmlspecialchars($categoria['nombre']); ?>
                                                </button>
                                            </form>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="activity-actions">
                                    <!-- Botón para agregar categoría -->
                                    <button class="add-category-to-activity"
                                        onclick="openAddCategoryModal(<?php echo $actividad['ID_Actividad']; ?>)">➕</button>
                                    <button class="edit-activity" onclick="openEditActivityModal(
    <?php echo $actividad['ID_Actividad']; ?>, 
    '<?php echo htmlspecialchars($actividad['nombre']); ?>', 
    '<?php echo htmlspecialchars($actividad['descripcion']); ?>')">✏️</button>
                                    <!-- Formulario para eliminar actividad -->
                                    <form method="POST" style="display: inline;"
                                        action="listaActividades.php<?php echo $agenda_id_actual > 0 ? '?agenda_id=' . $agenda_id_actual : ''; ?>">
                                        <input type="hidden" name="eliminarActividad" value="1">
                                        <input type="hidden" name="id_actividad"
                                            value="<?php echo $actividad['ID_Actividad']; ?>">
                                        <button type="submit" class="delete-activity">🗑️</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No hay actividades de tipo <strong>Renewing</strong></p>
                    <?php endif; ?>
                </div>
            </div>

        </div>



        <!-- Section for Categories -->
        <div class="category-section">
            <h3>Categorías</h3>
            <div class="column" id="categoryColumn">
                <?php if (!empty($categoriasUsuario)): ?>
                    <?php foreach ($categoriasUsuario as $categoria): ?>
                        <div class="category-item"
                            style="background-color: <?php echo htmlspecialchars($categoria['color']); ?>;">
                            <h4><?php echo htmlspecialchars($categoria['nombre']); ?></h4>
                            <div class="category-actions">

                                <?php
                                if ($categoria['id_usuario'] != -1): ?>
                                    <button class="edit-category" onclick="openEditCategoryModal(
    <?php echo $categoria['ID_Categoria']; ?>, 
    '<?php echo htmlspecialchars($categoria['nombre']); ?>', 
    '<?php echo htmlspecialchars($categoria['color']); ?>'
)">✏️</button>

                                    <!-- Formulario para eliminar categoría -->
                                    <form method="POST" style="display: inline;"
                                        action="listaActividades.php<?php echo $agenda_id_actual > 0 ? '?agenda_id=' . $agenda_id_actual : ''; ?>">
                                        <input type="hidden" name="eliminarCategoria" value="1">
                                        <input type="hidden" name="id_categoria" value="<?php echo $categoria['ID_Categoria']; ?>">
                                        <button type="submit" class="delete-category">🗑️</button>
                                    </form>
                                <?php else: ?>
                                    <p></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No tienes categorías registradas.</p>
                <?php endif; ?>
            </div>

            <button class="add-category-button" onclick="openCategoryModal()">Agregar Nueva Categoría</button>
        </div>


    </div>

    <!-- Modal para agregar una categoría a una actividad -->
    <div class="modalLista" id="addCategoryModal">
        <div class="modalLista-content">
            <h3>Agregar Categoría a la Actividad</h3>
            <form method="POST"
                action="listaActividades.php<?php echo $agenda_id_actual > 0 ? '?agenda_id=' . $agenda_id_actual : ''; ?>">
                <input type="hidden" name="id_actividad" id="modalAddCategoryActivityId">
                <select name="id_categoria" required>
                    <option value="" disabled selected>Seleccionar Categoría</option>
                    <?php foreach ($categoriasUsuario as $categoria): ?>
                        <option value="<?php echo $categoria['ID_Categoria']; ?>">
                            <?php echo htmlspecialchars($categoria['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="modalLista-buttons">
                    <button type="submit" name="agregarCategoriaActividad" class="save-button">Agregar</button>
                    <button type="button" class="cancel-button" onclick="closeAddCategoryModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para agregar Actividades -->
    <div class="modalLista" id="activityModal">
        <div class="modalLista-content">
            <h3 id="modalTitle">Crear Nueva Actividad</h3>
            <form method="POST"
                action="listaActividades.php<?php echo $agenda_id_actual > 0 ? '?agenda_id=' . $agenda_id_actual : ''; ?>">
                <input type="hidden" name="accion" value="agregarActividad">

                <!-- Título -->
                <input type="text" id="modalInputTitle" name="titulo" placeholder="Título" required minlength="3"
                    maxlength="100" title="El título es obligatorio, debe tener entre 3 y 100 caracteres.">

                <!-- Descripción -->
                <textarea id="modalInputDescription" name="descripcion" placeholder="Descripción..." required
                    minlength="10" maxlength="500"
                    title="La descripción es obligatoria, debe tener entre 10 y 500 caracteres."></textarea>

                <!-- Tipo -->
                <select id="modalInputType" name="tipo" required title="Debes seleccionar un tipo de actividad."
                    onchange="habilitarCategoria()">
                    <option value="" disabled selected>Tipo de Actividad</option>
                    <option value="depleting">Depleting</option>
                    <option value="renewing">Renewing</option>
                </select>

                <!-- Categoría -->
                <select id="modalInputCategory" name="categoria" required disabled
                    title="Debes seleccionar una categoría.">
                    <option value="" disabled selected>Seleccionar Categoría</option>
                    <!-- Las categorías se agregan dinámicamente con JavaScript -->
                </select>

                <!-- Botones -->
                <div class="modalLista-buttons">
                    <button type="submit" class="save-button" name="enviarAct">Guardar</button>
                    <button type="button" class="cancel-button" onclick="closeActivityModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const categorias = <?php echo json_encode($categoriasUsuario); ?>;
    </script>

    <!-- Modal para agregar Categorías -->
    <div class="modalLista" id="categoryModal">
        <div class="modalLista-content">
            <h3>Crear Nueva Categoría</h3>
            <form method="POST"
                action="listaActividades.php<?php echo $agenda_id_actual > 0 ? '?agenda_id=' . $agenda_id_actual : ''; ?>">
                <input type="hidden" name="accion" value="agregarCategoria">

                <!-- Título -->
                <input type="text" id="categoryInputTitle" name="titulo" placeholder="Título de la Categoría" required
                    minlength="3" maxlength="100"
                    title="El título de la categoría es obligatorio, debe tener entre 3 y 100 caracteres.">

                <!-- Color -->
                <label for="modalInputColor">Color de Categoría:</label>
                <input type="color" id="modalInputColor" name="color" value="#ffffff" required
                    title="Debes seleccionar un color para la categoría.">

                <!-- Botones -->
                <div class="modalLista-buttons">
                    <button type="submit" class="save-button" name="enviarCat">Guardar</button>
                    <button type="button" class="cancel-button" onclick="closeCategoryModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modalLista" id="editActivityModal">
        <div class="modalLista-content">
            <h3>Editar Actividad</h3>
            <form method="POST" action="listaActividades.php">
                <input type="hidden" name="id_actividad" id="editActivityId">
                <input type="text" id="editActivityTitle" name="titulo" placeholder="Título" required>
                <textarea id="editActivityDescription" name="descripcion" placeholder="Descripción..."
                    required></textarea>
                <div class="modalLista-buttons">
                    <button type="submit" name="editarAct" class="save-button">Guardar Cambios</button>
                    <button type="button" class="cancel-button" onclick="closeEditActivityModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modalLista" id="editCategoryModal">
        <div class="modalLista-content">
            <h3>Editar Categoría</h3>
            <form method="POST" action="listaActividades.php">
                <input type="hidden" name="id_categoria" id="editCategoryId">
                <input type="text" id="editCategoryTitle" name="titulo" placeholder="Título de la Categoría" required>
                <input type="color" id="editCategoryColor" name="color" required>
                <div class="modalLista-buttons">
                    <button type="submit" name="editarCat" class="save-button">Guardar Cambios</button>
                    <button type="button" class="cancel-button" onclick="closeEditCategoryModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>


</body>
<!-- Script de manejo de modales -->
<script src="JavaScript/listaActividades.js"></script>

<footer style="text-align: center; font-size: 0.8em; color: #a0a0a0;">
    © 2024 Timergy. All rights reserved.
</footer>

</html>