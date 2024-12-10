<?php
date_default_timezone_set('America/Mexico_City');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include "../registro.php";
    include "../informes.php";
    $agenda_id = json_decode(file_get_contents('php://input'), true);
    // Extraer el valor de 'agenda_id' si es un array asociativo
    if (is_array($agenda_id) && isset($agenda_id['agenda_id'])) {
        $agenda_id = $agenda_id['agenda_id'];
    }
    $fechaActual = date('Y-m-d');
    $horaActual = date('H:i:s');

    $semana = getCurrentSemanaFromAgenda($agenda_id, $fechaActual);

    if (!$semana) {
        $semanaId = addSemana($agenda_id);
    } else {
        $semanaId = $semana['ID_Semana'];
    }

    $dia = getCurrentDayFromAgenda($agenda_id, $fechaActual);

    if (!$dia) {
        $diaId = addDia($fechaActual, $horaActual, $semanaId);
    } else {
        $diaId = $dia['ID_Dia'];
    }

    // Devolver la información al frontend
    echo json_encode([
        'estado' => 'exito',
        'semanaId' => $semanaId,
        'diaId' => $diaId,
        'fecha' => $fechaActual,
        'horaInicio' => $horaActual
    ]);
}
?>