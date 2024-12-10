<?php
    include_once("conectar.php");
    function agregarCategoriaAActividad($id_actividad, $id_categoria) {
        $conn=connection();
        // Verificar si ya existe la relación
        $stmt = $conn->prepare("SELECT * FROM organizacion_cat_act WHERE id_actividad = ? AND id_categoria = ?");
        $stmt->bind_param("ii", $id_actividad, $id_categoria);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) { // Si no existe, agregarla
            $stmt = $conn->prepare("INSERT INTO organizacion_cat_act (id_actividad, id_categoria) VALUES (?, ?)");
            $stmt->bind_param("ii", $id_actividad, $id_categoria);
            $stmt->execute();
        }
        $stmt->close();
    }

    function updateBlocks($id_temporal, $hora_ini, $hora_fin, $tipo, $notas, $titulo, $color, $dia_semana, $actividad = null) {
        $conn = connection();
    
        $sql = "UPDATE bloques
                SET hora_ini = ?, hora_fin = ?, tipo = ?, notas = ?, titulo = ?, color = ?, dia_semana = ?, id_actividad = ?
                WHERE ID_Bloque = ?";
    
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssssssi",
            $hora_ini,
            $hora_fin,
            $tipo,
            $notas,
            $titulo,
            $color,
            $dia_semana,
            $actividad,
            $id_temporal
        );
    
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    function updateDayEndTime($id_dia, $horaFin) {
        $conn = connection();
    
        $sql = "UPDATE dias SET hora_fin = ? WHERE ID_Dia = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $horaFin, $id_dia);
        $stmt->execute();
    
        $success = $stmt->affected_rows > 0;
    
        $stmt->close();
        $conn->close();
    
        return $success;
    }
    /**
     * Función para cerrar el lapso actual
     */
    function cerrarLapsoActual($idDia, $metodoFin) {
        $conn = connection();  
        // Buscar el lapso actual sin hora de fin dentro del día especificado
        $sqlBuscarLapso = "SELECT ID_Lapso FROM registros_lapsos 
                           WHERE hora_fin_real IS NULL AND id_dia = ? 
                           LIMIT 1";
        $stmtBuscar = $conn->prepare($sqlBuscarLapso);
        $stmtBuscar->bind_param("i", $idDia);
        $stmtBuscar->execute();
        $result = $stmtBuscar->get_result();
    
        if ($result->num_rows > 0) {
            $lapso = $result->fetch_assoc();
    
            // Actualizar la hora de fin del lapso
            $horaFin = date('H:i:s');
            $sqlActualizarLapso = "UPDATE registros_lapsos 
                                   SET hora_fin_real = ?, metodo_fin = ? 
                                   WHERE ID_Lapso = ?";
            $stmtActualizar = $conn->prepare($sqlActualizarLapso);
            $stmtActualizar->bind_param("ssi", $horaFin, $metodoFin, $lapso['ID_Lapso']);
            $resultado = $stmtActualizar->execute();
    
            $stmtActualizar->close();
            $stmtBuscar->close();
            $conn->close();
            return $resultado;
        } else {
            $stmtBuscar->close();
            $conn->close();
            return false; // No hay lapso actual para el día especificado
        }
    }

    function modificarActividad($id, $titulo, $descripcion) {
        $conn = connection();
        $stmt = $conn->prepare("UPDATE actividades SET nombre = ?, descripcion = ? WHERE ID_Actividad = ?");
        $stmt->bind_param('ssi', $titulo, $descripcion,$id);
        $stmt->execute();
    }

    function modificarCategoria($id, $nombre, $color) {
        $conn = connection();
        $stmt = $conn->prepare("UPDATE categorias SET nombre = ?, color = ? WHERE ID_Categoria = ?");
        $stmt->bind_param('ssi', $nombre, $color, $id);
        $stmt->execute();
    }

    function updateUsuarioPassword($idUsuario, $hashedPassword){
        $conn = connection();
        $sql = "UPDATE usuarios SET contrasenia = ? WHERE ID_Usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hashedPassword, $idUsuario);
        $stmt->execute();
        $success = $stmt->affected_rows > 0;
        $stmt->close();
        $conn->close();
        return $success;
    }

    function updateAgendaName($idAgenda, $nuevoNombre) {
        $conn = connection();
        $sql = "UPDATE agendas SET nombre = ? WHERE ID_Agenda = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nuevoNombre, $idAgenda);
        $stmt->execute();
        $success = $stmt->affected_rows > 0;
        $stmt->close();
        $conn->close();
        return $success;
    }
?>