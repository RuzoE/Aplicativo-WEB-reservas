// Formulario de habitaciones - drag & drop de imágenes
// Elementos del DOM
const uploadWrapper = document.getElementById('uploadWrapper');
const imageInput = document.getElementById('imageInput');
const newImagePreview = document.getElementById('newImagePreview');
const previewImage = document.getElementById('previewImage');

if (uploadWrapper && imageInput) {
    // Click para seleccionar archivo
    uploadWrapper.addEventListener('click', () => {
        imageInput.click();
    });

    // Drag & Drop
    uploadWrapper.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadWrapper.classList.add('dragover');
    });

    uploadWrapper.addEventListener('dragleave', () => {
        uploadWrapper.classList.remove('dragover');
    });

    uploadWrapper.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadWrapper.classList.remove('dragover');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            imageInput.files = files;
            handleImageSelection();
        }
    });

    // Cambio de archivo
    imageInput.addEventListener('change', handleImageSelection);
}

function handleImageSelection() {
    if (!imageInput || !previewImage || !newImagePreview || !uploadWrapper) {
        return;
    }

    const file = imageInput.files[0];

    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();

        reader.onload = (e) => {
            previewImage.src = e.target.result;
            newImagePreview.style.display = 'block';
            uploadWrapper.style.display = 'none';
        };

        reader.readAsDataURL(file);
    } else {
        alert('Por favor selecciona un archivo de imagen válido');
        imageInput.value = '';
    }
}

function resetImageUpload() {
    if (!imageInput || !newImagePreview || !uploadWrapper) {
        return;
    }

    imageInput.value = '';
    newImagePreview.style.display = 'none';
    uploadWrapper.style.display = 'block';
}

function toggleImageField() {
    if (!uploadWrapper) {
        return;
    }

    uploadWrapper.style.display = 'block';
}

function removeImage() {
    resetImageUpload();
}

window.resetImageUpload = resetImageUpload;
window.toggleImageField = toggleImageField;
