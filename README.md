# ExcelLab

Plataforma educativa para aprender Microsoft Excel (unidad curricular **Paquetes Informáticos**).
Lecciones, laboratorio de ejercicios, retos, casos prácticos, evaluaciones, logros y panel docente. Todo en español.

**Tecnología:** PHP 8.1+ nativo (PDO con PostgreSQL/Neon), HTML5, CSS3, JavaScript, Bootstrap 5.3, Bootstrap Icons y Chart.js (todas las librerías van incluidas en `public/assets/vendor`, sin depender de internet).

- Instalación en XAMPP: [docs/INSTALACION.md](docs/INSTALACION.md)
- Manual de uso y funcionamiento: [docs/MANUAL.md](docs/MANUAL.md)

## Credenciales de demostración

| Perfil | Correo | Contraseña inicial |
|---|---|---|
| Administrador | `admin@excellab.test` | `Admin#2026` |
| Docente | `docente@excellab.test` | `Docente#2026` |
| Estudiante | `ana.perez@excellab.test` (u otro correo cualquiera) | No usa contraseña: solo escribe su correo en la pantalla de entrada |

Los estudiantes entran solo con su correo (si no existe, la cuenta se crea sola y el avance queda ligado a ese correo). Docente y administrador entran por «Acceso docentes» (`/docentes`) y deben cambiar la contraseña en su primer acceso.
Antes de publicar el sistema: poner `show_demo_credentials` y `debug` en `false` en `config/config.php` y eliminar las cuentas de demostración desde el panel.

## Estructura

```
excel/
├─ public/                 Único directorio expuesto (index.php, assets/css, js, img, vendor)
├─ app/
│  ├─ Core/                Router, Auth, DB (PDO), helpers (CSRF, XSS, vistas), Activity, Entities
│  ├─ Controllers/         Un controlador por módulo (+ Admin/ para el panel docente)
│  ├─ Models/              Consultas reutilizables (Exercise)
│  ├─ Services/            ExerciseEvaluator, ProgressService, BadgeService, Stats, Notifier, XlsxWriter
│  └─ Views/               layouts/, components/ (hoja Excel, ejercicio), vistas por módulo
├─ config/                 config.php y routes.php
├─ database/               schema.sql, seed.sql y el generador build_seed.php (+ seed/*.php con los contenidos)
├─ storage/                uploads/ (entregas, fuera de la web) y logs/
└─ docs/                   Instalación y manual
```

## Cómo crecer sin tocar la arquitectura

- **Nuevo contenido** (módulos, lecciones, ejercicios, retos, casos, evaluaciones, insignias): desde el panel docente, sin código.
- **Nuevo tipo de entidad administrable:** añadir una entrada en `app/Core/Entities.php` (tabla, columnas y campos); el CRUD, los filtros y los estados se generan solos.
- **Nuevo tipo de ejercicio:** el tipo de actividad es una etiqueta; la corrección depende del *tipo de respuesta* (`option`, `number`, `formula`, `text`) en `ExerciseEvaluator`.
- **Nueva insignia:** crearla en «Insignias» eligiendo la métrica (lecciones, ejercicios, retos, puntos, módulos, evaluaciones, casos).

## Seguridad aplicada

Contraseñas con `password_hash`/`password_verify`; consultas preparadas PDO (sin SQL concatenado con datos del usuario); escape de salida con `e()` y HTML docente filtrado con lista blanca; token CSRF en todos los POST (formularios y AJAX); sesiones con cookie `HttpOnly` + `SameSite`, regeneración de ID al iniciar sesión y caducidad por inactividad; bloqueo temporal tras 5 intentos fallidos; control de acceso por rol en cada ruta; subida de archivos validada (extensión, MIME, firma binaria, tamaño) con nombre aleatorio y descarga protegida; registro de actividades relevantes (`activity_log`); no se borran datos académicos (estados activo/inactivo/archivado).
