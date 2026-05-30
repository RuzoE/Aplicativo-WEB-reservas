# 🚀 GUÍA DE ACTIVACIÓN EN PRODUCCIÓN

## 📌 INTRODUCCIÓN

Este documento describe los pasos exactos para activar los cambios implementados en el módulo de Usuarios y Empleados en un ambiente de producción.

**Tiempo estimado:** 5-10 minutos  
**Complejidad:** Baja  
**Riesgos:** Muy bajos (solo cambios de BD)

---

## ✅ PRE-REQUISITOS

Verificar que estén presentes:
- [ ] Proyecto Laravel actualizado
- [ ] Composer instalado
- [ ] Acceso a línea de comandos
- [ ] Backup de base de datos (RECOMENDADO)

---

## 📋 PASOS DE ACTIVACIÓN

### PASO 1: Respaldar Base de Datos
**Tiempo:** 1-2 minutos

```bash
# Para MySQL
mysqldump -u usuario -p nombre_bd > backup_bd_$(date +%Y%m%d_%H%M%S).sql

# Para SQLite
cp database/database.sqlite database/database_backup_$(date +%Y%m%d_%H%M%S).sqlite
```

✅ **Estado:** Base de datos respaldada

---

### PASO 2: Instalar Dependencias (si no están instaladas)
**Tiempo:** 2-5 minutos (depende de velocidad de red)

```bash
# Navegar al directorio del proyecto
cd /ruta/del/proyecto

# Instalar/actualizar dependencias
composer install --no-dev --optimize-autoloader
```

✅ **Estado:** Dependencias listas

---

### PASO 3: Ejecutar Migraciones
**Tiempo:** < 1 minuto

```bash
# Ejecutar TODAS las migraciones pendientes
php artisan migrate --force

# Alternativamente, ejecutar solo la migración nueva:
# php artisan migrate --path=database/migrations/2026_05_29_000001_enhance_user_employee_relation.php --force
```

**Esperado:**
```
Migrating: 2026_05_29_000001_enhance_user_employee_relation
Migrated:  2026_05_29_000001_enhance_user_employee_relation (xxx.xx ms)
```

✅ **Estado:** Nuevas columnas agregadas a tabla `users`

---

### PASO 4: Ejecutar Seeders (Asegurar Roles)
**Tiempo:** < 1 minuto

```bash
# Ejecutar seeder de roles y permisos
php artisan db:seed --class=RoleAndPermissionSeeder
```

**Esperado:**
```
Seeding: Database\Seeders\RoleAndPermissionSeeder
Seeded: Database\Seeders\RoleAndPermissionSeeder (xxx.xx ms)
```

✅ **Estado:** Roles configurados correctamente

---

### PASO 5: Limpiar Caché
**Tiempo:** < 1 minuto

```bash
# Limpiar todos los cachés
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Alternativamente, limpiar todo:
# php artisan cache:clear
# php artisan config:clear
```

✅ **Estado:** Caché actualizado

---

### PASO 6: Verificar Instalación
**Tiempo:** 1 minuto

```bash
# Verificar migraciones ejecutadas
php artisan migrate:status

# Verificar que el servidor Laravel funciona
php artisan serve --host=0.0.0.0 --port=8000
```

Visitar en navegador: `http://localhost:8000/admin`

✅ **Estado:** Verificación completada

---

## 🔍 VERIFICACIÓN POST-ACTIVACIÓN

### Test 1: Crear Empleado
```
1. Ir a Admin → Empleados
2. Crear nuevo empleado con rol "Minibar"
3. Verificar que:
   - Usuario creado en tabla users ✅
   - is_employee = true ✅
   - employee_department = "minibar" ✅
   - role = "minibar" ✅
```

### Test 2: Acceder a Panel de Usuarios
```
1. Ir a Admin → Usuarios
2. Verificar que:
   - Botón "Nuevo Usuario" NO existe ✅
   - Descripción menciona "automáticamente" ✅
   - Rol mostrado es automático ✅
```

### Test 3: Cambiar Contraseña
```
1. Ir a Admin → Usuarios → Editar usuario → Cambiar Contraseña
2. Verificar que:
   - Campo "Contraseña Actual" NO existe ✅
   - Solo hay: "Nueva contraseña" y "Confirmar" ✅
   - Indicador de fortaleza funciona ✅
   - Guardado exitoso ✅
```

### Test 4: Rol "Invitado"
```
1. Registrar usuario desde página pública
2. Verificar que:
   - Usuario creado sin admin ✅
   - Rol = "invitado" ✅
   - is_employee = false ✅
3. Intentar acceder a /admin
   - Debe mostrar error 403 ✅
```

---

## ⚠️ TROUBLESHOOTING

### Problema: "Migration class not found"
```bash
# Solución
composer dump-autoload
php artisan migrate:refresh --path=database/migrations/2026_05_29_000001_enhance_user_employee_relation.php
```

### Problema: "Doctrine\DBAL\Driver\PDOException"
```bash
# Solución: Asegurar que la columna no existe
php artisan tinker
>>> DB::select("SHOW COLUMNS FROM users WHERE Field = 'is_employee'")
# Si existe, verificar que la migración no se haya ejecutado dos veces
```

### Problema: "Middleware not found"
```bash
# Solución: Limpiar caché de rutas
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

### Problema: Botón "Nuevo Usuario" aún aparece
```bash
# Solución: Limpiar caché de vistas
php artisan view:clear
# Si persiste, verificar el archivo fue modificado correctamente
```

---

## 📊 ROLLBACK (Si es necesario)

En caso de que algo falle, revertir es simple:

```bash
# Ver migraciones ejecutadas
php artisan migrate:status

# Revertir última migración
php artisan migrate:rollback --step=1

# O revertir a una migración específica
php artisan migrate:rollback --path=database/migrations/2026_05_29_000001_enhance_user_employee_relation.php

# Restaurar de backup
mysql -u usuario -p nombre_bd < backup_bd_YYYYMMDD_HHMMSS.sql
```

---

## ✨ CHECKLIST DE ACTIVACIÓN

- [ ] Backup de BD realizado
- [ ] Composer install ejecutado
- [ ] Migraciones ejecutadas (`php artisan migrate --force`)
- [ ] Seeders ejecutados (`php artisan db:seed --class=RoleAndPermissionSeeder`)
- [ ] Caché limpiado
- [ ] Test 1: Crear empleado ✅
- [ ] Test 2: Panel usuarios ✅
- [ ] Test 3: Cambiar contraseña ✅
- [ ] Test 4: Rol invitado ✅
- [ ] Usuarios notificados de cambios
- [ ] Documentación actualizada

---

## 📞 SOPORTE

Si hay problemas durante la activación:

1. **Verificar logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Verificar BD:**
   ```bash
   php artisan tinker
   >>> DB::table('users')->select('id','name','is_employee','employee_department')->first()
   ```

3. **Contactar soporte técnico** con los siguientes datos:
   - Error exacto en logs
   - Versión de Laravel (`php artisan --version`)
   - Versión de PHP (`php -v`)
   - Última migración ejecutada (`php artisan migrate:status`)

---

## 🎯 VALIDACIÓN FINAL

```bash
# Ejecutar todos los tests
php artisan test

# Verificar que no hay errores en el log
grep -i "error" storage/logs/laravel.log | tail -20

# Verificar estado de la BD
php artisan tinker
>>> User::count()  # Debe mostrar número de usuarios
>>> Role::all()    # Debe mostrar roles incluido 'invitado'
```

---

## 📝 DOCUMENTOS DE REFERENCIA

- `RESUMEN_FINAL.md` - Resumen ejecutivo
- `CAMBIOS_IMPLEMENTADOS.md` - Detalles técnicos
- `VERIFICACION_CAMBIOS.md` - Cambios específicos por archivo
- `README.md` - Documentación general del proyecto

---

## ✅ CONFIRMACIÓN DE ÉXITO

Si ves esto en tu panel de usuarios, **todo fue exitoso:**

```
Panel de Usuarios
├─ [NO hay botón "Nuevo Usuario"] ✅
├─ Tabla muestra:
│  ├─ admin@hotel.com → Rol: Administrador ✅
│  ├─ minibar@hotel.com → Rol: Minibar ✅
│  └─ cliente@gmail.com → Rol: Invitado ✅
└─ Cambiar contraseña [sin campo anterior] ✅
```

---

**Guía actualizada:** 2026-05-29  
**Versión:** 1.0  
**Estado:** Listo para producción
