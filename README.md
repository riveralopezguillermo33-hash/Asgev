# ASGEV

Sistema educativo mobile-first para colegios, construido con Laravel 12, Livewire 3, Tailwind CSS 4 y Eloquent. MySQL 8 es el motor local recomendado; PostgreSQL se puede seleccionar con `DB_CONNECTION=pgsql`.

## Estado

La base incluye el esquema versionado, roles, aislamiento por colegio, modelos, políticas iniciales, auditoría de notas, autenticación y recuperación de contraseña, PWA básica y navegación del portal. El módulo de matrícula está implementado; notas, pagos, cursos y caja se desarrollarán por etapas.

La aplicación no inserta datos de muestra al migrar. Los ejemplos se aíslan en `database/seeders/DemoSeeder.php` y solo se cargan cuando se solicitan explícitamente.

## Matrícula

Administración y Caja pueden abrir **Crear matrícula** desde el portal (`/matriculas/crear`). El formulario obtiene estudiantes y opciones académicas de la base de datos, con selects dependientes para nivel, grado y sección. Antes de guardar valida los campos, que el estudiante no esté matriculado en el año elegido y que queden vacantes en la sección.

Al confirmar, una transacción crea la matrícula y programa una pensión por cada mes comprendido en las fechas del año escolar. El monto mensual y el día de vencimiento se ingresan en el formulario porque el esquema todavía no define una tarifa por colegio o grado. Si la operación no puede completarse, no quedan pensiones parciales.

## Requisitos

- PHP 8.2 o superior con PDO para el motor elegido, OpenSSL, Mbstring, Fileinfo y ZIP.
- Composer 2.
- Node.js y npm.
- MySQL 8 o PostgreSQL.
- Extensión PHP `zip` habilitada en `php.ini` para que Composer pueda instalar dependencias desde archivos.

## Instalación local

1. Instala dependencias:

   ```powershell
   composer install
   Copy-Item .env.example .env
   php artisan key:generate
   ```

2. Crea una base `asgev` y configura `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` en `.env`. Para PostgreSQL, usa `DB_CONNECTION=pgsql` y el puerto `5432`.

3. Ejecuta las migraciones y prepara archivos estáticos:

   ```powershell
   php artisan migrate
   php artisan db:seed
   php artisan storage:link
   npm install
   npm run dev
   ```

4. En otra terminal, inicia Laravel:

   ```powershell
   php artisan serve
   ```

La autenticación, recuperación de contraseña y verificación de correo usan las rutas y notificaciones de Laravel. Configura un proveedor de correo antes de enviar mensajes fuera del entorno local.

## Datos de demostración opcionales

Configura `ASGEV_DEMO_PASSWORD` en `.env` con una contraseña de al menos 12 caracteres y ejecuta:

```powershell
php artisan db:seed --class=DemoSeeder
```

La semilla crea un colegio, una familia con tres estudiantes, cuentas de docentes y administración, matrículas y tres pensiones por estudiante, competencias con notas de 0 a 20 y seis sesiones. Las cuentas usan correos `@asgev.test`; el comando imprime los correos al finalizar. No ejecutes esta semilla en una base de producción.

Para procesar notificaciones en cola en un entorno que lo requiera:

```powershell
php artisan queue:work
```

## Pruebas y compilación

```powershell
php artisan test
npm run build
```

La base de pruebas usa SQLite en memoria; la configuración de producción/local puede usar MySQL o PostgreSQL. Las notas tienen restricciones de base de datos de 0 a 20.
