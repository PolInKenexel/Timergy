<?php
    include_once("conectar.php");
    function addUsuario($email, $contrasenia) {
        // Realizar la conexión
        $conn = connection();

        // Verificar si el correo electrónico ya existe
        $sql_check_email = "SELECT * FROM usuarios WHERE email = ?";
        $stmt_check = $conn->prepare($sql_check_email);
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $result_check_email = $stmt_check->get_result();

        if ($result_check_email->num_rows > 0) {
            echo "Error: El e-mail '$email' ya está registrado.";
            $stmt_check->close();
            $conn->close();
            return false;
        }

        // Insertar el nuevo usuario
        $sql = "INSERT INTO usuarios (email, contrasenia, rol, status) VALUES ('$email','$contrasenia', 'user', 'activo')";
        $stmt = $conn->prepare($sql);

        if ($stmt->execute()) {
            echo "Usuario registrado exitosamente";
        } else {
            echo "Error: " . $stmt->error;
            $stmt->close();
            $conn->close();
            return false;
        }

        // Cerrar las conexiones
        $stmt->close();
        $conn->close();
        return true;
    }

    function addActividad($nombre, $descripcion, $tipo, $id_categoria, $id_usuario){
        // Conexión a la base de datos
        $conn = connection();

        // Insertar la nueva actividad en la tabla "actividades" o similar
        $sqlInsertActividad = "INSERT INTO actividades (nombre, descripcion, tipo, id_usuario) VALUES ('$nombre', '$descripcion', '$tipo' ,'$id_usuario')";

        if (mysqli_query($conn, $sqlInsertActividad)) {
            // Obtener el ID de la actividad recién creada
            $id_actividad = mysqli_insert_id($conn);

            // Insertar la relación en la tabla "organizacion_cat_act"
            $sqlInsertRelacion = "INSERT INTO organizacion_cat_act (id_categoria, id_actividad) VALUES ('$id_categoria', '$id_actividad')";
            
            if (mysqli_query($conn, $sqlInsertRelacion)) {
                echo "Actividad y relación con categoría añadidas correctamente.";
            } else {
                echo "Error al asociar la actividad con la categoría: " . mysqli_error($conn);
            }
        } else {
            echo "Error al agregar la actividad: " . mysqli_error($conn);
        }

        // Cerrar conexión
        mysqli_close($conn);
    }

    function addCategoria($nombre, $color, $id_usuario){
        $conn = connection();

        $sql = "INSERT INTO categorias (nombre, color, tipo, id_usuario) VALUES ('$nombre','$color','tiempo','$id_usuario')";
        $stmt = $conn->prepare($sql);

        if ($stmt->execute()) {
            $id_actividad = mysqli_insert_id($conn);
            echo "<script>alert('$id_actividad: esta es la id de la actividad recien creada');</script>";
        } else {
            echo "Error al agregar la categoría: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }

    function addAgenda($nombre, $id_usuario) {
        // Realizar la conexión
        $conn = connection();
    
        // Verificar si la agenda ya existe
        $sql_check_name = "SELECT * FROM agendas WHERE nombre = ?";
        $stmt_check = $conn->prepare($sql_check_name);
        $stmt_check->bind_param("s", $nombre);
        $stmt_check->execute();
        $result_check_name = $stmt_check->get_result();
    
        if ($result_check_name->num_rows > 0) {
            $stmt_check->close();
            $conn->close();
            return false; // Agenda ya existe
        }
    
        $stmt_check->close();
    
        // Insertar la nueva agenda
        $sql = "INSERT INTO agendas (nombre, id_usuario) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nombre, $id_usuario);
    
        if ($stmt->execute()) {
            $new_agenda_id = $conn->insert_id; // Obtener el ID recién insertado
            $stmt->close();
            $conn->close();
            return $new_agenda_id; // Devolver el ID
        } else {
            $stmt->close();
            $conn->close();
            return false; // Error al insertar
        }
    }

    function addBlock($hora_ini, $hora_fin, $tipo, $notas, $titulo, $color, $dia_semana, $id_agenda, $id_actividad) {
        $conn = connection();
    
        $sql = "INSERT INTO bloques 
                (hora_ini, hora_fin, tipo, notas, titulo, color, dia_semana, id_agenda, id_actividad) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
    
        if (!$stmt) {
            echo "Error al preparar la consulta: " . $conn->error;
            return false;
        }
    
        $stmt->bind_param(
            "sssssssii",
            $hora_ini,    // Hora inicial (cadena)
            $hora_fin,    // Hora final (cadena)
            $tipo,        // Tipo (cadena)
            $notas,       // Notas (cadena)
            $titulo,      // Título (cadena)
            $color,       // Color (cadena)
            $dia_semana,  // Día de la semana (cadena)
            $id_agenda,   // ID de agenda (entero)
            $id_actividad // ID de actividad (entero o -1 para wildblocks)
        );
    
        $result = $stmt->execute();
    
        if (!$result) {
            echo "Error al insertar el bloque: " . $stmt->error;
        }
    
        $stmt->close();
        $conn->close();
    
        return $result;
    }
    function addSemana($agenda_id){
        // Realizar la conexión
        $conn = connection();

        $fechaInicio = date('Y-m-d', strtotime('monday this week'));
        $fechaFin = date('Y-m-d', strtotime('sunday this week'));
        $sqlNuevaSemana = "INSERT INTO semanas (fecha_ini, fecha_fin, id_agenda) VALUES (?, ?, ?)";
        $stmtNuevaSemana = $conn->prepare($sqlNuevaSemana);
        $stmtNuevaSemana->bind_param("ssi", $fechaInicio, $fechaFin, $agenda_id);
        $stmtNuevaSemana->execute();
        $result = $stmtNuevaSemana->insert_id;

        if (!$result) {
            echo "Error al crear la semana: " . $stmt->error;
        }
        
        $stmtNuevaSemana->close();
        $conn->close();

        return $result;
    }
    function addDia($fechaActual, $horaActual, $semanaId){
        // Realizar la conexión
        $conn = connection();

        $sqlNuevoDia = "INSERT INTO dias (fecha, hora_ini, id_semana) VALUES (?, ?, ?)";
        $stmtNuevoDia = $conn->prepare($sqlNuevoDia);
        $stmtNuevoDia->bind_param("ssi", $fechaActual, $horaActual, $semanaId);
        $stmtNuevoDia->execute();
        $result = $stmtNuevoDia->insert_id;

        if (!$result) {
            echo "Error al crear el día: " . $stmt->error;
        }
        
        $stmtNuevoDia->close();
        $conn->close();

        return $result;
    }
    /**
     * Función para crear un nuevo lapso
     */
    function crearLapso($idDia, $horaIniPlan, $horaFinPlan, $metodoCreac, $idActividad) {
        $conn = connection();

        $horaInicioReal = date('H:i:s');
        $sqlCrearLapso = "INSERT INTO registros_lapsos 
                        (hora_ini_plan, hora_fin_plan, hora_ini_real, metodo_creac, id_dia, id_actividad) 
                        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sqlCrearLapso);
        $stmt->bind_param("ssssii", $horaIniPlan, $horaFinPlan, $horaInicioReal, $metodoCreac, $idDia, $idActividad);
        $resultado = $stmt->execute();

        if ($resultado) {
            $idLapso = $stmt->insert_id;
            $stmt->close();
            $conn->close();
            return ['ID_Lapso' => $idLapso];
        } else {
            $stmt->close();
            $conn->close();
            return false;
        }
    }
?>