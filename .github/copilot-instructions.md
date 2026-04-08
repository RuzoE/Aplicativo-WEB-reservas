# Hotel Piloto SAM — Guías del proyecto

## Stack y arquitectura
- Backend en `Laravel 10` con `PHP 8.2+`.
- Frontend híbrido con `Blade`, componentes puntuales en `Vue 3`, `Vite` y `Tailwind CSS`.
- Mantén los controladores ligeros; la lógica de negocio suele vivir en `app/Services/` y los módulos están organizados por dominio (`Admin`, `Api`, `Reception`, `Minibar`).
- Usa como fuente de verdad las rutas reales en `routes/api.php`, los controladores en `app/Http/Controllers/`, y las pruebas en `tests/Feature/`.

## Comandos de trabajo
- Instalar dependencias: `composer install` y `npm install`
- Desarrollo local: `php artisan serve` y `npm run dev`
- Pruebas: `php artisan test`
- Compilación frontend: `npm run build`
- Formato PHP: `php artisan pint`

## Convenciones para API REST y su documentación
- Escribe la documentación técnica en **español**, alineada con `README.md` y `docs/`.
- Al documentar o probar endpoints REST, incluye siempre:
  - método HTTP y ruta exacta
  - si requiere `auth:sanctum`
  - roles y/o abilities necesarios (`administrador`, `recepcion`, `reservas`, `minibar`, `mantenimiento`)
  - payload JSON de ejemplo
  - validaciones reales tomadas de `FormRequest` o de `$request->validate()`
  - respuestas esperadas y códigos HTTP (`200`, `201`, `204`, `401`, `403`, `422`)
  - ejemplo utilizable en Postman/cURL con `Authorization: Bearer {token}` cuando aplique
- No inventes la forma de la respuesta: verifica el JSON real en el controlador o en las pruebas. En este proyecto varias respuestas API devuelven modelos/paginación de Laravel directamente, no `JsonResource`.
- Para endpoints autenticados, documenta también el flujo de obtención del token vía `/api/auth/login` o `/api/auth/register`.

### Ejemplo documentado: `POST /api/auth/login`
- **Método y ruta:** `POST /api/auth/login`
- **Autenticación:** no requiere `auth:sanctum`, pero está limitado por `throttle:auth-login`.
- **Payload de ejemplo:**
  ```json
  {
    "email": "recepcion@gmail.com",
    "password": "PasswordSeguro123!",
    "device_name": "postman"
  }
  ```
- **Validaciones reales** tomadas de `app/Http/Controllers/Api/AuthController.php`:
  - `email`: `required|email` + regla `AllowedEmailDomain` (actualmente solo acepta dominios `gmail.com` o `hotmail.com`)
  - `password`: `required`
  - `device_name`: `nullable|string|max:255`
- **Respuesta esperada `200`:**
  ```json
  {
    "success": true,
    "message": "Autenticación exitosa.",
    "token_type": "Bearer",
    "token": "1|token_generado",
    "user": {
      "id": 1,
      "name": "Usuario Demo",
      "email": "recepcion@gmail.com"
    }
  }
  ```
- **Respuesta esperada `422` por credenciales inválidas:**
  ```json
  {
    "success": false,
    "message": "Estas credenciales no coinciden con nuestros registros."
  }
  ```
- **Ejemplo para Postman/cURL:**
  ```bash
  curl -X POST http://127.0.0.1:8000/api/auth/login \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -d '{
      "email": "recepcion@gmail.com",
      "password": "PasswordSeguro123!",
      "device_name": "postman"
    }'
  ```

### Ejemplo documentado: `GET /api/auth/me`
- **Método y ruta:** `GET /api/auth/me`
- **Autenticación:** requiere `auth:sanctum` con token Bearer válido.
- **Roles/abilities:** cualquier usuario autenticado con token activo; no exige un rol específico.
- **Headers requeridos:**
  - `Accept: application/json`
  - `Authorization: Bearer {token}`
- **Respuesta esperada `200`:**
  ```json
  {
    "success": true,
    "user": {
      "id": 1,
      "name": "Usuario Demo",
      "email": "recepcion@gmail.com",
      "roles": []
    }
  }
  ```
- **Respuesta esperada `401`:** si no envías token o el token es inválido, Sanctum rechaza la petición.
- **Ejemplo para Postman/cURL:**
  ```bash
  curl -X GET http://127.0.0.1:8000/api/auth/me \
    -H "Accept: application/json" \
    -H "Authorization: Bearer {token}"
  ```

## Patrones de pruebas
- Para pruebas de API, sigue el estilo de `tests/Feature/Api/MinibarProductsTest.php`:
  - `use RefreshDatabase`
  - `Sanctum::actingAs(...)` para autenticar
  - asignación explícita de roles/permisos antes de probar endpoints protegidos
  - aserciones sobre estructura JSON y códigos HTTP
- Si agregas o modificas endpoints API, añade o actualiza pruebas de `Feature` que cubran:
  - caso exitoso
  - acceso no autenticado (`401`)
  - falta de permisos (`403`) si aplica
  - errores de validación (`422`)

## Rutas y archivos clave
- Rutas API: `routes/api.php`
- Auth API: `app/Http/Controllers/Api/AuthController.php`
- CRUD de ejemplo: `app/Http/Controllers/Api/MinibarProductController.php`
- Pruebas de ejemplo: `tests/Feature/Api/MinibarProductsTest.php`

## Referencias existentes
En lugar de duplicar contexto general, enlaza la documentación ya disponible:
- `docs/MANUAL_TECNICO.md` — arquitectura, módulos, rutas y buenas prácticas
- `docs/RECEPCION_MODULE.md` — flujo de recepción y entidades principales
- `docs/RECEPCION_INTEGRACIONES.md` — integraciones y puntos de conexión
- `docs/EJEMPLOS_CODIGO.md` — patrones de implementación

## Pitfalls conocidos
- Los nombres de roles están en español; usa los nombres reales del proyecto.
- Algunas rutas tienen `throttle` (`auth-register`, `auth-login`, `reception-sensitive`), así que menciónalo al documentar pruebas manuales con Postman.
- Las abilities de tokens Sanctum importan en endpoints protegidos, por ejemplo `minibar:write` y `reception:write`.
