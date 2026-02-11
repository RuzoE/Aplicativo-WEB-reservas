# Módulo de Recepción — Hotel Piloto SAM

Este documento describe la arquitectura, funcionalidad y uso del módulo de Recepción implementado en el sistema Hotel Piloto SAM.

---

## 📋 Descripción General

El módulo de **Recepción** gestiona las operaciones de front desk del hotel, incluyendo:

- **Check-in**: Registro de huéspedes y apertura de estadías
- **Gestión de Estadías (Stays)**: Control de huéspedes en casa
- **Folios Financieros**: Cargos, pagos y balance de cuentas
- **Check-out**: Cierre de estadías y folios

---

## 🏗️ Arquitectura del Módulo

### Modelos de Dominio

#### 1. **Guest** (Huésped)
Información del huésped/cliente.

**Campos:**
- `first_name`, `last_name`: Nombre completo
- `document_type`, `document_number`: Identificación
- `email`, `phone`: Contacto
- `country`: País de origen
- `notes`: Notas adicionales

**Relaciones:**
- `hasMany(Stay)`: Un huésped puede tener múltiples estadías

---

#### 2. **Stay** (Estadía)
Representa la estadía activa de un huésped en el hotel.

**Campos:**
- `reservation_id`: FK a `orders` (reserva vinculada)
- `room_id`: FK a `rooms`
- `guest_id`: FK a `guests`
- `status`: Estado (`Booked`, `PreCheckIn`, `InHouse`, `CheckedOut`, `NoShow`)
- `arrival_at`, `departure_at`: Fechas planificadas
- `actual_check_in_at`, `actual_check_out_at`: Fechas reales
- `adults`, `children`: Ocupantes
- `rate_plan`, `daily_rate`: Plan tarifario
- `notes`: Observaciones

**Relaciones:**
- `belongsTo(Guest)`: Huésped principal
- `belongsTo(Room)`: Habitación asignada
- `belongsTo(Order)`: Reserva (si existe)
- `hasMany(Folio)`: Folios de cuenta

**Estados del Ciclo de Vida:**
```
Booked → PreCheckIn → InHouse → CheckedOut
                            ↘ NoShow
```

---

#### 3. **Folio** (Cuenta/Folio)
Registro financiero de cargos y pagos de una estadía.

**Campos:**
- `stay_id`: FK a `stays`
- `number`: Número único de folio (UUID)
- `status`: Estado (`Open`, `Closed`)
- `currency`: Moneda (USD por defecto)
- `balance`: Balance actual (decimal)

**Relaciones:**
- `belongsTo(Stay)`
- `hasMany(Charge)`: Cargos
- `hasMany(Payment)`: Pagos

**Lógica de Balance:**
- Balance = Σ(cargos + impuestos) - Σ(pagos)
- El balance debe ser 0 para cerrar el folio en check-out

---

#### 4. **Charge** (Cargo)
Línea de cargo al folio.

**Campos:**
- `folio_id`: FK a `folios`
- `source`: Fuente (`RoomRate`, `Minibar`, `Service`, `Adjustment`)
- `description`: Descripción del cargo
- `amount`: Monto
- `tax`: Impuesto
- `posted_by`: FK a `users` (empleado)
- `posted_at`: Fecha/hora
- `reference_type`, `reference_id`: Referencia polimórfica (ej. orden minibar)

**Relaciones:**
- `belongsTo(Folio)`
- `belongsTo(User, 'posted_by')`

---

#### 5. **Payment** (Pago)
Registro de pago recibido.

**Campos:**
- `folio_id`: FK a `folios`
- `method`: Método (`Cash`, `Card`, `Transfer`, `Voucher`)
- `amount`: Monto
- `currency`: Moneda
- `received_by`: FK a `users`
- `received_at`: Fecha/hora
- `external_ref`: Referencia externa (núm. transacción, voucher)

**Relaciones:**
- `belongsTo(Folio)`
- `belongsTo(User, 'received_by')`

---

## 🔐 Roles y Permisos

### Roles
- **`receptionist`**: Recepcionista
- **`frontdesk_manager`**: Gerente de recepción

### Permisos
- `reception.access`: Acceso al módulo
- `reception.checkin`: Realizar check-in
- `reception.checkout`: Realizar check-out
- `reception.folio.view`: Ver folios
- `reception.folio.post_charge`: Agregar cargos
- `reception.folio.receive_payment`: Registrar pagos
- `reception.room_move`: Mover huésped de habitación
- `reception.keycard.manage`: Gestionar tarjetas/llaves
- `reception.incident.manage`: Gestionar incidentes

**Seeder:**
```bash
php artisan db:seed --class=ReceptionPermissionsSeeder
```

---

## 🛣️ Rutas

### Web (Blade)

**Middleware:** `auth`, `role:administrador|reservas,web`

```
GET  /reception/dashboard                → Dashboard de recepción
GET  /reception/check-in/{reservation}   → Formulario check-in
POST /reception/check-in/{reservation}   → Procesar check-in
GET  /reception/stay/{stay}/folio        → Ver folio
POST /reception/stay/{stay}/charges      → Agregar cargo
POST /reception/stay/{stay}/payments     → Registrar pago
POST /reception/check-out/{stay}         → Procesar check-out
```

**Nota:** Actualmente usa rol `administrador|reservas`; puedes refinar para incluir `receptionist|frontdesk_manager`.

---

## 🎬 Flujos de Trabajo

### 1. Check-in

**Entrada:** Order (reserva) existente

**Proceso:**
1. Acceder a `/reception/check-in/{orderId}`
2. Completar datos del huésped (nombre, documento, email, teléfono)
3. Submit → Crea `Guest`, `Stay` con status `InHouse`, `Folio` abierto
4. Redirección a folio

**Controlador:** `Reception\CheckInController::store`

---

### 2. Gestión de Folio (In-House)

**Pantalla:** `/reception/stay/{stayId}/folio`

**Operaciones:**
- **Agregar cargo:** POST `/stay/{stay}/charges`
  - Campos: `source`, `description`, `amount`, `tax`
  - Actualiza `folio.balance`
- **Registrar pago:** POST `/stay/{stay}/payments`
  - Campos: `method`, `amount`, `currency`, `external_ref`
  - Reduce `folio.balance`

**Controlador:** `Reception\FolioController`

---

### 3. Check-out

**Proceso:**
1. Revisar folio en `/reception/stay/{stay}/folio`
2. Asegurar `balance == 0`
3. Submit check-out → POST `/check-out/{stay}`
4. Cierra folio (`status=Closed`), marca stay (`status=CheckedOut`, `actual_check_out_at`)
5. Redirección a dashboard

**Controlador:** `Reception\CheckOutController::store`

**Validación:** Balance debe ser 0; retorna error si hay saldo pendiente.

---

## 🎨 Vistas (Blade)

### `reception/dashboard.blade.php`
Dashboard principal con:
- **Llegadas hoy:** Stays con `arrival_at = today`
- **Salidas hoy:** Stays con `departure_at = today`
- **En casa:** Stays con `status = InHouse`

### `reception/check_in.blade.php`
Formulario de check-in con campos:
- Nombre, apellido
- Tipo/número de documento
- Email, teléfono

### `reception/folio.blade.php`
Vista de folio con:
- Resumen de stay y balance
- Tabla de cargos
- Formulario para agregar cargo
- Tabla de pagos
- Formulario para registrar pago
- Botón de check-out

---

## 🔗 Integración con Módulos Existentes

### Reservas (Orders)
- El check-in consume un `Order` existente
- `Stay.reservation_id` → `orders.id`
- Campos mapeados:
  - `order.check_in` → `stay.arrival_at`
  - `order.check_out` → `stay.departure_at`
  - `order.room_id` → `stay.room_id`

### Minibar
**Próximo:** Vincular órdenes de minibar como cargos al folio.

**Propuesta:**
- Al finalizar compra de minibar, crear `Charge`:
  - `source = 'Minibar'`
  - `reference_type = 'MinibarOrder'`
  - `reference_id = {compra_id}`
  - `amount = total`
- Adjuntar al folio del stay activo del usuario

---

## 📊 Migraciones

Las siguientes tablas fueron creadas:

```
2025_12_02_000001_create_guests_table.php
2025_12_02_000002_create_stays_table.php
2025_12_02_000003_create_folios_table.php
2025_12_02_000004_create_charges_table.php
2025_12_02_000005_create_payments_table.php
```

**Ejecutar:**
```bash
php artisan migrate
```

---

## 🧪 Testing

### Pruebas Manuales

**Check-in:**
1. Crear una reserva (Order) con `room_id`, `check_in`, `check_out`
2. Visitar `/reception/check-in/{orderId}`
3. Completar formulario y enviar
4. Verificar creación de `Guest`, `Stay`, `Folio`

**Folio:**
1. Navegar a `/reception/stay/{stayId}/folio`
2. Agregar cargo (ej. Room Service $50)
3. Verificar balance actualizado
4. Registrar pago (ej. Cash $50)
5. Verificar balance = 0

**Check-out:**
1. Con balance = 0, hacer check-out
2. Verificar `stay.status = CheckedOut`, `folio.status = Closed`

### Feature Tests (Próximos)
- `tests/Feature/Reception/CheckInTest.php`
- `tests/Feature/Reception/FolioTest.php`
- `tests/Feature/Reception/CheckOutTest.php`

---

## 🚀 Próximos Pasos

### 1. Servicios de Negocio
Crear clases de servicio para encapsular lógica:
- `app/Services/Reception/CheckInService.php`
- `app/Services/Reception/FolioService.php`
- `app/Services/Reception/CheckOutService.php`

### 2. Políticas (Policies)
Implementar:
- `StayPolicy`: controlar acceso por role
- `FolioPolicy`: validar operaciones de cargo/pago

### 3. Eventos y Jobs
- **Event:** `StayStarted`, `ChargePosted`, `PaymentReceived`, `StayEnded`
- **Job:** `NightAuditPostRoomRatesJob` (postear cargos de tarifa nocturna)
- **Job:** `OverdueCheckoutsReminderJob`

### 4. Entidades Adicionales
- **Incident:** Incidencias (quejas, mantenimiento)
- **KeyCard:** Gestión de tarjetas/llaves

### 5. Integración Minibar → Folio
Hook en `CheckoutController::pay()` para crear `Charge` automáticamente.

### 6. UI/UX Enhancements
- Componentes Blade reutilizables
- Validación en tiempo real
- Impresión de recibo/folio (PDF)

---

## 📝 Comandos Rápidos

```bash
# Migrar tablas
php artisan migrate

# Seed permisos y roles
php artisan db:seed --class=ReceptionPermissionsSeeder

# Limpiar cache de rutas (si modificas web.php)
php artisan route:clear

# Ver todas las rutas de recepción
php artisan route:list --name=reception
```

---

## 💡 Notas Técnicas

- **UUID para folios:** Cada folio tiene un `number` único generado con `Str::uuid()`
- **Decimales:** Montos y balances usan `decimal(12,2)`
- **Timestamps:** `posted_at`, `received_at` capturan momento exacto
- **Middleware existente:** Usa `role:administrador|reservas,web`; ajusta según necesidad
- **Sin conflictos:** Todas las rutas/controladores/modelos son aditivos; no tocan código existente

---

## 🤝 Contribución

Para agregar funcionalidad:
1. Modelos/Migraciones → `app/Models`, `database/migrations`
2. Controladores → `app/Http/Controllers/Reception`
3. Vistas → `resources/views/reception`
4. Rutas → `routes/web.php` grupo `reception.*`
5. Tests → `tests/Feature/Reception`

---

## 📞 Soporte

Para dudas o mejoras, contacta al equipo de desarrollo.

---

**Versión:** 1.0  
**Fecha:** Diciembre 2, 2025  
**Autor:** GitHub Copilot & Equipo Hotel Piloto SAM
