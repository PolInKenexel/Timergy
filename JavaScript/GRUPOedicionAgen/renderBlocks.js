/*FUNCIONES QUE PASEN DE DÍA/TIEMPO A POSICIÓN*/
/**
 * Calcula la posición horizontal `x` en función del día de la semana.
 * @param {HTMLElement} container - Contenedor principal.
 * @param {string} diaSemana - Día de la semana (Lunes, Martes, etc.).
 * @returns {number} Posición horizontal `x` en píxeles.
 */
export function calculateXFromDay(container, diaSemana) {
    const daysOfWeek = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
    const dayIndex = daysOfWeek.indexOf(diaSemana);
    if (dayIndex === -1) throw new Error(`Día inválido: ${diaSemana}`);

    const containerWidth = container.offsetWidth;
    const dayWidth = containerWidth / 7; // Dividir el contenedor en 7 días
    return dayIndex * dayWidth;
}

/**
 * Calcula la posición vertical `y` y la altura en función de las horas de inicio y fin.
 * @param {HTMLElement} container - Contenedor principal.
 * @param {string} horaIni - Hora de inicio en formato HH:MM.
 * @param {string} horaFin - Hora de fin en formato HH:MM.
 * @returns {{y: number, height: number}} Posición vertical y altura del bloque en píxeles.
 */
export function calculateYAndHeightFromTime(container, horaIni, horaFin) {
    const containerHeight = container.offsetHeight;

    // Convertir tiempo a minutos totales
    const convertTimeToMinutes = (time) => {
        const [hours, minutes] = time.split(':').map(Number);
        return hours * 60 + minutes;
    };

    const minutesStart = convertTimeToMinutes(horaIni);
    const minutesEnd = convertTimeToMinutes(horaFin);

    const y = Math.round(minutesStart);
    const height = Math.round(minutesEnd - minutesStart);

    return { y, height };
}