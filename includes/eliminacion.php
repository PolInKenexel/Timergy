<?php
    /**
     * Eliminar relaciones de una actividad en la tabla intermedia.
     */
    function eliminarRelacionesActividad($id_actividad) {
        include_once("includes/conectar.php");
        $conn = connection();
        $query = "DELETE FROM organizacion_cat_act WHERE ID_Actividad = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_actividad);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }

    /**
     * Eliminar una actividad.
     */
    function eliminarActividad($id_actividad) {
        include_once("includes/conectar.php");
        $conn = connection();
        $query = "DELETE FROM actividades WHERE ID_Actividad = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_actividad);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }

    /**
     * Eliminar relaciones de una categoría en la tabla intermedia.
     */
    function eliminarRelacionesCategoria($id_categoria) {
        include_once("includes/conectar.php");
        $conn = connection();
        $query = "DELETE FROM organizacion_cat_act WHERE ID_Categoria = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_categoria);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }

    /**
     * Eliminar una categoría.
     */
    function eliminarCategoria($id_categoria) {
        include_once("includes/conectar.php");
        $conn = connection();
        $query = "DELETE FROM categorias WHERE ID_Categoria = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_categoria);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }

    function eliminarBloque($idBloque){
        include_once("conectar.php");
        $conn = connection();
        $query = "DELETE FROM bloques WHERE ID_Bloque = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $idBloque);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }

    function eliminarRelacionActividadCategoria($id_actividad, $id_categoria) {
        include_once("conectar.php");
        $conn=connection(); // Conexión a la base de datos
    
        $stmt = $conn->prepare("DELETE FROM organizacion_cat_act WHERE id_actividad = $id_actividad AND id_categoria = $id_categoria");
        $stmt->execute();
        $stmt->close();
        $conn->close();
        
    }

//Borrar lapsos
    function deleteLapso($id_lapso) {
        include_once("conectar.php");
        $conn = connection();
    
        $sql = "DELETE FROM registros_lapsos WHERE ID_Lapso = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_lapso);
        $stmt->execute();
    
        $success = $stmt->affected_rows > 0;
    
        $stmt->close();
        $conn->close();
    
        return $success;
    }
    function deleteDay($id_dia){
        include_once("conectar.php");
        $conn = connection();
    
        $sql = "DELETE FROM dias WHERE ID_Dia = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_dia);
        $stmt->execute();
    
        $success = $stmt->affected_rows > 0;
    
        $stmt->close();
        $conn->close();
    
        return $success;
    }
    function deleteUsuarioById($idUsuario) {
        $conn = connection();
        $sql = "DELETE FROM usuarios WHERE ID_Usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $idUsuario);
        $stmt->execute();
        $success = $stmt->affected_rows > 0;
        $stmt->close();
        $conn->close();
        return $success;
    }
    function deleteGraphsByAgenda($agenda_id) {
        $conn = connection();
    
        $sql = "DELETE FROM agendas WHERE id_agenda = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $agenda_id);
    
        $stmt->execute();
        $resultado = $stmt->affected_rows > 0;
    
        $stmt->close();
        $conn->close();
    
        return $resultado;
    }
?>