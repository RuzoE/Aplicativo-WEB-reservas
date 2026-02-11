// Filtro de catálogo del minibar
document.getElementById('tipo-filter')?.addEventListener('change', function(){
    setTimeout(function(){ document.getElementById('catalog-form').submit(); }, 100);
});
