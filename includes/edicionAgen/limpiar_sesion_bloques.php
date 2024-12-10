<?php
    session_start();

    // Elimina los bloques de la sesión si existen
    if (isset($_SESSION['bloques'])) {
        unset($_SESSION['bloques']);
    }
    if (isset($_SESSION['bloque_id_counter'])) {
        unset($_SESSION['bloque_id_counter']);
    }

    // Respuesta al cliente
    echo json_encode(['status' => 'success', 'message' => 'Sesión de bloques limpiada.']);
?>