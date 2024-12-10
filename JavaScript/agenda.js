import { calculateXFromDay, calculateYAndHeightFromTime } from './GRUPOedicionAgen/renderBlocks.js';
import { showTemporaryMessage } from './GRUPOedicionAgen/nicheEdicionAgen.js';
import { saveWeekAndOrDay, cerrarLapsoActual, crearNuevoLapso, finalizarDiaServidor } from './fetchActionsAgenda.js';

document.addEventListener('DOMContentLoaded', function () {
    console.log(idDiaActual);
    const container = document.querySelector('.container');
    // Selección de elementos para mostrar detalles
    const detalleTitulo = document.getElementById('detalle-titulo');
    const detalleTipo = document.getElementById('detalle-tipo');
    const detalleActividad = document.getElementById('detalle-actividad');
    const detalleNotas = document.getElementById('detalle-notas');
    const detalleColor = document.querySelector('.color-show');
    // Obtener referencias a los elementos del modal
    const botonInicioFin = document.querySelector('.btn.inicio-fin');

    const modalRespiros = document.getElementById('modalRespiros');
    const cancelarRespiroBtn = document.getElementById('cancelarRespiro');
    const confirmarRespiroBtn = document.getElementById('confirmarRespiro');

    // Verificar que bloquesBD esté definido antes de usarlo
    if (typeof bloquesBD === 'undefined' || !Array.isArray(bloquesBD)) {
        console.error('La variable bloquesBD no está definida o no es un array.');
        return;
    }

    // Llamar a renderBlocks con los datos obtenidos del backend
    renderBlocks(bloquesBD, container);
    
    if (diaFinalizado) {
        desactivarBotonesFinalizados();
        console.log('El día ya está finalizado.');
        return;
    }

    const body = document.body;

    function configurarBotonRespiro() {
        const botonRespiro = document.querySelector('.btn.respiro');
        const modalRespiros = document.getElementById('modalRespiros');
    
        // Asegurarse de que el botón esté habilitado
        botonRespiro.disabled = false;
        botonRespiro.classList.remove('disabled');
    
        // Asignar evento al botón "Respiros"
        botonRespiro.addEventListener('click', () => {
            modalRespiros.style.display = 'flex'; // Mostrar el modal de Respiros
        });
    }
    // Cerrar el modal al hacer clic en "Cancelar"
    cancelarRespiroBtn.addEventListener('click', () => {
        modalRespiros.style.display = 'none';
    });

    // Confirmar selección (de momento solo cierra el modal)
    confirmarRespiroBtn.addEventListener('click', () => {
        console.log('Respiro confirmado.'); // Aquí se podrá manejar lógica futura
        modalRespiros.style.display = 'none';
    });

    // Cerrar el modal si se hace clic fuera del contenido
    modalRespiros.addEventListener('click', (event) => {
        if (event.target === modalRespiros) {
            modalRespiros.style.display = 'none';
        }
    });

    // Verificar si no hay día activo
    if (body.getAttribute('data-no-active-day') === 'true') {
        handleNoActiveDayOrWeek();

        // Comprobar si hay bloques restantes
        if (bloquesRestantes.length > 0) {
            botonInicioFin.textContent = `Comenzar el día (${bloquesRestantes[0].titulo})`;
            botonInicioFin.disabled = false; // Habilitar el botón si hay bloques
            botonInicioFin.classList.remove('disabled');
        } else {
            botonInicioFin.textContent = 'No hay bloques programados por hoy!';
            botonInicioFin.disabled = true; // Deshabilitar el botón si no hay bloques
            botonInicioFin.classList.add('disabled');
        }
    } else {
        activarBotonesYListeners();
    }

    /**
     * Renderiza los bloques en la agenda.
     * @param {Array} bloquesBD - Lista de bloques obtenidos del backend.
     * @param {HTMLElement} container - Contenedor principal de la agenda.
     */
    function renderBlocks(bloquesBD, container) {
        bloquesBD.forEach((bloqueData) => {
            const { ID_Bloque, dia_semana, hora_ini, hora_fin, titulo, color, tipo } = bloqueData;

            // Calcular posiciones y dimensiones
            const x = calculateXFromDay(container, dia_semana);
            const { y, height } = calculateYAndHeightFromTime(container, hora_ini, hora_fin);
            const width = container.offsetWidth / 7;

            // Crear el bloque en el DOM
            const bloque = document.createElement('div');
            bloque.classList.add('bloque', `bloque-${tipo}`);
            bloque.textContent = titulo;
            bloque.style.backgroundColor = tipo === 'wildblock' ? '#ffffff' : color; // Wildblocks siempre blancos
            bloque.style.width = `${width}px`;
            bloque.style.height = `${height}px`;
            bloque.style.transform = `translate(${x}px, ${y}px)`;

            // Agregar un atributo de identificación al bloque para enlazarlo con los datos
            bloque.dataset.id = ID_Bloque;

            // Agregar evento de clic al bloque
            bloque.addEventListener('click', () => {
                console.log(ID_Bloque);
                toggleBlockSelection(ID_Bloque);
            });

            // Agregar el bloque al contenedor
            container.appendChild(bloque);
        });
    }

    // Función para manejar clic en los bloques
    function toggleBlockSelection(blockId) {
        // Busca el bloque seleccionado en bloquesBD
        const bloqueSeleccionado = bloquesBD.find(bloque => bloque.ID_Bloque === blockId);
    
        if (bloqueSeleccionado) {
            // Actualiza los detalles del bloque
            detalleTitulo.textContent = bloqueSeleccionado.titulo || 'Sin titulo';
            detalleActividad.textContent = bloqueSeleccionado.nombre_actividad || 'Sin actividad'; // Mostrar el nombre
            detalleNotas.textContent = bloqueSeleccionado.notas || 'Sin notas';
            detalleColor.style.backgroundColor = bloqueSeleccionado.color || '#f9f9f9';
            detalleTipo.textContent = bloqueSeleccionado.tipo || 'Sin tipo';
        } else {
            // Si no se encuentra el bloque, muestra valores por defecto
            detalleTitulo.textContent = 'N/A';
            detalleActividad.textContent = 'N/A';
            detalleNotas.textContent = 'N/A';
            detalleColor.style.backgroundColor = '#f9f9f9';
            detalleTipo.textContent = 'N/A';
        }
    }

    // Manejo del botón "Inicio-Fin"
    botonInicioFin.addEventListener('click', function () {
        const noActiveBlocks = document.body.getAttribute('data-no-active-blocks') === 'true';

        if (noActiveBlocks) {
            console.warn('Intento de iniciar día sin bloques.');
            showTemporaryMessage('No puedes iniciar el día sin actividades programadas.', 3000);
            return;
        }

        if (botonInicioFin.textContent.startsWith('Comenzar el día')) {
            iniciarDia(); // Lógica para iniciar el día
        } else if (botonInicioFin.textContent === 'Finalizar el día') {
            finalizarDia(); // Preparar funcionalidad para finalizar el día
        }
    });

    function iniciarDia() {  
        saveWeekAndOrDay(agendaId)
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Error al iniciar el día');
                }
                return response.json();
            })
            .then((data) => {
                if (data.estado === 'exito') {
                    idDiaActual = data.diaId; // Actualizar el ID del día actual
                    showTemporaryMessage(`Día iniciado`, 3000);
                    activarBotonesYListeners(); // Activar botones y listeners
                } else {
                    console.error('No se pudo iniciar el día:', data.mensaje);
                }
            })
            .catch((error) => {
                console.error('Error en la solicitud:', error);
            });
    }
    async function finalizarDia() {
        console.log('Finalizar el día iniciado.');
    
        if (!idDiaActual) {
            console.error('No se puede finalizar el día sin un id_dia válido.');
            showTemporaryMessage('Error: No se puede finalizar el día.', 3000);
            return;
        }
    
        try {
            // Cerrar el lapso actual
            const lapsoCerrado = await cerrarLapsoActual(idDiaActual, 'finalizar');
            if (lapsoCerrado?.estado === 'exito') {
                console.log('Último lapso cerrado correctamente.');
    
                // Llamar a la función para finalizar el día en el servidor
                finalizarDiaServidor(idDiaActual);

                console.log('Día finalizado correctamente.');

                // Actualizar la interfaz para reflejar el cambio
                desactivarBotonesFinalizados();
                showTemporaryMessage('Día finalizado exitosamente.', 3000);
            } else {
                console.error('Error al cerrar el último lapso:', lapsoCerrado?.mensaje || 'Desconocido');
                showTemporaryMessage('Error al cerrar el último lapso.', 3000);
            }
        } catch (error) {
            console.error('Error en la solicitud:', error);
            showTemporaryMessage('Error de conexión con el servidor.', 3000);
        }
    }

    function activarBotonesYListeners() {
        const botonInicioFin = document.querySelector('.btn.inicio-fin');
        botonInicioFin.textContent = 'Finalizar el día';
    
        const botones = document.querySelectorAll('.botones .btn');
    
        botones.forEach((boton) => {
            boton.disabled = false;
            boton.classList.remove('disabled');
    
            if (boton.classList.contains('siguiente')) {
                configurarBotonSiguiente(bloquesRestantes); // Pasar la lista temporal
            } else if (boton.classList.contains('respiro')) {
                configurarBotonRespiro(); 
            } else if (boton.classList.contains('pausa')) {
                // este todavía no, tal vez después 
            }
        });
    }

    function configurarBotonSiguiente(bloquesRestantes) {
        const botonSiguiente = document.querySelector('.btn.siguiente');
    
        // Inicializar índice para recorrer bloquesRestantes
        let indiceActual = 0;
    
        // Mostrar el nombre y color del primer bloque o deshabilitar el botón si no hay bloques
        if (bloquesRestantes.length > 0) {
            botonSiguiente.textContent = `Siguiente: ${bloquesRestantes[indiceActual].titulo}`;
            botonSiguiente.style.backgroundColor = bloquesRestantes[indiceActual].color || '#d9d2c5';
        } else {
            botonSiguiente.textContent = 'Sin actividades';
            botonSiguiente.style.backgroundColor = '';
            botonSiguiente.disabled = true;
            return;
        }
    
        // Evento para recorrer bloques
        botonSiguiente.replaceWith(botonSiguiente.cloneNode(true)); // Reemplazar para eliminar eventos antiguos
        const nuevoBotonSiguiente = document.querySelector('.btn.siguiente');
        nuevoBotonSiguiente.addEventListener('click', async () => {
            console.log(`Actividad actual: ${bloquesRestantes[indiceActual].titulo}`);
    
            if (!idDiaActual) {
                console.error('No se puede cerrar o crear un lapso porque no se encontró un id_dia válido.');
                return;
            }
            console.log(idDiaActual);

            // Cerrar lapso actual solo si pertenece al día actual
            const lapsoCerrado = await cerrarLapsoActual(idDiaActual, 'siguiente');
            if (lapsoCerrado?.estado === 'exito') {
                console.log('Lapso cerrado correctamente.');
            } else {
                console.error('Error al cerrar el lapso:', lapsoCerrado?.mensaje || 'Desconocido');
            }

            // Crear un nuevo lapso con los datos del siguiente bloque
            const bloqueActual = bloquesRestantes[indiceActual];
            const nuevoLapso = await crearNuevoLapso(agendaId, bloqueActual);
            if (nuevoLapso?.estado === 'exito') {
                console.log('Nuevo lapso creado:', nuevoLapso);
            } else {
                console.error('Error al crear el nuevo lapso:', nuevoLapso?.mensaje || 'Desconocido');
            }

            // Incrementar índice y actualizar texto y color
            indiceActual++;
            if (indiceActual < bloquesRestantes.length) {
                nuevoBotonSiguiente.textContent = `Siguiente: ${bloquesRestantes[indiceActual].titulo}`;
                nuevoBotonSiguiente.style.backgroundColor = bloquesRestantes[indiceActual].color || '#d9d2c5';
            } else {
                nuevoBotonSiguiente.textContent = 'Sin más actividades';
                nuevoBotonSiguiente.style.backgroundColor = '';
                nuevoBotonSiguiente.disabled = true;
                nuevoBotonSiguiente.classList.add('disabled');
            }
        });
    
        // Activar automáticamente el evento de "Siguiente" una vez si el día no ha comenzado
        if (document.body.getAttribute('data-no-active-day') === 'true' && bloquesRestantes.length > 0) {
            nuevoBotonSiguiente.click(); // Simula el clic automático
        }
    }
    
    /**
     * Desactiva todos los botones excepto "Inicio-Fin" y configura su comportamiento.
     */
    function handleNoActiveDayOrWeek() {
        // Seleccionar todos los botones
        const botones = document.querySelectorAll('.botones .btn');

        botones.forEach((boton) => {
            // Desactivar todos los botones excepto "Inicio-Fin"
            if (boton.classList.contains('inicio-fin')) {
                boton.disabled = false;
                boton.classList.remove('disabled');
            } else {
                boton.disabled = true;
                boton.classList.add('disabled');
            }
        });
    }
    // Desactivar botones tras finalizar el día
    function desactivarBotonesFinalizados() {
        const botones = document.querySelectorAll('.botones .btn');
        botones.forEach((boton) => {
            boton.disabled = true;
            boton.classList.add('disabled');
        });

        const botonInicioFin = document.querySelector('.btn.inicio-fin');
        botonInicioFin.textContent = 'Día Finalizado';
        botonInicioFin.disabled = true;
        botonInicioFin.classList.add('disabled');
    }
});