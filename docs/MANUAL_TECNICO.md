# MANUAL TÉCNICO COMPLETO - SISTEMA HOTELERO INTEGRADO

**Versión:** 1.0  
**Última actualización:** Marzo 2026  
**Plataforma:** Hotel Piloto SAM  
**Desarrollador Principal:** [Tu Equipo]

---

## TABLA DE CONTENIDOS

1. [Introducción](#introducción)
2. [Arquitectura del Sistema](#arquitectura-del-sistema)
3. [Stack Tecnológico](#stack-tecnológico)
4. [Estructura del Proyecto Laravel](#estructura-del-proyecto-laravel)
5. [Configuración del Entorno de Desarrollo](#configuración-del-entorno-de-desarrollo)
6. [Sistema de Autenticación](#sistema-de-autenticación)
7. [Módulos del Sistema](#módulos-del-sistema)
8. [Rutas y API REST](#rutas-y-api-rest)
9. [Modelo de Datos](#modelo-de-datos)
10. [Seguridad y Control de Acceso](#seguridad-y-control-de-acceso)
11. [Validaciones y Manejo de Errores](#validaciones-y-manejo-de-errores)
12. [Generación de Reportes](#generación-de-reportes)
13. [Buenas Prácticas y Patrones](#buenas-prácticas-y-patrones)
14. [Mejoras Técnicas Futuras](#mejoras-técnicas-futuras)

---

## 1. INTRODUCCIÓN

Este manual técnico proporciona documentación completa y detallada sobre la **arquitectura del sistema hotelero integrado "Hotel Piloto SAM"**. Está dirigido a desarrolladores que necesitan mantener, extender o depurar el código fuente.

### Propósito

El sistema es una **plataforma hotelera integral** que integra múltiples módulos operativos para gestionar reservas, recepción, minibar, mantenimiento y auditoría de un hotel. Utiliza una arquitectura **moderna, escalable y segura** basada en Laravel 11 para el backend y Vue.js 3 para el frontend.

### Público Objetivo

- Desarrolladores de mantenimiento del proyecto
- Nuevos miembros del equipo de desarrollo
- Arquitectos/diseñadores que revisen la estructura
- DevOps encargados del despliegue

---

## 2. ARQUITECTURA DEL SISTEMA

### 2.1 Visión General de Capas

La aplicación sigue una **arquitectura de capas de tres niveles (3-tier)**:

```
┌─────────────────────────────────────────────────────────────────┐
│                     CAPA DE PRESENTACIÓN                         │
│              Frontend: Vue.js 3 + TailwindCSS                   │
│         (SPA - Single Page Application / Inertia)              │
└─────────────────────────────────────────────────────────────────┘
                              ↕ HTTP/REST
┌─────────────────────────────────────────────────────────────────┐
│                    CAPA DE APLICACIÓN                           │
│      Backend: Laravel 11 + Inertia + Laravel Jetstream         │
│    (Controllers, Services, Middleware, Validaciones)            │
└─────────────────────────────────────────────────────────────────┘
                              ↕ SQL/ORM
┌─────────────────────────────────────────────────────────────────┐
│                    CAPA DE DATOS                                 │
│         MySQL 8.0+ (Bases de datos relacional)                 │
│    (Modelos Eloquent ORM, Migraciones)                          │
└─────────────────────────────────────────────────────────────────┘
```

### 2.2 Flujo de Datos y Comunicación

1. **Request del Usuario (Frontend → Backend)**
   - Usuario interactúa con componente Vue.js
   - Componente emite petición HTTP (GET/POST/PUT/DELETE)
   - Laravel recibe el request vía Inertia o API REST
   - Middleware valida autenticación y autorización

2. **Procesamiento (Backend)**
   - Route matchea el request con el Controller correspondiente
   - Controller valida datos de entrada
   - Service ejecuta lógica de negocio
   - Modelos Eloquent interactúan con la base de datos
   - Respuesta se serializa (JSON o Inertia props)

3. **Response al Usuario (Backend → Frontend)**
   - JSON REST API: respuesta JSON pura
   - Inertia: renderiza componente Vue con props
   - Frontend actualiza DOM reactivamente

### 2.3 Patrones Arquitectónicos Utilizados

#### MVC (Model-View-Controller)
- **Models:** Eloquent basados
- **Views:** Blade templates + Componentes Vue.js
- **Controllers:** Contienen lógica de enrutamiento

#### Service Layer Pattern
- Lógica de negocio abstracta en `app/Services/`
- Controllers deleguen a Services
- Facilita testing y reutilización

#### API RESTful
- Endpoints con métodos HTTP estándar
- Respuestas con status codes HTTP apropiados
- JSON como formato de datos

#### Middleware Pipeline
- Autenticación (Auth)
- Autorización (Roles/Permissions)
- Auditoría de acceso
- Validación de CSRF

---

## 3. STACK TECNOLÓGICO

### 3.1 Backend

| Componente | Versión | Descripción |
|-----------|---------|------------|
| **Laravel** | 11.x | Framework PHP moderno |
| **Inertia.js** | Última | Adaptador Laravel-Vue |
| **Laravel Jetstream** | Última | Autenticación + Multi-tenancy |
| **Laravel Sanctum** | Integrado | API Token Authentication |
| **Spatie Permissions** | Última | Control de roles y permisos |
| **Barryvdh/DomPDF** | Última | Generación de PDFs |
| **Google API Client** | Última | Integración con Google Drive |
| **Guzzle HTTP** | Última | Cliente HTTP |

### 3.2 Frontend

| Componente | Versión | Descripción |
|-----------|---------|------------|
| **Vue.js** | 3.5.x | Framework reactivo |
| **Vite** | 4.0+ | Build tool y dev server |
| **TailwindCSS** | 3.4.x | Utilidades CSS |
| **Axios** | 1.1+ | Cliente HTTP |
| **Laravel Vite Plugin** | 0.7+ | Plugin para integración |

### 3.3 Base de Datos

| Componente | Versión | Descripción |
|-----------|---------|------------|
| **MySQL** | 8.0+ | Base de datos relacional |
| **Eloquent ORM** | Laravel | ORM para modelos |
| **Migraciones** | Laravel | Versionamiento del schema |

### 3.4 Herramientas de Desarrollo

| Herramienta | Propósito |
|------------|----------|
| **Laragon** | Entorno local (Apache, MariaDB, PHP) |
| **Composer** | Gestor de dependencias PHP |
| **npm** | Gestor de paquetes JavaScript |
| **PHPUnit** | Testing unitario |
| **Puppeteer** | Screenshots automatizados |

---

## 4. ESTRUCTURA DEL PROYECTO LARAVEL

### 4.1 Estructura de Directorios

```
project-root/
├── app/
│   ├── Console/          # Comandos Artisan personalizados
│   ├── Events/           # Eventos que disparan listeners
│   ├── Http/
│   │   ├── Controllers/  # Controllers por módulo
│   │   │   ├── Admin/
│   │   │   ├── Api/
│   │   │   ├── Auth/
│   │   │   ├── Minibar/
│   │   │   ├── Reception/
│   │   │   └── OrderController.php
│   │   ├── Middleware/   # Middleware personalizado
│   │   ├── Requests/     # Form Requests (validación)
│   │   └── Kernel.php    # Registro de middleware
│   ├── Jobs/             # Jobs asincronos
│   ├── Listeners/        # Event listeners
│   ├── Mail/             # Mailables
│   ├── Models/           # Modelos Eloquent
│   ├── Policies/         # Políticas de autorización
│   ├── Providers/        # Service Providers
│   ├── Rules/            # Reglas de validación personalizadas
│   ├── Services/         # Lógica de negocio
│   └── Support/          # Funciones helper
├── bootstrap/
│   └── app.php           # Bootstrap la aplicación
├── config/               # Archivos de configuración
├── database/
│   ├── factories/        # Model factories para testing
│   ├── migrations/       # Migraciones del schema
│   └── seeders/          # Seeders para datos de prueba
├── resources/
│   ├── css/              # Estilos (input TailwindCSS)
│   ├── js/               # Componentes Vue.js
│   │   ├── Pages/        # Componentes página (vistas)
│   │   ├── Components/   # Componentes reutilizables
│   │   ├── Layouts/      # Layouts base
│   │   └── app.js        # Punto de entrada Vue
│   └── views/            # Plantillas Blade (si aplica)
├── routes/
│   ├── api.php           # Rutas API REST (Sanctum)
│   ├── web.php           # Rutas web (sesión Inertia)
│   ├── channels.php      # Broadcasting channels
│   └── console.php       # Comandos console
├── storage/              # Archivos temporales, logs
├── tests/                # Pruebas unitarias e integración
├── vendor/               # Dependencias Composer
├── .env                  # Variables de entorno
├── .env.example          # Plantilla .env
├── artisan               # CLI de Laravel
├── composer.json         # Dependencias PHP
├── package.json          # Dependencias Node.js
├── vite.config.js        # Configuración Vite
└── tailwind.config.js    # Configuración TailwindCSS
```

### 4.2 Descripción de Directorios Clave

#### `app/Http/Controllers/`
Organizados por módulo de negocio:

- **Admin/**: Controllers administrativos (gestión de empleados, roles, reportes)
  - `AdminDashboardController`: Dashboard principal
  - `EmployeeController`: CRUD de empleados
  - `RoleController`: Gestión de roles
  - `Habitaciones/`: Módulo de habitaciones
  - `Minibar/`: Módulo minibar
  - `Mantenimiento/`: Módulo mantenimiento
  - `ReportController`: Generación de reportes
  - `AuditoriaController`: Auditoría del sistema

- **Api/**: Controllers específicos para API REST
  - `AuthController`: Autenticación con Sanctum
  - `MinibarProductController`: CRUD de productos

- **Reception/**: Módulo de recepción
  - `CheckInController`: Ingreso de huéspedes
  - `CheckOutController`: Salida de huéspedes
  - `FolioController`: Gestión de folios (resumen de cargos)
  - `DashboardController`: Dashboard recepción

- **Minibar/User/**: Frontend para huéspedes
  - `CatalogController`: Catálogo de productos
  - `CartController`: Carrito de compras
  - `CheckoutController`: Proceso de pago
  - `BebidaController`: Gestión de bebidas

#### `app/Models/`
Modelos principales del sistema:

```
User.php                 # Usuario (empleado o huésped)
Guest.php               # Huésped (información personal)
Order.php               # Reserva/Pedido de habitación
Stay.php                # Estancia (check-in/check-out)
Room.php                # Habitación física
RoomType.php            # Tipo de habitación (suite, matrimonial, etc.)
Folio.php               # Resumen de cargos de una estancia
Charge.php              # Cargo individual (minibar, servicio, etc.)
Payment.php             # Pago realizado
MinibarProduct.php      # Producto minibar (bebida)
BebidaType.php          # Clasificación de bebida (alcohólica/no)
Compra.php              # Compra de minibar por huésped
CompraProducto.php      # Pivot: Compra → Productos
MaintenanceOrder.php    # Orden de mantenimiento
Auditoria.php           # Registro de auditoría
Invoice.php             # Factura
```

#### `app/Services/`
Lógica de negocio:

- `AuditoriaService.php`: Registra acciones de usuario
- `Reception/CheckInService.php`: Lógica de check-in

#### `app/Http/Middleware/`
Pipeline de procesamiento:

- `Authenticate.php`: Valida autenticación
- `AuditAdminAccess.php`: Audita acceso administrativo
- `IsAdmin.php`: Verifica rol administrador

#### `routes/`

- **web.php**: Rutas Inertia (sesión)
  - Rutas públicas
  - Rutas protegidas por autenticación
  - Rutas administrativas
  - Agrupadas por módulo

- **api.php**: Rutas API REST (Sanctum tokens)
  - `/api/auth/*`: Autenticación
  - `/api/minibar-products`: CRUD de productos
  - `/api/admin/*`: Endpoints administrativos

#### `resources/js/`

Componentes Vue.js por funcionalidad:

```
Pages/                  # Componentes de página (Inertia)
  ├── Admin/
  ├── Reception/
  ├── Minibar/
  └── ...

Components/            # Componentes reutilizables
  ├── Forms/
  ├── Tables/
  ├── Modals/
  └── ...

Layouts/              # Layouts base
  ├── AppLayout.vue
  ├── AdminLayout.vue
  └── ...
```

---

## 5. CONFIGURACIÓN DEL ENTORNO DE DESARROLLO

### 5.1 Requisitos Previos

```
PHP 8.2+           (Language)
Composer            (Dependency manager)
Node.js 18+         (JavaScript runtime)
MySQL 8.0+          (Database)
Laragon             (Local development environment)
Git                 (Version control)
```

### 5.2 Setup Inicial en Laragon

#### Paso 1: Clonar el Repositorio

```bash
# En directorio www de Laragon
cd c:\laragon\www
git clone https://github.com/RuzoE/Aplicativo-WEB-reservas.git hotel-piloto-sam
cd hotel-piloto-sam
```

#### Paso 2: Instalar Dependencias PHP

```bash
# Instalar dependencias de Composer
composer install

# Si hay conflicto de versiones
composer install --no-interaction --no-progress --prefer-dist --ignore-platform-reqs
```

#### Paso 3: Configurar .env

```bash
# Copiar archivo de ejemplo
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate

# Configurar variables clave
# APP_NAME=Hotel Piloto SAM
# APP_ENV=local
# APP_DEBUG=true
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=hotel_piloto
# DB_USERNAME=root
# DB_PASSWORD=
```

#### Paso 4: Configurar Base de Datos

```bash
# En Laragon, crear BD en HeidiSQL
# Nombre: hotel_piloto
# Charset: utf8mb4

# Ejecutar migraciones
php artisan migrate

# (Opcional) Seed datos de prueba
php artisan db:seed
```

#### Paso 5: Instalar Dependencias de Node.js

```bash
npm install
```

#### Paso 6: Compilar Assets

```bash
# Desarrollo (watch mode)
npm run dev

# Producción
npm run build
```

#### Paso 7: Iniciar Servidor Local

```bash
# En otra terminal, dentro del directorio
php artisan serve

# O dejar que Laragon maneje Apache
# Acceder a: http://hotel-piloto-sam.test
```

### 5.3 Archivo .env Ejemplo Completo

```ini
APP_NAME="Hotel Piloto SAM"
APP_ENV=local
APP_KEY=base64:xxxxx=
APP_DEBUG=true
APP_URL=http://hotel-piloto-sam.test
APP_TIMEZONE=America/Bogota

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hotel_piloto
DB_USERNAME=root
DB_PASSWORD=

# Mail (desarrollo)
MAIL_MAILER=log
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=

# Google Drive (opcional)
GOOGLE_DRIVE_CLIENT_ID=your-client-id
GOOGLE_DRIVE_CLIENT_SECRET=your-secret
GOOGLE_DRIVE_REFRESH_TOKEN=your-token
GOOGLE_DRIVE_FOLDER_ID=your-folder

# Seguridad
APP_FORCE_HTTPS=false
LOG_LEVEL=debug

# Session
SESSION_DRIVER=cookie
SESSION_LIFETIME=120

# Cache
CACHE_DRIVER=file

# Queue
QUEUE_CONNECTION=sync
```

### 5.4 Verificación Post-instalación

```bash
# Revisar salud de la aplicación
php artisan tinker
> version()  # Debe mostrar versión de Laravel

# Probar conexión a DB
php artisan tinker
> DB::connection()->getPdo()  # Conexión exitosa

# Revisar que la clave esté generada
php artisan key:show  # Debe mostrar una clave encriptada

# Listar rutas disponibles
php artisan route:list
```

---

## 6. SISTEMA DE AUTENTICACIÓN

### 6.1 Arquitectura de Autenticación

El sistema utiliza un enfoque **dual de autenticación**:

```
┌─────────────────────────────────────────────┐
│        SISTEMA DE AUTENTICACIÓN             │
├─────────────────────────────────────────────┤
│                                              │
│  ┌──────────────────┐   ┌──────────────────┐│
│  │  SESSION-BASED   │   │  TOKEN-BASED     ││
│  │  (Inertia Web)   │   │  (REST API)      ││
│  ├──────────────────┤   ├──────────────────┤│
│  │ - Cookies HTTP   │   │ - Sanctum tokens ││
│  │ - CSRF tokens    │   │ - Bearer auth    ││
│  │ - Guards: web    │   │ - Guards:sanctum ││
│  │ - Jetstream      │   │ - Token DB       ││
│  └──────────────────┘   └──────────────────┘│
│                                              │
└─────────────────────────────────────────────┘
```

### 6.2 Laravel Jetstream

**Jetstream** proporciona scaffolding de autenticación moderna:

#### Características
- Sistema de login/registro
- Verificación de email
- Recuperación de contraseña
- Gestión de sesiones
- Autenticación de dos factores (2FA, optional)
- Avatar de usuario
- Profile management

#### Ubicación
- Controllers: `app/Http/Controllers/Auth/`
- Views: `resources/views/auth/`
- Modelos: `app/Models/User.php`

#### Flujo de Login

```
┌─────────────┐      ┌──────────────────┐      ┌──────────────┐
│   Usuario   │─────→│  AuthController  │─────→│   Database   │
│ Ingresa     │      │                  │      │   Valida     │
│ credenciales│      │ - Valida email   │      │   contraseña│
│             │      │ - Hash contraseña│      │              │
└─────────────┘      └──────────────────┘      └──────────────┘
       ↑                      ↓
       │             ┌─────────────────┐
       │             │ Crea sesión HTTP│
       │             │ o Token Sanctum │
       └─────────────┤                 │
                     └─────────────────┘
```

### 6.3 Spatie Roles & Permissions

Sistema de control de acceso granular:

#### Instalación en use

```php
// app/Models/User.php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable {
    use HasRoles;
}
```

#### Roles Disponibles

| Rol | Descripción | Permisos |
|-----|------------|----------|
| `administrador` | Acceso total | Todas las acciones |
| `recepcion` | Gestión de huéspedes | Check-in/out, Folios |
| `minibar` | Gestión de bebidas | Inventario, Ventas |
| `reservas` | Gestión de reservas | CRUD órdenes |
| `mantenimiento` | Órdenes de mantenimiento | Crear, asignar, completar |

#### Asignación de Roles

```php
// Asignar rol a usuario
$user->assignRole('recepcion');

// Verificar rol
if ($user->hasRole('administrador')) {
    // ...
}

// Puede múltiples roles
$user->syncRoles(['recepcion', 'minibar']);
```

### 6.4 Laravel Sanctum (API Authentication)

Para autenticación de API REST:

#### Token Generation

```php
// En AuthController
$token = $user->createToken('api-token')->plainTextToken;

// Retorna token para usar en headers
return response()->json(['token' => $token]);
```

#### Uso del Token

```bash
# Request con Bearer token
curl -H "Authorization: Bearer YOUR_TOKEN_HERE" \
     http://api.example.com/api/minibar-products
```

#### Middleware

```php
// En api.php routes
Route::middleware('auth:sanctum')->group(function () {
    // Rutas protegidas solo con token
});
```

### 6.5 Guards en config/auth.php

```php
'guards' => [
    'web' => [
        'driver' => 'session',  // Session-based (Jetstream)
        'provider' => 'users',
    ],
    'sanctum' => [
        'driver' => 'sanctum',  // Token-based (API)
        'provider' => 'users',
    ],
],
```

---

## 7. MÓDULOS DEL SISTEMA

### 7.1 MÓDULO: RESERVAS

#### Propósito
Gestión completa del ciclo de vida de reservas de habitaciones, desde creación, pago anticipado hasta finalización.

#### Componentes Principales

**Controladores:**
- `OrderController` (web/api)
- `Admin\Habitaciones\OrderController` (admin)
- `Admin\Habitaciones\RoomController`
- `Admin\Habitaciones\RoomTypeController`

**Modelos:**
```
Order
├── Relaciones
│   ├── user (belongsTo)
│   ├── room (belongsTo)
│   ├── stays (hasMany)
│   └── room_type_id (info tipo habitación)
```

**Rutas Web:**
```
GET/POST   /rooms                  # Listar/Buscar habitaciones
POST       /orders                 # Crear reserva
GET        /orders                 # Listar mis reservas
GET        /orders/payment/{token} # Página de pago
POST       /orders/payment/{token}/confirm # Confirmar pago
```

**Rutas Admin:**
```
GET        /admin/habitaciones/dashboard
GET        /admin/habitaciones/reservas            # Listar
GET        /admin/habitaciones/reservas/create     # Crear
POST       /admin/habitaciones/reservas            # Guardar
GET        /admin/habitaciones/reservas/{id}/edit  # Editar
PUT        /admin/habitaciones/reservas/{id}       # Actualizar
DELETE     /admin/habitaciones/reservas/{id}       # Eliminar

GET        /admin/habitaciones/habitaciones        # CRUD habitaciones
GET        /admin/habitaciones/tipos-habitacion    # CRUD tipos
```

**Estados de Order:**
```php
const STATUS_PENDIENTE = 'pendiente';           // Creada, sin pago
const STATUS_ANTICIPO_PAGADO = 'anticipo_pagado'; // 30% pagado
const STATUS_RESERVA_PREVIA = 'reserva_previa';   // Previa
const STATUS_OCUPADA = 'ocupada';               // Check-in realizado
const STATUS_FINALIZADA = 'finalizada';         // Check-out realizado
```

**Atributos Clave:**
```php
Order {
    id,
    nombre_cliente,
    check_in (datetime),        # Fecha/hora entrada
    check_out (datetime),        # Fecha/hora salida
    room_id,
    room_type_id,
    user_id,
    room_number,                # Número físico habitación
    status,
    payment_token (str, 40),    # Token para pago
    down_payment_amount (decimal), # 30% anticipo
    is_paid (boolean),
    created_at, updated_at
}
```

**Lógica Clave:**

1. **Búsqueda de Habitaciones:**
   - Filtra por fecha check-in/check-out
   - Excluye habitaciones en mantenimiento
   - Retorna disponibilidad por tipo

2. **Creación de Reserva:**
   - Usuario crea Order
   - Sistema genera payment_token único
   - Se calcula down_payment (30%)
   - Status = 'pendiente'

3. **Pago Anticipado:**
   - Usuario accede a `/orders/payment/{token}`
   - Procesa pago (integración PSP)
   - Si éxito: `is_paid = true`, `status = 'anticipo_pagado'`

4. **Check-in (Recepción):**
   - Crea Stay record (estancia)
   - Vincula Guest
   - Status Order = 'ocupada'

#### Consideraciones Técnicas

- **Room-Number Logic:** Se almacena en `Order.room_number` y también en `Stay.notes` como `[ROOM_NUM:123]`
- **Fallback en Precio:** Si Room no tiene precio, intenta obtener del RoomType
- **Token Pago:** Generado via `Str::random(40)` en boot() del modelo
- **TotalAmount:** Calculado como `room.price * stayDays` (lazy attribute)

---

### 7.2 MÓDULO: RECEPCIÓN

#### Propósito
Gestión del ciclo completo de huésped: check-in, check-out, asignación de habitaciones, folios y cargos.

#### Componentes Principales

**Controladores:**
```
app/Http/Controllers/Reception/
├── DashboardController      # Dashboard recepción
├── CheckInController        # Ingreso huéspedes
├── CheckOutController       # Salida huéspedes
├── FolioController          # Folios (resumen cargos)
├── AssignmentController     # Asignación habitaciones
├── StayController           # Gestión de estancias
├── AdvanceController        # Anticipos de pago
├── WalkInController         # Huéspedes sin reserva
```

**Modelos:**
```
Stay (Estancia = una noche de un huésped)
├── Relaciones
│   ├── guest (belongsTo)
│   ├── room (belongsTo)
│   ├── order (belongsTo)      # Reserva padre
│   ├── user (belongsTo)
│   ├── folios (hasMany)
│
Guest (Información del huésped)
├── Relaciones
│   └── stays (hasMany)

Folio (Resumen de cargos por estancia)
├── Relaciones
│   ├── stay (belongsTo)
│   ├── charges (hasMany)
│   └── payments (hasMany)

Charge (Cargo individual)
├── Relaciones
│   └── folio (belongsTo)

Payment (Pago realizado)
├── Relaciones
│   └── folio (belongsTo)
```

**Rutas Web:**
```
# Check-in
GET        /reception/dashboard                 # Dashboard
POST       /reception/check-in/search           # Buscar reservas
GET        /reception/check-in/{id}             # Formulario check-in
POST       /reception/check-in/{id}             # Procesar check-in

# Check-out
GET        /reception/check-out/search          # Buscar huéspedes
POST       /reception/check-out/{stay_id}       # Procesar check-out

# Folios (estados de cuenta)
GET        /reception/folios/{stay_id}          # Ver folio
POST       /reception/folios/{folio_id}/charges # Agregar cargo
POST       /reception/folios/{folio_id}/payments # Registrar pago

# Asignación de habitaciones
GET        /reception/asignacion                # Tablero de asignación
POST       /reception/asignacion/assign         # Asignar habitación
```

**Estados de Stay:**
```
'pendiente'        # Reserva creada, sin check-in
'activa'           # Check-in realizado
'finalizada'       # Check-out realizado
'cancelada'        # Cancelada
```

**Atributos Clave:**
```php
Stay {
    id,
    reservation_id,             # Vinculada a Order
    room_id,
    guest_id,
    user_id,                    # Empleado que registró
    status,
    arrival_at (datetime),      # Fecha entrada reservada
    departure_at (datetime),    # Fecha salida reservada
    actual_check_in_at,         # Fecha/hora entrada real
    actual_check_out_at,        # Fecha/hora salida real
    adults,                     # Número de adultos
    children,                   # Número de niños
    rate_plan,                  # Plan de tarifa
    daily_rate (decimal),       # Tarifa diaria
    notes (text),               # Incluye [ROOM_NUM:123]
    created_at, updated_at
}

Guest {
    id,
    first_name,
    last_name,
    document_type,              # CC, CE, PA, NIT, TI
    document_number,            # Cédula única
    email,
    phone,
    country,
    notes,
    created_at, updated_at
}

Folio {
    id,
    stay_id,
    number (string, unique),    # Folio-001-2026
    status,                     # open, settled, pending_payment
    currency,                   # COP
    balance (decimal),          # Saldo pendiente
    created_at, updated_at
}
```

**Lógica Clave:**

1. **Check-in (Ingreso de Huésped):**
   - Busca Order sin Stay asociado con fecha check-in ≤ hoy
   - Usuario selecciona:
     - Número físico de habitación
     - Información del huésped (Guest)
   - Crea Guest record
   - Crea Stay asociado
   - Registra `[ROOM_NUM:X]` en notes
   - EventListener: `StayStarted` event

2. **Asignación de Habitaciones:**
   - Tablero interactivo (Vue.js)
   - Muestra habitaciones disponibles por tipo
   - Drag-drop para asignar
   - Valida no duplicar asignación
   - Actualiza notes con `[ROOM_NUM:X]`

3. **Gestión de Folios:**
   - Al check-in: se crea Folio automático
   - Cargos se agregan a través de API
   - Minibar cargos: referencia a Compra
   - Servicios adicionales: cargos manuales
   - Pagos reducen balance del Folio

4. **Check-out (Salida de Huésped):**
   - Busca Stays activos (sin check-out)
   - Verifica cargos pendientes
   - Calcula balance total
   - Si balance = 0: finaliza Stay
   - Si balance > 0: marca como pending_payment
   - Registra `actual_check_out_at`
   - Libera habitación (disponible nuevamente)

**Consideraciones Técnicas:**

- **Room-Number:** Se almacena en `Stay.notes` como tag `[ROOM_NUM:123]` para historial
- **Billing Breakdown:** Método `Stay::getBillingBreakdown()` calcula reserva base + cargos + pagos
- **Folio Balance:** `balance = suma_cargos - suma_pagos`
- **Multi-currency:** Aunque es COP, el sistema soporta currency field
- **User Tracking:** `Stay.user_id` es el empleado que registró (auditoría)

---

### 7.3 MÓDULO: MINIBAR

#### Propósito
Catálogo de bebidas (alcohólicas y no alcohólicas) para venta a huéspedes, con carrito de compras y checkout integrado al folio de la estancia.

#### Componentes Principales

**Controladores:**
```
Admin:
app/Http/Controllers/Admin/Minibar/
├── DashboardController           # Dashboard minibar
├── BebidaTypeController          # CRUD tipos (alcoholic/non)
├── MinibarProductController      # CRUD productos (bebidas)
├── CompraController              # Historial de ventas

User (Huésped):
app/Http/Controllers/Minibar/User/
├── CatalogController             # Catálogo (listado)
├── BebidaController              # Detalle producto
├── CartController                # Carrito
├── CheckoutController            # Checkout/compra

API:
app/Http/Controllers/Api/
└── MinibarProductController      # REST API productos
```

**Modelos:**
```
MinibarProduct (alias: Bebida)
├── Relaciones
│   ├── type (belongsTo BebidaType)
│   └── compras (belongsToMany through CompraProducto)

BebidaType (Clasificación)
├── Atributos
│   ├── name
│   ├── es_alcoholica (boolean)
│   └── description

Compra (Pedido de minibar)
├── Relaciones
│   ├── user (belongsTo)
│   ├── stay (belongsTo)
│   ├── productos (belongsToMany through CompraProducto)
│   └── posted_by (User)

CompraProducto (Pivot table)
└── Fields: minibar_product_id, compra_id, cantidad, precio_unitario
```

**Rutas Web (Usuario):**
```
GET        /minibar/catalogo              # Listado productos
GET        /minibar/bebida/{id}           # Detalle producto
POST       /api/carrito/agregar           # Agregar al carrito
GET        /api/carrito                   # Ver carrito
POST       /api/carrito/checkout          # Procesar compra
```

**Rutas Admin:**
```
# Dashboard
GET        /admin/minibar/dashboard

# Tipos de bebida
GET        /admin/minibar/bebida-types           # Listar
POST       /admin/minibar/bebida-types           # Crear
PUT        /admin/minibar/bebida-types/{id}      # Editar
DELETE     /admin/minibar/bebida-types/{id}      # Eliminar

# Bebidas (Productos)
GET        /admin/minibar/bebidas                # Listar todas
GET        /admin/minibar/bebidas/create         # Crear
POST       /admin/minibar/bebidas                # Guardar
GET        /admin/minibar/bebidas/{id}/edit      # Editar
PUT        /admin/minibar/bebidas/{id}           # Actualizar
DELETE     /admin/minibar/bebidas/{id}           # Eliminar

# Ventas (Compras)
GET        /admin/minibar/ventas                 # Historial
GET        /admin/minibar/ventas/{compra}        # Detalle compra
```

**Rutas API REST:**
```
# Públicas (lectura)
GET        /api/minibar-products           # Listar productos
GET        /api/minibar-products/{id}      # Detalle producto

# Protegidas (admin/minibar)
POST       /api/minibar-products           # Crear
PUT        /api/minibar-products/{id}      # Actualizar
DELETE     /api/minibar-products/{id}      # Eliminar

Middleware: ['auth:sanctum', 'role:administrador|minibar', 'abilities:minibar:write']
```

**Atributos Clave:**
```php
MinibarProduct {
    id,
    bebida_type_id,             # Tipo (alcohólica/no)
    nombre,                     # Nombre producto
    descripcion,
    precio (float),             # Precio unitario
    stock (integer),            # Cantidad disponible
    imagen,                     # URL/path imagen
    created_at, updated_at
}

BebidaType {
    id,
    nombre,                     # Ej: "Cerveza"
    es_alcoholica (bool),       # Controla acceso según edad
    descripcion,
    created_at, updated_at
}

Compra {
    id,
    user_id,                    # Usuario que compró
    stay_id (nullable),         # Estancia asociada
    posted_by (nullable),       # Empleado que registró
    posted_at (nullable),       # Fecha registro
    created_at, updated_at

    # Total calculado via relación productos
}

CompraProducto {
    minibar_product_id,
    compra_id,
    cantidad (integer),         # Cantidad comprada
    precio_unitario (float),    # Precio al momento
    created_at, updated_at
}
```

**Lógica Clave:**

1. **Catálogo para Huéspedes:**
   - Listado de productos disponibles (stock > 0)
   - Filtrado por tipo (alcohólicas/no alcohólicas)
   - Búsqueda por nombre/descripción
   - Validaciones de acceso según rol

2. **Carrito de Compras (Vue.js):**
   - LocalStorage o Vuex state
   - Agregar/eliminar productos
   - Ajustar cantidades
   - Cálculo subtotal + impuestos

3. **Checkout:**
   - Verifica stock cada producto
   - Crea Compra record
   - Crea CompraProducto entries
   - Reduce stock de MinibarProduct
   - Crea Charge en Folio del huésped
   - Retorna confirmación

4. **Gestión Administrativa:**
   - CRUD de tipos (alcohólicas/no)
   - CRUD de productos (incluido imagen)
   - Reporte de ventas por período
   - Control de stock

**Consideraciones Técnicas:**

- **Filtrado Alcohólico:** En frontend se puede validar edad para mostrar solo no-alcohólicas
- **Stock:** Se decrementa en checkout, validación antes de procesar
- **Precio Histórico:** Se guarda en CompraProducto.precio_unitario (no vinculado a cambios posteriores)
- **Integración Folio:** Compra agrega Charge automático cuando se checkout
- **Permisos:** Middleware valida rol 'minibar' o 'administrador' + ability 'minibar:write'

---

### 7.4 MÓDULO: MANTENIMIENTO

#### Propósito
Sistema de órdenes de trabajo para mantenimiento preventivo y correctivo de habitaciones y áreas comunes.

#### Componentes Principales

**Controladores:**
```
app/Http/Controllers/Admin/Mantenimiento/
└── MaintenanceController        # CRUD órdenes mantenimiento
```

**Modelos:**
```
MaintenanceOrder
├── Relaciones
│   └── room (belongsTo)
│
Room
└── Relaciones
    ├── maintenance_orders (hasMany)
    └── tipos_habitacion...
```

**Rutas Admin:**
```
GET        /admin/mantenimiento/dashboard          # Dashboard
GET        /admin/mantenimiento/ordenes            # Listar
GET        /admin/mantenimiento/ordenes/create     # Crear
POST       /admin/mantenimiento/ordenes            # Guardar
GET        /admin/mantenimiento/ordenes/{id}/edit  # Editar
PUT        /admin/mantenimiento/ordenes/{id}       # Actualizar
DELETE     /admin/mantenimiento/ordenes/{id}       # Eliminar
```

**Atributos Clave:**
```php
MaintenanceOrder {
    id,
    room_id (nullable),         # Habitación afectada
    room_number (integer),      # Número físico habitación
    priority,                   # 'urgente', 'normal', 'baja'
    status,                     # 'asignada', 'en_proceso', 'completada', 'cancelada'
    description,                # Qué necesita mantenimiento
    notes (text),               # Notas adicionales
    estimated_time,             # Tiempo estimado en horas
    started_at (datetime),      # Cuándo inició trabajo
    completed_at (datetime),    # Cuándo se completó
    created_at, updated_at
}
```

**Estados y Prioridades:**
```
Status: asignada | en_proceso | completada | cancelada
Priority: urgente | normal | baja
```

**Lógica Clave:**

1. **Creación de Orden:**
   - Usuario admin crea orden
   - Vincula a sala/número de habitación
   - Asigna prioridad y descripción
   - Status = 'asignada'

2. **Tracking de Trabajo:**
   - started_at: marca inicio
   - en_proceso: status actualizado
   - completed_at: fecha finalización
   - Cálculo de tiempo de resolución

3. **Reportes:**
   - Órdenes no completadas por prioridad
   - Tiempo promedio resolución
   - Órdenes por habitación

**Consideraciones Técnicas:**

- **Room Binding:** `room_id` vincula a modelo Room, pero `room_number` se almacena redundantemente
- **Duración:** Calculada como `datediff(completed_at, created_at)` en query
- **Scopes:** Métodos helper para queries comunes (`active()`, `urgent()`)

---

### 7.5 MÓDULO: AUDITORÍA

#### Propósito
Registro centralizado de acciones del sistema para compliance, seguridad y análisis de actividad usuario.

#### Componentes Principales

**Controladores:**
```
app/Http/Controllers/Admin/
└── AuditoriaController          # Visualización auditorías
```

**Modelos:**
```
Auditoria
└── Relaciones
    └── usuario (belongsTo User)
```

**Rutas Admin:**
```
GET        /admin/auditorias                # Listar registros
GET        /admin/auditorias/filtrar        # Filtrar por módulo/fecha
```

**Atributos Clave:**
```php
Auditoria {
    id,
    usuario_id,                 # Quién realizó acción
    accion,                     # 'ACCESS', 'CREATE', 'UPDATE', 'DELETE'
    modulo,                     # 'recepcion', 'minibar', 'reservas', 'usuarios'
    registro_id (nullable),     # ID del registro afectado
    descripcion,                # Detalles de qué pasó
    created_at (datetime),      # Timestamp sin update
    // No tiene updated_at: registro es inmutable
}
```

**Service: AuditoriaService**

```php
class AuditoriaService {
    /**
     * Registrar una acción en auditoría
     * @param string $accion      ACCESS, CREATE, UPDATE, DELETE, etc
     * @param string $modulo      recepcion, minibar, reservas, etc
     * @param ?int $registro_id   ID del registro afectado
     * @param string $descripcion Detalle de la acción
     * @param ?int $usuario_id    ID del usuario (por defecto Auth::id())
     * @param array $opciones     ['skip_duplicate' => true] para evitar duplicados
     */
    public function registrar(
        string $accion,
        string $modulo,
        ?int $registro_id,
        string $descripcion,
        ?int $usuario_id = null,
        array $opciones = []
    ): void {
        // Lógica de registración
    }
}
```

**Lógica de Auditoría:**

1. **Middleware AuditAdminAccess:**
   - Registra acceso a rutas `/admin/*`
   - Determina módulo según ruta
   - Crea registro de acceso

2. **Helper Function:**
   ```php
   // Uso desde cualquier parte
   registrarAuditoria(
       'CREATE',
       'reservas',
       $order->id,
       'Nueva reserva creada para ' . $guest_name,
       Auth::id(),
       ['skip_duplicate' => true]
   );
   ```

3. **Vistas/Reportes:**
   - Listado cronológico de acciones
   - Filtrado por usuario, módulo, fecha
   - Búsqueda por descripción
   - Export a CSV/PDF

**Consideraciones Técnicas:**

- **Inmutabilidad:** No tiene `updated_at`, registros nunca se modifican
- **Índices DB:** Índices en `usuario_id`, `modulo`, `created_at` para queries eficientes
- **No Soft Deletes:** Los registros de auditoría nunca se eliminen
- **Skip Duplicate:** Opción para evitar múltiples registros de mismo evento en corto tiempo

---

### 7.6 MÓDULO: REPORTES

#### Propósito
Generación de reportes ejecutivos en PDF con métricas de operación, financieras y de huéspedes.

#### Componentes Principales

**Controladores:**
```
app/Http/Controllers/Admin/
└── ReportController             # Generación reportes
```

**Rutas Admin:**
```
GET        /admin/reportes/ejecutivo       # Reporte ejecutivo general
POST       /admin/reportes/descargar       # Download PDF
GET        /admin/reportes/ocupacion       # Por período
GET        /admin/reportes/ventas          # Ventas minibar
GET        /admin/reportes/financiero      # Estado financiero
```

**Reportes Disponibles:**

1. **Reporte Ejecutivo:**
   - Ocupación de habitaciones
   - Reservas activas/pendientes
   - Huéspedes actuales
   - Órdenes mantenimiento
   - Ventas minibar
   - Recaudos del día

2. **Ocupación:**
   - Gráfico por tipo de habitación
   - Tendencias últimos 30 días
   - Proyecciones

3. **Financiero:**
   - Ingresos por reservas
   - Pagos recibidos
   - Cuentas por cobrar
   - Ventas minibar

4. **Huéspedes:**
   - Registros últimos arrivals/departures
   - Tendencias nacionalidad
   - Servicio prestado

**Generación PDF:**

```php
// En ReportController
$pdf = Pdf::loadView('reports.ejecutivo', compact('data'))
    ->setPaper('a4', 'portrait');

return $pdf->download('reporte-ejecutivo-' . date('Y-m-d') . '.pdf');
```

**Data Structure:**

```php
$reportData = [
    'totalHabitaciones' => 50,
    'habitacionesEnMant' => 2,
    'huespedesEnCasa' => 35,
    'habitacionesDisponibles' => 13,
    'pctOcupacion' => 70.0,
    'distribucionPorTipo' => [...],
    'totalReservas' => 120,
    'reservasActivas' => 45,
    'reservasPendientes' => 30,
    'checkinHoy' => 5,
    'checkoutHoy' => 8,
    // ... más métricas
];
```

**Consideraciones Técnicas:**

- **Performance:** Usa raw SQL queries y agregaciones eficientes
- **Datos Complejos:** Constructor privado `buildReportData()` centraliza lógica
- **Cacheo:** Reportes se pueden cachear X minutos
- **Permisos:** Solo administradores acceden

---

## 8. RUTAS Y API REST

### 8.1 Estructura de Rutas

El proyecto utiliza dos archivo principales:
- **`routes/web.php`**: Rutas web con sesión (Inertia)
- **`routes/api.php`**: Rutas API REST con autenticación Sanctum

#### Sistema de Prefijos y Grupos

```php
// Rutas públicas (sin auth)
Route::get('/', ...).name('home');
Route::post('/search', ...);

// Rutas protegidas por sesión
Route::middleware('auth')->group(...);

// Admin (sesión)
Route::prefix('admin')
    ->middleware(['auth', 'role:administrador,web', 'audit.admin'])
    ->group(...);

// API: Public
Route::get('/api/health', ...);

// API: Auth
Route::prefix('auth')->name('api.auth.')->group(...);

// API: Protected
Route::middleware('auth:sanctum')
    ->middleware(['role:administrador|minibar,sanctum', 'abilities:minibar:write'])
    ->group(...);
```

### 8.2 Endpoints Públicos

**Health & Status:**
```
GET     /api/health
        Response: { ok: true, app: string, time: ISO8601 }

GET     /api/ping-db          (solo local/testing)
        Response: { db: "ok" }
```

**Búsqueda de Habitaciones:**
```
GET     /rooms                # Listar disponibles
POST    /rooms                # Buscar por criterios
GET     /search               # Alias búsqueda genérica
POST    /search
```

### 8.3 Endpoints de Autenticación

**Registro:**
```
POST    /auth/register
Body:   {
          name, last_name, email, phone, password,
          password_confirmation
        }
Response: { token?, user?, message }
Throttle: throttle:auth-register
```

**Login:**
```
POST    /auth/login
Body:   { email, password, remember }
Response: { token?, user?, message }
Returns:  
  - Web: session + redirect
  - API: { token: bearer_token, user: {...} }
Throttle: throttle:auth-login
```

**Logout:**
```
POST    /auth/logout
Headers: Authorization: Bearer {token}
Response: { message: "success" }
```

**User Info:**
```
GET     /auth/me   (API)
GET     /api/auth/me
GET     /api/auth/user
Headers: Authorization: Bearer {token}
Response: { id, name, email, roles, permissions }
```

### 8.4 Endpoints de Órdenes (Reservas)

**Listar Mis Órdenes:**
```
GET     /orders
Headers: Authorization: Bearer {token}
Response: [{ id, cliente, check_in, check_out, status, total }]
```

**Crear Orden:**
```
POST    /orders
Headers: Authorization: Bearer {token}
Body:   {
          nombre_cliente,
          check_in,     # YYYY-MM-DD HH:mm
          check_out,    # YYYY-MM-DD HH:mm
          room_id,
          room_type_id
        }
Response: { id, payment_token, down_payment, status }
```

**Pago de Anticipó:**
```
GET     /orders/payment/{token}
        Página de pago (renderiza formulario)

POST    /orders/payment/{token}/confirm
Body:   {
          card_number, exp_month, exp_year, cvv,
          cardholder_name
        }
Response: { success, message, order_id }
```

### 8.5 Endpoints Minibar

**Productos (Público):**
```
GET     /api/minibar-products
Query:  ?tipo=bebida&alcoholica=0&page=1
Response: {
  data: [{ id, nombre, precio, imagen, en_stock }],
  pagination: { total, per_page, current_page }
}
```

**Detalle Producto:**
```
GET     /api/minibar-products/{id}
Response: { id, nombre, descripcion, precio, imagen, stock }
```

**Crear/Actualizar Producto (Admin):**
```
POST    /api/minibar-products
PUT     /api/minibar-products/{id}
Headers: Authorization: Bearer {token}
         Role: administrador|minibar
Abilities: minibar:write
Body:   {
          bebida_type_id,
          nombre,
          descripcion,
          precio,
          stock,
          imagen
        }
Response: { id, ... }
```

**Eliminar Producto:**
```
DELETE  /api/minibar-products/{id}
Headers: Authorization: Bearer {token}
         Role: administrador|minibar
Abilities: minibar:write
Response: { message: "deleted" }
```

### 8.6 Endpoints Administrativos

**Dashboard Admin:**
```
GET     /admin                (web)
GET     /api/admin            (API)
Headers: Authorization: Bearer {token}
         Role: administrador
Response: { 
  habitaciones, reservas, huespedes, mantenimiento,
  ventas_minibar, recaudos
}
```

**Empleados (CRUD):**
```
GET     /admin/empleados      # Listar
POST    /admin/empleados      # Crear
PUT     /admin/empleados/{id} # Actualizar
DELETE  /admin/empleados/{id} # Eliminar

Assign Role:
POST    /admin/empleados/{user_id}/roles
Body:   { roles: ['recepcion', 'minibar'] }
```

**Roles:**
```
POST    /admin/roles
Body:   { name: 'nuevo_rol' }
Response: { id, name }
```

**Habitaciones (Admin):**
```
GET     /admin/habitaciones/habitaciones
POST    /admin/habitaciones/habitaciones
PUT     /admin/habitaciones/habitaciones/{id}
DELETE  /admin/habitaciones/habitaciones/{id}
```

**Tipos de Habitación:**
```
GET     /admin/habitaciones/tipos-habitacion
POST    /admin/habitaciones/tipos-habitacion
PUT     /admin/habitaciones/tipos-habitacion/{id}
DELETE  /admin/habitaciones/tipos-habitacion/{id}
```

### 8.7 Endpoints Recepción

**Check-in Search:**
```
POST    /reception/check-in/search
Body:   { fecha: "YYYY-MM-DD" }
Response: {
  success: boolean,
  reservations: [{
    id, codigo, guest_name, room_type, check_in, check_out, total
  }]
}
```

**Check-in Store:**
```
POST    /reception/check-in/{reservation_id}
Body:   {
          room_number,
          first_name, last_name,
          document_type,    # CC, CE, PA, NIT, TI
          document_number,
          email,
          phone,
          country,
          notes
        }
Response: { success, stay_id, message }
```

**Check-out:**
```
POST    /reception/check-out/{stay_id}
Body:   { settled: boolean }
Response: { success, folio_id, balance }
```

**Folios (Cargos):**
```
GET     /reception/folios/{folio_id}
Response: {
  id, stay_id, number, status, balance,
  charges: [{ id, source, description, amount, tax }],
  payments: [{ id, method, amount, received_at }]
}

POST    /reception/folios/{folio_id}/charges
Body:   {
          source,         # "Minibar", "Servicio Extra", etc
          description,
          amount,
          tax:0
        }
Response: { charge_id, new_balance }

POST    /reception/folios/{folio_id}/payments
Body:   {
          method,         # "efectivo", "tarjeta"
          amount,
          description
        }
Response: { payment_id, new_balance }
```

### 8.8 Códigos de Respuesta HTTP

| Código | Significado | Uso |
|--------|------------|-----|
| 200 | OK | Request exitoso |
| 201 | Created | Recurso creado |
| 204 | No Content | Exitoso sin body |
| 400 | Bad Request | Datos inválidos |
| 401 | Unauthorized | No autenticado |
| 403 | Forbidden | Sin permisos |
| 404 | Not Found | Recurso no existe |
| 422 | Unprocessable | Validación falló |
| 429 | Too Many Requests | Rate limit |
| 500 | Server Error | Error interno |

---

## 9. MODELO DE DATOS

### 9.1 Diagrama Entidad-Relación (Texto)

```
USUARIOS
├─ User (Empleados + Huéspedes)
│  └─ Relaciones: Orders, Stays, Compras, Auditorias
│
RESERVAS Y ESTANCIAS
├─ Order (Reserva de habitación)
│  ├─ BelongsTo: User, Room
│  └─ HasMany: Stays
│
├─ Stay (Estancia de un huésped en habitación)
│  ├─ BelongsTo: Order, Guest, Room, User
│  └─ HasMany: Folios
│
├─ Guest (Información de huésped)
│  └─ HasMany: Stays
│
HABITACIONES
├─ Room (Habitación física)
│  ├─ BelongsTo: RoomType
│  └─ HasMany: MaintenanceOrders
│
├─ RoomType (Tipo/categoría: Doble, Simple, Suite)
│  └─ HasMany: Rooms
│
FOLIOS Y CARGOS
├─ Folio (Estado de cuenta de una estancia)
│  ├─ BelongsTo: Stay
│  └─ HasMany: Charges, Payments
│
├─ Charge (Cargo individual: minibar, servicio)
│  └─ BelongsTo: Folio
│
├─ Payment (Pago realizado)
│  └─ BelongsTo: Folio
│
MINIBAR
├─ MinibarProduct/Bebida (Producto inventario)
│  ├─ BelongsTo: BebidaType
│  └─ BelongsToMany: Compra (via CompraProducto)
│
├─ BebidaType (Clasificación: Cerveza, Vino, etc)
│  └─ HasMany: MinibarProduct
│
├─ Compra (Un pedido de minibar)
│  ├─ BelongsTo: User, Stay
│  └─ BelongsToMany: MinibarProduct (via CompraProducto)
│
├─ CompraProducto (Pivot: Compra ↔ Productos)
│  └─ Atributos: cantidad, precio_unitario
│
MANTENIMIENTO
├─ MaintenanceOrder (Orden de trabajo)
│  └─ BelongsTo: Room
│
AUDITORÍA
├─ Auditoria (Registro de acciones)
│  └─ BelongsTo: User (usuario_id)

AUTENTICACIÓN
├─ personal_access_tokens (Sanctum)
│  └─ Vinculado a: User
│
├─ roles (Spatie Permissions)
│  └─ Vinculado a: Users (via role_has_permissions)
│
├─ permissions (Spatie Permissions)
│  └─ Vinculado a: Roles
```

### 9.2 Relaciones Detalladas

#### User ↔ Order (1:N)
- Un usuario (cliente) puede tener múltiples órdenes
- `User.hasMany('orders')` → `Order.belongsTo('user')`

#### User ↔ Stay (1:N)
- Un usuario (empleado receptivo) puede registrar múltiples estancias
- `User.hasMany('stays')` → `Stay.belongsTo('user')`

#### Order ↔ Stay (1:N)
- Una orden genera una o más estancias (si se divide hospedaje)
- `Order.hasMany('stays')` → `Stay.belongsTo('order', 'reservation_id')`

#### Stay ↔ Guest (N:1)
- Una estancia es de un huésped
- `Stay.belongsTo('guest')` ← `Guest.hasMany('stays')`

#### Stay ↔ Room (N:1)
- Una estancia ocupa una habitación
- `Stay.belongsTo('room')` ← `Room.hasMany('stays')`

#### Room ↔ RoomType (N:1)
- Una habitación es de un tipo específico
- `Room.belongsTo('room_type')` ← `RoomType.hasMany('rooms')`

#### Stay ↔ Folio (1:N)
- Una estancia puede tener múltiples folios (por ciclo de carga)
- `Stay.hasMany('folios')` ← `Folio.belongsTo('stay')`

#### Folio ↔ Charge (1:N)
- Un folio tiene múltiples cargos
- `Folio.hasMany('charges')` ← `Charge.belongsTo('folio')`

#### Folio ↔ Payment (1:N)
- Un folio registra múltiples pagos
- `Folio.hasMany('payments')` ← `Payment.belongsTo('folio')`

#### MinibarProduct ↔ Compra (N:M)
- Un producto se vende en múltiples compras
- `MinibarProduct.belongsToMany('compras')` ↔ `Compra.belongsToMany('productos')`
- Tabla pivot: `compra_producto` (cantidad, precio_unitario)

#### Compra ↔ User (N:1)
- Una compra es hecha por un usuario (huésped)
- `Compra.belongsTo('user')` ← `User.hasMany('compras')`

#### Compra ↔ Stay (N:1)
- Una compra ocurre durante una estancia
- `Compra.belongsTo('stay')` ← `Stay.hasMany('compras')`

#### MaintenanceOrder ↔ Room (N:1)
- Una orden de mantenimiento afecta una habitación
- `MaintenanceOrder.belongsTo('room')` ← `Room.hasMany('maintenance_orders')`

#### Auditoria ↔ User (N:1)
- Un registro de auditoría es de un usuario
- `Auditoria.belongsTo('usuario')` ← `User.hasMany('auditorias')`

### 9.3 Tablas Principales y Estructura

#### Tabla: `users`
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    last_name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    phone VARCHAR(20),
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255),
    remember_token VARCHAR(100),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    KEY idx_email (email),
    KEY idx_created_at (created_at)
);
```

#### Tabla: `orders` (Reservas)
```sql
CREATE TABLE orders (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nombre_cliente VARCHAR(255),
    check_in TIMESTAMP,
    check_out TIMESTAMP,
    room_id BIGINT UNSIGNED NOT NULL,
    room_number INT,
    room_type_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED NOT NULL,
    status VARCHAR(50) DEFAULT 'pendiente',
    payment_token VARCHAR(40) UNIQUE,
    down_payment_amount DECIMAL(12, 2),
    is_paid BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    KEY idx_status (status),
    KEY idx_user_id (user_id),
    KEY idx_check_in (check_in)
);
```

#### Tabla: `stays` (Estancias)
```sql
CREATE TABLE stays (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    reservation_id BIGINT UNSIGNED,
    room_id BIGINT UNSIGNED,
    guest_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED,
    status VARCHAR(50) INDEX,
    arrival_at TIMESTAMP,
    departure_at TIMESTAMP,
    actual_check_in_at TIMESTAMP NULL,
    actual_check_out_at TIMESTAMP NULL,
    adults INT UNSIGNED DEFAULT 1,
    children INT UNSIGNED DEFAULT 0,
    rate_plan VARCHAR(100),
    daily_rate DECIMAL(10, 2) DEFAULT 0,
    notes LONGTEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (guest_id) REFERENCES guests(id),
    FOREIGN KEY (reservation_id) REFERENCES orders(id),
    FOREIGN KEY (room_id) REFERENCES rooms(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    KEY idx_status (status),
    KEY idx_actual_check_in (actual_check_in_at)
);
```

#### Tabla: `rooms` (Habitaciones)
```sql
CREATE TABLE rooms (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    room_number INT NOT NULL,
    room_type_id BIGINT UNSIGNED,
    total_room INT,      # Capacidad
    price DECIMAL(10, 2),
    status VARCHAR(50),   # 'activa', 'mantenimiento'
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (room_type_id) REFERENCES room_types(id),
    KEY idx_status (status)
);
```

#### Tabla: `folios`
```sql
CREATE TABLE folios (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    stay_id BIGINT UNSIGNED,
    number VARCHAR(50) UNIQUE,  # Folio-001-2026
    status VARCHAR(50),          # open, settled, pending_payment
    currency VARCHAR(3) DEFAULT 'COP',
    balance DECIMAL(12, 2) DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (stay_id) REFERENCES stays(id)
);
```

#### Tabla: `charges` (Cargos)
```sql
CREATE TABLE charges (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    folio_id BIGINT UNSIGNED,
    source VARCHAR(50),       # "Minibar", "Servicio Extra"
    description TEXT,
    amount DECIMAL(12, 2),
    tax DECIMAL(12, 2) DEFAULT 0,
    posted_by BIGINT UNSIGNED,
    posted_at TIMESTAMP NULL,
    reference_type VARCHAR(50),   # Para vinculación
    reference_id BIGINT UNSIGNED, # ID del recurso (Compra)
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (folio_id) REFERENCES folios(id),
    KEY idx_folio_posted (folio_id, posted_at)
);
```

#### Tabla: `payments` (Pagos)
```sql
CREATE TABLE payments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    folio_id BIGINT UNSIGNED,
    method VARCHAR(50),         # 'efectivo', 'tarjeta'
    amount DECIMAL(12, 2),
    currency VARCHAR(3) DEFAULT 'COP',
    received_by BIGINT UNSIGNED,
    received_at TIMESTAMP NULL,
    external_ref VARCHAR(255),   # Referencia pago
    description VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (folio_id) REFERENCES folios(id),
    KEY idx_folio_received (folio_id, received_at)
);
```

### 9.4 Convenciones de Almacenamiento

**Room Number Tracking:**
- Se almacena redundantemente en:
  - `Order.room_number` (asignadado en reserva)
  - `Stay.notes` como tag `[ROOM_NUM:123]` (para historial)
  - `MaintenanceOrder.room_number` (para órdenes de trabajo)

**Precios y Dinero:**
- Siempre DECIMAL(10,2) o DECIMAL(12,2)
- En COP, sin conversión (asumir COP)
- Nunca float para dinero

**Timestamps:**
- `created_at`: cuándo se creó el registro
- `updated_at`: última modificación
- Campos específicos: `actual_check_in_at`, `completed_at`, etc.

**Status/Estado Fields:**
- Siempre VARCHAR(50) para permitir nuevos valores
- INDEX en campos status frecuentemente filtrados

---

## 10. SEGURIDAD Y CONTROL DE ACCESO

### 10.1 Jerarquía de Defensa

```
┌─────────────────────────────────────────────────┐
│ 1. HTTPS / TLS (Transporte)                     │
│    APP_FORCE_HTTPS=true en producción           │
│                                                 │
│ 2. CSRF Token (Form Submission)                 │
│    Middleware: VerifyCsrfToken                  │
│    X-CSRF-TOKEN header o token en formulario    │
│                                                 │
│ 3. Autenticación (Identity)                     │
│    - Session (web)                              │
│    - Bearer Token (API)                         │
│                                                 │
│ 4. Autorización (Permiso)                       │
│    - Role-based (Spatie): role:admin|recepcion │
│    - Ability-based: abilities:minibar:write     │
│    - Policy-based: Gate autorización            │
│                                                 │
│ 5. Rate Limiting (DoS)                          │
│    throttle:auth-login (límite intentos)        │
│                                                 │
│ 6. Input Validation (Integridad)                │
│    - Request validation                         │
│    - Custom rules                               │
│    - Sanitización                               │
│                                                 │
│ 7. Auditoría (Compliance)                       │
│    - Middleware AuditAdminAccess                │
│    - Logs en tabla auditorias                   │
│                                                 │
└─────────────────────────────────────────────────┘
```

### 10.2 Middleware de Seguridad

#### VerifyCsrfToken
```php
// Protege contra ataques CSRF
// Valida X-CSRF-TOKEN en forms POST/PUT/DELETE
// Automático en Blade: {{ csrf_field() }}
// En Inertia/API: X-CSRF-TOKEN header
```

#### Authenticate (Autenticación)
```php
// Valida que usuario esté logueado
Route::middleware('auth')->group(...);
Route::middleware('auth:sanctum')->group(...);
```

#### IsAdmin (Verificación de Rol)
```php
// Verifica Auth::user()->is_admin
// Obsoleto: preferir Spatie role:administrador
```

#### AuditAdminAccess (Auditoría)
```php
// Registra acceso a rutas /admin
// Crea Auditoria record
// Detecta módulo según ruta
Route::middleware('audit.admin')->group(...);
```

#### Role & Ability (Spatie)
```php
// Role-based
Route::middleware('role:administrador,web')->group(...);
Route::middleware('role:administrador|minibar,sanctum')->group(...);

// Ability-based
Route::middleware('abilities:minibar:write')->group(...);
```

### 10.3 Roles y Permisos

**Roles Definidos:**

| Rol | Descripción | Módulos Acceso |
|-----|------------|--------|
| `administrador` | Acceso total | Todos |
| `recepcion` | Gestión huéspedes | Check-in, Check-out, Folios |
| `minibar` | Inventario bebidas | Catálogo, Ventas, Reportes |
| `reservas` | Gestión reservas | Orders, Habitaciones |
| `mantenimiento` | Órdenes de trabajo | MantenanceOrders |

**Asignación de Roles:**

```php
// En seeder o admin panel
$user = User::find(1);
$user->assignRole('recepcion');

// Múltiples roles
$user->syncRoles(['recepcion', 'minibar']);
```

**Verificación en Controller:**

```php
// En método del controller
if (Auth::user()->hasRole('administrador')) {
    // ...
}

// O autorización de modelo
$this->authorize('create', Stay::class);  // Usa Policy
```

### 10.4 Policies (Autorización de Modelo)

Ubicación: `app/Policies/`

**Ejemplo: StayPolicy**

```php
class StayPolicy {
    /**
     * Solo receptistas pueden crear stays
     */
    public function create(User $user): bool {
        return $user->hasRole(['recepcion', 'administrador']);
    }

    /**
     * Solo quien creó puede editar
     */
    public function update(User $user, Stay $stay): bool {
        return $user->id === $stay->user_id || $user->hasRole('administrador');
    }
}
```

**Registro en AuthServiceProvider:**

```php
protected $policies = [
    Stay::class => StayPolicy::class,
    Folio::class => FolioPolicy::class,
    // ...
];
```

**Uso en Controller:**

```php
$this->authorize('create', Stay::class);
$this->authorize('update', $stay);
```

### 10.5 Seguridad en Rutas API

**Middleware Stack:**

```php
// API que crea/actualiza minibar products
Route::apiResource('minibar-products', MinibarProductController::class)
    ->only(['store', 'update', 'destroy'])
    ->middleware([
        'auth:sanctum',                    // Debe tener token válido
        'role:administrador|minibar,sanctum',  // Rol apropiado
        'abilities:minibar:write'          // Ability específica
    ]);
```

### 10.6 Validaciones de Seguridad

**AllowedEmailDomain (Custom Rule):**

```php
// En FormRequest Check-in
new AllowedEmailDomain()  // Valida dominio email permitido
```

**PhoneNumberByPrefix (Custom Rule):**

```php
// Valida formato teléfono por código país
new PhoneNumberByPrefix()
```

**Sanitización en Modelo:**

```php
// En Model, si necesario
protected $fillable = ['email', 'phone', 'nome'];
// mutate/cast valores sensibles
```

### 10.7 Protección de Rutas

**Rutas Públicas (Sin auth):**

```php
Route::get('/', [PageController::class, 'index']);      // Home
Route::get('/rooms', [PageController::class, 'list_rooms']); // Buscar
```

**Rutas Protegidas (Auth):**

```php
Route::middleware('auth')->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
});
```

**Rutas Admin (Auth + Role):**

```php
Route::prefix('admin')
    ->middleware([
        'auth',
        'role:administrador,web',
        'audit.admin'
    ])
    ->group(function () {
        Route::resource('empleados', EmployeeController::class);
    });
```

**Rutas API (Sanctum + Role):**

```php
Route::middleware(['auth:sanctum', 'role:administrador|minibar,sanctum'])
    ->group(function () {
        Route::apiResource('minibar-products', MinibarProductController::class);
    });
```

### 10.8 Incongruencia de Roles (Issues Detectado)

**⚠️ Identificado en Security Audit:**

- Algunas rutas usan `role:administrador` (lowercase)
- Otras usan `role:admin` en FormRequests
- **Recomendación:** Estandarizar a `administrador` (lowercase)

**Verificación Spatie:**

```php
// Correcto
$user->hasRole('administrador')  // lowercase match DB

// Evitar
$user->hasRole('admin')          // No matchea en DB
```

---

## 11. VALIDACIONES Y MANEJO DE ERRORES

### 11.1 Sistema de Validación

#### Form Requests (app/Http/Requests/)

```php
// Centraliza validación en clase separada
class StoreCheckInRequest extends FormRequest {
    public function authorize(): bool {
        return $this->user()->hasRole('recepcion');
    }

    public function rules(): array {
        return [
            'room_number' => 'required|integer|min:1',
            'first_name' => 'required|string|max:100',
            'document_number' => 'required|string|unique:guests,document_number',
            'email' => ['required', 'email', new AllowedEmailDomain()],
            'phone' => ['required', new PhoneNumberByPrefix()],
        ];
    }

    public function messages(): array {
        return [
            'room_number.required' => 'Debe seleccionar una habitación',
            'document_number.unique' => 'Este documento ya existe',
        ];
    }
}
```

#### Validación en Controller

```php
// En CheckInController::store()
public function store(Request $request, $reservationId) {
    // Automáticamente valida via FormRequest
    $data = $request->validate([
        'room_number' => 'required|integer|min:1',
        'first_name' => 'required|string|max:100',
        // ...
    ]);
    // Si falla, redirecta atrás con errores
}
```

#### Custom Validation Rules

**AllowedEmailDomain:**

```php
namespace App\Rules;

class AllowedEmailDomain implements Rule {
    public function passes($attribute, $value): bool {
        // Valida que email sea de dominio permitido
        $allowedDomains = ['gmail.com', 'empresa.com'];
        $domain = substr(strrchr($value, "@"), 1);
        return in_array($domain, $allowedDomains);
    }

    public function message(): string {
        return 'El dominio del email no está permitido.';
    }
}
```

**PhoneNumberByPrefix:**

```php
class PhoneNumberByPrefix implements Rule {
    public function passes($attribute, $value): bool {
        // Valida formato teléfono según código país
        if (preg_match('/^\+?[1-9]\d{1,14}$/', $value)) {
            return true;  // Formato E.164 válido
        }
        return false;
    }

    public function message(): string {
        return 'El formato de teléfono no es válido.';
    }
}
```

### 11.2 Manejo de Excepciones

#### Exception Handler (app/Exceptions/Handler.php)

```php
public function render($request, Throwable $exception) {
    // Convierte excepciones a respuestas HTTP

    // AuthenticationException → 401
    if ($exception instanceof AuthenticationException) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    // AuthorizationException → 403
    if ($exception instanceof AuthorizationException) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    // ValidationException → 422
    if ($exception instanceof ValidationException) {
        return response()->json([
            'message' => 'validation_failed',
            'errors' => $exception->errors()
        ], 422);
    }

    // ModelNotFoundException → 404
    if ($exception instanceof ModelNotFoundException) {
        return response()->json(['message' => 'Resource not found'], 404);
    }

    return parent::render($request, $exception);
}
```

#### Errores de Validación en View

```blade
@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif
```

#### Respuestas de Error en API

```json
// 400 - Bad Request
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["The email field is required."],
        "phone": ["The phone format is invalid."]
    }
}

// 401 - Unauthorized
{
    "message": "Unauthenticated"
}

// 403 - Forbidden
{
    "message": "This action is unauthorized."
}

// 404 - Not Found
{
    "message": "Resource not found"
}

// 422 - Validation Failed
{
    "message": "Validation failed",
    "errors": { ... }
}

// 500 - Server Error
{
    "message": "Internal Server Error",
    "error": "..."  // Solo en debug=true
}
```

### 11.3 Validaciones de Lógica de Negocio

**Búsqueda de Habitaciones Disponibles:**

```php
// En CheckInController
public function buildRoomNumberOptions($roomTypeId, $reservationId) {
    // Obtiene opciones de números de habitación disponibles
    $activeRooms = Room::where('room_type_id', $roomTypeId)
        ->where('status', 'activa')
        ->get();

    $assignedRooms = Stay::whereNull('actual_check_out_at')
        ->where('arrival_at', '<=', now())
        ->pluck('room_id')
        ->toArray();

    $available = [];
    foreach ($activeRooms as $room) {
        // Valida habitación no esté ocupada
        $status = in_array($room->id, $assignedRooms) ? 'Ocupada' : 'Disponible';
        $available[] = [
            'number' => $room->total_room,
            'status' => $status,
            'capacity' => $room->total_room
        ];
    }

    return collect($available);
}
```

**Validación de Stock en Checkout Minibar:**

```php
// En CheckoutController
$cart_items = request()->input('items');  // [{product_id, qty}, ...]

foreach ($cart_items as $item) {
    $product = MinibarProduct::find($item['product_id']);
    
    // Valida stock disponible
    if ($product->stock < $item['quantity']) {
        return back()->withErrors([
            'stock' => "Stock insuficiente de {$product->nombre}"
        ]);
    }
}

// Procesa compra
```

**Validación de Pago de Anticipo:**

```php
// En OrderController::confirmPayment()
$order = Order::findOrFail($token);

// Valida orden aún no pagada
if ($order->is_paid) {
    return response()->json([
        'success' => false,
        'message' => 'Esta orden ya ha sido pagada'
    ], 400);
}

// Procesa pago con PSP
// Si éxito:
$order->update([
    'is_paid' => true,
    'status' => 'anticipo_pagado'
]);
```

---

## 12. GENERACIÓN DE REPORTES

### 12.1 Sistema de Reportes en PDF

#### ReportController

```php
namespace App\Http\Controllers\Admin;

use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller {

    /**
     * Genera reporte ejecutivo completo
     */
    public function ejecutivo() {
        $data = $this->buildReportData();
        
        $pdf = Pdf::loadView('reports.ejecutivo', $data)
            ->setPaper('a4', 'portrait')
            ->setOption('dpi', 150)
            ->setOption('defaultFont', 'sans-serif');

        return $pdf->download('reporte-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Recopila datos para reporte
     */
    private function buildReportData(): array {
        $now = Carbon::now();
        $today = $now->toDateString();

        return [
            // Habitaciones
            'totalHabitaciones' => Room::sum('total_room'),
            'habitacionesEnMant' => MaintenanceOrder::active()->distinct('room_id')->count('room_id'),
            'huespedesEnCasa' => Stay::whereNull('actual_check_out_at')
                ->whereNotNull('actual_check_in_at')->count(),
            'habitacionesDisponibles' => // cálculo,
            'pctOcupacion' => // cálculo,
            'distribucionPorTipo' => RoomType::with('rooms')->get(),

            // Reservas
            'totalReservas' => Order::count(),
            'reservasActivas' => Order::whereIn('status', [
                Order::STATUS_ANTICIPO_PAGADO,
                Order::STATUS_OCUPADA,
            ])->count(),
            'reservasPendientes' => Order::where('status', Order::STATUS_PENDIENTE)->count(),
            'checkinHoy' => Stay::whereDate('actual_check_in_at', $today)->count(),
            'checkoutHoy' => Stay::whereDate('actual_check_out_at', $today)->count(),

            // Mantenimiento
            'mantPendiente' => MaintenanceOrder::where('status', 'asignada')->count(),
            'mantCompletado' => MaintenanceOrder::where('status', 'completada')->count(),
            'mantUrgente' => MaintenanceOrder::where('priority', 'urgente')->count(),
            'tiempoPromedioMant' => // cálculo,

            // Minibar
            'totalVentasMinibar' => Compra::sum('total'),
            'productosVendidos' => CompraProducto::sum('cantidad'),
            'productosTopVentas' => // top 5,

            // Huéspedes
            'huespedesActuales' => Stay::with(['guest', 'room'])
                ->whereNull('actual_check_out_at')
                ->limit(10)->get(),
            'nuevasBlancos' => // últimos arrivals,
            'salidaPredecida' => // próximos departures,
        ];
    }
}
```

#### Vista Blade: resources/views/reports/ejecutivo.blade.php

```blade
@extends('layouts.report')

@section('content')
<div class="report-header">
    <h1>REPORTE EJECUTIVO</h1>
    <p>Generado: {{ now()->format('d/m/Y H:i') }}</p>
</div>

<section class="section">
    <h2>OCUPACIÓN DE HABITACIONES</h2>
    <div class="metrics">
        <div class="metric">
            <strong>Total Habitaciones:</strong> {{ $totalHabitaciones }}
        </div>
        <div class="metric">
            <strong>Ocupadas:</strong> {{ $huespedesEnCasa }}
        </div>
        <div class="metric">
            <strong>Disponibles:</strong> {{ $habitacionesDisponibles }}
        </div>
        <div class="metric">
            <strong>En Mantenimiento:</strong> {{ $habitacionesEnMant }}
        </div>
        <div class="metric big">
            <strong>% Ocupación:</strong> {{ $pctOcupacion }}%
        </div>
    </div>

    @if($distribucionPorTipo)
    <table>
        <thead>
            <tr>
                <th>Tipo Habitación</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($distribucionPorTipo as $tipo)
            <tr>
                <td>{{ $tipo->name }}</td>
                <td>{{ $tipo->total }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</section>

<section class="section">
    <h2>RESERVAS</h2>
    <div class="metrics">
        <div class="metric">
            <strong>Totales:</strong> {{ $totalReservas }}
        </div>
        <div class="metric">
            <strong>Activas:</strong> {{ $reservasActivas }}
        </div>
        <div class="metric">
            <strong>Pendientes:</strong> {{ $reservasPendientes }}
        </div>
        <div class="metric">
            <strong>Check-in Hoy:</strong> {{ $checkinHoy }}
        </div>
        <div class="metric">
            <strong>Check-out Hoy:</strong> {{ $checkoutHoy }}
        </div>
    </div>
</section>

<!-- Más secciones ... -->

@endsection
```

### 12.2 Bibliotecas Utilizadas

**Barryvdh/DomPDF:**

```bash
composer require barryvdh/laravel-dompdf
```

**Configuración (config/dompdf.php):**

```php
'public_path' => public_path(),
'convert_to_base64' => true,
'logOutputFile' => storage_path('logs/dompdf.log'),

// Opciones de renderizado
'options' => [
    'dpi' => 150,
    'defaultFont' => 'sans-serif',
    'isPhpEnabled' => true,  // Permite PHP en vistas
],
```

### 12.3 Tipos de Reportes

1. **Ejecutivo:** Métricas generales, KPIs
2. **Ocupación:** Histórico, proyecciones
3. **Financiero:** Ingresos, pagos, cuentas por cobrar
4. **Huéspedes:** Arrivals, departures, nacionalidades
5. **Minibar:** Ventas, productos top, inventario
6. **Mantenimiento:** Órdenes, tiempos, prioridades
7. **Auditoría:** Acceso, cambios, usuarios activos

### 12.4 Exportación y Descargas

**Download PDF:**

```php
return $pdf->download('filename.pdf');  // Descarga
return $pdf->stream();                   // Abre en navegador
```

**Almacenamiento de Reportes:**

```php
// Guardar en storage
Storage::put('reports/ejecutivo-' . $date . '.pdf', 
    $pdf->output());
```

---

## 13. BUENAS PRÁCTICAS Y PATRONES

### 13.1 Patrones de Diseño Utilizados

#### Service Layer Pattern

```php
// Lógica de negocio aislada en Services
namespace App\Services\Reception;

class CheckInService {
    public function processCheckIn(Order $order, array $data) {
        // Centraliza toda lógica check-in
        // Testeable, reutilizable
    }
}

// En Controller
public function store(Request $request) {
    $service = app(CheckInService::class);
    return $service->processCheckIn($order, $validated);
}
```

**Ventajas:**
- Lógica de negocio separada de HTTP
- Fácil de testear
- Reutilizable en múltiples controllers
- Documentación clara de intención

#### Repository Pattern (Opcional)

```php
// Si se implementara
interface OrderRepository {
    public function getActiveOrders();
    public function getPendingPayment();
}

class EloquentOrderRepository implements OrderRepository {
    public function getActiveOrders() {
        return Order::whereIn('status', ['activa', 'ocupada'])->get();
    }
}
```

#### Observer Pattern (Events)

```php
// En Event
class StayStarted extends Event {
    public function __construct(public Stay $stay) {}
}

// Listener automático
class SendWelcomeEmail {
    public function __construct(private MailService $mail) {}
    
    public function handle(StayStarted $event) {
        $guest = $event->stay->guest;
        $this->mail->sendWelcome($guest);
    }
}
```

#### Trait para Código Reutilizable

```php
// app/Traits/Auditable.php
trait Auditable {
    protected static function bootAuditable() {
        static::created(function ($model) {
            registrarAuditoria('CREATE', ...);
        });
    }
}

// Usar en Model
class Stay extends Model {
    use Auditable;
}
```

### 13.2 Convenciones de Código

#### Naming Conventions

**Controllers:**
```
OrderController              # Recurso singular
ReceptionCheckInController  # Acción específica
Admin\EmployeeController    # Namespace por módulo
```

**Modelos:**
```php
User, Guest, Order, Stay, Room   # Singular en inglés
MinibarProduct                    # Camel case
```

**Métodos:**
```php
// Queries
$order->forUser($user)->pending();     // scope
$room->occupiedToday();                // scope
$user->hasRole('admin');               // boolean question

// Acciones
$this->authorize('create', Stay::class);
$this->checkInService->process($order);
```

**Variables:**
```php
$guestName          // camelCase
$stay_id            // snake_case en arrays/json
$STATUS_ACTIVE      // UPPER_CASE solo constantes

// Booleans
$is_paid
$has_pending_payment
```

#### Code Comments

```php
/**
 * Procesa el check-in de un huésped.
 * 
 * @param Order $order Reserva a procesar
 * @param array $data Datos del huésped y habitación
 * @return Stay Estancia creada
 * @throws ValidationException Si datos inválidos
 * @throws AuthorizationException Si usuario sin permiso
 */
public function processCheckIn(Order $order, array $data): Stay {
    // ...
}
```

**Comentarios en línea (uso mínimo):**

```php
// Solo si NO es obvio
$balance = $folio->charges()->sum('amount') 
    - $folio->payments()->sum('amount');  // Cálculo saldo

// ❌ Evitar
$name = $user->name;  // Asigna nombre a $name (obvio)
```

### 13.3 Principios SOLID Aplicados

#### S - Single Responsibility

```php
// ❌ MAL: MixedController mezcla lógica
class MixedController {
    public function checkIn() {
        // Valida
        // Audita
        // Envía email
        // Todo mezclado
    }
}

// ✅ BIEN: Separación de responsabilidades
class CheckInController {
    public function store(CheckInService $service) {
        return $service->process($data);  // Delega
    }
}
```

#### O - Open/Closed

```php
// ✅ Abierto para extensión, cerrado para modificación
interface PaymentProcessor {
    public function process(Payment $payment);
}

class StripeProcessor implements PaymentProcessor { }
class PaypalProcessor implements PaymentProcessor { }

// Nueva forma de pago sin modificar código existente
```

#### L - Liskov Substitution

```php
// ✅ Cualquier BebidaType se puede usar donde espera BebidaType
interface BebidaType {
    public function getName();
}

$alcoholica = new AlcoholicBeverage();
$noAlcoholica = new NonAlcoholicBeverage();

foreach ([$alcoholica, $noAlcoholica] as $bebida) {
    echo $bebida->getName();  // Funciona igual
}
```

#### I - Interface Segregation

```php
// ❌ MAL: Interfaz muy grande
interface AdminRepository {
    public function create();
    public function read();
    public function update();
    public function delete();
    public function bulkDelete();
    public function export();
    public function import();
}

// ✅ BIEN: Interfaces específicas
interface Creatable {
    public function create();
}

interface Writable {
    public function update();
}

class OrderService implements Creatable, Writable { }
```

#### D - Dependency Injection

```php
// ❌ MAL: Acoplamiento fuerte
class CheckInController {
    public function store() {
        $service = new CheckInService();  // Crea aquí
        $service->process($data);
    }
}

// ✅ BIEN: Inyección de dependencia
class CheckInController {
    public function __construct(protected CheckInService $service) {}
    
    public function store() {
        $this->service->process($data);  // Inyectado
    }
}
```

### 13.4 Testing (Caso de Estudio)

```php
// tests/Feature/ReceptionCheckInTest.php

class ReceptionCheckInTest extends TestCase {
    
    /** @test */
    public function can_check_in_guest_with_valid_data() {
        $user = User::factory()->state(['is_admin' => true])->create();
        $order = Order::factory()->create();
        
        $response = $this->actingAs($user)
            ->post("/reception/check-in/{$order->id}", [
                'room_number' => 101,
                'first_name' => 'Juan',
                'last_name' => 'Pérez',
                'document_type' => 'CC',
                'document_number' => '1234567890',
                'email' => 'juan@example.com',
                'phone' => '+573001234567',
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('stays', [
            'reservation_id' => $order->id,
            'guest_id' => Guest::latest()->first()->id,
        ]);
        
        $this->assertDatabaseHas('auditorias', [
            'accion' => 'CHECK_IN',
            'usuario_id' => $user->id,
        ]);
    }

    /** @test */
    public function fails_check_in_with_duplicate_document() {
        $user = User::factory()->state(['is_admin' => true])->create();
        Guest::factory()->create(['document_number' => '1234567890']);
        $order = Order::factory()->create();
        
        $response = $this->actingAs($user)
            ->post("/reception/check-in/{$order->id}", [
                'document_number' => '1234567890',
                // resto de datos
            ]);

        $response->assertSessionHasErrors(['document_number']);
    }
}
```

### 13.5 Performance Best Practices

#### Query Optimization

```php
// ❌ N+1 Query Problem
$orders = Order::all();
foreach ($orders as $order) {
    echo $order->user->name;  // Query por cada orden
}

// ✅ Eager Loading
$orders = Order::with('user')->get();
foreach ($orders as $order) {
    echo $order->user->name;  // Una sola query
}

// ✅ Nested relationships
$orders = Order::with(['user', 'room.roomtype'])->get();
```

#### Indexing

```php
// En migrations
$table->index('status');           // Búsquedas frecuentes
$table->index('user_id');
$table->index(['folio_id', 'posted_at']);  // Índice compuesto
$table->unique('email');           // Único
```

#### Soft Deletes (si aplica)

```php
// Lógica borrado
use SoftDeletes;

$user->delete();           // Marca como deleted_at
$user->restore();          // Recupera
$user->forceDelete();      // Borrado permanente
```

### 13.6 Error Handling Best Practices

```php
// ✅ Validación in-form early
public function store(StoreCheckInRequest $request) {
    // Ya fue validado 
}

// ✅ Manejo granular de excepciones
try {
    $response = $psyPaymentGateway->charge($amount);
} catch (PaymentGatewayException $e) {
    Log::error('Payment failed', ['error' => $e->getMessage()]);
    return back()->withError('Pago rechazado: ' . $e->getMessage());
} catch (Exception $e) {
    Log::critical('Unexpected error', ['error' => $e]);
    return back()->withError('Error interno');
}

// ✅ Logging apropiado
Log::info('Check-in successful', ['stay_id' => $stay->id]);
Log::warning('Low minibar stock', ['product_id' => $product->id]);
Log::error('Payment failure', ['order_id' => $order->id, 'error' => ...]);

---

## 14. MEJORAS TÉCNICAS FUTURAS

### 14.1 Corto Plazo (1-2 meses)

#### 1. Corrección de Incongruencias de Roles
**Problema:** Uso mixto de 'administrador' vs 'admin' en validaciones
```php
// Estandarizar a 'administrador' (lowercase)
// Auditar todos los usos en FormRequests
// Actualizar PolicyChecks
```

**Impacto:** Alta | **Esfuerzo:** Bajo

#### 2. Implementación de Queue para Emails
**Justificación:** Envío de confirmaciones de reserva, bienvenidas
```php
// Usar Redis o database queue
// dispatch(new SendWelcomeEmail($guest))->delay(now()->addSeconds(30));
```

**Stack:** Illuminate/Queue + Redis/Database

**Impacto:** Media | **Esfuerzo:** Medio

#### 3. Rate Limiting Granular
**Problema:** Protección contra fuerza bruta en pago de anticipo
```php
// Limitar reintentos de pago por token/usuario
Route::post('/orders/payment/{token}/confirm')
    ->middleware('throttle:3,1');  // 3 intentos por minuto
```

**Impacto:** Alta | **Esfuerzo:** Bajo

#### 4. API Documentation (OpenAPI/Swagger)
**Herramienta:** Laravel Swagger / OpenAPI Generator
```php
// Documenta automáticamente endpoints
// Genera cliente SDK
// Testing interactivo en Swagger UI
```

**Impacto:** Media | **Esfuerzo:** Medio-Alto

---

### 14.2 Mediano Plazo (2-4 meses)

#### 5. Arquitectura Específica para Módulos
**Current State:** Controllers monolíticos
**Target:** Bounded contexts (DDD)

```
app/Modules/
├── Reservations/
│   ├── Controllers/
│   ├── Models/
│   ├── Services/
│   ├── Requests/
│   └── routes.php
├── Reception/
├── Minibar/
└── Maintenance/
```

**Beneficios:**
- Escalabilidad
- Independencia de módulos
- Más fácil para múltiples equipos

**Impacto:** Alta | **Esfuerzo:** Alto

#### 6. Caching Estratégico
**Casos de uso:**
```php
// Catálogo minibar (público)
Cache::remember('minibar.products', 3600, function() {
    return MinibarProduct::with('type')->get();
});

// Disponibilidad habitaciones
Cache::remember('rooms.availability.' . $date, 300, ...);

// Reporting (caché 1 hora)
```

**Impacto:** Alta | **Esfuerzo:** Medio

#### 7. WebSocket Real-Time Updates (Broadcasting)
**Casos:**
- Room assignment board (updates en tiempo real)
- Ocupación live
- Notificaciones maintenance

**Stack:** Pusher / Laravel WebSockets / Soketi

```php
// En AssignmentController
broadcast(new RoomAssigned($stay))->toOthers();

// En JavaScript
Echo.channel('rooms')
    .listen('RoomAssigned', (e) => {
        // Actualizar tablero
    });
```

**Impacto:** Media | **Esfuerzo:** Medio-Alto

---

### 14.3 Largo Plazo (4+ meses)

#### 8. Microservicios de Reportes
**Problema:** Reportes pesados bloquean aplicación
**Solución:** Servicio separado de reportes

```
┌─────────────────────────────────┐
│  Hotel Management (main app)    │
├─────────────────────────────────┤
│ Reservas, Recepción, Minibar    │
└────────────────────┬────────────┘
                     │ API
                     ↓
        ┌────────────────────────┐
        │ Report Service         │
        │ (Node.js + Bull)       │
        │ - Generación PDF       │
        │ - Export CSV           │
        │ - Análisis complejos    │
        └────────────────────────┘
```

**Impacto:** - | **Esfuerzo:** Muy Alto

#### 9. Machine Learning para Forecasting
**Casos:**
- Predicción de ocupación
- Demanda por tipo habitación
- Análisis de tendencias huéspedes

**Stack:** Python + Jupyter + Scikit-learn

**Impacto:** Baja | **Esfuerzo:** Muy Alto

#### 10. Mobile App Dedicada (React Native)
**Features:**
- Check-in/check-out desde móvil (recepción)
- Pedidos minibar desde cuarto (huésped)
- Órdenes mantenimiento (técnicos)

**Impacto:** Alta | **Esfuerzo:** Muy Alto

---

### 14.4 Refactoring y Tech Debt

#### Deuda Técnica Identificada

| Item | Severidad | Nota |
|------|-----------|------|
| Inconsistencia de roles (admin/administrador) | 🔴 Alta | Requiere auditoría |
| `Stay.notes` para room tracking es frágil | 🟡 Media | Migrar a columna |
| TailwindCSS PurgeCSS no optimizado | 🟡 Media | Reduce bundle size |
| Logging insuficiente en endpoints críticos | 🟡 Media | Agregar observabilidad |
| No hay versionioning de API | 🟡 Media | Implementar v1, v2 |
| Database indexes incompletos | 🟡 Media | Performance audit |
| Tests unitarios limitados | 🔴 Alta | <50% coverage actual |

#### REFACTORING: StayNotes Tracking

**Problema Actual:**
```php
$stay->notes = '[ROOM_NUM:101] Check-in rápido';
// Frágil a parsing, no normalizado
```

**Mejora Propuesta:**
```php
// Agregar columnas explícitas
Schema::table('stays', function (Blueprint $table) {
    $table->integer('assigned_room_number')->nullable()->index();
    $table->json('metadata')->nullable();  // {check_in_speed, ...}
    $table->dropColumn('notes');  // Mantener en table separada
});

// Crear tabla de notas
Schema::create('stay_notes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('stay_id');
    $table->text('content');
    $table->string('type');  // 'system', 'manual'
    $table->timestamps();
});
```

---

### 14.5 Plan de Ejecución

**Q2 2026 (Ahora):**
- ✅ Corrección roles
- ✅ Rate limiting
- ⏳ Queue emails
- ⏳ API Documentation

**Q3 2026:**
- Arquitectura modular
- Caching
- Real-time broadcasting
- Testing (cobertura 70%)

**Q4 2026+:**
- Microservicios reportes
- ML forecasting  
- Mobile app
- Optimizaciones DB

---

## 15. REFERENCIAS Y RECURSOS

### Documentación Oficial

- [Laravel 11 Documentation](https://laravel.com/docs)
- [Vue.js 3 Guide](https://vuejs.org/)
- [Inertia.js Documentation](https://inertiajs.com/)
- [Spatie Roles & Permissions](https://spatie.be/docs/laravel-permission)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [TailwindCSS](https://tailwindcss.com/)

### Recursos del Proyecto

- **Repositorio:** https://github.com/RuzoE/Aplicativo-WEB-reservas
- **Branch Actual:** `main`
- **Issues/PRs:** [GitHub Issues](https://github.com/RuzoE/issues)
- **CI/CD:** [Verificar workflows]

### Documentación Interna

- [RECEPCION_RESUMEN.md](docs/RECEPCION_RESUMEN.md) - Resumen módulo recepción
- [RECEPCION_INTEGRACIONES.md](docs/RECEPCION_INTEGRACIONES.md) - Integraciones recepción
- [RECEPCION_MODULE.md](docs/RECEPCION_MODULE.md) - Detalles módulo
- [Security Audit Notes](memories/repo/security-audit-notes.md)

---

## 16. GLOSARIO DE TÉRMINOS

| Término | Definición |
|---------|-----------|
| **Stay** | Una estancia de un huésped (una noche en una habitación) |
| **Folio** | Resumen de cargos durante una estancia |
| **Order** | Reserva de habitación (puede contener múltiples stays) |
| **Guest** | Información del huésped |
| **Charge** | Un cargo individual al folio (minibar, servicio extra) |
| **Compra** | Una compra de minibar hecha por un huésped |
| **Room Number** | Número físico de la habitación (101, 102, etc) |
| **Room Type** | Categoría de habitación (Doble, Simple, Suite) |
| **Scope** | Método de query helper en modelo |
| **Policy** | Clase de autorización basada en modelo |
| **Middleware** | Capa de procesamiento de request |
| **Guard** | Mecanismo de autenticación (web, sanctum) |
| **Ability** | Permiso granular en Spatie |
| **Role** | Rol de usuario en Spatie Permissions |
| **Service** | Clase con lógica de negocio |
| **Controller** | Controlador HTTP que maneja requests |
| **Form Request** | Clase con validación de entrada |
| **Blade** | Template engine de Laravel |
| **Inertia** | Adaptador Laravel ↔ Vue.js |
| **ORM** | Object-Relational Mapping (Eloquent) |
| **N+1 Query** | Anti-patrón de queries ineficientes |
| **Broadcasting** | Comunicación en tiempo real |
| **Queue** | Sistema de tareas asincrónicas |

---

## 17. CONCLUSIÓN Y NOTAS FINALES

### Resumen del Sistema

El **Hotel Piloto SAM** es una plataforma integral bien estructurada que integra:

✅ **Fortalezas:**
- Arquitectura modular clara
- Stack moderno (Laravel + Vue.js)
- Autenticación y autorización robustas
- Modelo de datos normalizado
- Auditoría de acciones
- Generación de reportes completa
- API REST bien organizada

⚠️ **Áreas de Mejora:**
- Testing unitario (cobertura baja)
- Documentación de API
- Manejo de excepciones inconsistente
- Deuda técnica acumulada
- Performance optimization

### Mantenimiento Futuro

**Para nuevos desarrolladores:**
1. Leer esta documentación completa
2. Revisar memoria del repositorio en `/memories/repo/`
3. Ejecutar `php artisan tinker` para explorar datos
4. Revisar tests existentes
5. Contactar tech lead para arquitectura compleja

**Para arquitectos:**
1. Planificar refactoring modular (bounded contexts)
2. Implementar estrategia de caching
3. Establecer SLAs y monitoring
4. Roadmap de escalabilidad

---

**Fin del Manual Técnico**  
**Versión:** 1.0  
**Última Actualización:** Marzo 31, 2026
