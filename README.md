# Hotel Piloto SAM

Aplicación web de gestión hotelera desarrollada con Laravel 10. El sistema integra operación de reservas, recepción, minibar, mantenimiento y administración, con control de acceso por roles y trazabilidad de acciones.

## Módulos Principales

- Reservas de habitaciones (búsqueda, disponibilidad, órdenes y pagos).
- Recepción (check-in, folio, cargos/pagos, check-out, walk-ins, anticipos y asignación de habitaciones).
- Minibar administrativo (tipos de bebida, catálogo, productos, compras/ventas).
- Minibar de usuario (catálogo, carrito y checkout).
- Mantenimiento (órdenes, prioridad urgente, historial por habitación).
- Administración (empleados, roles, auditoría e informe general PDF).

## Stack Tecnológico

- PHP 8.2+
- Laravel 10
- Blade + Vue 3 (componentes puntuales)
- Vite 4
- Tailwind CSS 3
- MySQL/MariaDB
- PHPUnit 10

Dependencias destacadas:

- `spatie/laravel-permission` (roles/permisos)
- `barryvdh/laravel-dompdf` (reportes PDF)
- `spatie/laravel-backup` (respaldo)

## Requisitos

- PHP 8.2 o superior
- Composer
- Node.js + npm
- Base de datos MySQL/MariaDB

## Instalación Local

1. Clonar el repositorio.
2. Copiar variables de entorno:

  `cp .env.example .env`

  En Windows PowerShell:

  `Copy-Item .env.example .env`

3. Configurar credenciales de base de datos en `.env`.
4. Instalar dependencias PHP:

  `composer install`

5. Instalar dependencias frontend:

  `npm install`

6. Generar clave de aplicación:

  `php artisan key:generate`

7. Ejecutar migraciones y seeders:

  `php artisan migrate --seed`

8. Iniciar backend:

  `php artisan serve`

9. Iniciar frontend (modo desarrollo):

  `npm run dev`

## Comandos Útiles

- `npm run dev` - Levanta Vite en desarrollo.
- `npm run build` - Compila assets para producción.
- `php artisan test` - Ejecuta pruebas automatizadas.
- `npm run screenshot` - Captura de pantalla con Puppeteer (crea carpetas de salida automáticamente).

## Documentación Técnica

La documentación funcional y técnica del proyecto está en `docs/`.

| Documento | Descripción |
|-----------|-------------|
| [MANUAL_TECNICO.md](docs/MANUAL_TECNICO.md) | Referencia técnica general del sistema |
| [EJEMPLOS_CODIGO.md](docs/EJEMPLOS_CODIGO.md) | Patrones y ejemplos de implementación |
| [RECEPCION_MODULE.md](docs/RECEPCION_MODULE.md) | Arquitectura y flujo del módulo de recepción |
| [RECEPCION_INTEGRACIONES.md](docs/RECEPCION_INTEGRACIONES.md) | Integraciones y puntos de conexión de recepción |
| [RECEPCION_RESUMEN.md](docs/RECEPCION_RESUMEN.md) | Resumen operativo del módulo de recepción |
| [FIX_BOTON_COMPLETAR_MANTENIMIENTO.md](docs/FIX_BOTON_COMPLETAR_MANTENIMIENTO.md) | Nota técnica de corrección específica |

## Seguridad

- No publicar credenciales reales en este repositorio.
- Los usuarios de prueba deben gestionarse mediante seeders o documentación interna del entorno.

## Estado

Proyecto en evolución activa, con mejoras continuas en recepción, mantenimiento y experiencia operativa de panel administrativo.

