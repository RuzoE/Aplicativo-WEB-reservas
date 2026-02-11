# Integraciones del Módulo de Recepción

## ✅ Servicios de Negocio

### `CheckInService`
- `processCheckIn()`: Crea guest, stay, folio y marca habitación ocupada
- `validateCheckIn()`: Valida que no haya check-in duplicado

### `FolioService`
- `postCharge()`: Agrega cargo y recalcula balance
- `receivePayment()`: Registra pago y recalcula balance
- `recalculateBalance()`: Calcula balance = Σ(cargos+impuestos) - Σ(pagos)
- `canClose()`: Valida balance ≈ 0 (tolerancia 0.01)
- `closeFolio()`: Cierra folio si balance es cero

### `CheckOutService`
- `processCheckOut()`: Cierra folio, marca stay como CheckedOut, libera habitación
- `canCheckOut()`: Verifica si es posible hacer checkout

---

## 🔐 Políticas de Autorización

### `StayPolicy`
- `viewAny()`, `view()`: Requiere `reception.access`
- `create()`, `update()`: Requiere `reception.checkin`
- `checkOut()`: Requiere `reception.checkout`
- `moveRoom()`: Requiere `reception.room_move`

### `FolioPolicy`
- `view()`: Requiere `reception.folio.view`
- `postCharge()`: Requiere `reception.folio.post_charge` + folio abierto
- `receivePayment()`: Requiere `reception.folio.receive_payment` + folio abierto
- `close()`: Requiere `reception.checkout`

**Registradas en:** `AuthServiceProvider`

---

## 📡 Eventos de Dominio

### `StayStarted`
Disparado cuando se completa check-in.

**Payload:** `Stay $stay`

**Uso:** Notificar housekeeping, enviar bienvenida

### `ChargePosted`
Disparado cuando se agrega un cargo al folio.

**Payload:** `Charge $charge`

**Uso:** Contabilidad, auditoría

### `PaymentReceived`
Disparado cuando se registra un pago.

**Payload:** `Payment $payment`

**Uso:** Conciliación bancaria, recibos

### `StayEnded`
Disparado cuando se completa check-out.

**Payload:** `Stay $stay`

**Uso:** Housekeeping, reportes, encuestas

---

## 🔗 Integración Minibar → Folio

### Flujo Automático

1. **Usuario completa compra en minibar**
2. **Sistema busca stay activo del usuario:**
   - Busca `Guest` por email o document_number del usuario logueado
   - Filtra stays con `status = 'InHouse'`
3. **Si existe stay activo con folio abierto:**
   - Crea `Charge`:
     - `source = 'Minibar'`
     - `description = 'Minibar Order #{compraId}'`
     - `amount = total`
     - `tax = total * 0.19`
     - `reference_type = 'Compra'`
     - `reference_id = {compraId}`
   - Actualiza balance del folio
   - Dispara evento `ChargePosted`
4. **Si no hay stay activo:**
   - Compra se procesa normalmente sin cargo a folio

### Controlador Modificado
`app/Http/Controllers/Minibar/User/CheckoutController.php`

**Cambios:**
- Importa `Stay`, `FolioService`, `ChargePosted`
- Después de crear `Compra`, busca stay activo
- Usa `FolioService::postCharge()` para agregar cargo
- Mantiene flujo original intacto (no rompe funcionalidad)

---

## ⏰ Night Audit Job

### `NightAuditPostRoomRates`

**Propósito:** Postear cargos de tarifa de habitación diariamente.

**Lógica:**
1. Busca todos los stays con `status = 'InHouse'`
2. Para cada stay:
   - Verifica que tenga folio abierto
   - Chequea que no se haya posteado tarifa hoy
   - Crea cargo:
     - `source = 'RoomRate'`
     - `description = 'Room Rate - {roomName}'`
     - `amount = {daily_rate}`
     - `tax = {daily_rate} * 0.19`
     - `posted_at = now()`
   - Actualiza balance

**Programación (agregar a `app/Console/Kernel.php`):**
```php
protected function schedule(Schedule $schedule): void
{
    $schedule->job(new \App\Jobs\Reception\NightAuditPostRoomRates())
        ->dailyAt('00:15')
        ->name('night-audit-room-rates')
        ->onOneServer();
}
```

**Ejecución manual (testing):**
```bash
php artisan queue:work --once
```

Despachar manualmente:
```php
\App\Jobs\Reception\NightAuditPostRoomRates::dispatch();
```

---

## 🎯 Actualizaciones en Controladores

### `CheckInController`
- ✅ Usa `CheckInService`
- ✅ Autoriza con `authorize('create', Stay::class)`
- ✅ Valida check-in duplicado
- ✅ Dispara evento `StayStarted`

### `FolioController`
- ✅ Usa `FolioService`
- ✅ Autoriza `view`, `postCharge`, `receivePayment`
- ✅ Dispara eventos `ChargePosted`, `PaymentReceived`

### `CheckOutController`
- ✅ Usa `CheckOutService`
- ✅ Autoriza con `authorize('checkOut', $stay)`
- ✅ Maneja excepciones (balance no cero)
- ✅ Dispara evento `StayEnded`

### `Minibar\User\CheckoutController`
- ✅ Busca stay activo por email/documento del usuario
- ✅ Postea cargo al folio si existe
- ✅ No interrumpe flujo si no hay stay
- ✅ Dispara evento `ChargePosted`

---

## 🧪 Pruebas de Integración

### Test 1: Check-in → Minibar → Folio
1. Crear Order con usuario/guest vinculado
2. Check-in desde `/reception/check-in/{orderId}`
3. Como mismo usuario, comprar en minibar
4. Verificar que cargo aparece en folio con `reference_type='Compra'`

### Test 2: Night Audit
1. Crear stay InHouse con `daily_rate = 100`
2. Ejecutar `NightAuditPostRoomRates::dispatch()`
3. Verificar cargo RoomRate con amount=100, tax=19

### Test 3: Autorización
1. Usuario sin permisos intenta agregar cargo → 403
2. Usuario con `reception.folio.post_charge` puede agregar cargo
3. Folio cerrado no permite nuevos cargos

### Test 4: Check-out con Balance
1. Stay con balance > 0 → Error
2. Agregar pago para balance = 0
3. Check-out exitoso

---

## 📦 Archivos Creados

### Servicios
- `app/Services/Reception/CheckInService.php`
- `app/Services/Reception/FolioService.php`
- `app/Services/Reception/CheckOutService.php`

### Políticas
- `app/Policies/StayPolicy.php`
- `app/Policies/FolioPolicy.php`

### Eventos
- `app/Events/Reception/StayStarted.php`
- `app/Events/Reception/ChargePosted.php`
- `app/Events/Reception/PaymentReceived.php`
- `app/Events/Reception/StayEnded.php`

### Jobs
- `app/Jobs/Reception/NightAuditPostRoomRates.php`

### Actualizados
- `app/Http/Controllers/Reception/CheckInController.php`
- `app/Http/Controllers/Reception/FolioController.php`
- `app/Http/Controllers/Reception/CheckOutController.php`
- `app/Http/Controllers/Minibar/User/CheckoutController.php`
- `app/Providers/AuthServiceProvider.php`

---

## ⚡ Comandos Rápidos

```bash
# Clear cache después de agregar políticas
php artisan optimize:clear

# Ver eventos registrados
php artisan event:list

# Test job manual
php artisan tinker
>>> \App\Jobs\Reception\NightAuditPostRoomRates::dispatch();

# Ver rutas con políticas
php artisan route:list --name=reception
```

---

## 🚀 Próximos Pasos Opcionales

1. **Listeners para eventos:** Email bienvenida en `StayStarted`, recibo en `PaymentReceived`
2. **Validaciones avanzadas:** Policies más granulares por estado de stay
3. **API REST:** Endpoints JSON para integración con otros sistemas
4. **Reportes:** Dashboard con estadísticas de ocupación, revenue
5. **Key cards:** Modelo `KeyCard` con asignación/desactivación
6. **Incidents:** Modelo `Incident` para quejas/mantenimiento

---

**Versión:** 1.1  
**Fecha:** Diciembre 2, 2025
