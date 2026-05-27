# Integración de Brevo (Sendinblue) para Email en Railway

## 📋 Resumen

Se ha integrado la API HTTP de Brevo para enviar correos transaccionales en lugar de SMTP, resolviendo el bloqueo de puertos SMTP en Railway (25, 465, 587).

## 🏗️ Arquitectura de la Solución

### Componentes principales:

1. **BrevoTransport** (`app/Mail/Transport/BrevoTransport.php`)
   - Custom mail transport que implementa la API REST de Brevo
   - Usa Guzzle HTTP client para hacer llamadas HTTPS
   - Maneja adjuntos, CC, BCC, Reply-To
   - Logs automáticos de éxito y errores

2. **BrevoMailServiceProvider** (`app/Providers/BrevoMailServiceProvider.php`)
   - Registra el transport personalizado en Laravel's Mail Manager
   - Se carga automáticamente desde `config/app.php`

3. **Configuración** (`config/mail.php` y `config/services.php`)
   - Define el mailer 'brevo' como nuevo transport
   - Almacena la API Key en `services.brevo.api_key`

4. **Variables de entorno** (`.env`)
   - `MAIL_MAILER=brevo` - Define el mailer por defecto
   - `BREVO_API_KEY=xxx` - API Key de Brevo
   - `MAIL_FROM_ADDRESS=hoteloasisreservas1@gmail.com` - Email remitente
   - `MAIL_FROM_NAME=Hotel Oasis` - Nombre del remitente

## 🚀 Instalación

### 1. Copiar los archivos creados ✅

Ya están creados:
- `app/Mail/Transport/BrevoTransport.php`
- `app/Providers/BrevoMailServiceProvider.php`
- `app/Console/Commands/TestBrevoEmail.php`
- `tests/Feature/Mail/BrevoMailTest.php`

### 2. Configuración en .env

Reemplaza los valores en tu `.env` (local y Railway):

```env
# Mail Driver
MAIL_MAILER=brevo
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=aca5db001@smtp-brevo.com
MAIL_PASSWORD=xxxxxxxxxxx
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hoteloasisreservas1@gmail.com
MAIL_FROM_NAME="Hotel Oasis"
MAIL_TIMEOUT=30

# Brevo API
BREVO_API_KEY=tu_api_key_aqui
```

### 3. Obtener la API Key de Brevo

1. Ir a [app.brevo.com](https://app.brevo.com)
2. Ingresar con tu cuenta (crea una si no tienes)
3. Ir a **Settings** → **SMTP & API**
4. Copiar el **API Key**
5. Guardar en `.env`: `BREVO_API_KEY=xxx`

⚠️ **Importante**: Mantener la API Key segura. Nunca commitearla a Git.

## 🧪 Pruebas

### Test local (Artisan Command)

```bash
php artisan mail:test-brevo test@example.com
```

Salida esperada:
```
🧪 Iniciando prueba de envío de correo vía Brevo...

⚙️  Configuración actual:
│ Parámetro         │ Valor                                          │
├───────────────────┼────────────────────────────────────────────────┤
│ MAIL_MAILER       │ brevo                                          │
│ MAIL_FROM_ADDRESS │ hoteloasisreservas1@gmail.com                 │
│ MAIL_FROM_NAME    │ Hotel Oasis                                   │
│ BREVO_API_KEY     │ xxxxx*****xxxxx                               │

📧 Enviando correo de reserva pendiente a: test@example.com
✅ ¡Correo enviado exitosamente vía Brevo!
```

### Test con PHPUnit

```bash
php artisan test tests/Feature/Mail/BrevoMailTest.php
```

## 📧 Compatibilidad con Mailables existentes

Todos los Mailables actuales funcionan **sin cambios**:

```php
// app/Services/ReservationEmailService.php
Mail::to($email)->send(new ReservationPendingMail($order));
```

El sistema usa automáticamente el transport Brevo configurado en `MAIL_MAILER=brevo`.

## 🔌 Railway Configuration

En Railway, agregar estas variables en **Environment Variables**:

```
MAIL_MAILER=brevo
MAIL_FROM_ADDRESS=hoteloasisreservas1@gmail.com
MAIL_FROM_NAME=Hotel Oasis
MAIL_TIMEOUT=30
BREVO_API_KEY=[tu_api_key]
```

Las otras variables de SMTP (MAIL_HOST, MAIL_PORT, etc.) se pueden dejar sin cambios, ya que el transport Brevo no las usa.

## 📊 API de Brevo - Detalles técnicos

### Endpoint utilizado
```
POST https://api.brevo.com/v3/smtp/email
```

### Headers requeridos
```
Content-Type: application/json
Accept: application/json
api-key: {BREVO_API_KEY}
```

### Payload de ejemplo
```json
{
  "sender": {
    "email": "hoteloasisreservas1@gmail.com",
    "name": "Hotel Oasis"
  },
  "to": [
    {
      "email": "guest@example.com",
      "name": "Guest Name"
    }
  ],
  "subject": "Información de tu Reserva",
  "htmlContent": "<h1>Hola</h1><p>Tu reserva está confirmada</p>",
  "textContent": "Hola, tu reserva está confirmada",
  "attachment": [
    {
      "content": "base64content",
      "name": "document.pdf"
    }
  ]
}
```

## 🔍 Monitoreo y Logs

Los logs se guardan en `storage/logs/` con nivel de detalle:

```
[INFO] Correo enviado vía Brevo: to=guest@example.com, subject=Información de tu Reserva
[ERROR] Error al enviar correo vía Brevo: to=guest@example.com, error=Invalid API Key
```

Para ver logs en tiempo real:
```bash
tail -f storage/logs/laravel.log
```

## 🛠️ Troubleshooting

### ❌ "Invalid API Key"
- Verificar que `BREVO_API_KEY` esté correctamente configurada
- Comprobar que no tenga espacios antes/después
- Regenerar la key en app.brevo.com

### ❌ "Daily limit exceeded"
- Brevo tiene límite de emails según el plan
- Verificar el plan en app.brevo.com/settings/account

### ❌ "Invalid sender address"
- El email remitente debe estar verificado en Brevo
- Ir a app.brevo.com/settings/sender-emails
- Verificar `hoteloasisreservas1@gmail.com`

### ❌ Transport not found
- Ejecutar: `php artisan config:clear`
- Verificar que `BrevoMailServiceProvider::class` esté en `config/app.php`

## 📝 Notas importantes

1. **No se requieren cambios en Mailables existentes**
   - `ReservationPendingMail` y otros funcionan automáticamente

2. **Adjuntos soportados**
   - El transport maneja PDFs, imágenes, etc. automáticamente

3. **Fallback a SMTP**
   - Para revertir, cambiar `MAIL_MAILER=smtp` en `.env`

4. **Rate Limiting**
   - Brevo tiene rate limits según el plan
   - Para mass email, considerar Queue + Throttling

5. **Historial de emails**
   - Todos los emails se registran en Brevo dashboard
   - Útil para debugging y auditoría

## 🔗 Referencias

- [Documentación oficial Brevo API](https://developers.brevo.com/reference/sendtransacemail)
- [Laravel Mail Documentation](https://laravel.com/docs/11.x/mail)
- [Brevo Plans & Pricing](https://www.brevo.com/pricing/)

## ✅ Checklist de implementación

- [x] Transport personalizado creado
- [x] Service Provider registrado
- [x] Config/mail.php actualizado
- [x] Config/services.php actualizado
- [x] .env configurado
- [x] Comando de prueba creado
- [x] Tests de integración creados
- [x] Documentación completada
- [ ] API Key agregada en Railway (pendiente del usuario)
- [ ] Prueba de envío exitosa (pendiente del usuario)
