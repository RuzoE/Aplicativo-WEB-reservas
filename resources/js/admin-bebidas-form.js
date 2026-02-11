// Formulario de bebidas - vista previa de imagen
document.getElementById('imagen').addEventListener('change', function(e) {
    const label = this.previousElementSibling;
    if (this.files.length > 0) {
        label.querySelector('.file-text').textContent = this.files[0].name;
        label.querySelector('.file-subtext').style.display = 'none';
    }
});
