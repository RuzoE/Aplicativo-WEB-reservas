# API Auth REST — Pruebas manuales con Postman

Documentación práctica para probar la autenticación API del proyecto usando `Laravel Sanctum`.

> Referencias relacionadas:
> - `routes/api.php`
> - `app/Http/Controllers/Api/AuthController.php`
> - `docs/MANUAL_TECNICO.md`

---

## Flujo recomendado

1. Consumir `POST /api/auth/login` para obtener un token Bearer.
2. Reutilizar ese token en `GET /api/auth/me`.
3. Cuando termines, invalidar el token con `POST /api/auth/logout`.

---

## Endpoint: `POST /api/auth/login`

- **Método y ruta:** `POST /api/auth/login`
- **Autenticación:** no requiere `auth:sanctum`
- **Throttle:** `throttle:auth-login`
- **Controlador:** `App\Http\Controllers\Api\AuthController@login`

### Payload de ejemplo

```json
{
  "email": "recepcion@gmail.com",
  "password": "PasswordSeguro123!",
  "device_name": "postman"
}
```

### Validaciones reales

Tomadas de `app/Http/Controllers/Api/AuthController.php`:

- `email`: `required|email` + regla `AllowedEmailDomain`
- `password`: `required`
- `device_name`: `nullable|string|max:255`

> **Nota:** la regla `AllowedEmailDomain` actualmente solo acepta correos con dominio `gmail.com` o `hotmail.com`.

### Respuesta esperada `200 OK`

```json
{
  "success": true,
  "message": "Autenticación exitosa.",
  "token_type": "Bearer",
  "token": "1|token_generado",
  "user": {
    "id": 1,
    "name": "Recepción Demo",
    "email": "recepcion@gmail.com",
    "roles": []
  }
}
```

### Respuesta esperada `422 Unprocessable Entity`

Cuando las credenciales no son válidas:

```json
{
  "success": false,
  "message": "Estas credenciales no coinciden con nuestros registros."
}
```

### Ejemplo cURL

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

### Configuración en Postman

- **Method:** `POST`
- **URL:** `http://127.0.0.1:8000/api/auth/login`
- **Headers:**
  - `Accept: application/json`
  - `Content-Type: application/json`
- **Body → raw → JSON:** usar el payload de ejemplo

---

## Endpoint: `GET /api/auth/me`

- **Método y ruta:** `GET /api/auth/me`
- **Autenticación:** requiere `auth:sanctum`
- **Controlador:** `App\Http\Controllers\Api\AuthController@me`
- **Roles/abilities:** cualquier usuario autenticado con token válido

### Headers requeridos

```http
Accept: application/json
Authorization: Bearer {token}
```

### Respuesta esperada `200 OK`

```json
{
  "success": true,
  "user": {
    "id": 1,
    "name": "Recepción Demo",
    "email": "recepcion@gmail.com",
    "roles": []
  }
}
```

### Respuesta esperada `401 Unauthorized`

Si el token no se envía o no es válido, `Sanctum` rechaza la petición con error de autenticación.

### Ejemplo cURL

```bash
curl -X GET http://127.0.0.1:8000/api/auth/me \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {token}"
```

### Configuración en Postman

- **Method:** `GET`
- **URL:** `http://127.0.0.1:8000/api/auth/me`
- **Headers:**
  - `Accept: application/json`
  - `Authorization: Bearer {token}`

---

## Uso sugerido en pruebas automatizadas

Para pruebas `Feature`, seguir el patrón ya existente en `tests/Feature/Api/`:

- `use RefreshDatabase`
- `postJson()` y `getJson()` para consumir la API
- aserciones sobre `status`, `message`, `token_type` y estructura JSON
- para endpoints protegidos, autenticación con `Sanctum::actingAs(...)`
