<?php
    // Configuración de la base de datos
	function connection(){
	    $servername = "localhost";
	    $username = "root";
	    $password = "";
	    $dbname = "abp_timergy_bd";

	    // Crear conexión
	    $conn = new mysqli($servername, $username, $password, $dbname);

	    // Verificar conexión
	    if ($conn->connect_error) {
	        die("La conexión ha fallado con exito!: " . $conn->connect_error);
	    }
	    return $conn;
	}
?>