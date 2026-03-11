# DEBUG: Botón Completar Mantenimiento

## PRUEBA RÁPIDA (Test directo sin modal)

1. **Abre la consola del navegador** (F12 → pestaña Console)
2. **Identifica el ID de una orden** mirando la imagen adjunta (habitación 1 o 6 que están "En Mant.")
3. **Ejecuta en la consola**: 
   ```javascript
   testCompleteOrder(1)
   ```
   (cambia el 1 por el ID correcto de la orden si es necesario)

4. **Observa qué pasa**:
   - ✅ Si funciona: verás "Test exitoso!" y la página se recargará
   - ❌ Si falla: verás el error en consola

**Esto nos dice si el problema es el modal o el backend.**

---

## PRUEBA COMPLETA (Con el botón y modal)

1. **Abre la consola del navegador**:
   - Presiona `F12` en el navegador
   - Ve a la pestaña "Console" (Consola)

2. **Recarga la página del mantenimiento**:
   - Ve a `/admin/mantenimiento`
   - Presiona `Ctrl + F5` para forzar recarga sin caché

3. **Haz clic en el botón "Completar"** de una habitación en mantenimiento

4. **Observa los logs en consola**. Deberías ver:
   ```
   🔧 completeOrder llamada con ID: [número]
   📋 showConfirmModal llamada con opciones: {...}
   ✅ Callback guardado: function
   ```

5. **Haz clic en "Sí, Completar"** en el modal

6. **Observa más logs**. Deberías ver:
   ```
   🎯 confirmAction llamada
   📞 Callback pendiente: SÍ
   🚀 Ejecutando callback...
   ✅ Confirmación aceptada, iniciando fetch...
   📝 Order ID: [número]
   🔑 CSRF Token: Encontrado
   🌐 URL: /admin/mantenimiento/[número]/complete
   📡 Respuesta recibida - Status: [código]
   ✅ Success: {...}
   ```

## ¿Qué logs ves?

Copia y pega TODOS los mensajes que aparecen en la consola aquí.

## Si no ves ningún log:
- Verifica que estás en la pestaña correcta (Console)
- Asegúrate de que la página se recargó después de los cambios
- Presiona `Ctrl + F5` para forzar la recarga sin caché

## Si ves un error:
- Copia el error completo (incluyendo el stack trace rojo)
- Revisa si hay algún mensaje "❌" rojo

## Posibles resultados:

### A) No ves el primer log (🔧 completeOrder llamada...)
→ El onclick del botón no funciona o JavaScript está bloqueado

### B) Ves hasta "✅ Callback guardado" pero no "🎯 confirmAction llamada"
→ El botón del modal no ejecuta confirmAction()

### C) Ves "📞 Callback pendiente: NO"
→ El callback se perdió (problema con la variable pendingConfirmCallback)

### D) Ves un error de CSRF
→ Problema con el token de seguridad

### E) Ves "Status: 500" o similar
→ Error en el servidor
