<?php 
// Iniciar sesión y configuración
session_start();
date_default_timezone_set('America/Mexico_City');

// Incluir dependencias
include 'includes/reutilizables/sidebar.php';
include_once 'includes/eliminacion.php';
include_once 'includes/modificacion.php';

$URLLegal = false;

if ($agenda_act) {
    foreach ($agendas as $agenda) {
        if ($agenda['ID_Agenda'] == $agenda_id_actual) {
            $URLLegal = true;
        }
    }
}

if ($URLLegal) {
    $bloquesBD = getAllBlocksWithDetails($agenda_id_actual);
    $actividadesRenewing = getActividadesPorTipo('renewing', $_SESSION['id_usuario']);

    // Obtener fecha y hora actual
    $fechaActual = date('Y-m-d');
    $diaDeLaSemana = date('l');
    $horaActual = date("H:i:s");

    // Convertir día de la semana al español
    $diasSemanaEspanol = [
        'Sunday' => 'Domingo',
        'Monday' => 'Lunes',
        'Tuesday' => 'Martes',
        'Wednesday' => 'Miércoles',
        'Thursday' => 'Jueves',
        'Friday' => 'Viernes',
        'Saturday' => 'Sábado'
    ];
    $diaDeLaSemanaEspanol = $diasSemanaEspanol[$diaDeLaSemana];

    // Verificar día actual
    $diaActual = getCurrentDayFromAgenda($agenda_id_actual, $fechaActual);
    

    $idDiaActual = $diaActual ? $diaActual['ID_Dia'] : null;
    
    $diaFinalizado = false;
    if ($diaActual && $diaActual['hora_fin']) {
        $diaFinalizado = true;
    }

    if ($diaActual && !$diaFinalizado) {
        // Si hay día activo, buscar el último lapso y determinar la hora de referencia
        $ultimoLapso = getLastLapsoFromDay($diaActual['ID_Dia']);
        $horaReferencia = $horaActual;
        
        if ($ultimoLapso) {
            $horaReferencia = (strtotime($ultimoLapso['hora_fin_plan']) > strtotime($horaActual)) 
                ? $ultimoLapso['hora_fin_plan'] 
                : $horaActual;
        }

        // Crear lista de bloques restantes basados en la hora de referencia
        $bloquesRestantes = array_filter($bloquesBD, function ($bloque) use ($diaDeLaSemanaEspanol, $horaReferencia) {
            return $bloque['dia_semana'] === $diaDeLaSemanaEspanol &&
                ($bloque['hora_ini'] > $horaReferencia || $bloque['hora_fin'] > $horaReferencia);
        });

        // Reindexar y ordenar bloques restantes
        $bloquesRestantes = array_values($bloquesRestantes);
        usort($bloquesRestantes, function ($a, $b) {
            return strcmp($a['hora_ini'], $b['hora_ini']); // Ordenar por hora de inicio
        });

        // Estado del lapso actual
        if ($ultimoLapso && !$ultimoLapso['hora_fin_real']) {
            echo "Lapso activo: " . $ultimoLapso['ID_Lapso'];
        } else {
            echo "No hay lapso activo en este momento.";
        }
    } else {
        // Si no hay día activo o está finalizado
        desactivarBotonesSinDiaIniciado();
        verificarConclusionDelUltimoDia($agenda_id_actual);

        // Crear lista de bloques restantes sin día activo
        $horaReferencia = $horaActual; // Referencia predeterminada
        $bloquesRestantes = array_filter($bloquesBD, function ($bloque) use ($diaDeLaSemanaEspanol, $horaReferencia) {
            return $bloque['dia_semana'] === $diaDeLaSemanaEspanol &&
                ($bloque['hora_ini'] > $horaReferencia || $bloque['hora_fin'] > $horaReferencia);
        });

        // Reindexar y ordenar bloques restantes
        $bloquesRestantes = array_values($bloquesRestantes);
        usort($bloquesRestantes, function ($a, $b) {
            return strcmp($a['hora_ini'], $b['hora_ini']); // Ordenar por hora de inicio
        });
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Agenda: <?php echo $agenda_act['nombre'] ?></title>
        <link rel="stylesheet" href="CSS/estilosAgenda.css">
        <link rel="stylesheet" href="CSS/METAestilosDefault.css">
        <link rel="stylesheet" href="CSS/METAestilosModals.css">

        <!-- w3schools CSS -->
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    </head>
    <body>
        <!-- Título de la página -->
        <div class="w3-container" style="color: #3b4e40;background-color: #d9d2c5; box-shadow: 0px 4px 2px -2px rgba(0, 0, 0, 0.1);">
            <h1>Panel de seguimiento: <?php echo $agenda_act['nombre'] ?></h1>
        </div>

        <!-- Indicadores de días y contenedor de la agenda -->
        <div class="estructuraBanner">
            <div class="semana">
                <?php 
                $diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                foreach ($diasSemana as $index => $dia) {
                    // Verificar si el día actual coincide con el día del bucle
                    $claseDia = ($dia == $diaDeLaSemanaEspanol) ? 'dia dia-actual' : 'dia';
                ?>
                    <div class="<?php echo $claseDia; ?>">
                        <?php echo $dia; ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="estructura">
            <div class="panel-detalles">
                <div class="detalles-bloque">
                    <h2 id="detalle-titulo">Detalles del Bloque</h2>
                    <p><strong>Tipo:</strong> <span id="detalle-tipo">N/A</span></p>
                    <p><strong>Actividad:</strong> <span id="detalle-actividad">N/A</span></p>
                    <p><strong>Notas:</strong> <span id="detalle-notas">N/A</span></p>
                    <div class="color-show"></div>
                </div>

                <div class="botones">
                    <button class="btn siguiente">Actividad</button>
                    <button class="btn respiro">Respiro</button>
                    <button class="btn pausa">Pausa</button>
                    <button class="btn inicio-fin">Comenzar el día</button>
                </div>
            </div>

            <!-- Contenedor para los bloques -->
            <div class="scroll-wrapper">
                <!-- Indicador de horas -->
                <div class="horario">
                    <?php 
                    $horaSolo = date('H', strtotime($horaActual)); // Extraer solo la hora de $horaActual
                    for ($i = 0; $i < 24; $i++): 
                        $claseHora = ($i == $horaSolo) ? 'hora hora-actual' : 'hora'; // Añadir clase si es la hora actual
                    ?>
                        <div class="<?php echo $claseHora; ?>">
                            <?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>:00
                        </div>
                    <?php endfor; ?>
                </div>
                <div class="container"></div>
            </div>
        </div>

        <!-- Modal de Respiros -->
        <div id="modalRespiros" class="modal">
            <div class="modal-content">
                <h2>Seleccionar Respiro</h2>
                <label for="selectRespiros">Elige una opción:</label>
                <select id="selectRespiros">
                    <?php foreach ($actividadesRenewing as $respiro): ?>
                        <option value="<?php echo $respiro['ID_Actividad']; ?>">
                            <?php echo htmlspecialchars($respiro['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="modal-buttons">
                    <button id="cancelarRespiro" class="btn cancelar">Cancelar</button>
                    <button id="confirmarRespiro" class="btn confirmar">Confirmar</button>
                </div>
            </div>
        </div>

        <script>
            const bloquesBD = <?php echo json_encode($bloquesBD ?? [], JSON_HEX_TAG | JSON_HEX_AMP); ?>;
            const bloquesRestantes = <?php echo json_encode($bloquesRestantes ?? [], JSON_HEX_TAG | JSON_HEX_AMP); ?>;
            const agendaId = <?php echo json_encode($agenda_id_actual ?? null); ?>;
            var idDiaActual = <?php echo json_encode($idDiaActual ?? null); ?>;
            const diaFinalizado = <?php echo json_encode($diaFinalizado); ?>; // Variable que indica si el día está finalizado
        </script>
        <script type="module" src="JavaScript/agenda.js"></script>
    </body>
    <footer style="text-align: center; font-size: 0.8em; color: #3b4e40;background-color: #d9d2c5;">
        © 2024 Timergy. All rights reserved.
    </footer>
</html>

<?php }else{ ?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Agenda</title>
        <link rel="stylesheet" href="CSS/METAestilosDefault.css">
        <link rel="stylesheet" href="CSS/METAestilosModals.css">

        <!-- w3schools CSS -->
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    </head>
    <body>
        <!-- Titulo de la página -->
        <div class="w3-container">
            <div class="agenda-card">
                <h1>Panel de seguimiento</h1>

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
    function desactivarBotonesSinDiaIniciado(){
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                document.body.setAttribute('data-no-active-day', 'true');
            });
        </script>";
    }
    function verificarConclusionDelUltimoDia($agenda_id_actual) {
        $ultimoDia = getLastDayFromAgenda($agenda_id_actual);
    
        if ($ultimoDia) {
            $ultimoLapso = getLastLapsoFromDay($ultimoDia['ID_Dia']);
    
            if ($ultimoLapso && is_null($ultimoLapso['hora_fin_real'])) {
                // Si el último lapso no está cerrado, eliminarlo
                $lapsoEliminado = deleteLapso($ultimoLapso['ID_Lapso']);
            }
    
            // Obtener nuevamente el último lapso tras eliminar (si fue necesario)
            $ultimoLapso = getLastLapsoFromDay($ultimoDia['ID_Dia']);
    
            if (!$ultimoLapso) {
                // Si no hay lapsos en el día, eliminar el día
                $diaEliminado = deleteDay($ultimoDia['ID_Dia']);
            } else if ($ultimoDia['hora_fin'] === null) {
                // Si el último día no está cerrado, usar la hora de fin del último lapso
                $horaFin = $ultimoLapso['hora_fin_real'] ?? $ultimoLapso['hora_ini_real'];
                $diaActualizado = updateDayEndTime($ultimoDia['ID_Dia'], $horaFin);
            }
        }
    }
?>