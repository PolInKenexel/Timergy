// Enviar datos al servidor
export function saveBlockToServer(agenda_id_actual, titulo, tipo, notas, color, actividad, isNew) {
    const actividadFinal = tipo === 'wildblock' ? null : actividad; // `null` para wildblocks

    return fetch('includes/edicionAgen/procesar_sesion_bloques.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ agenda_id_actual, titulo, tipo, notas, color, actividad: actividadFinal, isNew }),
    }).then((response) => response.json());
}

// Función para eliminar bloques de la sesión
export function deleteBlockFromServer(blockId) {
    return fetch('includes/edicionAgen/procesar_sesion_bloques.php', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_temporal: blockId }),
    }).then((response) => response.json());
}

// Función para obtener datos de un bloque específico
export function fetchBlockData(blockId) {
    return fetch(`includes/edicionAgen/procesar_sesion_bloques.php?id_temporal=${blockId}`)
        .then((response) => response.json());
}

// Función para limpiar la sesión de bloques
export function clearSessionBlocks() {
    return fetch('includes/edicionAgen/limpiar_sesion_bloques.php', {
        method: 'POST',
    }).then((response) => response.json());
}

// Función para actualizar un bloque existente
export function updateBlockOnServer(blockId, titulo, color, tipo, notas, actividad) {
    return fetch('includes/edicionAgen/procesar_sesion_bloques.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_temporal: blockId, titulo, color, tipo, notas, actividad }),
    }).then((response) => response.json());
}

/**
 * Actualiza los detalles de un bloque (día y rango de tiempo) en el servidor.
 * @param {string} blockId - ID temporal del bloque.
 * @param {string} day - Día de la semana asociado al bloque.
 * @param {string} hora_ini - Hora inicial del bloque.
 * @param {string} hora_fin - Hora final del bloque.
 * @returns {Promise<object>} Promesa con la respuesta del servidor.
 */
export function updateBlockDetailsOnServer(blockId, day, hora_ini, hora_fin) {
    return fetch("includes/edicionAgen/actualizar_bloques_final.php?action=update", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            id_temporal: blockId,
            day,
            hora_ini,
            hora_fin,
        }),
    }).then((response) => {
        // Comprobar si la respuesta es JSON antes de intentar procesarla
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

export function saveFinalAgenda() {
    return fetch("includes/edicionAgen/actualizar_bloques_final.php?action=save", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
    })
        .then((response) => {
            // Comprobar si la respuesta es JSON antes de intentar procesarla
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
