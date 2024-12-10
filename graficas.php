<?php
// Iniciar sesión
session_start();

// Incluir sidebar y funciones necesarias
include 'includes/reutilizables/sidebar.php';
include_once 'includes/informes.php';

$URLLegal = false;

if ($agenda_act) {
    foreach ($agendas as $agenda) {
        if ($agenda['ID_Agenda'] == $agenda_id_actual) {
            $URLLegal = true;
        }
    }
}

if ($URLLegal) {
    // Obtener el id del usuario actual
    $id_usuario = $_SESSION['id_usuario']; // Cambiar según cómo manejes sesiones

    // Obtener categorías para el gráfico
    $categorias = getCategoriasPorUsuario($id_usuario);

    // Obtener actividades del usuario
    $actividades = getActividadesPorTipo('cualquiera', $id_usuario); // Cambia "cualquiera" según tu lógica

    // Preparar datos para la gráfica
    $labels = array_column($categorias, 'nombre');
    $data = array_fill(0, count($categorias), 0); // Inicializar con ceros
    $colors = array_column($categorias, 'color');

    foreach ($actividades as $actividad) {
        foreach ($categorias as $index => $categoria) {
            if ($categoria['ID_Categoria'] == $actividad['color']) {
                $data[$index]++;
            }
        }
    }

    ?>
    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gráficas: <?php echo $agenda_act['nombre'] ?></title>
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <link rel="stylesheet" href="CSS/METAestilosDefault.css">
        <link rel="stylesheet" href="CSS/METAestilosModals.css">
        <link rel="stylesheet" href="CSS/estilosGraficas.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <style>
            /* Estilos personalizados */
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            th,
            td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: center;
            }

            th {
                background-color: #f2f2f2;
            }

            body {
                background-color: #d9d2c5;
                /* Fondo principal */
                color: #333;
                font-family: 'Arial', sans-serif;
            }

            h1,
            h2 {
                text-align: center;
                color: #3b4e40;
                font-family: 'Georgia', serif;
            }

            .chart-container {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                margin: 20px;
                padding: 20px;
                background-color: #c5cbb3;
                /* Fondo del contenedor para gráficas circulares */
                border-radius: 15px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }

            .chart-container-lineal {
                padding: 15px;
                background-color: #e3e3d3;
                /* Fondo claro */
                border: 2px solid #aaa;
                border-radius: 10px;
                box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
                margin: 20px;
            }

            canvas {
                width: 400px !important;
                height: 400px !important;
            }

            footer {
                background-color: #f5f2eb;
                color: #3b4e40;
                text-align: center;
                padding: 20px;
                margin-top: 40px;
                font-size: 14px;
            }

            footer a {
                color: #3b4e40;
                text-decoration: none;
                margin: 0 10px;
            }

            footer a:hover {
                text-decoration: underline;
            }
        </style>
    </head>

    <body>
        <!-- Título de la página -->
        <div class="w3-container">
            <h1>Gráfica de tiempo y energía</h1>
        </div>

        <!-- Contenedor para la gráfica Doughnut -->
        <div class="chart-container">
            <h2>Distribución del tiempo semanal</h2>
            <canvas id="doughnutChart"></canvas>
        </div>

        <!-- Tabla de actividades -->
        <div class="chart-container">
            <h2>Tabla de Actividades</h2>
            <table>
                <thead>
                    <tr>
                        <th>Actividad</th>
                        <th>Tiempo Real</th>
                        <th>Tiempo Planificado</th>
                        <th>Categoría(s)</th>
                        <th>Descripción</th>
                        <th>Tipo</th>
                    </tr>
                </thead>
                <tbody>
            <tr>
                <td>Escuela</td>
                <td>27 horas</td>
                <td>25 horas</td>
                <td>Clases, Programación</td>
                <td>Asistir a las aburridas clases de siempre</td>
                <td>depleting</td>
            </tr>
            <tr>
                <td>Prueba actividad</td>
                <td>1 hora</td>
                <td>2 hora</td>
                <td>Prueba categoría</td>
                <td>Esta es una prueba</td>
                <td>depleting</td>
            </tr>
            <tr>
                <td>Tomar una siesta</td>
                <td>1.5 horas</td>
                <td>2 horas</td>
                <td>Físico, Prueba categoría</td>
                <td>NULL</td>
                <td>renewing</td>
            </tr>
            <tr>
                <td>Estudiar</td>
                <td>6 horas</td>
                <td>4 horas</td>
                <td>Prueba categoría</td>
                <td>Necesito estudiar lo mejor posible para que me vaya bien</td>
                <td>depleting</td>
            </tr>
            <tr>
                <td>Programar</td>
                <td>15 horas</td>
                <td>16 horas</td>
                <td>Programación, pasatiempo</td>
                <td>Una sesión de programación en PHP al día</td>
                <td>depleting</td>
            </tr>
            <tr>
                <td>Respiración profunda</td>
                <td>.5 hora</td>
                <td>.7 hora</td>
                <td>Físico, espiritual, pasatiempo</td>
                <td>Inhala, y exhala</td>
                <td>renewing</td>
            </tr>
        </tbody>
            </table>
        </div>



        <!-- Contenedor para la gráfica Lineal -->
        <div class="chart-container-lineal">
            <h2>Flujo de energía diario (promedio)</h2>
            <canvas id="lineChart"></canvas>
        </div>

        <!-- Pie de página -->
        <footer style="text-align: center; font-size: 0.8em; color: #3b4e40;background-color: #d9d2c5;">
            © 2024 Timergy. All rights reserved.
        </footer>

        <script>
            // Datos para el gráfico Doughnut
            const doughnutData = {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    data: [25, 35, 10, 5, 5, 2, 1, 4, 7, 1, 5],
                    backgroundColor: <?= json_encode($colors) ?>,
                }]
            };

            // Configuración del gráfico
            const doughnutConfig = {
                type: 'doughnut',
                data: doughnutData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'right' },
                        title: { display: true, text: 'Categorías del Usuario' }
                    }
                }
            };

            // Renderizar gráfico
            const doughnutCtx = document.getElementById('doughnutChart').getContext('2d');
            new Chart(doughnutCtx, doughnutConfig);

            const lineData = {
                labels: ['1', '2', '3', '4', '5', '6', '7'],
                datasets: [{
                    label: 'Valores',
                    data: [3, 4, 6, 7, 5, 2, 0],
                    borderColor: '#5b9bd5', // Azul para la línea
                    backgroundColor: 'rgba(91, 155, 213, 0.3)', // Fondo suave
                    pointBackgroundColor: '#ffffff', // Fondo de los puntos
                    pointBorderColor: '#5b9bd5', // Borde de los puntos
                    borderWidth: 2,
                    pointRadius: 5,
                    tension: 0.3
                }]
            };

            // Configuración para la gráfica Lineal estilizada
            const lineConfig = {
                type: 'line',
                data: lineData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: '#cfcfcf'
                            }
                        },
                        y: {
                            ticks: {
                                stepSize: 1
                            },
                            grid: {
                                color: '#cfcfcf'
                            }
                        }
                    }
                }
            };

            // Renderizar gráfica Lineal estilizada
            const lineCtx = document.getElementById('lineChart').getContext('2d');
            new Chart(lineCtx, lineConfig);
        </script>
    </body>

    </html>
<?php } else { ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Gráficas</title>
            <link rel="stylesheet" href="CSS/METAestilosDefault.css">
            <link rel="stylesheet" href="CSS/METAestilosModals.css">

            <!-- w3schools CSS -->
            <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        </head>
        <body>
            <!-- Titulo de la página -->
            <div class="w3-container">
                <div class="agenda-card">
                    <h1>Graficas</h1>

                        <p>Parece que hubo un error... intente seleccionar una de sus agendas.</p>
                </div>
            </div>
        </body>
        <footer style="text-align: center; font-size: 0.8em; color: #a0a0a0;">
            © 2024 Timergy. All rights reserved.
        </footer>
    </html>
<?php } ?>