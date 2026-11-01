# Instalación de ExcelLab en XAMPP (Windows)

## Requisitos
- XAMPP con **Apache** (PHP 8.1 o superior; recomendado 8.2). **No hace falta MySQL**: la base de datos vive en PostgreSQL (Neon o cualquier servidor Postgres).
- Extensiones PHP habilitadas (vienen activas en XAMPP): `pdo_pgsql`, `mbstring`, `fileinfo`, `zip`.
- Módulo `mod_rewrite` de Apache activo (por defecto lo está) y `AllowOverride All` en `htdocs` (valor por defecto de XAMPP).
- Una base de datos PostgreSQL con su **URL de conexión** (en Neon: consola → tu proyecto → *Connect*; usa siempre la URL con `sslmode=require`).

## Pasos

1. **Copiar el proyecto.** Copia la carpeta `excel` a `C:\xampp\htdocs\` y renómbrala a `excellab`
   (ruta final: `C:\xampp\htdocs\excellab`). Si prefieres mantenerla en otra ubicación, crea un enlace simbólico/junction con ese nombre dentro de `htdocs`.
2. **Definir la variable de entorno.** La conexión se lee de `DATABASE_URL` (no se guarda ninguna clave en los archivos):
   ```powershell
   setx DATABASE_URL "postgresql://USUARIO:CLAVE@HOST:5432/NEONDB?sslmode=require"
   ```
   **Cierra y vuelve a abrir el XAMPP Control Panel** (Apache solo toma las variables de entorno al arrancar). No pegues la URL dentro de `config/config.php` ni de `.htaccess`.
3. **Iniciar servicios.** En el panel de XAMPP inicia **Apache** (MySQL no se usa).
4. **Cargar la base de datos.** Con cualquier cliente de PostgreSQL (psql, pgAdmin, DBeaver…) ejecuta, **en este orden**, sobre la base de la URL:
   1. `database/schema.sql` (crea las 22 tablas)
   2. `database/seed.sql` (datos de demostración)

   Ejemplo con psql:
   ```bash
   psql "%DATABASE_URL%" -f database\schema.sql
   psql "%DATABASE_URL%" -f database\seed.sql
   ```
5. **Abrir la aplicación:** <http://localhost/excellab/>

> Nota: `app/Core/DB.php` se adapta solo a librerías antiguas de PostgreSQL (retira `channel_binding` y añade el identificador de endpoint de Neon si falta SNI), así que sirve la URL tal cual la da la consola de Neon.

## Credenciales de demostración

| Perfil | Correo | Contraseña |
|---|---|---|
| Administrador | admin@excellab.test | Admin#2026 |
| Docente | docente@excellab.test | Docente#2026 |
| Estudiante | ana.perez@excellab.test | (sin contraseña: solo el correo) |

Los estudiantes entran en la página principal escribiendo solo su correo; docentes y administradores usan el enlace «Acceso docentes» y deben cambiar su contraseña la primera vez.

## Logo y pantalla de carga
Copia el logo a `public/assets/img/logo.png` (también se acepta .webp, .jpg o .svg). Se muestra en la pantalla de carga inicial y en la entrada de estudiantes. Sin ese archivo se usa la marca de texto «ExcelLab».

## Solución de problemas

| Síntoma | Causa y solución |
|---|---|
| Todas las páginas dan 404 salvo `/excellab/public/` | `mod_rewrite` o `AllowOverride` desactivados. En `httpd.conf` verifica `LoadModule rewrite_module` y que el bloque de `htdocs` tenga `AllowOverride All`; reinicia Apache. |
| «Falta la variable de entorno DATABASE_URL» | No está definida para Apache. Usa `setx DATABASE_URL …` y **reinicia** el XAMPP Control Panel por completo. |
| «Endpoint ID is not specified» o «invalid connection option channel_binding» | La librería PostgreSQL es antigua; `DB.php` los resuelve automáticamente. Si persiste, usa la URL **directa** de Neon (sin `-pooler`) y con `sslmode=require`. |
| «password authentication failed» o «connection is insecure» | Clave o URL incorrectas, o falta `sslmode=require` al final de la URL. |
| «Estoy conectado…» con lentitud ocasional | Neon suspende el cómputo por inactividad (scale to zero): el primer intento tarda 1–3 s y luego se conecta. |
| «SQLSTATE … could not connect» / Connection refused | Sin internet, URL equivocada o el proyecto de Neon está pausado. |
| Estilos o iconos no cargan | La carpeta debe llamarse `excellab` o la URL debe coincidir con el nombre real de la carpeta; la ruta base se calcula sola. |
| No se pueden subir archivos | Revisa `upload_max_filesize` y `post_max_size` en `php.ini` (≥ 5 MB) y que `storage/uploads` sea escribible. |
| No llegan correos de recuperación | XAMPP no trae servidor de correo. Con `mail_dev = true` el enlace se muestra en pantalla y se guarda en `storage/logs/mail.log`. Para producción configura SMTP y pon `mail_dev` en `false`. |

## Regenerar los datos de demostración
`php database/build_seed.php` reescribe `database/seed.sql` (SQL de PostgreSQL) a partir de los contenidos en `database/seed/`. Luego vuelve a ejecutar `schema.sql` y `seed.sql` (esto **borra** los datos existentes).

## Puesta en producción (resumen)
`debug` y `show_demo_credentials` en `false`, `DATABASE_URL` como variable de entorno del servidor (nunca en el repositorio), HTTPS activo, servidor de correo real, copia de seguridad periódica de `storage/uploads` (Neon conserva instantáneas y el historial de la base) y eliminar las cuentas de demostración.
