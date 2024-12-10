import { showTemporaryMessage, resetForm, checkColorChange, calculateDayOfWeek,
    calculateTimeRange, makeDraggable, detectCollisionAt, makeResizable } from './nicheEdicionAgen.js';
import { deleteBlockFromServer, saveBlockToServer, clearSessionBlocks, fetchBlockData, 
    updateBlockOnServer, updateBlockDetailsOnServer, saveFinalAgenda } from './fetchActionsEdicionAgen.js';
import { calculateXFromDay, calculateYAndHeightFromTime } from './renderBlocks.js';

document.addEventListener('DOMContentLoaded', function () {    
    // Elementos principales
    const container = document.querySelector('.container');
    const nuevoBtn = document.querySelector('.btn.nuevo');
    const editarBtn = document.querySelector('.btn.editar');
    const eliminarBtn = document.querySelector('.btn.eliminar');
    const guardarBtn = document.querySelector('.btn.guardar');
    const modal = document.getElementById('modalNuevoBloque');
    const cancelarBtn = document.getElementById('cancelarNuevoBloque');
    const confirmarBtn = document.getElementById('confirmarNuevoBloque');

    const confirmModal = document.getElementById('confirmGuardarModal');
    const cancelarGuardarBtn = document.getElementById('cancelarGuardar');
    const confirmarGuardarBtn = document.getElementById('confirmarGuardar');

    //Aquí se van a pasar la lista de datos de DOM de los bloques al array de bloques, en lugar de hacer un array vacio
    const bloques = renderBlocks(bloquesBD, container);
    let selectedBlock = null;
    let selectedBlockId = null; // ID del bloque seleccionado
    let isEditing = false; // Indica si el formulario se usa para editar o crear un bloque

    // Deshabilitar inicialmente los botones de edición y eliminación
    editarBtn.disabled = true;
    eliminarBtn.disabled = true;
    editarBtn.classList.add('disabled');
    eliminarBtn.classList.add('disabled');

    /**
     * Renderiza los bloques cargados desde PHP y los agrega al contenedor.
     * @param {HTMLElement} container - Contenedor principal de bloques.
     */
    function renderBlocks(bloquesBD, container) {
        const bloques = []; // Lista para guardar los datos renderizados
    
        bloquesBD.forEach((bloqueData) => {
            const { dia_semana, hora_ini, hora_fin, titulo, color, tipo, ID_Bloque } = bloqueData;
    
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
    
            // Asignar el ID temporal como atributo de datos
            bloque.dataset.id = ID_Bloque;
    
            container.appendChild(bloque);
    
            // Guardar los datos renderizados en el array en el mismo formato que createBlock
            const bloqueRenderizado = {
                element: bloque,
                x,
                y,
                width,
                height,
            };
    
            bloques.push(bloqueRenderizado);
    
            // Hacer el bloque draggable
            makeDraggable(container, bloque, bloqueRenderizado, bloques);
            makeResizable(container, bloqueRenderizado);
    
            // Configurar evento de selección
            bloque.addEventListener('click', () => toggleBlockSelection(bloque));
        });
    
        return bloques;
    }

    // Actualizar estado de los botones de edición y eliminación
    function updateButtonStates() {
        if (selectedBlock) {
            editarBtn.disabled = false;
            eliminarBtn.disabled = false;
            editarBtn.classList.remove('disabled');
            eliminarBtn.classList.remove('disabled');
        } else {
            editarBtn.disabled = true;
            eliminarBtn.disabled = true;
            editarBtn.classList.add('disabled');
            eliminarBtn.classList.add('disabled');
        }
    }

    // Manejar selección de bloques
    function toggleBlockSelection(block) {
        if (selectedBlock) {
            selectedBlock.style.border = ''; // Remover el borde del bloque previamente seleccionado
        }
    
        selectedBlock = selectedBlock === block ? null : block;
    
        if (selectedBlock) {
            selectedBlock.style.border = '2px solid black'; // Agregar borde al bloque seleccionado
            selectedBlockId = selectedBlock.dataset.id || null; // Actualizar el ID seleccionado
    
            // Usar fetchBlockData para obtener los datos del bloque seleccionado
            fetchBlockData(selectedBlockId)
                .then((data) => {
                    if (data.status === 'success') {  
                        const blockData = data.data;
    
                        // Rellenar el formulario con los datos obtenidos
                        document.getElementById('tituloBloque').value = blockData.titulo || '';
                        document.getElementById('colorBloque').value = blockData.color || '#ffffff';
    
                        const tipoSelect = document.getElementById('tipoBloque');
                        tipoSelect.value = blockData.tipo || 'generico';
    
                        document.getElementById('notasBloque').value = blockData.notas || '';
    
                        const actividadSelect = document.getElementById('modalInputActivity');
                        console.log(blockData);
                        actividadSelect.value = blockData.actividad || '';
    
                        checkColorChange(); // Ajustar si wildblock desactiva el color
                    } else {
                        console.error('Error al recuperar datos del bloque:', data.message);
                    }
                })
                .catch((error) => console.error('Error en la solicitud fetch:', error));

                console.log('Esta es la PORQUERÍA que aparece en bloques:');
                console.log(bloques);
        } else {
            selectedBlockId = null; // No hay bloque seleccionado
        }
    
        updateButtonStates(); // Actualizar el estado visual de los botones
    }
    
    // Crear y agregar un bloque
    function createBlock(titulo, color, tipo) {
        const initialX = 0;
        const initialY = 0;
        const width = container.offsetWidth / 7;
        const height = 60;
    
        if (detectCollisionAt(initialX, initialY, width, height, bloques)) {
            showTemporaryMessage('La zona de izquierda superior está ocupada...', 3000);
            return false; // Indicar que no se pudo crear el bloque
        }
    
        const bloque = document.createElement('div');
        bloque.classList.add('bloque', `bloque-${tipo}`); // Asignar clase específica según el tipo
        bloque.textContent = titulo;
    
        // Estilo general para todos los bloques
        bloque.style.backgroundColor = tipo === 'wildblock' ? '#ffffff' : color; // Wildblocks siempre blancos
        bloque.style.width = `${width}px`;
        bloque.style.height = `${height}px`;
        bloque.style.transform = `translate(${initialX}px, ${initialY}px)`;
    
        container.appendChild(bloque);
        const bloqueData = { element: bloque, x: initialX, y: initialY, width, height };
        bloques.push(bloqueData);
        makeDraggable(container, bloque, bloqueData, bloques);
        makeResizable(container, bloqueData);
    
        bloque.addEventListener('click', () => toggleBlockSelection(bloque));
    
        showTemporaryMessage('Bloque agregado', 3000);
        return true; // Indicar que el bloque se creó con éxito
    }

    // Manejo de eventos
    nuevoBtn.addEventListener('click', () => {
        if (selectedBlock) {
            toggleBlockSelection(selectedBlock);
        }
        isEditing = false; // Modo de creación
        modal.style.display = 'flex';
        document.getElementById('tituloFormularioModal').textContent = 'Nuevo Bloque';
        resetForm(); // Limpia el formulario
        checkColorChange();
    });
    editarBtn.addEventListener('click', () => {
        if (!selectedBlock || !selectedBlockId) {
            showTemporaryMessage('Selecciona un bloque para editar', 3000);
            return;
        }
        isEditing = true; // Modo de edición
        modal.style.display = 'flex';
        document.getElementById('tituloFormularioModal').textContent = 'Editar Bloque';
    });
    cancelarBtn.addEventListener('click', () => (modal.style.display = 'none'));
    
    document.getElementById('tipoBloque').addEventListener('change', function () {
        checkColorChange();
    });

    // Confirmar, guardar y crear el bloque al hacer clic en "Confirmar"
    confirmarBtn.addEventListener('click', () => {
        const titulo = document.getElementById('tituloBloque').value;
        const color = document.getElementById('colorBloque').value;
        const tipo = document.getElementById('tipoBloque').value;
        const notas = document.getElementById('notasBloque').value;
        const id_actividad = tipo === 'wildblock' ? null : document.getElementById('modalInputActivity').value;
        const agenda_id_actual = document.querySelector('meta[name="agenda_id_actual"]').content;
    
        if (!titulo) {
            showTemporaryMessage('Es obligatorio tener un título', 3000);
            return;
        }
    
        // Validar actividad solo si el tipo no es wildblock
        if (tipo !== 'wildblock' && !id_actividad) {
            showTemporaryMessage('Debe seleccionar una actividad', 3000);
            return;
        }
    
        if (isEditing) {
            // Editar bloque existente
            updateBlockInSession(selectedBlockId, titulo, color, tipo, notas, id_actividad);
        } else {
            // Crear un nuevo bloque
            const blockCreated = createBlock(titulo, color, tipo, id_actividad);
            if (!blockCreated) return;
    
            saveBlockToServer(agenda_id_actual, titulo, tipo, notas, color, id_actividad, true) // isNew: true
                .then((data) => {
                    if (data.status === 'success') {
                        console.log('Bloque guardado correctamente:', data.message);
                        if (data.id_temporal) {
                            const lastBlock = bloques[bloques.length - 1];
                            lastBlock.element.dataset.id = data.id_temporal;
                            toggleBlockSelection(lastBlock.element);
                        }
                    } else {
                        console.error('Error al guardar el bloque:', data.message);
                    }
                })
                .catch((error) => console.error('Error en la solicitud fetch:', error));
        }
    
        modal.style.display = 'none';
    });     

    // Actualizar un bloque existente en sesión y en el servidor
    function updateBlockInSession(blockId, titulo, color, tipo, notas, id_actividad) {
        const blockIndex = bloques.findIndex((b) => b.element.dataset.id === blockId);
        if (blockIndex === -1) {
            console.error('El bloque no existe.');
            showTemporaryMessage('El bloque seleccionado no existe', 3000);
            return;
        }
    
        // Actualizar datos en memoria
        const blockData = bloques[blockIndex];
        blockData.titulo = titulo;
        blockData.color = tipo === 'wildblock' ? '#ffffff' : color;
        blockData.tipo = tipo;
        blockData.notas = notas;
        blockData.id_actividad = id_actividad;
    
        console.log(blockData.id_actividad);
        // Actualizar la clase del bloque en el DOM según el tipo
        const blockElement = blockData.element;
        blockElement.className = `bloque bloque-${tipo}`; // Actualiza la clase
        blockElement.style.backgroundColor = tipo === 'wildblock' ? '#ffffff' : color;
        blockElement.textContent = titulo; // Actualiza el título en el DOM
    
        // Enviar cambios al servidor
        updateBlockOnServer(blockId, titulo, blockData.color, tipo, notas, blockData.id_actividad)
            .then((data) => {
                if (data.status === 'success') {
                    showTemporaryMessage('Bloque actualizado correctamente', 3000);
                } else {
                    console.error('Error al actualizar bloque:', data.message);
                    showTemporaryMessage('No se pudo actualizar el bloque', 3000);
                }
            })
            .catch((error) => {
                console.error('Error en la solicitud PUT:', error);
                showTemporaryMessage('Error de conexión con el servidor', 3000);
            });
    }    

    eliminarBtn.addEventListener('click', () => {
        if (selectedBlock && selectedBlockId) {
            // Solicitar al servidor que elimine el bloque
            deleteBlockFromServer(selectedBlockId)
                .then((response) => {
                    if (response.status === 'success') {
                        // Eliminar del DOM y limpiar el estado
                        const index = bloques.findIndex((b) => b.element.dataset.id === selectedBlockId);
                        if (index !== -1) bloques.splice(index, 1); // Eliminar del array de bloques
    
                        selectedBlock.remove(); // Eliminar del DOM
                        selectedBlock = null; // Limpiar selección
                        selectedBlockId = null; // Limpiar ID seleccionado
                        updateButtonStates(); // Actualizar botones
                        showTemporaryMessage('Bloque eliminado con exito', 3000);
                    } else {
                        console.error('Error al eliminar el bloque:', response.message);
                    }
                })
                .catch((error) => console.error('Error en la solicitud fetch:', error));
        }
    });    

    // Cerrar el modal al cancelar
    cancelarGuardarBtn.addEventListener('click', () => {
        confirmModal.style.display = 'none';
    });

    // Proceder con el guardado al confirmar
    confirmarGuardarBtn.addEventListener('click', async () => {
        confirmModal.style.display = 'none'; // Cerrar el modal
        const agenda_id_actual = document.querySelector('meta[name="agenda_id_actual"]').content;
        let exito = true; // Inicializamos en true, asumimos éxito hasta que ocurra un error
    
        // Sincronizar datos completos de cada bloque usando fetchBlockData
        const bloquesCompletos = await Promise.all(
            bloques.map(async (bloqueData) => {
                const blockId = bloqueData.element.dataset.id;
                try {
                    const response = await fetchBlockData(blockId); // Obtener datos completos
                    if (response.status === 'success') {
                        return { ...response.data, domData: bloqueData }; // Combina datos completos y DOM
                    } else {
                        console.error(`Error al obtener datos del bloque con ID ${blockId}:`, response.message);
                        exito = false;
                        return null;
                    }
                } catch (error) {
                    console.error(`Error en la solicitud para el bloque con ID ${blockId}:`, error);
                    exito = false;
                    return null;
                }
            })
        ).then((result) => result.filter(Boolean)); // Filtrar nulos en caso de errores
    
        if (!exito) {
            showTemporaryMessage('Error al sincronizar los datos de los bloques.', 3000);
            return;
        }
    
        // Actualizar bloques en la sesión
        const promises = bloquesCompletos.map((bloque) => {
            const { domData, isNew } = bloque;
            const { hora_ini, hora_fin } = calculateTimeRange(container, domData.y, domData.height);
            const day = calculateDayOfWeek(container, domData.x);
    
            // Actualizar solo posiciones y horarios en la sesión, ignorando isNew aquí
            return updateBlockDetailsOnServer(
                domData.element.dataset.id, // ID del bloque
                day,
                hora_ini,
                hora_fin
            )
                .then((data) => {
                    if (data.status === 'success') {
                        console.log(isNew ? 'Nuevo bloque actualizado en sesión.' : 'Bloque existente actualizado en sesión.');
                    } else {
                        console.error(`Error al actualizar bloque: ${data.message}`);
                        exito = false;
                    }
                })
                .catch((error) => {
                    console.error('Error al actualizar bloque:', error);
                    exito = false;
                });
        });
    
        // Esperar a que todas las actualizaciones terminen
        Promise.all(promises).then(() => {
            if (exito) {
                saveFinalAgenda() // Guardado definitivo desde la sesión
                    .then(() => {
                        showTemporaryMessage('Guardado finalizado exitosamente.', 1000);
                        setTimeout(() => {
                            window.location.href = `agenda.php?agenda_id=${agenda_id_actual}`;
                        }, 2000);
                    })
                    .catch((error) => console.error('Error en el guardado final:', error));
            } else {
                showTemporaryMessage('Algunos bloques no se pudieron guardar. Revise los errores.', 3000);
            }
        });
    });

    // Mostrar el modal al hacer clic en "Guardar"
    guardarBtn.addEventListener('click', () => {
        confirmModal.style.display = 'flex';
    });

    //Borrar sesión si se sale de la página (por cualquier metodo)
    window.addEventListener('beforeunload', (event) => {
        clearSessionBlocks()
            .then((data) => {
                if (data.status === 'success') {
                    console.log(data.message); // Confirmar limpieza
                } else {
                    console.error('Error al limpiar la sesión:', data.message);
                }
            })
            .catch((error) => console.error('Error en la solicitud para limpiar la sesión:', error));
    }); 
});