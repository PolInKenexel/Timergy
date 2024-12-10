export function saveWeekAndOrDay(agendaId) {
    return fetch('includes/agenda/procesar_configuracion_agenda.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ agenda_id: agendaId }),
    });
}
// Cerrar lapso actual en el backend
export async function cerrarLapsoActual(idDia, metodoFin = 'siguiente') {
    return fetch('includes/agenda/procesar_lapsos.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'cerrar_lapso',
            id_dia: idDia, // Identificar explícitamente el día
            metodo_fin: metodoFin,
        }),
    }).then((response) => {
        // Comprobar si la respuesta es JSON antes de intentar procesarla
        console.log('Estado HTTP:', response.status); // Log de estado HTTP
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text(); // Primero obtener el texto completo
    })
    .then((text) => {
        try {
            return JSON.parse(text); // Intentar convertir a JSON
        } catch (e) {
            console.error('Respuesta no válida:', text); // Loguear la respuesta no válida
            throw new Error('El servidor no devolvió un JSON válido');
        }
    });
}
// Crear lapso en el backend
export async function crearNuevoLapso(agendaId, bloque) {
    return fetch('includes/agenda/procesar_lapsos.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'crear_lapso',
            agenda_id: agendaId,
            hora_ini_plan: bloque.hora_ini,
            hora_fin_plan: bloque.hora_fin,
            metodo_creac: bloque.tipo,
            id_actividad: bloque.id_actividad,
        }),
    }).then((response) => {
        // Comprobar si la respuesta es JSON antes de intentar procesarla
        console.log('Estado HTTP:', response.status); // Log de estado HTTP
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text(); // Primero obtener el texto completo
    })
    .then((text) => {
        try {
            return JSON.parse(text); // Intentar convertir a JSON
        } catch (e) {
            console.error('Respuesta no válida:', text); // Loguear la respuesta no válida
            throw new Error('El servidor no devolvió un JSON válido');
        }
    });
}

// Llamar al servidor para finalizar el día
export async function finalizarDiaServidor(idDia) {
    return fetch('includes/agenda/procesar_lapsos.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'finalizar_dia',
            id_dia: idDia, // ID del día
        }),
    }).then((response) => {
        // Comprobar si la respuesta es JSON antes de intentar procesarla
        console.log('Estado HTTP:', response.status); // Log de estado HTTP
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text(); // Primero obtener el texto completo
    })
    .then((text) => {
        try {
            return JSON.parse(text); // Intentar convertir a JSON
        } catch (e) {
            console.error('Respuesta no válida:', text); // Loguear la respuesta no válida
            throw new Error('El servidor no devolvió un JSON válido');
        }
    });
}

/* SOLO PARA DEPURACIÓN Y BUSQUEDA DE ERRORES EN EL PHP
    .then((response) => {
            // Comprobar si la respuesta es JSON antes de intentar procesarla
            console.log('Estado HTTP:', response.status); // Log de estado HTTP
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text(); // Primero obtener el texto completo
        })
        .then((text) => {
            try {
                return JSON.parse(text); // Intentar convertir a JSON
            } catch (e) {
                console.error('Respuesta no válida:', text); // Loguear la respuesta no válida
                throw new Error('El servidor no devolvió un JSON válido');
            }
        });
*/