# Clínica odontológica Sonrisa Sana · Laravel

Sistema de gestión para una clínica odontológica, desarrollado a partir de las historias de usuario, las tareas de ingeniería y el diagrama de clases del proyecto (Ingeniería de Software I). Los datos se guardan en MySQL/MariaDB (base `clinica_odontologica`, puerto 3307).

## Funcionalidades por historia de usuario

| HU | Nombre | Rol | Qué hace el sistema |
|----|--------|-----|---------------------|
| 1 | Gestión Paciente | Secretaria | CRUD de pacientes, búsqueda por CI, nombre o apellido, validación de campos y de CI duplicado. |
| 2 | Historial Clínico | Doctor | Buscar un paciente y consultar su historial: fechas de atención, diente, diagnóstico, procedimiento, observaciones, materiales y radiografía. |
| 3 | Atención | Doctor | Registrar la atención de una ficha (diente, diagnóstico, procedimiento, observaciones, radiografía opcional, materiales usados y costo del procedimiento). Al guardar: descuenta el inventario, la agrega al historial, genera el cobro (costo del procedimiento + precio × cantidad de cada material) y marca la ficha como Atendida. |
| 4 | Liquidación | Secretaria | Ver los cobros pendientes, registrar pagos (monto, fecha, método) y actualizar el estado (Pendiente → Parcial → Pagado) con el saldo pendiente. |
| 5 | Fichas de pacientes | Secretaria | Asignar fichas con doctores disponibles según fecha y hora, sin permitir dos fichas del mismo doctor en el mismo horario. |
| 6 | Producto e Inventario | Secretaria | CRUD de productos y proveedores, registro de compras (aumenta el stock) y consulta del inventario con alerta de stock bajo (5 unidades o menos). |
| 7 | Gestión de Roles | Administrador | CRUD de doctores y secretarias con su usuario, inicio de sesión por rol y permisos. El administrador tiene acceso total. |

## Usuarios de prueba

| Rol | Usuario | Contraseña |
|-----|---------|------------|
| Administrador | `admin` | `admin123` |
| Secretaria | `lucia` | `secretaria123` |
| Doctor | `carlos` | `doctor123` |
| Doctor | `sofia` | `doctor123` |

Los datos de ejemplo siguen el diagrama de objetos: paciente María Gutiérrez, Dr. Carlos Rojas, secretaria Lucía Mamani, proveedor DentalSur, producto Resina compuesta, la ficha N.º 1 atendida con su cobro pagado y una ficha pendiente para mañana.

## Requisitos

- PHP 8.2 o posterior con extensiones requeridas por Laravel.
- Composer 2.
- MySQL o MariaDB (por ejemplo XAMPP) escuchando en el puerto 3307.

## Puesta en marcha

1. Crea la base de datos vacía `clinica_odontologica` (utf8mb4) en tu servidor MySQL del puerto 3307, por ejemplo desde phpMyAdmin.
2. Desde esta carpeta del proyecto:

```powershell
Copy-Item .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Abre `http://127.0.0.1:8000` e inicia sesión con uno de los usuarios de prueba.

Para volver a dejar la base con los datos de ejemplo: `php artisan migrate:fresh --seed` (borra todos los datos).

La conexión se configura en `.env` (`DB_HOST`, `DB_PORT=3307`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`). Las radiografías se guardan en `storage/app/private/radiografias` y solo las pueden abrir los doctores y el administrador.

## Despliegue en Render

El repositorio incluye un `Dockerfile` y un `render.yaml` que crean en Render el servicio web y una base de datos PostgreSQL (Render no ofrece MySQL; el sistema funciona con ambos motores).

1. Genera una clave para la aplicación y cópiala (empieza con `base64:`):

   ```powershell
   php artisan key:generate --show
   ```

2. En Render: **New → Blueprint**, conecta el repositorio de GitHub y confirma.
3. Cuando lo pida, completa las variables:
   - `APP_KEY`: la clave del paso 1.
   - `ADMIN_PASSWORD`: la contraseña que tendrá el usuario `admin`.
4. Espera a que termine el despliegue y abre la URL `https://....onrender.com`.

En el primer arranque se crean las tablas y se cargan los datos de ejemplo. Después de entrar como `admin`, cambia las contraseñas de `lucia`, `carlos` y `sofia` desde **Doctores** y **Secretarias**, porque las de ejemplo están publicadas en este README.

Cada `git push` a la rama `main` vuelve a desplegar el sistema; los datos de la base se conservan.

Limitaciones del plan gratuito de Render:

- El servicio se suspende tras unos minutos sin visitas; la primera visita después tarda alrededor de un minuto en responder.
- La base de datos PostgreSQL gratuita vence después de un tiempo (30 días según las condiciones de Render al momento de escribir esto; revisa su sitio). Antes de que venza hay que pasarla a un plan de pago o crear otra.
- Los archivos subidos (radiografías) se pierden cada vez que el servicio se reinicia o se vuelve a desplegar, porque el disco del plan gratuito no es permanente.
