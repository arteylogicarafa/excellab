# Manual de ExcelLab

## 1. Perfiles
- **Estudiante:** aprende, practica, supera retos, resuelve casos, rinde evaluaciones y consulta su progreso.
- **Docente:** administra estudiantes y contenidos, revisa entregas, consulta el seguimiento y genera reportes.
- **Administrador:** todo lo del docente, más la gestión de docentes/administradores y los ajustes generales.

## 2. Estudiante

| Sección | Qué hace |
|---|---|
| **Inicio** | Saludo, progreso general, módulos completados, ejercicios realizados, puntos, retos pendientes, últimas actividades, logros y gráficos. El botón «Continuar aprendiendo» abre la siguiente lección pendiente. |
| **Aprender Excel** | 8 módulos y 42 lecciones. Cada lección trae explicación, ejemplo con hoja tipo Excel, ilustración (cuando aplica), ejercicio de comprobación y botón «Marcar como completada». Al completar todas las lecciones de un módulo se habilita el **certificado** (imprimible / PDF). |
| **Laboratorio** | Banco de ejercicios filtrable por módulo, tema, dificultad, tipo y estado. Siete tipos: selección múltiple, verdadero/falso, completar fórmulas, identificar funciones, resolver operaciones, analizar tablas y situaciones prácticas. Retroalimentación inmediata, explicación y registro de intentos. |
| **Excel Challenge** | Retos básico / intermedio / avanzado con situación, objetivo, datos, instrucciones, tiempo estimado y puntos. El resultado se valida automáticamente; los puntos se otorgan una sola vez por reto. |
| **Casos prácticos** | Administración, Ventas, Inventario, Recursos Humanos y Educación. Unos se responden en la plataforma (calificación automática) y otros se resuelven en un Excel que se descarga, completa y entrega. |
| **Evaluaciones** | Preguntas sorteadas del banco, tiempo límite con temporizador, calificación automática, porcentaje de aprobación, intentos e historial con revisión detallada. |
| **Mis logros / Ranking** | Insignias por metas. El ranking es opcional y solo informativo (se activa o desactiva en Ajustes). |
| **Buscador y notificaciones** | Barra superior: busca lecciones, ejercicios, retos, casos y evaluaciones. La campana avisa de logros, calificaciones y módulos completados. |

### Cómo se calculan los puntos
Por cada ejercicio cuenta su **mejor** resultado; por cada reto, los puntos del primer acierto; por cada caso, la mejor calificación. Repetir actividades no infla el puntaje.

### Cómo se corrigen las respuestas
- **Número:** admite coma o punto decimal y una tolerancia configurable.
- **Fórmula:** se ignoran espacios y mayúsculas, y se aceptan `;` o `,` como separador. Debe empezar con `=`.
- **Texto:** sin distinguir mayúsculas ni acentos.
- Se pueden definir **alternativas válidas** separadas por `||` (p. ej. `=SUMA(B2:B6)||=SUM(B2:B6)`).

## 3. Docente

**Gestión académica** (menú lateral): Estudiantes, Módulos, Lecciones, Ejercicios, Retos, Casos prácticos, Evaluaciones e Insignias. En cada una: listado con buscador y filtros, alta, edición y cambio de estado (Habilitar / Deshabilitar / Archivar). **No se elimina nada**: lo archivado deja de verse para los estudiantes pero conserva su historial.

Notas por tipo de contenido:
- **Lecciones:** la teoría y el ejemplo aceptan HTML básico; la hoja de ejemplo es un JSON `{"headers":[...],"rows":[[...]]}`.
- **Ejercicios:** con alternativas, una por línea y marcando la correcta con `*`. Si se asocia a una lección, aparece como su ejercicio de comprobación.
- **Casos:** en modalidad *plataforma* se definen preguntas con su respuesta (JSON); en modalidad *archivo* los datos generan la plantilla `.xlsx` descargable.
- **Evaluaciones:** se elige el banco de preguntas, tiempo límite, % de aprobación, preguntas por intento, intentos máximos y aleatoriedad.

**Seguimiento:**
- *Progreso individual:* avance por módulo, ejercicios, retos, evaluaciones, casos, insignias e historial de cada estudiante.
- *Entregas de archivos:* descarga, calificación (0 a puntaje del caso) y retroalimentación; el estudiante recibe una notificación.
- *Reportes:* filtros por estudiante, módulo, tipo de actividad y fechas, con resumen y **exportación a CSV** (se abre en Excel).

## 4. Funcionamiento técnico en breve
- Todas las URL pasan por `public/index.php` → `Router` (`config/routes.php`) → controlador → vista.
- Cada ruta declara su nivel de acceso (`public`, `auth`, `student`, `staff`, `admin`).
- La lógica de corrección está en `app/Services/ExerciseEvaluator.php`; el avance y certificados en `ProgressService`; las insignias en `BadgeService`.
- Los formularios del panel se definen en `app/Core/Entities.php`.

## 5. Estados de los registros
`active` (visible y operativo), `inactive` (oculto temporalmente) y `archived` (retirado, con historial intacto). Las cuentas inactivas no pueden iniciar sesión.
