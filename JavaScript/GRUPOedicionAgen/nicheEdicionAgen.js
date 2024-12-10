const collisionPadding = -1;
/* FUNCIONES VERDADERAMENTE NICHE */
export function showTemporaryMessage(message, duration) {
    const messageBox = document.createElement('div');
    messageBox.textContent = message;
    messageBox.style.position = 'fixed';
    messageBox.style.bottom = '10px';
    messageBox.style.right = '10px';
    messageBox.style.backgroundColor = '#333';
    messageBox.style.color = '#fff';
    messageBox.style.padding = '10px 20px';
    messageBox.style.borderRadius = '5px';
    messageBox.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.3)';
    messageBox.style.zIndex = '1000';
    messageBox.style.userSelect = 'none';
    document.body.appendChild(messageBox);

    setTimeout(() => {
        messageBox.remove();
    }, duration);
}

export function resetForm() {
    const form = document.getElementById('formNuevoBloque');
    form.reset(); // Restablece todos los campos del formulario
}

export function checkColorChange() {
    const tipoBloqueInput = document.getElementById('tipoBloque');
    const colorInput = document.getElementById('colorBloque');
    const labelColorInput = document.getElementById('colorBloqueLabel');
    const actividadInput = document.getElementById('modalInputActivity'); // Selector de actividad

    if (!tipoBloqueInput || !colorInput || !labelColorInput || !actividadInput) {
        console.warn('No se encontraron algunos elementos necesarios para checkColorChange.');
        return;
    }

    if (tipoBloqueInput.value === 'wildblock') {
        // Desactivar y ocultar el selector de color
        colorInput.disabled = true;
        colorInput.style.display = 'none';
        labelColorInput.style.display = 'none';

        // Desactivar y ocultar el selector de actividad
        actividadInput.disabled = true;
        actividadInput.style.display = 'none';
    } else {
        // Reactivar y mostrar el selector de color
        colorInput.disabled = false;
        colorInput.style.display = 'block';
        labelColorInput.style.display = 'block';

        // Reactivar y mostrar el selector de actividad
        actividadInput.disabled = false;
        actividadInput.style.display = 'block';
    }
}

/* FUNCIONES RELACIONADAS AL CALCULO DE TIEMPO EN RELACIÓN A LA POSICIÓN DE LOS BLOQUES */
export function calculateDayOfWeek(container, bloqueX) {
    const containerWidth = container.offsetWidth;
    const dayWidth = containerWidth / 7; // Dividimos el contenedor en 7 días

    // Desplazamos el cálculo medio día para mejorar precisión
    const adjustedX = bloqueX + dayWidth / 2;

    // Índices de días: 0 (Lunes) - 6 (Domingo)
    const dayIndex = Math.floor(adjustedX / dayWidth);

    const daysOfWeek = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

    // Validar y retornar el día correspondiente
    return daysOfWeek[dayIndex] || 'Desconocido';
}

export function calculateTimeRange(container, bloqueY, bloqueHeight) {
    const containerHeight = container.offsetHeight;
    const minutesPerPixel = 1;

    // Redondear la posición y altura a múltiplos de 10
    const roundedY = Math.round(bloqueY / 10) * 10;
    const roundedHeight = Math.round(bloqueHeight / 10) * 10;

    // Calcular el tiempo inicial en minutos
    const totalMinutesStart = roundedY * minutesPerPixel;
    const hora_ini = convertMinutesToTime(totalMinutesStart);

    // Calcular el tiempo final en minutos
    const totalMinutesEnd = (roundedY + roundedHeight) * minutesPerPixel;
    const hora_fin = convertMinutesToTime(totalMinutesEnd);

    return { hora_ini, hora_fin };
}

function convertMinutesToTime(totalMinutes) {
    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;
    return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
}

/* FUNCIONES RELACIONADAS AL MOVIMIENTO DE LOS BLOQUES */
// Hacer bloques arrastrables usando interact.js
export function makeDraggable(container, element, bloqueData, bloques) {
    interact(element)
        .draggable({
            modifiers: [
                interact.modifiers.snap({
                    targets: [
                        interact.snappers.grid({
                            x: container.offsetWidth / 7,
                            y: container.offsetHeight / 144,
                        }),
                    ],
                    range: Infinity,
                    relativePoints: [{ x: 0, y: 0 }],
                }),
                interact.modifiers.restrict({
                    restriction: container,
                    elementRect: { top: 0, left: 0, bottom: 0, right: 0 },
                }),
            ],
            inertia: false,
        })
        .on('dragmove', function (event) {
            const dx = event.dx;
            const dy = event.dy;

            if (!detectCollision(bloqueData, dx, dy, bloques)) {
                bloqueData.x = Math.max(
                    0,
                    Math.min(container.offsetWidth - bloqueData.width, bloqueData.x + dx)
                );

                // Ajustar bloqueData.y al múltiplo más cercano de 10
                bloqueData.y = Math.round(
                    Math.max(
                        0,
                        Math.min(container.offsetHeight - bloqueData.height, bloqueData.y + dy)
                    ) / 10
                ) * 10;

                event.target.style.transform = `translate(${bloqueData.x}px, ${bloqueData.y}px)`;
            }
        });
}

// Detectar colisión entre bloques
function detectCollision(bloqueData, dx, dy, bloques) {
    const rect1 = {
        x: bloqueData.x + dx - collisionPadding,
        y: bloqueData.y + dy - collisionPadding,
        width: bloqueData.width + 2 * collisionPadding,
        height: bloqueData.height + 2 * collisionPadding,
    };

    return bloques.some((other) => {
        if (other === bloqueData) return false;
        const rect2 = {
            x: other.x - collisionPadding,
            y: other.y - collisionPadding,
            width: other.width + 2 * collisionPadding,
            height: other.height + 2 * collisionPadding,
        };
        return (
            rect1.x < rect2.x + rect2.width &&
            rect1.x + rect1.width > rect2.x &&
            rect1.y < rect2.y + rect2.height &&
            rect1.y + rect1.height > rect2.y
        );
    });
}

// Detectar colisión en una posición específica
export function detectCollisionAt(x, y, width, height, bloques) {
    const testRect = {
        x: x - collisionPadding,
        y: y - collisionPadding,
        width: width + 2 * collisionPadding,
        height: height + 2 * collisionPadding,
    };

    return bloques.some((other) => {
        const rect2 = {
            x: other.x - collisionPadding,
            y: other.y - collisionPadding,
            width: other.width + 2 * collisionPadding,
            height: other.height + 2 * collisionPadding,
        };
        return (
            testRect.x < rect2.x + rect2.width &&
            testRect.x + testRect.width > rect2.x &&
            testRect.y < rect2.y + rect2.height &&
            testRect.y + testRect.height > rect2.y
        );
    });
}

export function makeResizable(container, bloqueData) {
    interact(bloqueData.element).resizable({
        edges: { bottom: true }, // Solo permite redimensionar desde el borde inferior
        listeners: {
            move(event) {
                const target = event.target;

                // Calcula nueva altura en múltiplos de 10px
                const newHeight = Math.round((event.rect.height / 10)) * 10;

                // Actualiza la altura en los estilos y datos del bloque
                target.style.height = `${newHeight}px`;
                bloqueData.height = newHeight;

                // Actualiza las coordenadas del bloque para que no salga del contenedor
                bloqueData.y = Math.min(
                    bloqueData.y,
                    container.offsetHeight - bloqueData.height
                );
                target.style.transform = `translate(${bloqueData.x}px, ${bloqueData.y}px)`;
            }
        },
        modifiers: [
            interact.modifiers.restrictEdges({
                outer: container, // Restringe dentro del contenedor
            }),
            interact.modifiers.restrictSize({
                min: { height: 20 }, // Altura mínima (simula 20px)
            }),
        ],
        inertia: true,
    });
}