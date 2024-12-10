<?php
date_default_timezone_set('America/Mexico_City');
    
// Incluir archivos necesarios
include "../registro.php";
include "../informes.php";
include "../modificacion.php";

// Verificar el método de solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Verificar si se envió la acción
    if (!isset($data['action'])) {
        echo json_encode([
            'estado' => 'error',
            'mensaje' => 'No se especificó ninguna acción.'
        ]);
        exit;
    }

    $action = $data['action'];

    if ($action === 'cerrar_lapso') {
        if (!isset($data['id_dia']) || !isset($data['metodo_fin'])) {
            echo json_encode([
                'estado' => 'error',
                'mensaje' => 'Faltan datos para cerrar el lapso.'
            ]);
            exit;
        }
    
        $idDia = $data['id_dia']; // Recibir explícitamente el id_dia
        $metodoFin = $data['metodo_fin'];
    
        // Solo cierra el lapso si pertenece al día especificado
        $ultimoLapso = getLastLapsoFromDay($idDia);
        if ($ultimoLapso) {
            $resultado = cerrarLapsoActual($idDia, $metodoFin);
            if ($resultado) {
                echo json_encode([
                    'estado' => 'exito',
                    'mensaje' => 'Lapso cerrado exitosamente.',
                ]);
            } else {
                echo json_encode([
                    'estado' => 'error',
                    'mensaje' => 'No se pudo cerrar el lapso.'
                ]);
            }
        } else {
            echo json_encode([
                'estado' => 'error',
                'mensaje' => 'No se encontró un lapso abierto para este día.'
            ]);
        }
    } elseif ($action === 'crear_lapso') {
        // Crear un nuevo lapso
        $camposRequeridos = ['agenda_id', 'hora_ini_plan', 'hora_fin_plan', 'metodo_creac', 'id_actividad'];
        foreach ($camposRequeridos as $campo) {
            if (!isset($data[$campo])) {
                echo json_encode([
                    'estado' => 'error',
                    'mensaje' => "Falta el campo $campo para crear el lapso."
                ]);
                exit;
            }
        }

        $agenda_id = $data['agenda_id'];
        $horaIniPlan = $data['hora_ini_plan'];
        $horaFinPlan = $data['hora_fin_plan'];
        $metodoCreac = $data['metodo_creac'];
        $idActividad = $data['id_actividad'];
        $fechaActual = date('Y-m-d');

        // Obtener el día actual como array
        $diaActual = getCurrentDayFromAgenda($agenda_id, $fechaActual);

        if (!$diaActual) {
            echo json_encode([
                'estado' => 'error',
                'mensaje' => 'No hay un día activo para crear el lapso.'
            ]);
            exit;
        }
        // Extraer el ID_Dia del día actual
        if ($diaActual && isset($diaActual['ID_Dia'])) {
            $idDia = $diaActual['ID_Dia'];
        } else {
            echo json_encode([
                'estado' => 'error',
                'mensaje' => 'No hay un día activo para crear el lapso.'
            ]);
            exit;
        }
        

        $nuevoLapso = crearLapso($idDia, $horaIniPlan, $horaFinPlan, $metodoCreac, $idActividad);

        if ($nuevoLapso) {
            echo json_encode([
                'estado' => 'exito',
                'lapso' => $nuevoLapso,
                'mensaje' => 'Lapso creado exitosamente.'
            ]);
        } else {
            echo json_encode([
                'estado' => 'error',
                'mensaje' => 'No se pudo crear el lapso.'
            ]);
        }
    } else {
        echo json_encode([
            'estado' => 'error',
            'mensaje' => 'Acción no válida.'
        ]);
    }
    if ($action === 'finalizar_dia') {
        if (!isset($data['id_dia'])) {
            echo json_encode([
                'estado' => 'error',
                'mensaje' => 'Faltan datos para finalizar el día.',
            ]);
            exit;
        }
    
        $idDia = $data['id_dia'];
    
        // Lógica para finalizar el día en la base de datos
        $resultado = updateDayEndTime($idDia, date('H:i:s')); // Registrar hora de fin actual
        if ($resultado) {
            echo json_encode([
                'estado' => 'exito',
                'mensaje' => 'Día finalizado exitosamente.',
            ]);
        } else {
            echo json_encode([
                'estado' => 'error',
                'mensaje' => 'No se pudo finalizar el día.',
            ]);
        }
        exit;
    }
}
?>