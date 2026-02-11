// Formulario inline de empleados
document.addEventListener('DOMContentLoaded', function() {
    const btnNuevo = document.getElementById('btnNuevoEmpleado');
    const wrapper = document.getElementById('empleadoFormWrapper');
    const btnCerrar = document.getElementById('btnCerrarForm');
    const btnReset = document.getElementById('btnResetEmpleado');
    const passInput = document.getElementById('passwordInline');
    const passBar   = document.getElementById('passBarInline');
    const btnToggle = document.getElementById('togglePassInline');
    const btnGen    = document.getElementById('genPassInline');

    const tableWrapper = document.getElementById('empleadosTableWrapper');

    function strength(p){
        let s=0; if(!p) return 0; if(p.length>=8) s+=25; if(/[a-z]/.test(p)) s+=15; if(/[A-Z]/.test(p)) s+=20; if(/[0-9]/.test(p)) s+=20; if(/[^A-Za-z0-9]/.test(p)) s+=20; return Math.min(s,100);
    }
    function genPass(){const c='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+';let o='';for(let i=0;i<12;i++)o+=c[Math.floor(Math.random()*c.length)];return o;}

    if(btnNuevo){
        btnNuevo.addEventListener('click',()=>{
            wrapper.classList.toggle('d-none');
            if(!wrapper.classList.contains('d-none')){
                tableWrapper.classList.add('d-none');
            }
            window.scrollTo({top:0,behavior:'smooth'});
        });
    }
    if(btnCerrar){
        btnCerrar.addEventListener('click',()=>{
            wrapper.classList.add('d-none');
            tableWrapper.classList.remove('d-none');
        });
    }
    if(btnReset){btnReset.addEventListener('click',()=>document.getElementById('empleadoCreateForm').reset());}
    if(passInput){passInput.addEventListener('input',()=>{passBar.style.width=strength(passInput.value)+'%';});}
    if(btnToggle){btnToggle.addEventListener('click',()=>{passInput.type=passInput.type==='password'?'text':'password';});}
    if(btnGen){btnGen.addEventListener('click',()=>{passInput.value=genPass();passInput.dispatchEvent(new Event('input'));});}
});
