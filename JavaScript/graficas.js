// Datos para la gráfica Doughnut
const doughnutData = {
    labels: ['Energía usada', 'Energía restante', 'Tiempo restante'],
    datasets: [{
        label: 'Datos de ejemplo',
        data: [55, 30, 15],
        backgroundColor: [
            'rgba(255, 99, 132, 0.6)',
            'rgba(54, 162, 235, 0.6)',
            'rgba(255, 206, 86, 0.6)'
        ],
        borderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)'
        ],
        borderWidth: 1
    }]
};

// Configuración para el gráfico Doughnut
const doughnutConfig = {
    type: 'doughnut',
    data: doughnutData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Gráfica de ejemplo: Doughnut'
            }
        }
    }
};

// Renderizar gráfica Doughnut
const doughnutCtx = document.getElementById('doughnutChart').getContext('2d');
new Chart(doughnutCtx, doughnutConfig);

// Datos para la gráfica Line
const lineData = {
    labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo'],
    datasets: [{
        label: 'Progreso mensual',
        data: [10, 20, 15, 25, 30],
        borderColor: 'rgba(75, 192, 192, 1)',
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        borderWidth: 2,
        tension: 0.3 // Suaviza las líneas
    }]
};

// Configuración para el gráfico Line
const lineConfig = {
    type: 'line',
    data: lineData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                text: 'Gráfica de ejemplo: Line'
            }
        },
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Meses'
                }
            },
            y: {
                title: {
                    display: true,
                    text: 'Valores'
                }
            }
        }
    }
};

// Renderizar gráfica Line
const lineCtx = document.getElementById('lineChart').getContext('2d');
new Chart(lineCtx, lineConfig);