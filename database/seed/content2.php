<?php
// Módulos 5 a 8.

// ============ MÓDULO 5 · Funciones condicionales ============
$l = L(5, 'Función SI', 'Tomar decisiones según una condición.',
    '<p>Sintaxis: <code>=SI(prueba_lógica; valor_si_verdadero; valor_si_falso)</code>. Evalúa una condición y devuelve un resultado u otro. Operadores de comparación: <code>&gt;</code>, <code>&lt;</code>, <code>&gt;=</code>, <code>&lt;=</code>, <code>=</code> y <code>&lt;&gt;</code> (distinto). El texto va entre comillas.</p>',
    '<p>Para indicar si un alumno aprobó con nota mínima 10: <code>=SI(B2&gt;=10;"Aprobado";"Reprobado")</code>.</p>',
    S(['Alumno', 'Nota', 'Condición'], [['Ana', 15, 'Aprobado'], ['Pedro', 8, 'Reprobado']]));
X(5, $l, 'Usar SI', 'SI', 'intermedio', 'complete_formula',
    'Escribe en C2 una fórmula que muestre "Aprobado" si la nota de B2 es mayor o igual a 10 y "Reprobado" en caso contrario.',
    '=SI(B2>=10;"Aprobado";"Reprobado")', 'La prueba lógica es B2>=10; si se cumple devuelve "Aprobado", si no, "Reprobado". Puedes usar ; o , como separador.', null,
    ['data' => S(['Alumno', 'Nota', 'Condición'], [['Ana', 15, '']]), 'kind' => 'formula']);

$est = S(['Alumno', 'Condición'], [['Ana', 'Aprobado'], ['Luis', 'Reprobado'], ['Marta', 'Aprobado'], ['Pedro', 'Aprobado'], ['Sofía', 'Reprobado']]);
$l = L(5, 'Función CONTAR.SI', 'Contar celdas que cumplen un criterio.',
    '<p>Sintaxis: <code>=CONTAR.SI(rango; criterio)</code>. Cuenta cuántas celdas del rango cumplen el criterio. El criterio puede ser un valor (<code>"Aprobado"</code>) o una comparación entre comillas (<code>"&gt;10"</code>).</p>',
    '<p>Para saber cuántos alumnos aprobaron: <code>=CONTAR.SI(B2:B6;"Aprobado")</code>.</p>', $est);
X(5, $l, 'Usar CONTAR.SI', 'CONTAR.SI', 'intermedio', 'solve_operation',
    '¿Qué valor devuelve =CONTAR.SI(B2:B6;"Aprobado") con los datos de la hoja?', '3',
    'Hay tres celdas con el texto Aprobado: Ana, Marta y Pedro.', null, ['data' => $est, 'kind' => 'number']);

$vend = S(['Vendedor', 'Ventas'], [['Ana', 500], ['Luis', 300], ['Ana', 450], ['Luis', 200], ['Marta', 600]]);
$l = L(5, 'Función SUMAR.SI', 'Sumar solo los valores que cumplen un criterio.',
    '<p>Sintaxis: <code>=SUMAR.SI(rango; criterio; [rango_suma])</code>. Recorre el rango buscando el criterio y suma los valores correspondientes del rango_suma.</p>',
    '<p>Para sumar las ventas de Ana: <code>=SUMAR.SI(A2:A6;"Ana";B2:B6)</code>.</p>', $vend);
X(5, $l, 'Usar SUMAR.SI', 'SUMAR.SI', 'intermedio', 'solve_operation',
    '¿Qué valor devuelve =SUMAR.SI(A2:A6;"Ana";B2:B6) con los datos de la hoja?', '950', 'Las ventas de Ana son 500 y 450: 500 + 450 = 950.', null, ['data' => $vend, 'kind' => 'number']);

$sec = S(['Sección', 'Nota'], [['A', 14], ['B', 12], ['A', 16], ['B', 10], ['A', 18]]);
$l = L(5, 'Función PROMEDIO.SI', 'Promediar solo lo que cumple un criterio.',
    '<p>Sintaxis: <code>=PROMEDIO.SI(rango; criterio; [rango_promedio])</code>. Calcula la media únicamente de las filas que cumplen el criterio.</p>',
    '<p>Promedio de las notas de la sección A: <code>=PROMEDIO.SI(A2:A6;"A";B2:B6)</code>.</p>', $sec);
X(5, $l, 'Usar PROMEDIO.SI', 'PROMEDIO.SI', 'intermedio', 'solve_operation',
    '¿Qué valor devuelve =PROMEDIO.SI(A2:A6;"A";B2:B6) con los datos de la hoja?', '16', 'Las notas de la sección A son 14, 16 y 18. (14 + 16 + 18) / 3 = 16.', null, ['data' => $sec, 'kind' => 'number']);

// ============ MÓDULO 6 · Organización de datos ============
$l = L(6, 'Ordenar información', 'Ordenar datos por uno o varios criterios.',
    '<p>En Datos → Ordenar, o con los botones A→Z / Z→A, puedes ordenar alfabética o numéricamente. Con <strong>Ordenar</strong> personalizado añades niveles (por ejemplo, primero por departamento y luego por salario).</p><p><strong>Importante:</strong> selecciona toda la tabla o una sola celda de ella; si ordenas una única columna aislada, se rompe la correspondencia entre los datos de cada fila.</p>',
    '<p>Para ver los salarios de mayor a menor, haz clic en una celda de la columna Salario y pulsa Ordenar de Z a A. Todas las filas se mueven juntas.</p>',
    S(['Nombre', 'Departamento', 'Salario'], [['Elena', 'Admin', 1000], ['Carla', 'Ventas', 900], ['Diego', 'Ventas', 1100]]));
X(6, $l, 'Ordenar una sola columna', 'Ordenar', 'basico', 'multiple_choice',
    'Ordenaste solamente la columna B de una tabla y los nombres de la columna A no se movieron. ¿Qué ocurrió?',
    ['*Se rompió la correspondencia entre los datos de cada fila', 'Excel mejoró la tabla automáticamente', 'La tabla se bloqueó', 'Nada, es el comportamiento normal'],
    'Cada fila representa un registro; si solo una columna cambia de orden, los datos quedan asociados a la persona equivocada.');

$l = L(6, 'Filtros', 'Mostrar solo las filas que interesan.',
    '<p>Activa los filtros con Datos → Filtro o <kbd>Ctrl+Mayús+L</kbd>. En cada encabezado aparece una flecha para filtrar por texto, número, fecha o color.</p><p>Un filtro <strong>no elimina</strong> filas: solo las oculta (los números de fila cambian a azul). Se quita con “Borrar filtro”.</p>',
    '<p>Filtra la columna Departamento por “Ventas” y verás solo esos trabajadores. Los demás siguen en la hoja, solo ocultos.</p>',
    S(['Nombre', 'Departamento', 'Salario'], [['Elena', 'Admin', 1000], ['Carla', 'Ventas', 900], ['Diego', 'Ventas', 1100]]));
X(6, $l, 'Efecto de un filtro', 'Filtros', 'basico', 'true_false',
    'Al aplicar un filtro, las filas que no cumplen el criterio se eliminan definitivamente.', ['Verdadero', '*Falso'],
    'Los filtros solo ocultan filas temporalmente; los datos siguen en la hoja y reaparecen al quitar el filtro.');

$cat = S(['Código', 'Producto', 'Precio'], [[101, 'Lápiz', 0.5], [102, 'Cuaderno', 2.5], [103, 'Regla', 1.2]]);
$l = L(6, 'Buscar información', 'Buscar valores en una tabla con BUSCARV.',
    '<p>Para buscar texto en la hoja usa <kbd>Ctrl+B</kbd> (Buscar) y <kbd>Ctrl+L</kbd> (Reemplazar). Para recuperar datos de una tabla se usa <code>=BUSCARV(valor_buscado; matriz; columna; [ordenado])</code>.</p><ul><li><em>valor_buscado:</em> el código que quieres encontrar.</li><li><em>matriz:</em> la tabla donde buscar (el valor buscado debe estar en la primera columna).</li><li><em>columna:</em> número de columna con el dato a devolver.</li><li><em>ordenado:</em> FALSO (o 0) para coincidencia exacta.</li></ul>',
    '<p>Para obtener el producto del código 103: <code>=BUSCARV(103;A2:C4;2;FALSO)</code> devuelve “Regla”.</p>', $cat);
X(6, $l, 'Usar BUSCARV', 'BUSCARV', 'intermedio', 'complete_formula',
    'Escribe una fórmula que busque el precio (columna 3) del código 102 en la tabla A2:C4, con coincidencia exacta.',
    '=BUSCARV(102;A2:C4;3;FALSO)||=BUSCARV(102;A2:C4;3;0)', 'BUSCARV busca 102 en la primera columna de A2:C4 y devuelve el valor de la columna 3 (el precio 2,5). FALSO exige coincidencia exacta.', null,
    ['data' => $cat, 'kind' => 'formula']);

$l = L(6, 'Validación de datos', 'Controlar qué valores se pueden escribir.',
    '<p>En Datos → Validación de datos defines reglas para una celda: <strong>lista desplegable</strong>, números enteros o decimales entre un mínimo y máximo, fechas, longitud del texto o una fórmula propia. Puedes añadir un mensaje de entrada y un mensaje de error.</p>',
    '<p>En la columna “Estado”, se crea una lista con “Aprobado;Reprobado”. Al hacer clic en la celda aparece una flecha con las dos opciones y no se acepta nada más.</p>', null);
X(6, $l, 'Lista desplegable', 'Validación de datos', 'basico', 'multiple_choice',
    '¿Qué criterio de validación permite crear una lista desplegable con opciones fijas?',
    ['*Lista', 'Número entero', 'Fecha', 'Longitud del texto'], 'El criterio Lista permite elegir entre valores predefinidos y evita errores de escritura.');

$l = L(6, 'Formato condicional', 'Resaltar datos automáticamente según reglas.',
    '<p>En Inicio → Formato condicional creas reglas que cambian el aspecto de las celdas según su valor: <strong>resaltar celdas</strong> (mayor que, menor que, entre, texto que contiene), <strong>barras de datos</strong>, <strong>escalas de color</strong> y <strong>conjuntos de iconos</strong>. Si el dato cambia, el formato se actualiza solo.</p>',
    '<p>Para resaltar en rojo las notas menores a 10: selecciona B2:B20 → Formato condicional → Resaltar reglas de celdas → Es menor que → 10.</p>',
    S(['Alumno', 'Nota'], [['Ana', 15], ['Pedro', 8], ['Marta', 18]]));
X(6, $l, 'Herramienta adecuada', 'Formato condicional', 'basico', 'multiple_choice',
    'Quieres resaltar automáticamente en rojo las notas menores a 10. ¿Qué herramienta usas?',
    ['*Formato condicional → Resaltar reglas de celdas → Es menor que', 'Validación de datos → Lista', 'Ordenar de menor a mayor', 'Combinar y centrar'],
    'El formato condicional aplica formato según el valor, y se actualiza solo cuando los datos cambian.');

// ============ MÓDULO 7 · Gráficos ============
$l = L(7, 'Gráficos de barras', 'Comparar valores entre categorías.',
    '<p>Los gráficos de <strong>barras</strong> (horizontales) y <strong>columnas</strong> (verticales) comparan magnitudes entre categorías: ventas por sucursal, notas por alumno, gastos por rubro. Se crean seleccionando los datos (con encabezados) y eligiendo Insertar → Gráficos.</p>',
    '<p>Con las ventas de cuatro sucursales, un gráfico de columnas permite ver de un vistazo cuál vende más y cuál menos.</p>',
    S(['Sucursal', 'Ventas'], [['Centro', 5200], ['Norte', 4100], ['Sur', 3800], ['Este', 4700]]), 'assets/img/grafico-barras.svg');
X(7, $l, 'Gráfico para comparar', 'Gráficos de barras', 'basico', 'multiple_choice',
    '¿Qué gráfico es el ideal para comparar las ventas de cinco sucursales?',
    ['*Gráfico de barras o columnas', 'Gráfico de líneas', 'Gráfico de radar', 'Ninguno es adecuado'], 'Las barras o columnas facilitan la comparación entre categorías independientes.');

$l = L(7, 'Gráficos circulares', 'Mostrar la proporción de cada parte en un total.',
    '<p>El gráfico <strong>circular</strong> muestra qué porcentaje del total representa cada categoría. Úsalo con <strong>una sola serie</strong> de datos y pocas categorías (hasta unas 6), cuyos valores sumen un todo (100 %). Agrega etiquetas de porcentaje para facilitar la lectura.</p>',
    '<p>La distribución de los gastos del mes (vivienda, comida, transporte, ocio) se entiende bien en un gráfico circular.</p>',
    S(['Categoría', 'Gasto'], [['Vivienda', 450], ['Comida', 280], ['Transporte', 75], ['Ocio', 40]]), 'assets/img/grafico-circular.svg');
X(7, $l, 'Cuándo usar circular', 'Gráficos circulares', 'basico', 'true_false',
    'Un gráfico circular es adecuado para mostrar la evolución de las ventas mes a mes durante tres años.', ['Verdadero', '*Falso'],
    'Para evolución en el tiempo es mejor un gráfico de líneas. El circular sirve para partes de un todo.');

$l = L(7, 'Gráficos de líneas', 'Representar tendencias en el tiempo.',
    '<p>El gráfico de <strong>líneas</strong> une puntos consecutivos y es ideal para mostrar la <strong>evolución</strong> de un dato en el tiempo: ventas mensuales, temperatura por hora, inscripciones por año. Permite comparar varias series en el mismo gráfico.</p>',
    '<p>Con las ventas de seis meses, una línea ascendente indica crecimiento, y una caída marcada señala un problema que investigar.</p>',
    S(['Mes', 'Ventas'], [['Ene', 1000], ['Feb', 1200], ['Mar', 900], ['Abr', 1500], ['May', 1650], ['Jun', 1800]]), 'assets/img/grafico-lineas.svg');
X(7, $l, 'Gráfico de tendencia', 'Gráficos de líneas', 'basico', 'multiple_choice',
    'Para mostrar cómo cambia la temperatura cada hora a lo largo de un día, el gráfico más adecuado es:',
    ['*El gráfico de líneas', 'El gráfico circular', 'El gráfico de barras apiladas al 100 %', 'Ninguno'], 'Los datos ordenados en el tiempo se representan mejor con líneas, porque muestran la tendencia.');

$vm = S(['Mes', 'Ventas'], [['Enero', 1000], ['Febrero', 1200], ['Marzo', 900], ['Abril', 1500]]);
$l = L(7, 'Interpretación de resultados', 'Leer un gráfico y sacar conclusiones.',
    '<p>Un gráfico bien hecho tiene <strong>título</strong>, <strong>ejes rotulados</strong>, <strong>leyenda</strong> cuando hay varias series y escalas claras. Para interpretarlo: identifica el máximo y el mínimo, compara categorías, observa la tendencia y busca valores atípicos. Finalmente, redacta una conclusión con tus palabras.</p>',
    '<p>En las ventas de enero a abril, marzo cae por debajo de enero y abril logra el mayor valor: la conclusión es una recuperación fuerte tras el bajón de marzo.</p>', $vm);
X(7, $l, 'Leer datos', 'Interpretación', 'basico', 'analyze_table',
    'Según la tabla (que sería la base de un gráfico), ¿en qué mes se registró la mayor venta?', 'Abril||Abr', 'La venta de abril (1500) supera a las de enero (1000), febrero (1200) y marzo (900).', null,
    ['data' => $vm, 'kind' => 'text']);

// ============ MÓDULO 8 · Aplicaciones prácticas ============
$ven = S(['Producto', 'Cantidad', 'Precio'], [['Camisa', 3, 20], ['Pantalón', 2, 35], ['Gorra', 5, 8]]);
$l = L(8, 'Control de ventas', 'Registrar ventas y calcular importes y totales.',
    '<p>Una hoja de ventas típica tiene Producto, Cantidad, Precio e Importe (<code>=B2*C2</code>). Al final se calcula el total con <code>=SUMA(D2:D4)</code>. Con SUMAR.SI puedes sumar por vendedor o por producto, y un gráfico ayuda a comparar resultados.</p>',
    '<p>Con tres productos vendidos, el importe de cada línea se calcula con una multiplicación y el total del día con SUMA.</p>', $ven);
X(8, $l, 'Total de ventas', 'Control de ventas', 'basico', 'solve_operation',
    '¿Cuál es el total general de la venta (suma de cantidad × precio de cada producto)?', '170',
    'Camisa: 3×20 = 60; Pantalón: 2×35 = 70; Gorra: 5×8 = 40. Total: 60 + 70 + 40 = 170.', null, ['data' => $ven, 'kind' => 'number']);

$inv = S(['Producto', 'Existencia', 'Mínimo'], [['Arroz', 8, 10], ['Harina', 25, 12], ['Aceite', 4, 6]]);
$l = L(8, 'Control de inventario', 'Controlar existencias y detectar reposiciones.',
    '<p>En un inventario registras entradas y salidas: <code>existencia = entradas − salidas</code>. El <strong>valor del inventario</strong> es existencia × costo. Con SI puedes generar alertas: <code>=SI(B2&lt;C2;"Reponer";"OK")</code>, y con formato condicional resaltarlas.</p>',
    '<p>Si el arroz tiene 8 unidades y el mínimo es 10, la fórmula muestra “Reponer” para que se haga un pedido.</p>', $inv);
X(8, $l, 'Alerta de reposición', 'Control de inventario', 'intermedio', 'complete_formula',
    'Escribe en D2 una fórmula que muestre "Reponer" si la existencia (B2) es menor que el mínimo (C2) y "OK" en caso contrario.',
    '=SI(B2<C2;"Reponer";"OK")', 'La condición es B2<C2. Si se cumple devuelve "Reponer"; si no, "OK".', null, ['data' => $inv, 'kind' => 'formula']);

$not = S(['Alumno', 'Nota 1', 'Nota 2', 'Nota 3'], [['Ana', 16, 14, 18], ['Luis', 10, 8, 9]]);
$l = L(8, 'Registro de calificaciones', 'Calcular promedios y condiciones de aprobación.',
    '<p>Una planilla de notas calcula el promedio por alumno (<code>=PROMEDIO(B2:D2)</code>), la condición (<code>=SI(E2&gt;=10;"Aprobado";"Reprobado")</code>), el promedio del curso y cuántos aprobaron con <code>=CONTAR.SI(F2:F20;"Aprobado")</code>.</p>',
    '<p>Ana tiene 16, 14 y 18: su promedio es 16 y aprueba. Luis tiene 10, 8 y 9: su promedio es 9 y reprueba.</p>', $not);
X(8, $l, 'Promedio de un alumno', 'Calificaciones', 'basico', 'solve_operation',
    '¿Cuál es el promedio de Ana (=PROMEDIO(B2:D2))?', '16', '(16 + 14 + 18) / 3 = 48 / 3 = 16.', null, ['data' => $not, 'kind' => 'number']);

$gas = S(['Categoría', 'Monto'], [['Comida', 250], ['Transporte', 120], ['Ocio', 60], ['Servicios', 100]]);
$l = L(8, 'Control de gastos', 'Clasificar gastos y calcular su peso en el total.',
    '<p>Para controlar gastos: registra fecha, concepto, categoría y monto; suma por categoría con SUMAR.SI y calcula el peso de cada una: <code>=B2/SUMA($B$2:$B$5)</code> con formato Porcentaje. Un gráfico circular muestra cómo se reparte el dinero.</p>',
    '<p>Si Comida suma 250 de un total de 530, representa el 47,17 % del gasto.</p>', $gas);
X(8, $l, 'Peso de una categoría', 'Control de gastos', 'intermedio', 'solve_operation',
    '¿Qué porcentaje del gasto total representa Transporte? Escribe el valor con dos decimales y sin el símbolo %.', '22.64',
    'Total: 250 + 120 + 60 + 100 = 530. Transporte: 120 / 530 = 0,2264 → 22,64 %.', null,
    ['data' => $gas, 'kind' => 'number', 'tol' => 0.05, 'instr' => 'Escribe solo el número, por ejemplo 12.34']);

$trab = S(['Nombre', 'Departamento', 'Salario'], [['Carla', 'Ventas', 900], ['Diego', 'Ventas', 1100], ['Elena', 'Admin', 1000], ['Mario', 'Admin', 800]]);
$l = L(8, 'Registro de trabajadores', 'Calcular nóminas, promedios y bonificaciones.',
    '<p>Una hoja de personal incluye nombre, cargo, departamento y salario. Con funciones puedes obtener la nómina total (<code>SUMA</code>), el promedio por departamento (<code>PROMEDIO.SI</code>), el número de empleados por área (<code>CONTAR.SI</code>) y bonificaciones condicionales con <code>SI</code>.</p>',
    '<p>Para el salario promedio del departamento de Ventas: <code>=PROMEDIO.SI(B2:B5;"Ventas";C2:C5)</code>.</p>', $trab);
X(8, $l, 'Promedio por departamento', 'Registro de trabajadores', 'intermedio', 'solve_operation',
    '¿Qué valor devuelve =PROMEDIO.SI(B2:B5;"Ventas";C2:C5) con los datos de la hoja?', '1000', 'Los salarios de Ventas son 900 y 1100: (900 + 1100) / 2 = 1000.', null, ['data' => $trab, 'kind' => 'number']);
