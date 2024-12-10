<?php
    session_start();

    // PROCESAMIENTO DE LOS BLOQUES CREADOS
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $blockData = json_decode(file_get_contents('php://input'), true);

        if ($blockData && isset($blockData['agenda_id_actual'], $blockData['titulo'], $blockData['color'], $blockData['tipo'], $blockData['notas'])) {
            if (!isset($_SESSION['bloques'])) {
                $_SESSION['bloques'] = [];
            }

            if (!isset($_SESSION['existingBlockIds'])) {
                $_SESSION['existingBlockIds'] = [];
            }

            if (!isset($_SESSION['bloque_id_counter'])) {
                $_SESSION['bloque_id_counter'] = 0; // Inicializar el contador si no existe
            }

            // Validar actividad solo para tipos distintos de wildblock
            if ($blockData['tipo'] !== 'wildblock' && empty($blockData['actividad'])) {
                echo json_encode(['status' => 'error', 'message' => 'Debe seleccionar una actividad']);
                exit;
            }

            // Generar un ID temporal único que no esté en existingBlockIds
            do {
                $id_temporal = ++$_SESSION['bloque_id_counter'];
            } while (in_array($id_temporal, $_SESSION['existingBlockIds']));

            $blockData['id_temporal'] = $id_temporal;

            // Guardar en la sesión
            $_SESSION['bloques'][] = $blockData;

            echo json_encode(['status' => 'success', 'message' => 'Bloque guardado correctamente', 'id_temporal' => $id_temporal]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
        }
        exit;
    }
    
    //PROCESAMIENTO DE LOS BLOQUES ELIMINADOS
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);
    
        if (isset($data['id_temporal'])) {
            $id_temporal = $data['id_temporal'];
    
            if (isset($_SESSION['bloques'])) {
                // Buscar y eliminar el bloque de la sesión
                foreach ($_SESSION['bloques'] as $index => $bloque) {
                    if ($bloque['id_temporal'] == $id_temporal) {
                        unset($_SESSION['bloques'][$index]);
                        // Reindexar el array
                        $_SESSION['bloques'] = array_values($_SESSION['bloques']);
                        break;
                    }
                }
                if (empty($_SESSION['bloques'])) {
                    unset($_SESSION['bloques']);
                }
                echo json_encode(['status' => 'success', 'message' => 'Bloque eliminado']);
                exit;
            }
            echo json_encode(['status' => 'error', 'message' => 'Bloque no encontrado']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID temporal no proporcionado']);
        }
        exit;
    }
    
    // Obtener datos de un bloque específico
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $id_temporal = isset($_GET['id_temporal']) ? intval($_GET['id_temporal']) : null;
    
        if ($id_temporal && isset($_SESSION['bloques'])) {
            foreach ($_SESSION['bloques'] as $bloque) {
                if ($bloque['id_temporal'] == $id_temporal) {
                    echo json_encode(['status' => 'success', 'data' => $bloque]);
                    exit;
                }
            }
            echo json_encode(['status' => 'error', 'message' => 'Bloque no encontrado']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID temporal no proporcionado o sesión vacía']);
        }
        exit;
    }
    
    // PROCESAMIENTO DE BLOQUES ACTUALIZADOS
    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $blockData = json_decode(file_get_contents('php://input'), true);

        if (isset($blockData['id_temporal'], $_SESSION['bloques'])) {
            $id_temporal = $blockData['id_temporal'];

            foreach ($_SESSION['bloques'] as &$bloque) {
                if ($bloque['id_temporal'] == $id_temporal) {
                    // Actualizar los datos del bloque
                    $bloque['titulo'] = $blockData['titulo'] ?? $bloque['titulo'];
                    $bloque['color'] = $blockData['color'] ?? $bloque['color'];
                    $bloque['tipo'] = $blockData['tipo'] ?? $bloque['tipo'];
                    $bloque['notas'] = $blockData['notas'] ?? $bloque['notas'];

                    // Si el bloque es un wildblock, actividad debe ser null
                    $bloque['actividad'] = $blockData['tipo'] === 'wildblock' ? null : ($blockData['actividad'] ?? $bloque['actividad']);

                    echo json_encode(['status' => 'success', 'message' => 'Bloque actualizado correctamente']);
                    exit;
                }
            }
            echo json_encode(['status' => 'error', 'message' => 'Bloque no encontrado']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Datos incompletos o sesión vacía']);
        }
        exit;
    }
?>