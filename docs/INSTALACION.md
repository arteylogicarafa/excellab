# Instalación de ExcelLab en XAMPP (Windows)

## Requisitos
- XAMPP con **Apache** y **MySQL/MariaDB** (PHP 8.1 o superior; recomendado 8.2).
- Extensiones PHP habilitadas (vienen activas en XAMPP): `pdo_mysql`, `mbstring`, `fileinfo`, `zip`.
- Módulo `mod_rewrite` de Apache activo (por defecto lo está) y `AllowOverride All` en `htdocs` (valor por defecto de XAMPP).

## Pasos

1. **Copiar el proyecto.** Copia la carpeta `excel` a `C:\xampp\htdocs\` y renómbrala a `excellab`
   (ruta final: `C:\xampp\htdocs\excellab`). Si prefieres mantenerla en otra ubicación, crea un enlace simbólico/junction con ese nombre dentro de `htdocs`.
2. **Iniciar servicios.** En el panel de XAMPP inicia **Apache** y **MySQL**.
3. **Crear la base de datos.** Abre <http://localhost/phpmyadmin>, ve a **Importar** y sube, en este orden:
   1. `database/schema.sql` (crea la base `excellab` y las tablas)
   2. `database/seed.sql` (datos de demostración)

   O desde la consola:
   ```bash
   C:\xampp\mysql\bin\mysql.exe -u root --default-character-set=utf8mb4 < database\schema.sql
   C:\xampp\mysql\bin\mysql.exe -u root --default-character-set=utf8mb4 < database\seed.sql
   ```
4. **Revisar la conexión.** En `config/config.php` ajusta `db` si tu MySQL tiene contraseña (por defecto: usuario `root`, sin contraseña).
5. **Abrir la aplicación:** <http://localhost/excellab/>

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
| «SQLSTATE[HY000] [1049] Unknown database» | No se importó `schema.sql`. |
| «SQLSTATE[HY000] [1045] Access denied» | Usuario/contraseña de MySQL distintos a los de `config/config.php`. |
| Estilos o iconos no cargan | La carpeta debe llamarse `excellab` o la URL debe coincidir con el nombre real de la carpeta; la ruta base se calcula sola. |
| No se pueden subir archivos | Revisa `upload_max_filesize` y `post_max_size` en `php.ini` (≥ 5 MB) y que `storage/uploads` sea escribible. |
| No llegan correos de recuperación | XAMPP no trae servidor de correo. Con `mail_dev = true` el enlace se muestra en pantalla y se guarda en `storage/logs/mail.log`. Para producción configura SMTP y pon `mail_dev` en `false`. |

## Regenerar los datos de demostración
`php database/build_seed.php` reescribe `database/seed.sql` a partir de los contenidos en `database/seed/`. Luego vuelve a importar `schema.sql` y `seed.sql` (esto **borra** los datos existentes).

## Puesta en producción (resumen)
`debug` y `show_demo_credentials` en `false`, contraseñas de la base de datos propias, HTTPS activo, servidor de correo real, copia de seguridad periódica de la base de datos y de `storage/uploads`, y eliminar las cuentas de demostración.
