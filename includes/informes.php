<?php
//Obtiene todas las agendas que pertenezcan a cierto usuario
	function getAgendasFromUser($id_usuario){
		include_once("includes/conectar.php");
		$conn = connection();
		$sqlAgendas = "SELECT * FROM agendas WHERE id_usuario = ?";
		$stmt = $conn->prepare($sqlAgendas);
		$stmt->bind_param("i", $id_usuario); // 'i' indica un entero
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows > 0) {
			$agendasDeUser = array();
			while($elemento = $result->fetch_assoc()) {
				$agendasDeUser[] = $elemento;
			}
		} else {
			return false; // No agendas encontradas
		}

		$stmt->close();
		$conn->close();

		return $agendasDeUser;
	}

//Obtiene la agenda que actualmente se encuentra en el URL
	function getSeparatedAgenda($id_agenda) {
		include_once("includes/conectar.php");
		//Hace la conexión
		$conn = connection();
		$sql = "SELECT * FROM agendas WHERE ID_Agenda = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i", $id_agenda);
		$stmt->execute();
		$result = $stmt->get_result();
		$agenda = null;

		if ($result->num_rows > 0) {
			$agenda = $result->fetch_assoc();
		}else{
			return false; // No agenda encontrada
		}

		$stmt->close();
		$conn->close();
		return $agenda;
	}

//Obtiene los datos del usuario cuyo id se esta solicitando
	function getSeparatedUser($id_usuario) {
		include_once("includes/conectar.php");
		//Hace la conexión
	    $conn = connection();
	    $sql = "SELECT * FROM usuarios WHERE ID_Usuario = ?";
	    $stmt = $conn->prepare($sql);
	    $stmt->bind_param("i", $id_usuario);
	    $stmt->execute();
	    $result = $stmt->get_result();
	    $usuario = null;

	    if ($result->num_rows > 0) {
	        $usuario = $result->fetch_assoc();
	    }else{
			return false; // No usuarios encontrados
		}

	    $stmt->close();
	    $conn->close();
	    return $usuario;
	}
	//Obtiene las actividades de acuerdo a su tipo
	function getActividadesPorTipo($tipo, $id_usuario) {
		include_once("includes/conectar.php");
		$conn = connection();
	
		// Consulta SQL para obtener las actividades del usuario filtradas por tipo
		$query = "SELECT 
					ID_Actividad, 
					nombre, 
					descripcion
				  FROM actividades 
				  WHERE tipo = ? AND id_usuario = ?
				  ORDER BY nombre ASC";
	
		if ($stmt = $conn->prepare($query)) {
			$stmt->bind_param("si", $tipo, $id_usuario); // "s" para string (tipo), "i" para integer (id_usuario)
			$stmt->execute();
	
			$result = $stmt->get_result();
			$actividades = [];
	
			while ($row = $result->fetch_assoc()) {
				$actividades[] = $row;
			}
	
			$stmt->close();
			return $actividades;
		} else {
			return [];
		}
	}
	//Obtiene las categorías del usuario cuyo id se este solicitando
	function getCategoriasPorUsuario($id_usuario){
		include_once("includes/conectar.php");
		$conn = connection(); // Usamos la conexión proporcionada por $conn

		// Consulta para obtener las categorías del usuario
		$sql = "SELECT ID_Categoria, nombre, color, tipo, id_usuario 
					FROM categorias 
					WHERE id_usuario = ? OR id_usuario = -1";

		// Preparar la consulta
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i", $id_usuario); // "i" indica que es un entero
		$stmt->execute();

		// Obtener los resultados
		$result = $stmt->get_result();
		$categorias = [];
		while ($row = $result->fetch_assoc()) {
			$categorias[] = $row;
		}

		// Cerrar el statement y devolver las categorías
		$stmt->close();
		return $categorias;
	}
	//Obtiene las categorías de acuerdo al id de la actividad que se este solicitando
	function getCategoriasPorActividad($id_actividad){
		include_once("includes/conectar.php");
		// Conexión a la base de datos
		$conn = connection();

		// Consulta SQL para obtener las categorías de una actividad específica
		$query = "SELECT c.ID_Categoria, c.nombre, c.color 
					FROM organizacion_cat_act oca
					INNER JOIN categorias c ON oca.id_categoria = c.ID_Categoria
					WHERE oca.id_actividad = ?";

		if ($stmt = $conn->prepare($query)) {
			$stmt->bind_param("i", $id_actividad); // "i" para integer
			$stmt->execute();
			$result = $stmt->get_result();

			$categorias = array();
			while ($row = $result->fetch_assoc()) {
				$categorias[] = $row;
			}

			$stmt->close();
			return $categorias;
		} else {
			return array();
		}
	}
	function contarCategoriasPorActividad($id_actividad) {
		$conn=connection(); // Asegúrate de que $db es tu conexión activa a la base de datos.
	
		// Consulta SQL para contar las categorías asignadas a la actividad
		$sql = "SELECT COUNT(*) AS total FROM organizacion_cat_act WHERE id_actividad = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i", $id_actividad);
		$stmt->execute();
	
		// Obtener el resultado
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		
		// Cerrar la consulta
		$stmt->close();
	
		// Retornar el conteo de categorías
		return (int) $row['total'];
	}
	/**
	 * Obtiene bloques formateados para almacenarlos en la sesión.
	 * Este formato coincide con lo que se espera en el frontend (JS).
	 *
	 * @param int $id_agenda ID de la agenda.
	 * @return array Bloques formateados para la sesión.
	 */
	function getFormattedBlocksForSession($id_agenda) {
		include_once("includes/conectar.php");
		$conn = connection();

		$sql = "SELECT ID_Bloque, hora_ini, hora_fin, tipo, notas, titulo, color, dia_semana, id_actividad 
				FROM bloques 
				WHERE id_agenda = ?";

		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i", $id_agenda); // "i" indica que es un entero
		$stmt->execute();

		$result = $stmt->get_result();
		$bloques = [];
		while ($row = $result->fetch_assoc()) {
			// Formatear datos para la sesión
			$bloques[] = [
				'agenda_id_actual' => strval($id_agenda),
				'titulo' => $row['titulo'],
				'tipo' => $row['tipo'],
				'notas' => $row['notas'],
				'color' => $row['color'],
				'actividad' => strval($row['id_actividad'] ?? ''), // Convertir null a string vacío
				'isNew' => false, // Los bloques de la base de datos no son nuevos
				'id_temporal' => $row['ID_Bloque'],
			];
		}

		$stmt->close();
		return $bloques;
	}

	/**
	 * Obtiene todos los bloques con sus detalles completos.
	 * Esto incluye horarios y medidas para calcular posiciones en el frontend.
	 *
	 * @param int $id_agenda ID de la agenda.
	 * @return array Bloques con todos los detalles de la base de datos.
	 */
	function getAllBlocksWithDetails($id_agenda) {
		include_once("includes/conectar.php");
		$conn = connection();
	
		$sql = "SELECT ID_Bloque, hora_ini, hora_fin, tipo, notas, titulo, color, dia_semana, id_actividad 
				FROM bloques 
				WHERE id_agenda = ?";
	
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i", $id_agenda); // "i" indica que es un entero
		$stmt->execute();
	
		$result = $stmt->get_result();
		$bloques = [];
		while ($row = $result->fetch_assoc()) {
			$actividad = getSeparatedActividad($row['id_actividad']); // Obtener detalles de la actividad
			$nombreActividadFinal = null; 
			if($row['id_actividad'] == -1){
				$nombreActividadFinal = 'N/A'; 
			}else{
				$nombreActividadFinal = $actividad['nombre'];
			}
			$bloques[] = [
				'ID_Bloque' => $row['ID_Bloque'],
				'hora_ini' => $row['hora_ini'],
				'hora_fin' => $row['hora_fin'],
				'tipo' => $row['tipo'],
				'notas' => $row['notas'],
				'titulo' => $row['titulo'],
				'color' => $row['color'],
				'dia_semana' => $row['dia_semana'],
				'id_actividad' => $row['id_actividad'], // Incluir el ID
				'nombre_actividad' => $nombreActividadFinal
			];
		}
	
		$stmt->close();
		return $bloques;
	}

	function getSeparatedActividad($id_actividad){
		include_once("includes/conectar.php");
		//Hace la conexión
		$conn = connection();
		$sql = "SELECT * FROM actividades WHERE ID_Actividad = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i", $id_actividad);
		$stmt->execute();
		$result = $stmt->get_result();
		$actividad = null;

		if ($result->num_rows > 0) {
			$actividad = $result->fetch_assoc();
		}else{
			return false;
		}

		$stmt->close();
		$conn->close();
		return $actividad;
	}
/*METODOS PARA OBTENER LAPSOS POR DISTINTAS MANERAS*/
	function getCurrentLapsoFromDay($id_dia, $horaActual) {
		include_once("conectar.php");
		$conn = connection();
	
		// Consulta para obtener el lapso activo (sin hora_fin_real)
		$sql = "SELECT * FROM registros_lapsos 
				WHERE id_dia = ? AND hora_fin_real IS NULL
				LIMIT 1"; // Solo un lapso activo permitido
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i", $id_dia);
		$stmt->execute();
		$result = $stmt->get_result();
	
		if ($result->num_rows > 0) {
			$lapsoActual = $result->fetch_assoc(); // Traer el lapso activo
		} else {
			$lapsoActual = false; // No hay lapso activo
		}
	
		$stmt->close();
		$conn->close();
	
		return $lapsoActual;
	}
	function getLastLapsoFromDay($id_dia) {
		include_once("conectar.php");
		$conn = connection();
	
		$sql = "SELECT * 
				FROM registros_lapsos 
				WHERE id_dia = ? 
				ORDER BY hora_ini_real DESC 
				LIMIT 1";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i", $id_dia);
		$stmt->execute();
		$result = $stmt->get_result();
	
		$ultimoLapso = ($result->num_rows > 0) ? $result->fetch_assoc() : null;
	
		$stmt->close();
		$conn->close();
	
		return $ultimoLapso;
	}
/*METODOS PARA OBTENER DÍAS POR DISTINTAS MANERAS*/
	function getCurrentDayFromAgenda($id_agenda, $fechaActual) {
		include_once("conectar.php");
		$conn = connection();

		// Consultar el día más reciente cuya fecha coincida con la actual
		$sql = "SELECT dias.* 
				FROM dias 
				INNER JOIN semanas ON dias.id_semana = semanas.ID_Semana
				WHERE semanas.id_agenda = ? AND dias.fecha = ?
				ORDER BY dias.fecha DESC
				LIMIT 1";

		$stmt = $conn->prepare($sql);
		$stmt->bind_param("is", $id_agenda, $fechaActual); // 'i' para ID de agenda, 's' para fecha
		$stmt->execute();
		$result = $stmt->get_result();

		$diaActual = false;
		
		// Si existe un resultado, asignar el día actual
		if ($result->num_rows > 0) {
			$diaActual = $result->fetch_assoc();
		}

		$stmt->close();
		$conn->close();

		return $diaActual; // Retornar el día actual o false si no se encontró
	}
	function getLastDayFromAgenda($id_agenda) {
		include_once("conectar.php");
		$conn = connection();
	
		$sql = "SELECT dias.* 
				FROM dias 
				JOIN semanas ON dias.id_semana = semanas.ID_Semana 
				WHERE semanas.id_agenda = ? 
				ORDER BY dias.fecha DESC 
				LIMIT 1";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i", $id_agenda);
		$stmt->execute();
		$result = $stmt->get_result();
	
		$ultimoDia = ($result->num_rows > 0) ? $result->fetch_assoc() : null;
	
		$stmt->close();
		$conn->close();
	
		return $ultimoDia;
	}

	function getCurrentSemanaFromAgenda($id_agenda, $fechaActual) {
		include_once("conectar.php");
		$conn = connection();
	
		// Consulta para obtener la semana más reciente
		$sql = "SELECT * FROM semanas 
				WHERE id_agenda = ? 
				ORDER BY fecha_ini DESC 
				LIMIT 1"; // Ordenar por fecha_ini y tomar solo la más reciente
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i", $id_agenda);
		$stmt->execute();
		$result = $stmt->get_result();
	
		if ($result->num_rows > 0) {
			$semanaMasReciente = $result->fetch_assoc(); // Traer la semana más reciente
			if ($fechaActual >= $semanaMasReciente['fecha_ini'] && $fechaActual <= $semanaMasReciente['fecha_fin']) {
				$semanaActual = $semanaMasReciente;
			} else {
				$semanaActual = false; // No es la semana actual
			}
		} else {
			$semanaActual = false; // No hay semanas asociadas
		}
	
		$stmt->close();
		$conn->close();
	
		return $semanaActual;
	}
	
?>