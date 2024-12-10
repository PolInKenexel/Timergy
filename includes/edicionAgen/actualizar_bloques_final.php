<?php


session_start();

header('Content-Type: application/json'); // Garantiza que siempre devolvemos JSON

try {
    // Validar que la solicitud sea POST y que se proporcione 'action'
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    if (!isset($_GET['action'])) {
        throw new Exception('Acción no especificada');
    }

    $action = $_GET['action'];

    if ($action === 'update') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('El cuerpo de la solicitud contiene un JSON inválido');
        }

        if (isset($data['id_temporal'], $data['day'], $data['hora_ini'], $data['hora_fin'])) {
            $id_temporal = $data['id_temporal'];
            $day = $data['day'];
            $hora_ini = $data['hora_ini'];
            $hora_fin = $data['hora_fin'];

            if (!isset($_SESSION['bloques'])) {
                throw new Exception('No hay bloques en la sesión');
            }

            foreach ($_SESSION['bloques'] as &$bloque) {
                if ($bloque['id_temporal'] == $id_temporal) {
                    $bloque['dia_semana'] = $day;
                    $bloque['hora_ini'] = $hora_ini;
                    $bloque['hora_fin'] = $hora_fin;

                    echo json_encode(['status' => 'success', 'message' => 'Detalles del bloque actualizados']);
                    exit;
                }
            }

            throw new Exception('Bloque no encontrado');
        } else {
            throw new Exception('Datos incompletos');
        }
    }

    if ($action === 'save') {
        include_once '../eliminacion.php';
        include "../registro.php";
        include "../modificacion.php";
    
        if (!isset($_SESSION['bloques']) || empty($_SESSION['bloques'])) {
            throw new Exception('No hay bloques para guardar');
        }
    
        $errors = [];
    
        // Obtener IDs actuales de bloques guardados en la sesión
        $bloquesActualesIds = array_map(fn($bloque) => $bloque['id_temporal'], $_SESSION['bloques']);
    
        // Detectar bloques que deben eliminarse (los que están en existingBlockIds pero no en bloquesActualesIds)
        $bloquesEliminar = array_diff($_SESSION['existingBlockIds'], $bloquesActualesIds);
    
        foreach ($bloquesEliminar as $idBloque) {
            // Llamar a la función eliminarBloque para cada ID
            $result = eliminarBloque($idBloque);
    
            if (!$result) {
                $errors[] = "Error al eliminar el bloque con ID: $idBloque";
            }
        }
    
        // Procesar bloques existentes
        foreach ($_SESSION['bloques'] as $bloque) {
            if (!$bloque['isNew']) {
                // Enviar datos completos a updateBlocks para procesar la actualización
                $result = updateBlocks(
                    $bloque['id_temporal'],   // ID del bloque existente
                    $bloque['hora_ini'],      // Hora de inicio
                    $bloque['hora_fin'],      // Hora de fin
                    $bloque['tipo'],          // Tipo de bloque
                    $bloque['notas'],         // Notas del bloque
                    $bloque['titulo'],        // Título del bloque
                    $bloque['color'],         // Color del bloque
                    $bloque['dia_semana'],    // Día de la semana
                    $bloque['actividad'] ?? -1 // Actividad asociada
                );
    
                if (!$result) {
                    $errors[] = "Error al actualizar el bloque: " . $bloque['titulo'];
                }
    
                // Saltar a la siguiente iteración
                continue;
            }
    
            // Procesar bloques nuevos
            if ($bloque['tipo'] === 'wildblock') {
                $id_actividad = -1; // Wildblocks tendrán -1 como valor de id_actividad
            } else {
                $id_actividad = $bloque['actividad'] ?? null; // Para otros bloques, usar la actividad proporcionada
            }
    
            $result = addBlock(
                $bloque['hora_ini'],
                $bloque['hora_fin'],
                $bloque['tipo'],
                $bloque['notas'],
                $bloque['titulo'],
                $bloque['color'],
                $bloque['dia_semana'],
                $bloque['agenda_id_actual'],
                $id_actividad
            );
    
            if (!$result) {
                $errors[] = "Error al guardar el bloque: " . $bloque['titulo'];
            }
        }
    
        if (empty($errors)) {
            unset($_SESSION['bloques']);
            echo json_encode(['status' => 'success', 'message' => 'Todos los bloques fueron procesados correctamente (guardados, actualizados y eliminados).']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Algunos bloques no se pudieron procesar.', 'errors' => $errors]);
        }
        exit;
    }

    throw new Exception('Acción no válida');
} catch (Exception $e) {
    // Capturar errores y enviar una respuesta consistente
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}