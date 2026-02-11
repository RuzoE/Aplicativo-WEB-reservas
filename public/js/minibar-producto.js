// Lógica para el detalle de producto del minibar
// Imagen: zoom al mover el mouse
(function(){
    const v = document.getElementById('pd-viewport');
    const img = document.getElementById('pd-image');
    if(!v||!img) return;
    v.addEventListener('mousemove', function(e){
        const r = v.getBoundingClientRect();
        const x = ((e.clientX - r.left)/r.width)*100;
        const y = ((e.clientY - r.top)/r.height)*100;
        img.style.transformOrigin = x+'% '+y+'%';
        img.style.transform = 'scale(1.6)';
    });
    v.addEventListener('mouseleave', function(){
        img.style.transformOrigin = 'center center';
        img.style.transform = 'scale(1)';
    });
})();

// Cantidad stepper
function pdInc(){
    const i = document.getElementById('cantidad');
    const max = parseInt(i.max||'999',10);
    const val = Math.min(max, (parseInt(i.value||'1',10)+1));
    i.value = val;
}
function pdDec(){
    const i = document.getElementById('cantidad');
    const val = Math.max(1, (parseInt(i.value||'1',10)-1));
    i.value = val;
}
