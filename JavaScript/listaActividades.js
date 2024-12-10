// Revisen estas funciones, parece que muchas no hacen nada, pues no se mandan nunca a llamar (MARTÍN)
function openActivityModal() {
    document.getElementById('activityModal').style.display = 'flex';
}

function closeActivityModal() {
    document.getElementById('activityModal').style.display = 'none';
    document.getElementById('modalInputTitle').value = '';
    document.getElementById('modalInputDescription').value = '';
    document.getElementById('modalInputType').value = '';
    document.getElementById('modalInputColor').value = '#ffffff';
}

function saveCard() {
    const title = document.getElementById('modalInputTitle').value;
    const description = document.getElementById('modalInputDescription').value;
    const type = document.getElementById('modalInputType').value;
    const color = document.getElementById('modalInputColor').value;

    if (title && description && type && color) {
        addCard(title, description, type, color);
        closeActivityModal();
    } else {
        alert('Por favor, completa todos los campos.');
    }
}

function addCard(title, description, type, color) {
    const card = document.createElement('div');
    card.classList.add('card');
    card.style.backgroundColor = color;
    card.innerHTML = `
        <h4>${title}</h4>
        <p>${description}</p>
    `;

    const targetColumn = type === 'depleting' ? 'depletingColumn' : 'renewingColumn';
    document.getElementById(targetColumn).appendChild(card);
}

// Funciones para manejar el modal de categorías
function openCategoryModal() {
    document.getElementById('categoryModal').style.display = 'flex';
}

function closeCategoryModal() {
    document.getElementById('categoryModal').style.display = 'none';
    document.getElementById('categoryInputTitle').value = '';
    document.getElementById('categoryInputDescription').value = '';
}

function saveCategory() {
    const title = document.getElementById('categoryInputTitle').value;
    const description = document.getElementById('categoryInputDescription').value;

    if (title && description) {
        addCategory(title, description);
        closeCategoryModal();
    } else {
        alert('Por favor, completa todos los campos.');
    }
}

function addCategory(title, description) {
    const category = document.createElement('div');
    category.classList.add('card'); // Reutilizamos la clase de estilo de las tarjetas
    category.innerHTML = `
        <h4>${title}</h4>
        <p>${description}</p>
    `;

    document.getElementById('categoryColumn').appendChild(category);
}

document.addEventListener("DOMContentLoaded", () => {
    // Confirmar eliminación de actividad
    const deleteActivityButtons = document.querySelectorAll(".delete-activity");
    deleteActivityButtons.forEach(button => {
        button.addEventListener("click", event => {
            if (!confirm("¿Estás seguro de que deseas eliminar esta actividad?")) {
                event.preventDefault();
            }
        });
    });

    // Confirmar eliminación de categoría
    const deleteCategoryButtons = document.querySelectorAll(".delete-category");
    deleteCategoryButtons.forEach(button => {
        button.addEventListener("click", event => {
            if (!confirm("¿Estás seguro de que deseas eliminar esta categoría?")) {
                event.preventDefault();
            }
        });
    });

    const deleteCategoryActivityButtons = document.querySelectorAll(".delete-category-activity");
    deleteCategoryActivityButtons.forEach(button => {
        button.addEventListener("click", event => {
            if (!confirm("¿Estás seguro de que deseas eliminar esta categoría de la actividad?")) {
                event.preventDefault();
            }
        });
    });
});

// Abrir modal para agregar categoría
function openAddCategoryModal(idActividad) {
    const modal = document.getElementById('addCategoryModal');
    document.getElementById('modalAddCategoryActivityId').value = idActividad;
    modal.style.display = 'flex';
}

// Cerrar modal para agregar categoría
function closeAddCategoryModal() {
    const modal = document.getElementById('addCategoryModal');
    modal.style.display = 'none';
}

// Habilitar la categoría y actualizar las opciones según el tipo de actividad seleccionado
function habilitarCategoria() {
    const tipoSeleccionado = document.getElementById('modalInputType').value;
    const selectorCategoria = document.getElementById('modalInputCategory');

    // Habilitar el selector de categoría solo si se seleccionó un tipo
    if (tipoSeleccionado) {
        selectorCategoria.disabled = false;

        // Limpiar opciones existentes
        while (selectorCategoria.options.length > 1) {
            selectorCategoria.remove(1);
        }

        // Agregar opciones que coincidan con el tipo seleccionado
        categorias.forEach(categoria => {
            if ((tipoSeleccionado === 'depleting' && categoria.tipo === 'tiempo') ||
                (tipoSeleccionado === 'renewing' && categoria.tipo === 'energia')) {
                const opcion = document.createElement('option');
                opcion.value = categoria.ID_Categoria;
                opcion.textContent = categoria.nombre;
                selectorCategoria.appendChild(opcion);
            }
        });
    } else {
        selectorCategoria.disabled = true;
        selectorCategoria.value = ''; // Limpiar selección actual
    }
}

// Deshabilitar categoría si el tipo cambia
document.getElementById('modalInputType').addEventListener('change', () => {
    const selectorCategoria = document.getElementById('modalInputCategory');
    selectorCategoria.value = ''; // Limpiar selección actual
});

function openEditActivityModal(id, title, description) {
    document.getElementById('editActivityModal').style.display = 'flex';
    document.getElementById('editActivityId').value = id;
    document.getElementById('editActivityTitle').value = title;
    document.getElementById('editActivityDescription').value = description;
}

function closeEditActivityModal() {
    document.getElementById('editActivityModal').style.display = 'none';
}

function openEditCategoryModal(id, title, color) {
    document.getElementById('editCategoryModal').style.display = 'flex';
    document.getElementById('editCategoryId').value = id;
    document.getElementById('editCategoryTitle').value = title;
    document.getElementById('editCategoryColor').value = color;
}

function closeEditCategoryModal() {
    document.getElementById('editCategoryModal').style.display = 'none';
}
